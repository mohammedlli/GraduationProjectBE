<?php

namespace Database\Seeders;

use App\Models\Stage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        $stages =[
            'stage 1',
            'stage 2',
            'stage 3',
            'stage 4'
        ];


        foreach($stages as $stage)
            Stage::create(['name'=>$stage]);

    }
}