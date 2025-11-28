<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Survey extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'surveys';

    protected $fillable = [
        'user_id',
        'age',
        'driving_experience',
        'vehicle_type',
        'daily_distance',
        'avg_speed',
        'route_type',
        'incidents_count',
        'alerts_frequency',
        'stress_level',
        'satisfaction_score',
        'most_useful_feature',
        'alert_preference',
        'comments',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'age' => 'integer',
        'driving_experience' => 'integer',
        'daily_distance' => 'integer',
        'avg_speed' => 'integer',
        'incidents_count' => 'integer',
        'alerts_frequency' => 'integer',
        'stress_level' => 'integer',
        'satisfaction_score' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
