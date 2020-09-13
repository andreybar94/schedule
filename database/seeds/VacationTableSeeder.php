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
        $this->createRecord('2020-01-11', '2020-01-25', 1);
        $this->createRecord('2020-02-01', '2020-02-15', 1);
        $this->createRecord('2020-02-01', '2020-03-01', 2);
    }

    protected function createRecord(string $from, string $to, int $employee_id){
        DB::table('vacations')->insert([
            'from' => $from,
            'to' => $to,
            'employee_id' => $employee_id
        ]);
    }
}
