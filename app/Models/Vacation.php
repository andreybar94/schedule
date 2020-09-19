<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Vacation extends Model
{
    /**
     *Доступные массовому заполнению атрибуты
     *
     * @var string[]
     */
    protected $fillable = ['from', 'to', 'employee_id'];

    /**
     * Атрибуты, скрываемые от сериализации в массив.
     *
     * @var string[]
     */
    protected $hidden = ['created_at', 'updated_at'];

    /**
     *Атрибуты, преобразуемые в экземпляры Carbon
     *
     * @var string[]
     */
    protected $dates = ['from', 'to'];

    /**
     * Выбирает дату начала и конеца отпуска по id сотрудника
     *
     * @param $query
     * @param int $id
     * @return Builder
     */
    public function scopeGetByEmployeeId($query, int $id): Builder
    {
        return $query->select('from', 'to')
            ->where('employee_id', $id);
    }
}
