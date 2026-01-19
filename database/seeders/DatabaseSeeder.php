<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Device;
use App\Models\Vehicle;
use App\Models\Reminder;
use App\Models\Service;
use App\Models\Document;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RoleSeeder::class);

        $adminRole = Role::where('name', 'admin')->first();
        $userRole  = Role::where('name', 'user')->first();

        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        $user = User::factory()->create([
            'name' => 'User',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
        ]);

        // pivot role_user (jeśli masz relację belongsToMany, to użyj attach)
        // Jeśli jeszcze nie masz relacji w modelach, na razie wstawimy ręcznie:
        $admin->roles()->attach($adminRole->id);
        $user->roles()->attach($userRole->id);

        // Devices/Vehicles
        $devices = Device::factory()->count(5)->for($user)->create();
        $vehicles = Vehicle::factory()->count(3)->for($user)->create();

        // Reminders: część ogólne, część przypisane
        Reminder::factory()->count(4)->for($user)->create([
            'device_id' => null,
            'vehicle_id' => null,
        ]);

        Reminder::factory()->count(3)->for($user)->create([
            'device_id' => $devices->random()->id,
            'vehicle_id' => null,
        ]);

        Reminder::factory()->count(3)->for($user)->create([
            'device_id' => null,
            'vehicle_id' => $vehicles->random()->id,
        ]);

        // Services
        Service::factory()->count(4)->for($user)->create([
            'device_id' => $devices->random()->id,
            'vehicle_id' => null,
        ]);

        Service::factory()->count(4)->for($user)->create([
            'device_id' => null,
            'vehicle_id' => $vehicles->random()->id,
        ]);

        // Documents (na razie bez realnych plików - metadata)
        $docs = Document::factory()->count(6)->for($user)->create();

        // Powiązania dokumentów (documentables) z devices/vehicles/services
        foreach ($docs as $doc) {
            $pick = rand(1, 3);
            if ($pick === 1) {
                $doc->documentables()->create([
                    'documentable_type' => Device::class,
                    'documentable_id' => $devices->random()->id,
                ]);
            } elseif ($pick === 2) {
                $doc->documentables()->create([
                    'documentable_type' => Vehicle::class,
                    'documentable_id' => $vehicles->random()->id,
                ]);
            } else {
                $serviceId = Service::where('user_id', $user->id)->inRandomOrder()->first()->id;
                $doc->documentables()->create([
                    'documentable_type' => Service::class,
                    'documentable_id' => $serviceId,
                ]);
            }
        }
    }
}
