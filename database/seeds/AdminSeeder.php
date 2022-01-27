<?php

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $allAdmins = Admin::all();

        if ($allAdmins->isEmpty()) {
        	
        	$createAdmin = Admin::create([
        		'first_name' => 'Mr.',
        		'last_name' => 'Admin-1',
        		'mobile' => '01622888991',
        		'email' => 'admin-1@email.com',
        		'password' => Hash::make('admin'),
                'active' => 1
        	]);

        }
    }
}
