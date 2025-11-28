<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Survey;

class SurveySeeder extends Seeder
{
    public function run(): void
    {
        // Crear usuarios de prueba si no existen
        $users = [];
        
        // Verificar si ya existe el admin
        $admin = User::where('email', 'admin@siac.com')->first();
        if (!$admin) {
            $admin = User::create([
                'name' => 'Admin',
                'last_name' => 'Sistema',
                'email' => 'admin@siac.com',
                'password' => bcrypt('admin123'),
                'is_admin' => true,
            ]);
        }

        // Crear 50 usuarios normales para las encuestas
        for ($i = 1; $i <= 50; $i++) {
            $users[] = User::create([
                'name' => 'Usuario',
                'last_name' => 'Test ' . $i,
                'email' => 'usuario' . $i . '@test.com',
                'password' => bcrypt('password123'),
                'is_admin' => false,
            ]);
        }

        // Tipos de vehículos
        $vehicleTypes = ['Sedán', 'SUV', 'Pickup', 'Compacto', 'Deportivo', 'Van'];
        
        // Tipos de rutas
        $routeTypes = ['Urbano', 'Carretera', 'Mixto'];
        
        // Características más útiles
        $features = [
            'Detección de fatiga',
            'Alertas de colisión',
            'Monitoreo de puntos ciegos',
            'Asistencia de estacionamiento',
            'Alertas de velocidad',
            'Integración con smartwatch'
        ];
        
        // Preferencias de alertas
        $alertPreferences = ['Visual', 'Sonora', 'Vibración', 'Combinada'];
        
        // Comentarios simulados
        $comments = [
            'Excelente sistema, me siento más seguro al conducir',
            'Las alertas son muy útiles, especialmente en carretera',
            'Me gustaría más personalización en las alertas',
            'El sistema de detección de fatiga me ha salvado varias veces',
            'Muy buena integración con mi smartwatch',
            'Interfaz intuitiva y fácil de usar',
            'Las alertas de punto ciego son increíbles',
            'A veces las alertas son demasiado sensibles',
            'Perfecto para viajes largos',
            'Me ayuda a mantener una conducción más segura',
            'Gran herramienta para conductores novatos',
            'El historial de viajes es muy detallado',
            'Recomendaría este sistema a otros conductores',
            'La app móvil funciona perfectamente',
            'Buena relación calidad-precio',
        ];

        // Generar 50 encuestas con datos realistas
        foreach ($users as $user) {
            // Crear perfil de conductor realista
            $age = rand(18, 70);
            $experience = min($age - 18, rand(1, 40));
            
            // Conductores jóvenes tienden a conducir más rápido
            $avgSpeed = $age < 30 ? rand(80, 130) : rand(60, 110);
            
            // Correlación: mayor velocidad = más incidentes
            $incidentsCount = $avgSpeed > 100 ? rand(3, 15) : rand(0, 8);
            
            // Correlación: más incidentes = mayor estrés
            $stressLevel = $incidentsCount > 8 ? rand(6, 10) : rand(2, 7);
            
            // Correlación: menor estrés = mayor satisfacción
            $satisfaction = $stressLevel > 7 ? rand(3, 7) : rand(7, 10);
            
            // Frecuencia de alertas correlacionada con incidentes
            $alertsFrequency = min($incidentsCount * 3, rand(5, 50));

            Survey::create([
                'user_id' => $user->_id,
                'age' => $age,
                'driving_experience' => $experience,
                'vehicle_type' => $vehicleTypes[array_rand($vehicleTypes)],
                'daily_distance' => rand(10, 150), // km por día
                'avg_speed' => $avgSpeed,
                'route_type' => $routeTypes[array_rand($routeTypes)],
                'incidents_count' => $incidentsCount,
                'alerts_frequency' => $alertsFrequency, // alertas por semana
                'stress_level' => $stressLevel, // 1-10
                'satisfaction_score' => $satisfaction, // 1-10
                'most_useful_feature' => $features[array_rand($features)],
                'alert_preference' => $alertPreferences[array_rand($alertPreferences)],
                'comments' => $comments[array_rand($comments)],
            ]);
        }

        $this->command->info('✅ Se crearon 50 usuarios y 50 encuestas con datos correlacionados');
    }
}
