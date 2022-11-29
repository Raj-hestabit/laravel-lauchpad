<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UserTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userTypes = ['Admin', 'Teacher', 'Student'];
        foreach($userTypes as $userType){
            \App\Models\UserType::create([
                'type'      => $userType,
                'status'    => '1'
            ]);
        }
    }
}
