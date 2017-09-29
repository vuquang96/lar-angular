<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	Model::unguard();

        DB::table('users')->delete();
        $users = array(
                ['name' => 'supperadmin', 'email' => 'supperadmin@gmail.com', 'password' => Hash::make('123456'), 'level' => 0, 'id_intialized' => 0],
                ['name' => 'admin', 'email' => 'admin@scotch.io', 'password' => Hash::make('123456'), 'level' => 1, 'id_intialized' => 0],
                ['name' => 'member', 'email' => 'member@scotch.io', 'password' => Hash::make('123456'), 'level' => 2, 'id_intialized' => 0],
                ['name' => 'member2', 'email' => 'member2@scotch.io', 'password' => Hash::make('123456'), 'level' => 2, 'id_intialized' => 0],
        );
        foreach ($users as $user)
        {
            User::create($user);
        }
        Model::reguard();
    }
}
