<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    /**
     *Доступные массовому заполнению атрибуты
     *
     * @var string[]
     */
    protected $fillable = ['start', 'end', 'employee_id'];

    /**
     * Атрибуты, скрываемые от сериализации в массив.
     *
     * @var string[]
     */
    protected $hidden = ['created_at', 'updated_at'];

    /**
     * Выбирает начало и конец временного периода по id сотрудника
     *
     * @param $query
     * @param int $id
     * @return Builder
     */
    public function scopeByEmployeeId($query, int $id)
    {
        return $query->select('start', 'end')
            ->where('employee_id', $id)
            ->orderBy('start', 'asc');
    }
}
