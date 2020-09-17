<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    /**
     *Доступные массовому заполнению атрибуты
     *
     * @var string[]
     */
    protected $fillable = ['start', 'end'];

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
    protected $dates = ['start', 'end'];

}
