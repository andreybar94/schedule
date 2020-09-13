<?php

namespace App\Models;

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

}
