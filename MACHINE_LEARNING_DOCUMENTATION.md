# 🤖 Documentación de Machine Learning - SIAC

## Algoritmos Implementados

Este documento describe los 3 algoritmos de Machine Learning aplicados al análisis de encuestas de conductores en el sistema SIAC.

---

## 1. K-Means Clustering 🎯

### Descripción
K-Means es un algoritmo de **aprendizaje no supervisado** que agrupa datos en K clusters basándose en similitudes. Agrupa conductores con patrones de comportamiento similares.

### Implementación
```php
public function kMeansClustering($surveys, $k = 3, $maxIterations = 100)
```

### Parámetros
- **$surveys**: Colección de encuestas
- **$k**: Número de clusters (por defecto 3)
- **$maxIterations**: Máximo de iteraciones (por defecto 100)

### Características Utilizadas
1. **Velocidad promedio** (avg_speed)
2. **Número de incidentes** (incidents_count)
3. **Nivel de estrés** (stress_level)
4. **Satisfacción** (satisfaction_score)
5. **Experiencia de conducción** (driving_experience)

### Proceso
1. **Normalización**: Escala todas las características a rango [0, 1]
2. **Inicialización**: Selecciona K puntos aleatorios como centroides iniciales
3. **Asignación**: Cada conductor se asigna al centroide más cercano (distancia euclidiana)
4. **Actualización**: Recalcula centroides como promedio de puntos asignados
5. **Convergencia**: Repite hasta que centroides no cambien significativamente

### Interpretación de Clusters
- **Cluster 1 - Conductor Seguro**: Velocidad < 85 km/h, Incidentes < 5, Estrés < 5
- **Cluster 2 - Conductor Moderado**: Valores intermedios
- **Cluster 3 - Conductor de Alto Riesgo**: Velocidad > 100 km/h O Incidentes > 8 O Estrés > 7

### Salida
```json
{
  "clusters": {"user_id_1": 0, "user_id_2": 1, ...},
  "centroids": [centroid1, centroid2, centroid3],
  "interpretation": {
    "0": {
      "label": "Conductor Seguro",
      "count": 15,
      "avgSpeed": 72.3,
      "avgIncidents": 3.2,
      "avgStress": 4.1,
      "avgSatisfaction": 8.5
    }
  },
  "iterations": 12
}
```

### Fórmula de Distancia Euclidiana
```
d(p, q) = √(Σ(p_i - q_i)²)
```

Donde:
- p, q = dos puntos de datos
- i = cada característica (speed, incidents, stress, satisfaction, experience)

---

## 2. K-Nearest Neighbors (k-NN) 📍

### Descripción
k-NN es un algoritmo de **aprendizaje supervisado** que predice el nivel de riesgo de un conductor basándose en los K conductores más similares (vecinos más cercanos).

### Implementación
```php
public function kNearestNeighbors($surveys, $newDriver, $k = 5)
```

### Parámetros
- **$surveys**: Datos de entrenamiento (encuestas existentes)
- **$newDriver**: Nuevo conductor a clasificar
  ```php
  [
    'speed' => 95,
    'incidents' => 6,
    'stress' => 6,
    'experience' => 5
  ]
  ```
- **$k**: Número de vecinos a considerar (por defecto 5)

### Características Utilizadas
1. Velocidad promedio
2. Número de incidentes
3. Nivel de estrés
4. Años de experiencia

### Proceso
1. **Normalización**: Escala características a [0, 1]
2. **Cálculo de Distancias**: Calcula distancia euclidiana del nuevo conductor a todos los existentes
3. **Selección de Vecinos**: Ordena por distancia y toma los K más cercanos
4. **Votación**: Cuenta las etiquetas de riesgo de los K vecinos
5. **Predicción**: La clase con más votos es la predicción final

### Etiquetas de Riesgo
```php
private function getRiskLabel($survey)
{
    if ($survey->avg_speed > 100 || $survey->incidents_count > 8 || $survey->stress_level > 7) {
        return 'Alto Riesgo';
    } elseif ($survey->avg_speed < 85 && $survey->incidents_count < 5 && $survey->stress_level < 5) {
        return 'Bajo Riesgo';
    }
    return 'Riesgo Moderado';
}
```

### Salida
```json
{
  "prediction": "Riesgo Moderado",
  "confidence": 60.0,
  "neighbors": [
    {"index": 12, "distance": 0.23, "label": "Riesgo Moderado"},
    {"index": 45, "distance": 0.31, "label": "Riesgo Moderado"},
    {"index": 8, "distance": 0.35, "label": "Alto Riesgo"},
    {"index": 23, "distance": 0.41, "label": "Riesgo Moderado"},
    {"index": 17, "distance": 0.44, "label": "Bajo Riesgo"}
  ],
  "votes": {
    "Riesgo Moderado": 3,
    "Alto Riesgo": 1,
    "Bajo Riesgo": 1
  }
}
```

### Cálculo de Confianza
```
Confianza = (Votos de clase ganadora / K) × 100
```

Ejemplo: 3 votos de 5 = 60% de confianza

---

## 3. Feature Importance (Importancia de Características) 📊

### Descripción
Analiza qué características tienen mayor impacto en la satisfacción del usuario utilizando el **Coeficiente de Correlación de Pearson**.

### Implementación
```php
public function featureImportance($surveys)
```

### Características Analizadas
1. Velocidad Promedio
2. Número de Incidentes
3. Nivel de Estrés
4. Años de Experiencia
5. Distancia Diaria
6. Frecuencia de Alertas

### Coeficiente de Pearson
Mide la correlación lineal entre dos variables en el rango [-1, 1]:

```
r = Σ((x_i - x̄)(y_i - ȳ)) / √(Σ(x_i - x̄)² × Σ(y_i - ȳ)²)
```

Donde:
- x = valores de la característica
- y = valores de satisfacción
- x̄, ȳ = promedios

### Interpretación
- **r = 1**: Correlación positiva perfecta (cuando X aumenta, Y aumenta proporcionalmente)
- **r = -1**: Correlación negativa perfecta (cuando X aumenta, Y disminuye proporcionalmente)
- **r = 0**: Sin correlación
- **|r| > 0.7**: Correlación fuerte
- **|r| > 0.4**: Correlación moderada
- **|r| < 0.4**: Correlación débil

### Importancia
```
Importancia (%) = |r| × 100
```

### Salida
```json
{
  "Nivel de Estrés": {
    "correlation": -0.8234,
    "importance": 82.34
  },
  "Incidentes": {
    "correlation": -0.6521,
    "importance": 65.21
  },
  "Velocidad Promedio": {
    "correlation": -0.5123,
    "importance": 51.23
  },
  "Experiencia": {
    "correlation": 0.3456,
    "importance": 34.56
  }
}
```

**Interpretación del ejemplo:**
- **Nivel de Estrés**: Correlación negativa fuerte (-0.82). Cuando el estrés aumenta, la satisfacción disminuye significativamente.
- **Incidentes**: Correlación negativa moderada. Más incidentes reducen la satisfacción.
- **Velocidad**: Correlación negativa moderada. Velocidades muy altas reducen satisfacción.
- **Experiencia**: Correlación positiva débil. Más experiencia ligeramente aumenta satisfacción.

---

## Ventajas de Cada Algoritmo

### K-Means
✅ Rápido y escalable  
✅ Fácil de interpretar  
✅ Identifica patrones naturales  
❌ Requiere especificar K de antemano  
❌ Sensible a valores atípicos  

### k-NN
✅ Simple y efectivo  
✅ No requiere entrenamiento previo  
✅ Adaptable a nuevos datos  
❌ Lento con datasets grandes  
❌ Sensible a características no normalizadas  

### Feature Importance
✅ Identifica factores clave  
✅ Interpretación clara  
✅ Guía para mejorar sistema  
❌ Solo detecta relaciones lineales  
❌ No considera interacciones entre variables  

---

## Casos de Uso en SIAC

### 1. Segmentación Automática (K-Means)
- **Objetivo**: Agrupar conductores con comportamientos similares
- **Aplicación**: Personalizar alertas y recomendaciones por segmento
- **Beneficio**: Intervención específica para cada grupo de riesgo

### 2. Predicción de Riesgo (k-NN)
- **Objetivo**: Clasificar nuevos conductores sin historial extenso
- **Aplicación**: Asignar nivel de riesgo a conductores nuevos
- **Beneficio**: Prevención temprana de comportamientos riesgosos

### 3. Optimización de Características (Feature Importance)
- **Objetivo**: Identificar qué mejorar para aumentar satisfacción
- **Aplicación**: Priorizar desarrollo de funcionalidades
- **Beneficio**: Enfoque en características que más impactan satisfacción

---

## Mejoras Futuras

### 1. Redes Neuronales
- Implementar red neuronal multicapa para predicciones más complejas
- Detectar patrones no lineales en los datos

### 2. Support Vector Machines (SVM)
- Clasificación más robusta con margen máximo
- Manejo de espacios de alta dimensionalidad

### 3. Ensemble Methods
- Random Forest para feature importance más preciso
- Gradient Boosting para mejores predicciones

### 4. Deep Learning
- LSTM para análisis temporal de comportamiento
- Predicción de incidentes futuros

---

## Conclusión

Los 3 algoritmos implementados proporcionan un sistema completo de análisis:

1. **K-Means**: Descubre grupos naturales en los datos
2. **k-NN**: Predice riesgo de nuevos conductores
3. **Feature Importance**: Guía decisiones de mejora

Juntos permiten tomar decisiones basadas en datos para mejorar la seguridad vial y la satisfacción de usuarios del sistema SIAC.
