<?php

use Illuminate\Database\Seeder;
use App\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $adm=new User;
        $adm->login='adm';
        $adm->password='111';
        $adm->isAdmin=true;
        $adm->save();


        // $this->call(UsersTableSeeder::class);
    }
}
