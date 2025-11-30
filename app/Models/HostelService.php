<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HostelService extends Model
{
    use HasFactory;

    protected $fillable = [
        'hostel_id',
        'service_name',
        'service_description',
        'monthly_cost',
        'is_available',
        'provider_id',
    ];

    protected $casts = [
        'monthly_cost' => 'decimal:2',
        'is_available' => 'boolean',
    ];

    public function hostel()
    {
        return $this->belongsTo(Hostel::class);
    }

    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }
}
