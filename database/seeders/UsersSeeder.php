<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $user=User::create([
           'first_name'=>'super',
           'last_name'=>'admin',
           'email'=>'nassar@app.superadmin',
            'password'=>Hash::make('123321153')
        ]);
        $user->attachRole('super_admin');
        $user->syncPermissions([
            'users_create',
            'users_read',
            'users_update',
            'users_delete'
        ]);
    }
}
