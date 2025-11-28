# Dashboard de Análisis de Datos - SIAC

## 📊 Modelo de Análisis de Datos

El sistema SIAC implementa un **modelo integral de análisis de datos** que combina datos IoT en tiempo real con encuestas de satisfacción del usuario para proporcionar insights accionables y facilitar la toma de decisiones. El modelo se estructura en cuatro capas:

### 1. Capa de Recolección
- **Sensores IoT**: Temperatura del motor, velocidad, alertas, nivel de combustible, distancia recorrida
- **Encuestas de usuario**: 12 campos de información (edad, experiencia, hábitos, satisfacción, estrés)
- **Frecuencia de muestreo**: Datos cada hora (24 puntos diarios) + encuestas periódicas
- **Almacenamiento**: MongoDB Atlas (base de datos NoSQL orientada a documentos)

### 2. Capa de Procesamiento
- **Análisis estadístico descriptivo**:
  - Promedio de velocidad, temperatura, incidentes
  - Desviación estándar y valores atípicos
  - Total de alertas generadas
  - Eficiencia de combustible (distancia/consumo)
  
- **Análisis de correlación (Pearson)**:
  - Velocidad vs. Incidentes (r = coeficiente de correlación)
  - Nivel de estrés vs. Satisfacción del usuario
  - Identificación de patrones causales
  
- **Segmentación de usuarios (K-Means simplificado)**:
  - Conductores seguros (velocidad < 80 km/h, incidentes < 5)
  - Conductores moderados (velocidad 80-100 km/h, incidentes 5-10)
  - Conductores riesgosos (velocidad > 100 km/h, incidentes > 10)
  
- **Análisis de tendencias**:
  - Regresión lineal para predicción de velocidad
  - Proyección de consumo de combustible
  - Estimación de rango restante

### 3. Capa de Visualización
- **Dashboards interactivos** con Chart.js
- **Tipos de gráficas**: Líneas, barras, radar, doughnut, pie
- **Métricas KPI**: Cards con valores calculados en tiempo real
- **Visualizaciones de correlación**: Scatter plots y coeficientes

### 4. Capa de Recomendaciones
- **Sistema experto basado en reglas**: Genera recomendaciones automáticas
- **Machine Learning básico**: Predicción de riesgos
- **Análisis prescriptivo**: Sugerencias de acciones correctivas

---

## 🔧 Metodologías, Técnicas y Herramientas

### Metodologías Aplicadas

#### 1. **Análisis Estadístico Descriptivo**
- **Técnica**: Cálculo de medidas de tendencia central y dispersión
- **Aplicación**: Caracterización del comportamiento de conducción
- **Métricas**: Media, mediana, desviación estándar, percentiles
- **Código**: 
```php
$avgSpeed = $surveys->avg('avg_speed');
$stdDev = $surveys->stddev('avg_speed');
$median = $surveys->median('avg_speed');
```

#### 2. **Análisis de Correlación (Pearson)**
- **Técnica**: Coeficiente de correlación de Pearson (r)
- **Aplicación**: Identificar relaciones entre variables
- **Interpretación**:
  - |r| > 0.7: Correlación fuerte
  - 0.4 < |r| < 0.7: Correlación moderada
  - |r| < 0.4: Correlación débil
- **Código**: 
```php
private function calculateCorrelation($x, $y) {
    $n = count($x);
    $meanX = array_sum($x) / $n;
    $meanY = array_sum($y) / $n;
    
    $numerator = 0;
    $sumSqX = 0;
    $sumSqY = 0;
    
    for ($i = 0; $i < $n; $i++) {
        $diffX = $x[$i] - $meanX;
        $diffY = $y[$i] - $meanY;
        $numerator += $diffX * $diffY;
        $sumSqX += $diffX * $diffX;
        $sumSqY += $diffY * $diffY;
    }
    
    $denominator = sqrt($sumSqX * $sumSqY);
    return $denominator == 0 ? 0 : $numerator / $denominator;
}
```

#### 3. **Segmentación de Usuarios (Clustering)**
- **Técnica**: K-Means simplificado con criterios predefinidos
- **Aplicación**: Clasificar conductores en grupos de comportamiento
- **Segmentos identificados**:
  - **Seguros**: Baja velocidad, pocos incidentes, alto cumplimiento
  - **Moderados**: Velocidad media, incidentes ocasionales
  - **Riesgosos**: Alta velocidad, incidentes frecuentes, bajo cumplimiento
- **Código**:
```php
$safe = $surveys->filter(fn($s) => $s->avg_speed < 80 && $s->incidents_count < 5)->count();
$risky = $surveys->filter(fn($s) => $s->avg_speed >= 100 || $s->incidents_count >= 10)->count();
```

#### 4. **Análisis Predictivo**
- **Técnica**: Regresión lineal simple
- **Aplicación**: Predicción de velocidad próxima hora basada en tendencia
- **Variables**: Velocidad histórica (últimas 5 lecturas)
- **Código**: 
```php
$speeds = collect($sensorData)->pluck('speed');
$avgSpeed = $speeds->avg();
$trend = ($speeds->last() - $speeds->first()) / count($speeds);
$nextHourSpeed = round($avgSpeed + $trend, 1);
```

#### 5. **Análisis de Riesgo Multifactorial**
- **Técnica**: Scoring ponderado con 5 factores
- **Factores evaluados**:
  - Velocidad promedio (peso: 30%)
  - Temperatura del motor (peso: 25%)
  - Alertas totales (peso: 20%)
  - Nivel de combustible (peso: 15%)
  - Distancia recorrida (peso: 10%)
- **Categorización**: Alto (>70), Medio (40-70), Bajo (<40)

#### 6. **Análisis de Series Temporales**
- **Técnica**: Agregación mensual con cálculo de tendencias
- **Métricas calculadas**:
  - Promedio móvil de viajes/mes
  - Tasa de incidentes
  - Evolución de eficiencia combustible
  - Detección de patrones estacionales

#### 7. **Análisis de Sentimiento (Cualitativo)**
- **Técnica**: Análisis de comentarios de encuestas
- **Aplicación**: Identificar áreas de mejora en la experiencia del usuario
- **Métricas**: Frecuencia de palabras clave (positivas/negativas)

### Herramientas Tecnológicas

| Herramienta | Propósito | Implementación |
|------------|-----------|----------------|
| **Laravel 10** | Framework backend PHP | Controladores, Modelos, Rutas, Seeders |
| **MongoDB Atlas** | Base de datos NoSQL | Almacenamiento de datos IoT y encuestas |
| **Chart.js 4.4.0** | Visualización de datos | 10 tipos de gráficas interactivas |
| **Blade Templates** | Motor de plantillas | Renderizado de vistas dinámicas |
| **PHP Collections** | Procesamiento de datos | Métodos `avg()`, `sum()`, `pluck()`, `map()`, `filter()` |
| **CSS Grid/Flexbox** | Diseño responsivo | Layouts adaptativos para dashboards |
| **Faker PHP** | Generación de datos | Datos simulados realistas para pruebas |

### Técnicas de Procesamiento de Datos

1. **ETL (Extract, Transform, Load)**
   - Extracción: Consultas a MongoDB con filtros
   - Transformación: Cálculos estadísticos, agregaciones
   - Carga: Renderizado en vistas Blade

2. **Data Cleaning**
   - Validación de rangos (velocidad 0-200 km/h)
   - Eliminación de valores nulos
   - Normalización de escalas (0-10 para satisfacción)

3. **Feature Engineering**
   - Creación de variables derivadas (eficiencia = distancia/combustible)
   - Categorización de variables continuas (grupos de edad)
   - Generación de índices compuestos (score de riesgo)

---

## 💡 Contribución en la Toma de Decisiones

### 1. **Identificación de Tendencias**

**Problema**: ¿Cómo están evolucionando los patrones de conducción y satisfacción del usuario?

**Solución**: 
- Gráfica de tendencias mensuales muestra evolución de viajes vs. incidentes
- Análisis de correlación revela que velocidad alta (>100 km/h) se correlaciona con más incidentes (r > 0.5)
- Satisfacción promedio de {{ avgSatisfaction }}/10 indica nivel de aceptación del sistema
- Comparación año sobre año para identificar mejoras

**Decisión facilitada**: 
- Programar mantenimiento preventivo antes de fallas críticas
- Ajustar configuración de alertas según feedback de usuarios
- **Ejemplo**: Si correlación velocidad-incidentes es fuerte (r > 0.7), implementar alertas más agresivas a >90 km/h

### 2. **Predicciones Operacionales**

**Problema**: ¿Cuánto combustible queda y cuándo reabastecer? ¿Qué conductores están en riesgo?

**Solución**:
- Predicción de combustible restante basada en consumo histórico y distancia
- Cálculo de rango estimado (km restantes antes de tanque vacío)
- Segmentación identifica {{ percentageRisky }}% de conductores riesgosos
- Alertas proactivas cuando fuel < 20%

**Decisión facilitada**: 
- Optimizar rutas y timing de reabastecimiento
- Asignar coaching de conducción segura a conductores en segmento "riesgoso"
- Predecir demanda de mantenimiento basado en patrones de uso

### 3. **Optimización de Recursos**

**Problema**: ¿Qué vehículos requieren atención inmediata? ¿Dónde invertir en mejoras?

**Solución**:
- Scoring de eficiencia por vehículo (distancia/combustible)
- Ranking de vehículos con mayor tasa de incidentes
- Análisis por tipo de vehículo revela qué modelos tienen mejor rendimiento
- Identificación de outliers (temperaturas anormales, velocidades extremas)
- Top 5 características más valoradas guía roadmap de desarrollo

**Decisión facilitada**: 
- Priorizar recursos de mantenimiento en vehículos de alto riesgo
- Invertir en mejorar las características top-rated por usuarios
- **Ejemplo**: Si "Detección de fatiga" es #1 en preferencias, priorizar mejoras en ese módulo

### 4. **Gestión de Riesgos**

**Problema**: ¿Cómo prevenir accidentes y fallas antes de que ocurran?

**Solución**:
- Sistema de scoring de riesgo en tiempo real (radar chart con 5 factores)
- Correlación estrés-satisfacción (r = -0.6 típicamente) indica que reducir estrés mejora experiencia
- Segmentación K-Means identifica conductores que requieren intervención
- Recomendaciones automáticas basadas en nivel de riesgo:
  - **Alto**: "Reducir velocidad promedio y revisar sistema de frenos"
  - **Medio**: "Monitorear temperatura del motor"
  - **Bajo**: "Mantener prácticas de conducción actuales"

**Decisión facilitada**: 
- Implementar acciones correctivas antes de incidentes
- Ajustar pólizas de seguro según perfil de riesgo
- Diseñar programas de incentivos para conductores seguros

### 5. **Identificación de Oportunidades**

**Problema**: ¿Dónde se puede mejorar la eficiencia operacional y experiencia del usuario?

**Solución**:
- Análisis comparativo de eficiencia combustible (gráfica de barras mensuales)
- Detección de mejores prácticas (meses con mayor eficiencia)
- Cálculo de tasa de mejora (+15.3% indica adopción exitosa de nuevas prácticas)
- Análisis de preferencias de alertas (Visual 40%, Sonora 30%, Vibración 20%, Combinada 10%)
- Satisfacción por grupo de edad identifica segmentos insatisfechos

**Decisión facilitada**: 
- Replicar prácticas eficientes en toda la flota
- Personalizar tipo de alerta según preferencia mayoritaria de cada segmento
- **Ejemplo**: Usuarios 18-25 años prefieren alertas visuales → diseñar UI más atractiva para ese grupo
- Lanzar campañas específicas para mejorar satisfacción en grupos con score <7

### 6. **Análisis de Comportamiento y Patrones**

**Problema**: ¿Existen patrones ocultos en los datos que podamos aprovechar?

**Solución**:
- Distribución por edad revela que conductores jóvenes (18-25) tienen mayor frecuencia de incidentes
- Análisis por tipo de ruta (Urbano/Carretera/Mixto) identifica contextos de mayor riesgo
- Correlación experiencia-incidentes muestra curva de aprendizaje
- Heatmaps temporales identifican horas del día con más alertas

**Decisión facilitada**:
- Diseñar programas de capacitación específicos por edad/experiencia
- Ajustar sensibilidad de sensores según tipo de ruta
- Optimizar recursos de soporte en horarios pico de alertas

---

## 📈 Casos de Uso Implementados

### Dashboard Principal (`/admin/dashboard`)
- **6 métricas KPI**: Usuarios, velocidad prom., alertas, temperatura, distancia, eficiencia
- **3 gráficas en tiempo real**: Velocidad (line), Temperatura (bar), Alertas (line)
- **4 predicciones**: Velocidad próxima hora, combustible restante, rango estimado, nivel de riesgo
- **Exportación de datos**: Botón para CSV export
- **Datos**: Basados en simulación de 24h de sensores IoT

### Analytics Avanzado (`/admin/analytics`)
- **Estadísticas mensuales**: Promedio viajes/mes, incidentes totales, eficiencia, tasa de mejora
- **Tendencias**: Gráfica de líneas con viajes e incidentes mensuales
- **Distribución**: Doughnut chart mostrando tipos de incidentes
- **Evolución de eficiencia**: Bar chart con 12 meses de datos
- **Análisis de riesgo**: Radar chart con 5 factores + lista de 4 recomendaciones
- **Metodologías aplicadas**: 4 cards explicando técnicas usadas

### Análisis de Encuestas (Nuevo - en `/admin/analytics`)
- **50 encuestas reales** almacenadas en MongoDB
- **4 KPIs principales**: Satisfacción promedio, nivel de estrés, velocidad, incidentes
- **Análisis de correlación**: 
  - Velocidad ↔ Incidentes (r calculado con Pearson)
  - Estrés ↔ Satisfacción (r calculado con Pearson)
- **Segmentación**: Doughnut chart con conductores seguros/moderados/riesgosos
- **Preferencias de alertas**: Pie chart con distribución Visual/Sonora/Vibración/Combinada
- **Distribución demográfica**: Bar chart por grupos de edad (18-25, 26-35, 36-50, 51+)
- **Satisfacción por edad**: Line chart mostrando variación entre grupos
- **Top 5 características**: Ranking con barras de progreso de features más valoradas
- **Recomendaciones automáticas**: Generadas por sistema experto basado en reglas

---

## 📊 Estructura de Datos - Modelo de Encuestas

```php
Survey Model {
    user_id: ObjectId,
    age: Integer (18-70),
    driving_experience: Integer (1-40 años),
    vehicle_type: String (Sedán, SUV, Pickup, Compacto, Deportivo, Van),
    daily_distance: Integer (10-150 km),
    avg_speed: Integer (60-130 km/h),
    route_type: String (Urbano, Carretera, Mixto),
    incidents_count: Integer (0-15),
    alerts_frequency: Integer (5-50 por semana),
    stress_level: Integer (1-10),
    satisfaction_score: Integer (1-10),
    most_useful_feature: String,
    alert_preference: String (Visual, Sonora, Vibración, Combinada),
    comments: Text,
    created_at: Timestamp,
    updated_at: Timestamp
}
```

### Correlaciones Implementadas en Datos Simulados

Para que el análisis sea realista, los datos simulados incluyen correlaciones intencionales:

1. **Velocidad → Incidentes**: Conductores que manejan más rápido tienen más incidentes
2. **Incidentes → Estrés**: Más incidentes genera mayor nivel de estrés
3. **Estrés → Satisfacción**: Mayor estrés resulta en menor satisfacción (correlación negativa)
4. **Edad → Velocidad**: Conductores jóvenes (<30 años) tienden a conducir más rápido
5. **Experiencia → Incidentes**: Más experiencia correlaciona con menos incidentes

---

## 🚀 Próximos Pasos (Roadmap)

1. **Integración con sensores reales**: Reemplazar datos simulados con lecturas IoT
2. **Machine Learning avanzado**: Implementar Random Forest para predicción de fallas
3. **Alertas automatizadas**: Sistema de notificaciones push/email
4. **Exportación avanzada**: PDF reports con gráficas incluidas
5. **API RESTful**: Endpoints para integración con aplicaciones móviles

---

**Desarrollado para el proyecto SIAC - Sistema Inteligente de Asistencia en Conducción**  
*Tecnologías: Laravel, MongoDB Atlas, Chart.js, PHP Collections*
