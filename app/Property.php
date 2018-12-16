<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    protected $fillable = ['number', 'area', 'contract_id'];

    public static $rules = [
        '*.number'      => 'required|unique:properties,number',
        '*.area'        => 'required|numeric',
        '*.contract_id' => 'numeric',
        '*.letters'     => 'array',
    ];

    public static $messages = [
        '*.number.required'      => 'Номера на имота е задължителен параметър!',
        '*.number.unique'        => 'Номера на имота вече съществува!',
        '*.area.required'        => 'Площта на имота е задължителен параметър!',
        '*.area.numeric'         => 'Площта е невалидна!',
        '*.contract_id.numeric'  => 'ID-то на договора е невалидно!',
        '*.letters.array'        => 'Грешни данни за арендодателите!',
    ];

    public function contract() {
        return $this->belongsTo('App\Contract');
    }

    public function letters(){
        return $this->hasMany('App\Letter');
    }


}
