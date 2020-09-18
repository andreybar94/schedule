<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class EventTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->createRecord('2020-01-10 15:00', '2020-01-11 00:00');
    }

    protected function createRecord(string $start,string $end)
    {
        DB::table('events')->insert([
            'start' => $start,
            'end' => $end
        ]);
    }
}
