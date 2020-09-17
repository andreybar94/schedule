<?php

use Illuminate\Database\Seeder;

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
        $this->call(EventTableSeeder::class);
    }

}
