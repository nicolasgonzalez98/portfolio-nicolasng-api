<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Crea (o actualiza) el único usuario admin.
 * Las credenciales salen del .env (ADMIN_*) para no hardcodearlas ni versionarlas.
 * El password se hashea solo (cast 'hashed' en el modelo User).
 */
class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@nicolasngonzalez.com')],
            [
                'name' => env('ADMIN_NAME', 'Nicolás González'),
                'password' => env('ADMIN_PASSWORD', 'changeme123'),
            ]
        );
    }
}
