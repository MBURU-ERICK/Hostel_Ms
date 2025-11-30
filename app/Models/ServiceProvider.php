<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceProvider extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_name',
        'service_type',
        'description',
        'license_number',
        'experience_years',
        'hourly_rate',
        'is_verified',
        'rating',
        'total_jobs_completed'
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'hourly_rate' => 'decimal:2',
        'rating' => 'decimal:1',
        'experience_years' => 'integer',
        'total_jobs_completed' => 'integer'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function serviceRequests()
    {
        return $this->hasMany(ServiceRequest::class);
    }

    public function serviceTypes()
    {
        return [
            'wifi_installation' => 'WiFi Installation',
            'plumbing' => 'Plumbing & Water Leakage',
            'electrical' => 'Electrical Repairs',
            'sewage' => 'Sewage & Drainage',
            'carpentry' => 'Carpentry & Furniture',
            'cleaning' => 'Deep Cleaning',
            'pest_control' => 'Pest Control',
            'other' => 'Other Maintenance'
        ];
    }

    public function getServiceTypeNameAttribute()
    {
        return $this->serviceTypes()[$this->service_type] ?? 'Unknown';
    }
}
