<?php

use Illuminate\Database\Seeder;
use App\User;
use App\color as Color;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
//        $adm=new User;
//        $adm->login='adm';
//        $adm->password='111';
//        $adm->isAdmin=true;
//        $adm->save();
//        $colors=[
//            'red	',
//            'maroon	',
//            'yellow	',
//            'olive	',
//            'lime',
//            'green',
//            'aqua',
//            'teal',
//            'blue',
//            'navy',
//            'fuchsia',
//            'purple',
//            'gray'
//        ];
//        for($i=0;$i<count($colors);$i++){
//            $newColor= new Color;
//            $newColor->name=$colors[$i];
//            $newColor->save();
//        }
        $alluser= User::all();
        foreach ($alluser as $user){
            $user->color_id=Color::inRandomOrder()->first()->id;
            $user->save();
    }

        // $this->call(UsersTableSeeder::class);
    }
}
