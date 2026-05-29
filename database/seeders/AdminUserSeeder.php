<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['dni' => '70886319'],
            [
                'name' => 'ADMINISTRADOR HBT',
                'email' => null,
                'password' => Hash::make('70886319'),
                'rol' => 'administrador',
                'activo' => true,
                'cambiar_password' => true,
            ]
        );
    }
}