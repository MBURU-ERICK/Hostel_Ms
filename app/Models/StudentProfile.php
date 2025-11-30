<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'admission_number',
        'id_number',
        'gender',
        'institution_name',
        'course',
        'year_of_study',
        'address',
        'emergency_contact_name',
        'emergency_contact_phone',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
