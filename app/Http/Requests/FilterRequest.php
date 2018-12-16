<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FilterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'contract_numbers' => 'array',
            'property_numbers' => 'array',
            'owner_p_num'      => 'array',
            'date'             => 'date',
        ];
    }

    /**
     * Custom message for validation
     *
     * @return array
     */
    public function messages()
    {
        return [
            'contract_numbers.array' => 'Грешен формат на договорите!',
            'property_numbers.array' => 'Грешен формат на имотите!',
            'owner_p_num.array'      => 'Грешен формат на собсвениците!',
            'date.date'              => 'Грешен формат на датата!',
        ];
    }
}
