<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class VacationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->createRecord('2018-01-11', '2018-01-25', 1);
        $this->createRecord('2018-02-01', '2018-02-15', 1);
        $this->createRecord('2018-02-01', '2018-03-01', 2);
    }

    protected function createRecord(string $from, string $to, int $employee_id)
    {
        DB::table('vacations')->insert([
            'from' => $from,
            'to' => $to,
            'employee_id' => $employee_id
        ]);
    }
}
