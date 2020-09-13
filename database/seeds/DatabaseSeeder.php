<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(ScheduleTableSeeder::class);
        $this->call(VacationTableSeeder::class);
    }

}
