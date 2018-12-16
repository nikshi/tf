<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Letter extends Model
{
    protected $fillable = ['name', 'phone', 'personal_number', 'property_id', 'owned_percentage'];

    public static $rules = [
        '*.name'             => 'required',
        '*.phone'            => 'required',
        '*.personal_number'  => 'required',
        '*.property_id'      => 'numeric',
        '*.owned_percentage' => 'required|numeric',
    ];

    public static $messages = [
        '*.name.required'             => 'Името на арендодателя е задължителен параметър!',
        '*.phone.unique'              => 'Съществува арендодател със същия телефонен номер!',
        '*.phone.required'            => 'Телефонния номер е задължителен параметър!',
        '*.personal_number.required'  => 'ЕГН-то е задългителен параметър!',
        '*.property_id.numeric'       => 'ID-то на имота е невалидно!',
        '*.owned_percentage.required' => 'Процента на собственност е задължителен параметър!',
        '*.owned_percentage.numeric'  => 'Процента на собственност е невалиден!',
    ];

    public function property() {
        return $this->hasOne('App\Property');
    }
}
