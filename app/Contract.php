<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    public static $CONTRACT_TYPE_OWN = 'OWN';
    public static $CONTRACT_TYPE_RENTAL = 'RENTAL';

    protected $fillable = ['number', 'type', 'date_start', 'date_end', 'price'];

    public static $rules = [
        'number'     => 'required|unique:contracts,number',
        'type'       => 'required|in:OWN,RENTAL',
        'date_start' => 'required|date',
        'date_end'   => 'required|date',
        'price'      => 'required|numeric',
    ];

    public static $messages = [
        'number.required'     => 'Номера на договора е задължителен параметър!',
        'number.unique'       => 'Номера на договора вече съществува!',
        'type.required'       => 'Видът на договора е задължителен параметър!',
        'type.in'             => 'Грешен вид на договора!',
        'date_start.required' => 'Началната дата на договора е задължителен параметър!',
        'date_start.date'     => 'Формата на датата е невалиден!',
        'date_end.date'       => 'Формата на датата е невалиден!',
        'price.required'      => 'Цената/рентата на договора е задължителен параметър!',
        'price.numeric'       => 'Цената/рентата е невалидна!',
    ];

    public function properties(){
        return $this->hasMany('App\Property');
    }
}
