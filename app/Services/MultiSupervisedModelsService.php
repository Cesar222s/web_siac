<?php
namespace App\Services;

use Illuminate\Support\Facades\Cache;

class MultiSupervisedModelsService
{
    protected string $filePath;
    protected int $limit;
    protected int $k;

    public function __construct(string $filePath, int $limit = 3000, int $k = 5)
    {
        $this->filePath = $filePath;
        $this->limit = $limit;
        $this->k = $k;
    }

    public function evaluateAll(): array
    {
        if (!file_exists($this->filePath)) {
            return ['available' => false, 'message' => 'CSV no encontrado'];
        }
        // v2 cache key to reflect performance optimizations
        return Cache::remember('multi_supervised_v2', now()->addMinutes(30), function(){
            $data = $this->load();
            if ($data['count'] < 200) {
                return ['available' => false, 'message' => 'Datos insuficientes'];
            }
            // Tasks
            $binary = $this->taskBinaryInjury($data);
            $severity = $this->taskSeverity($data);
            $type = $this->taskAccidentType($data);
            $regression = $this->taskInjuryRegression($data);
            return [
                'available' => true,
                'summary' => [ 'rows' => $data['count'], 'features' => $data['featureNames'] ],
                'binary_injury' => $binary,
                'severity' => $severity,
                'type_classification' => $type,
                'injury_regression' => $regression,
            ];
        });
    }

    protected function load(): array
    {
        $handle = fopen($this->filePath, 'r');
        $header = fgetcsv($handle);
        if (!$header) return ['count'=>0];
        $injuryCols = ['CONDHERIDO','PASAHERIDO','PEATHERIDO','CICLHERIDO','OTROHERIDO','NEHERIDO'];
        $deathCols = ['CONDMUERTO','PASAMUERTO','PEATMUERTO','CICLMUERTO','OTROMUERTO','NEMUERTO'];
        $featureNames = ['HORA','DIASEMANA','URBANA','SUBURBANA','TIPACCID','AUTOMOVIL','CAMPASAJ','MICROBUS','CAMIONETA','CAMION','MOTOCICLET','BICICLETA','SEXO','EDAD'];
        $features = []; $injuryFlag=[]; $severity=[]; $typeLabel=[]; $injuryCount=[];
        $count=0; $typeFreq=[];
        while(($row=fgetcsv($handle))!==false){
            if (count($row)!==count($header)) continue;
            $assoc=array_combine($header,$row);
            $vec=[]; foreach($featureNames as $f){ $vec[] = isset($assoc[$f]) && is_numeric($assoc[$f]) ? (float)$assoc[$f] : 0.0; }
            $injured=0; $totalInj=0; foreach($injuryCols as $c){ $v=(int)($assoc[$c]??0); if($v>0){$injured=1;} $totalInj+=$v; }
            $dead=0; foreach($deathCols as $c){ $v=(int)($assoc[$c]??0); if($v>0){$dead=1; break;} }
            // severity: 0 none,1 injured only,2 any death
            $sev = $dead?2:($injured?1:0);
            $tip = isset($assoc['TIPACCID']) ? (int)$assoc['TIPACCID'] : 0;
            $typeFreq[$tip] = ($typeFreq[$tip]??0)+1;
            $features[]=$vec; $injuryFlag[]=$injured; $severity[]=$sev; $typeLabel[]=$tip; $injuryCount[]=$totalInj; $count++;
            if($count>=$this->limit) break;
        }
        fclose($handle);
        // choose top 8 types keep others as 0
        arsort($typeFreq); $topTypes = array_slice(array_keys($typeFreq),0,8);
        $mappedTypes=[]; $typeMap=[]; $next=1; foreach($topTypes as $t){ $typeMap[$t]=$next++; }
        foreach($typeLabel as $t){ $mappedTypes[] = $typeMap[$t]??0; }
        return [
            'features'=>$features,
            'injuryFlag'=>$injuryFlag,
            'severity'=>$severity,
            'type'=>$mappedTypes,
            'injuryCount'=>$injuryCount,
            'featureNames'=>$featureNames,
            'count'=>$count,
            'typeMapping'=>$typeMap
        ];
    }

    protected function split(array $X, array $y): array
    {
        $n=count($X); $split=(int)($n*0.8); return [
            array_slice($X,0,$split),
            array_slice($y,0,$split),
            array_slice($X,$split),
            array_slice($y,$split),
        ];
    }

    protected function knnPredict(array $vec, array $trainX, array $trainY, int $k): int
    {
        // Maintain a small list of top-k nearest instead of sorting all distances
        $nearest=[]; $maxIndex=-1; $maxDist=-1.0;
        foreach($trainX as $i=>$t){
            $d=0.0; foreach($vec as $j=>$v){ $dv=$v-$t[$j]; $d+=$dv*$dv; }
            if(count($nearest)<$k){
                $nearest[]=[ $d, $trainY[$i] ];
                if($d>$maxDist){ $maxDist=$d; $maxIndex=count($nearest)-1; }
            } elseif($d<$maxDist){
                // replace current worst
                $nearest[$maxIndex]=[ $d, $trainY[$i] ];
                // recompute worst
                $maxDist=$nearest[0][0]; $maxIndex=0;
                foreach($nearest as $idx=>$pair){ if($pair[0]>$maxDist){ $maxDist=$pair[0]; $maxIndex=$idx; } }
            }
        }
        $votes=[]; foreach($nearest as $pair){ $lab=$pair[1]; $votes[$lab]=($votes[$lab]??0)+1; }
        arsort($votes); return array_key_first($votes);
    }

    protected function evalClassification(array $true, array $pred): array
    {
        $labels = array_values(array_unique(array_merge($true,$pred))); sort($labels);
        $conf = []; foreach($labels as $a){ $conf[$a]=array_fill_keys($labels,0); }
        $n=count($true); $correct=0; for($i=0;$i<$n;$i++){ $conf[$true[$i]][$pred[$i]]++; if($true[$i]===$pred[$i]) $correct++; }
        $accuracy = $n? $correct/$n:0.0;
        return ['accuracy'=>round($accuracy,4),'labels'=>$labels,'matrix'=>$conf];
    }

    protected function taskBinaryInjury(array $data): array
    {
        [$trX,$trY,$teX,$teY] = $this->split($data['features'],$data['injuryFlag']);
        $pred=[]; foreach($teX as $v){ $pred[]=$this->knnPredict($v,$trX,$trY,$this->k); }
        $m = $this->evalClassification($teY,$pred);
        return [ 'description'=>'Heridos sí/no','k'=>$this->k ] + $m;
    }

    protected function taskSeverity(array $data): array
    {
        [$trX,$trY,$teX,$teY] = $this->split($data['features'],$data['severity']);
        // simple rule-based baseline: predict 0 if HORA<6 else 1 unless any feature CAMION>0 then 2 if EDAD>60 and CAMION>0
        $pred=[]; foreach($teX as $v){ $h=$v[0]; $tipAcc=$v[4]; $camion=$v[9]; $edad=$v[13]; $p= ($h<6)?0:1; if($camion>0 && $edad>60) $p=2; $pred[]=$p; }
        $m = $this->evalClassification($teY,$pred);
        return ['description'=>'Severidad (0 ninguno,1 heridos,2 muerte)','baseline'=>'Regla simple hora/camión/edad'] + $m;
    }

    protected function taskAccidentType(array $data): array
    {
        [$trX,$trY,$teX,$teY] = $this->split($data['features'],$data['type']);
        $pred=[]; foreach($teX as $v){ $pred[]=$this->knnPredict($v,$trX,$trY,$this->k); }
        $m=$this->evalClassification($teY,$pred);
        return ['description'=>'Tipo de accidente (agrupado top 8, resto=0)','k'=>$this->k,'mapping'=>$data['typeMapping']] + $m;
    }

    protected function taskInjuryRegression(array $data): array
    {
        // Linear regression using normal equation: y = Xb (add intercept)
        [$trX,$trY,$teX,$teY] = $this->split($data['features'],$data['injuryCount']);
        $X = $this->addIntercept($trX); $XT = $this->transpose($X); $XTX = $this->matMul($XT,$X); $XTy = $this->matVecMul($XT,$trY);
        $beta = $this->solveGaussian($XTX,$XTy); // coefficients
        $pred=[]; foreach($teX as $v){ $pred[] = $this->dot($beta, array_merge([1.0], $v)); }
        // metrics
        $n=count($teY); $se=0.0; $abs=0.0; $sumY=0.0; $sumY2=0.0; $sumRes=0.0;
        for($i=0;$i<$n;$i++){ $e=$pred[$i]-$teY[$i]; $se+= $e*$e; $abs+=abs($e); $sumY+=$teY[$i]; $sumY2+=$teY[$i]*$teY[$i]; }
        $mae = $n? $abs/$n:0.0; $rmse = $n? sqrt($se/$n):0.0; $mean = $n? $sumY/$n:0.0; $sst=0.0; for($i=0;$i<$n;$i++){ $d=$teY[$i]-$mean; $sst += $d*$d; } $r2 = $sst? 1-($se/$sst):0.0;
        return [ 'description'=>'Regresión del número de heridos','coefficients'=>$beta,'mae'=>round($mae,4),'rmse'=>round($rmse,4),'r2'=>round($r2,4) ];
    }

    // Helpers
    protected function addIntercept(array $X): array { return array_map(fn($r)=>array_merge([1.0],$r),$X); }
    protected function transpose(array $M): array { $rows=count($M); if($rows==0) return []; $cols=count($M[0]); $T=[]; for($c=0;$c<$cols;$c++){ $row=[]; for($r=0;$r<$rows;$r++){ $row[]=$M[$r][$c]; } $T[]=$row; } return $T; }
    protected function matMul(array $A,array $B): array { $ar=count($A); $ac=count($A[0]); $br=count($B); $bc=count($B[0]); $R=[]; for($i=0;$i<$ar;$i++){ $row=[]; for($j=0;$j<$bc;$j++){ $sum=0.0; for($k=0;$k<$ac;$k++){ $sum+=$A[$i][$k]*$B[$k][$j]; } $row[]=$sum; } $R[]=$row; } return $R; }
    protected function matVecMul(array $A,array $v): array { $out=[]; foreach($A as $row){ $sum=0.0; foreach($row as $i=>$val){ $sum += $val*$v[$i]; } $out[]=$sum; } return $out; }
    protected function solveGaussian(array $A,array $b): array { // naive Gaussian elimination
        $n=count($A); for($i=0;$i<$n;$i++){ // pivot
            $pivot=$A[$i][$i]; if(abs($pivot)<1e-12){ // swap
                for($r=$i+1;$r<$n;$r++){ if(abs($A[$r][$i])>1e-12){ [$A[$i],$A[$r]] = [$A[$r],$A[$i]]; [$b[$i],$b[$r]] = [$b[$r],$b[$i]]; $pivot=$A[$i][$i]; break; } }
            }
            if(abs($pivot)<1e-12) continue;
            // normalize
            for($c=$i;$c<$n;$c++){ $A[$i][$c]/=$pivot; } $b[$i]/=$pivot;
            // eliminate
            for($r=0;$r<$n;$r++){ if($r==$i) continue; $factor=$A[$r][$i]; if($factor==0) continue; for($c=$i;$c<$n;$c++){ $A[$r][$c]-=$factor*$A[$i][$c]; } $b[$r]-=$factor*$b[$i]; }
        }
        return $b;
    }
    protected function dot(array $a,array $b): float { $sum=0.0; foreach($a as $i=>$v){ $sum += $v*$b[$i]; } return $sum; }
}
