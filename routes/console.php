<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('admin:set {email=admin@example.com} {password=admin123456}', function ($email, $password) {
    $user = \App\Models\User::updateOrCreate(
        ['email' => $email],
        [
            'name' => 'Admin User',
            'password' => \Illuminate\Support\Facades\Hash::make($password),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]
    );
    $this->info("Admin account configured successfully!");
    $this->line("Email:    {$user->email}");
    $this->line("Password: {$password}");
    $this->line("Role:     {$user->role}");
})->purpose('Create or reset an admin account with verified email');

