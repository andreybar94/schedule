<?php

namespace App\Models;

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
}
