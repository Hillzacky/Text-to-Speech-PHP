<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vendor;

class VendorsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $vendors = [
            ['id' => 1, 'vendor_id' => 'aws_std', 'enabled' => 0, 'cost' => 0.000004],
            ['id' => 2, 'vendor_id' => 'aws_nrl', 'enabled' => 0, 'cost' => 0.000016],
            ['id' => 3, 'vendor_id' => 'azure_std', 'enabled' => 0, 'cost' => 0.000004],
            ['id' => 4, 'vendor_id' => 'azure_nrl', 'enabled' => 0, 'cost' => 0.000016],
            ['id' => 5, 'vendor_id' => 'gcp_std', 'enabled' => 0, 'cost' => 0.000004],
            ['id' => 6, 'vendor_id' => 'gcp_nrl', 'enabled' => 0, 'cost' => 0.000016],
            ['id' => 7, 'vendor_id' => 'ibm_nrl', 'enabled' => 0, 'cost' => 0.00002],
        ];

        foreach ($vendors as $vendor) {
            Vendor::updateOrCreate(['id' => $vendor['id']], $vendor);
        }
    }
}
