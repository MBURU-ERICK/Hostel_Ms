<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Models\StudentProfile;
use App\Models\ServiceProvider;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array  $input
     * @return \App\Models\User
     */
    public function create(array $input)
    {
        // Basic validation for all user types
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'phone' => ['required', 'string', 'max:15'],
            'user_type' => ['required', 'string', Rule::in(['student', 'service_provider', 'landlord'])],
        ];

        // Add ID number validation for all non-student types
        if ($input['user_type'] !== 'student') {
            $rules['id_number'] = ['required', 'string', 'max:255'];
        }

        Validator::make($input, $rules)->validate();

        // Base user data - only students are auto-approved
        $userData = [
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
            'user_type' => $input['user_type'],
            'phone' => $input['phone'],
            'is_approved' => $input['user_type'] === 'student', // Auto-approve students only
            'is_active' => true,
        ];

        // Add ID number for landlords and service providers
        if ($input['user_type'] !== 'student') {
            $userData['id_number'] = $input['id_number'];
        }

        // Create user
        $user = User::create($userData);

        // Create student profile if user is student
        if ($input['user_type'] === 'student') {
            StudentProfile::create([
                'user_id' => $user->id,
            ]);
        }

        // Create service provider profile if user is service provider
        if ($input['user_type'] === 'service_provider') {
            ServiceProvider::create([
                'user_id' => $user->id,
                'company_name' => $input['name'],
                'service_type' => 'other',
                'description' => 'Service provider account - pending approval',
                'is_verified' => false,
                'is_available' => false,
                'hourly_rate' => 0,
                'rating' => 0,
                'experience_years' => 0,
                'total_jobs_completed' => 0,
                'response_time' => 24,
            ]);
        }
         NotificationService::sendRegistrationConfirmation($user);
        return $user;
    }
}
