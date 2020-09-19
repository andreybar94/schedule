<?php

use Illuminate\Database\Seeder;

class TimeRangeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->createRecord('10:00', '13:00', 1);
        $this->createRecord('14:00', '19:00', 1);
        $this->createRecord('09:00', '12:00', 2);
        $this->createRecord('13:00', '18:00', 2);
    }

    protected function createRecord(string $start,string $end,int $employee_id)
    {
        DB::table('time_ranges')->insert([
            'start' => $start,
            'end' => $end,
            'employee_id' => $employee_id
        ]);
    }
}
