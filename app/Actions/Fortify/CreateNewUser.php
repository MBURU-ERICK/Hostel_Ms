<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Models\StudentProfile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'user_type' => ['required', 'in:student,landlord,service_provider'],
            'phone' => ['required', 'string', 'max:15'],
        ])->validate();

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'user_type' => $input['user_type'],
            'phone' => $input['phone'],
            'is_approved' => $input['user_type'] === 'student', // Auto-approve students
        ]);

        // If student, create student profile
        if ($input['user_type'] === 'student') {
            $user->studentProfile()->create([
                'admission_number' => $input['admission_number'] ?? '',
                'id_number' => $input['id_number'] ?? '',
                'gender' => $input['gender'] ?? 'male',
                'institution_name' => $input['institution_name'] ?? '',
                'course' => $input['course'] ?? '',
                'year_of_study' => $input['year_of_study'] ?? '',
            ]);
        }

        return $user;
    }
}
