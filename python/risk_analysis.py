import argparse
import json
import os
from typing import List, Dict, Any

try:
    from google.cloud import bigquery
except Exception as e:
    bigquery = None


def parse_hours_str(hours_str: str) -> List[int]:
    hours = []
    if not hours_str:
        return hours
    tokens = [t.strip() for t in hours_str.split(',') if t.strip()]
    for t in tokens:
        if '-' in t:
            try:
                start, end = t.split('-', 1)
                sh = int(start.split(':')[0])
                eh = int(end.split(':')[0])
                if sh <= eh:
                    hours.extend(list(range(sh, eh + 1)))
                else:
                    # wrap-around not supported; ignore
                    pass
            except Exception:
                continue
        else:
            try:
                hours.append(int(t.split(':')[0]))
            except Exception:
                continue
    # dedupe and clamp
    hours = [h for h in sorted(set(hours)) if 0 <= h <= 23]
    return hours


def fetch_rows(project_id: str, dataset: str, table: str,
               state_name: str = None,
               city: str = None,
               hours: List[int] = None,
               limit: int = 20000,
               keyfile: str = None) -> List[Dict[str, Any]]:
    if bigquery is None:
        return []
    if keyfile:
        os.environ['GOOGLE_APPLICATION_CREDENTIALS'] = keyfile
    client = bigquery.Client(project=project_id)
    hour_filter = ''
    if hours:
        hour_list = ','.join(str(int(h)) for h in hours)
        hour_filter = f"AND hour_of_crash IN ({hour_list})"
    loc_filter = ''
    if state_name:
        state = state_name.replace("'", "\'")
        loc_filter += f"AND state_name = '{state}' "
    if city:
        c = city.replace("'", "\'")
        loc_filter += f"AND city = '{c}' "
    query = (
        f"SELECT state_name, city, hour_of_crash, day_of_week, number_of_fatalities, "
        f"number_of_drunk_drivers, weather_condition, light_condition, driver_age, driver_sex "
        f"FROM `{project_id}.{dataset}.{table}` "
        f"WHERE 1=1 {loc_filter} {hour_filter} "
        f"LIMIT {int(limit)}"
    )
    job = client.query(query)
    rows = []
    for row in job:
        rows.append({
            'state_name': str(row.get('state_name', '') or ''),
            'city': str(row.get('city', '') or ''),
            'hour_of_crash': int(row.get('hour_of_crash') or -1),
            'day_of_week': str(row.get('day_of_week', '') or ''),
            'number_of_fatalities': int(row.get('number_of_fatalities') or 0),
            'number_of_drunk_drivers': int(row.get('number_of_drunk_drivers') or 0),
            'weather_condition': str(row.get('weather_condition', '') or ''),
            'light_condition': str(row.get('light_condition', '') or ''),
            'driver_age': int(row.get('driver_age')) if row.get('driver_age') is not None else None,
            'driver_sex': str(row.get('driver_sex', '') or ''),
        })
    return rows


def aggregate_hotspots(rows: List[Dict[str, Any]]) -> List[Dict[str, Any]]:
    agg: Dict[str, Dict[str, Any]] = {}
    for r in rows:
        key = f"{r['state_name']}|{r['city']}|{r['hour_of_crash']}"
        if key not in agg:
            agg[key] = {
                'state_name': r['state_name'],
                'city': r['city'],
                'hour_of_crash': r['hour_of_crash'],
                'count': 0,
                'fatalities': 0,
            }
        agg[key]['count'] += 1
        agg[key]['fatalities'] += r['number_of_fatalities']
    out = list(agg.values())
    out.sort(key=lambda x: (x['fatalities'], x['count']), reverse=True)
    return out[:50]


def risky_times(rows: List[Dict[str, Any]]) -> List[Dict[str, Any]]:
    by_hour = [{'count': 0, 'fatalities': 0, 'drunk': 0} for _ in range(24)]
    for r in rows:
        h = r['hour_of_crash']
        if 0 <= h < 24:
            by_hour[h]['count'] += 1
            by_hour[h]['fatalities'] += r['number_of_fatalities']
            by_hour[h]['drunk'] += r['number_of_drunk_drivers']
    out = [{'hour': h, **by_hour[h]} for h in range(24)]
    out.sort(key=lambda x: (x['fatalities'], x['count']), reverse=True)
    return out


def compute_score(rows: List[Dict[str, Any]], age: int = None) -> Dict[str, Any]:
    total = len(rows)
    if total == 0:
        return {'score': 0, 'factors': {}}
    fatal = sum(r['number_of_fatalities'] for r in rows)
    drunk = sum(r['number_of_drunk_drivers'] for r in rows)
    avg_fatal = fatal / max(1, total)
    avg_drunk = drunk / max(1, total)
    ages = sorted([r['driver_age'] for r in rows if r['driver_age'] is not None])
    median_age = ages[len(ages)//2] if ages else None
    age_score = 0.0
    if age is not None and median_age is not None:
        diff = abs(age - median_age)
        age_score = max(0.0, 1.0 - min(1.0, diff / 20.0))
    score = min(100, round((min(1.0, avg_fatal / 2.0) * 40) + (min(1.0, avg_drunk / 1.5) * 40) + (age_score * 20), 2))
    return {
        'score': score,
        'factors': {
            'avgFatalPerEvent': round(avg_fatal, 2),
            'avgDrunkDriversPerEvent': round(avg_drunk, 2),
            'medianDriverAge': median_age,
        }
    }


def derive_patterns(age: int, experience_years: int, usual_hours_str: str, hotspots: List[Dict[str, Any]]) -> List[str]:
    patterns = []
    if age is not None and age < 25:
        patterns.append('Edad menor a 25: mayor siniestralidad histórica')
    if experience_years is not None and experience_years < 3:
        patterns.append('Menos de 3 años conduciendo: incremento de riesgo')
    hours_list = parse_hours_str(usual_hours_str)
    if any(h in hours_list for h in [0,1,2,3,4,5,22,23]):
        patterns.append('Horarios nocturnos asociados a mayor severidad')
    if hotspots:
        patterns.append('Zona habitual cercana a hotspots detectados')
    return patterns


def main():
    parser = argparse.ArgumentParser()
    parser.add_argument('--driver_age', type=int)
    parser.add_argument('--experience_years', type=int)
    parser.add_argument('--state_name', type=str)
    parser.add_argument('--city', type=str)
    parser.add_argument('--usual_hours', type=str)
    # INEGI filters
    parser.add_argument('--edo', type=str)
    parser.add_argument('--mpio', type=str)
    parser.add_argument('--project_id', type=str, default=os.environ.get('BQP_PROJECT_ID', 'bigquery-public-data'))
    parser.add_argument('--dataset', type=str, default=os.environ.get('BQP_DATASET', 'nhtsa_traffic_fatalities'))
    parser.add_argument('--table', type=str, default=os.environ.get('BQP_TABLE', 'fatalities'))
    parser.add_argument('--keyfile', type=str, default=os.environ.get('BIGQUERY_KEYFILE', ''))
    args = parser.parse_args()

    hours = parse_hours_str(args.usual_hours or '')

    rows = []
    error = None
    if bigquery is None:
        error = 'google-cloud-bigquery no disponible. Instala dependencias.'
    else:
        try:
            # Switch query logic if INEGI_VIEW schema is selected
            if (args.table or '').upper() == 'INEGI_VIEW':
                # Build query for INEGI columns
                if args.keyfile:
                    os.environ['GOOGLE_APPLICATION_CREDENTIALS'] = args.keyfile
                client = bigquery.Client(project=args.project_id)
                hour_filter = ''
                if hours:
                    hour_list = ','.join(str(int(h)) for h in hours)
                    hour_filter = f"AND CAST(HORA AS INT64) IN ({hour_list})"
                loc_filter = ''
                if args.edo:
                    e = (args.edo or '').replace("'","\'")
                    loc_filter += f"AND EDO = '{e}' "
                if args.mpio:
                    m = (args.mpio or '').replace("'","\'")
                    loc_filter += f"AND MPIO = '{m}' "
                query = (
                    f"SELECT lat, lon, EDO, MPIO, HORA, CAUSAACCI, TIPACCID "
                    f"FROM `{args.project_id}.{args.dataset}.{args.table}` "
                    f"WHERE 1=1 {loc_filter} {hour_filter} "
                    f"LIMIT 20000"
                )
                job = client.query(query)
                rows = []
                for row in job:
                    # Normalize decimal commas
                    def _f(v):
                        if v is None:
                            return None
                        s = str(v)
                        try:
                            return float(s.replace(',', '.'))
                        except Exception:
                            return None
                    lat = _f(row.get('lat'))
                    lon = _f(row.get('lon'))
                    if lat is None or lon is None:
                        continue
                    rows.append({
                        'lat': lat,
                        'lon': lon,
                        'EDO': str(row.get('EDO') or ''),
                        'MPIO': str(row.get('MPIO') or ''),
                        'HORA': int(row.get('HORA') or -1),
                        'CAUSAACCI': str(row.get('CAUSAACCI') or ''),
                        'TIPACCID': str(row.get('TIPACCID') or ''),
                    })
            else:
                rows = fetch_rows(args.project_id, args.dataset, args.table,
                                  state_name=args.state_name,
                                  city=args.city,
                                  hours=hours,
                                  limit=20000,
                                  keyfile=args.keyfile)
        except Exception as e:
            error = f'Error BigQuery: {str(e)}'

    if (args.table or '').upper() == 'INEGI_VIEW':
        # Aggregate hotspots by EDO/MPIO/HORA; risky hours by HORA
        agg = {}
        for r in rows:
            key = f"{r['EDO']}|{r['MPIO']}|{r['HORA']}"
            if key not in agg:
                agg[key] = {'EDO': r['EDO'], 'MPIO': r['MPIO'], 'HORA': r['HORA'], 'count': 0}
            agg[key]['count'] += 1
        hot = sorted(agg.values(), key=lambda x: x['count'], reverse=True)[:50]
        by_hour = [{'count': 0} for _ in range(24)]
        for r in rows:
            h = r['HORA']
            if 0 <= h < 24:
                by_hour[h]['count'] += 1
        times = [{'hour': h, **by_hour[h]} for h in range(24)]
        times.sort(key=lambda x: x['count'], reverse=True)
        # Simple score: density of incidents + age pattern
        total = len(rows)
        avg_density = total / 20000.0
        age_score = 0.0
        if args.driver_age is not None:
            age_score = 0.2 if args.driver_age < 25 else 0.0
        score_val = min(100, round((min(1.0, avg_density) * 80) + (age_score * 100), 2))
        score = {'score': score_val, 'factors': {'events': total}}
        patterns = derive_patterns(args.driver_age, args.experience_years, args.usual_hours or '', hot)
    else:
        hot = aggregate_hotspots(rows) if rows else []
        times = risky_times(rows) if rows else []
        score = compute_score(rows, age=args.driver_age)
        patterns = derive_patterns(args.driver_age, args.experience_years, args.usual_hours or '', hot)

    out = {
        'profile': {
            'driver_age': args.driver_age,
            'experience_years': args.experience_years,
            'state_name': args.state_name,
            'city': args.city,
            'usual_hours': args.usual_hours,
        },
        'score': score.get('score', 0),
        'factors': score.get('factors', {}),
        'hotspots': hot,
        'risky_hours': times,
        'error': error,
    }
    print(json.dumps(out, ensure_ascii=False))


if __name__ == '__main__':
    main()
