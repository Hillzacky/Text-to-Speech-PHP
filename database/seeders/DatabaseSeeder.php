<?php

namespace Database\Seeders;

use App\Models\PaymentPlatform;
use Illuminate\Database\Seeder;

// Import DB and Faker services
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            PermissionsTableSeeder::class,
            RolesTableSeeder::class,
            VendorsSeeder::class,
            LanguagesSeeder::class,
            PaymentPlatformsSeeder::class,
            VoicesSeeder::class,
            SettingsSeeder::class,
        ]);
    }
}
