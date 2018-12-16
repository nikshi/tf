<?php

namespace App\Http\Services;

use App\Contract;
use App\Property;

class PropertyService
{

    /**
     * @param array $filters
     * @return array
     */
    public function getReportOwn(array $filters){
        $properties = Property::select('contracts.number as contract_number', 'contracts.date_start as buying_date', 'area', 'price');
        $properties->join('contracts', 'properties.contract_id', '=', 'contracts.id');

        $properties->where('contracts.type', Contract::$CONTRACT_TYPE_OWN);
        if(isset($filters['contract_numbers'])) {
            $properties->whereIn('contracts.number', $filters['contract_numbers']);
        }
        if(isset($filters['property_numbers'])) {
            $properties->whereIn('properties.number', $filters['property_numbers']);
        }
        if(isset($filters['owner_p_num'])) {
            $properties->join('letters', 'properties.id', '=', 'letters.property_id');
            $properties->whereIn('letters.personal_number', $filters['owner_p_num']);
        }
        if(isset($filters['date'])) {
            $properties->whereDate('date_start', '<=', $filters['date']);
        }
        $properties = $properties->get()->toArray();

        $sumAreasByContract = [];
        $sumPrice = 0;
        $sumAreas = 0;
        foreach ($properties as $property) {
            if(!isset($sumAreasByContract[$property['contract_number']])) {
                $sumAreasByContract[$property['contract_number']] = 0;
                $sumPrice += $property['price'];
            }
            $sumAreasByContract[$property['contract_number']] += $property['area'];
            $sumAreas += $property['area'];
        }
        foreach ($properties as $k => &$property) {
            $properties[$k]['pricePerAcre'] = $property['price'] / $sumAreasByContract[$property['contract_number']];
            $properties[$k]['price'] = $property['pricePerAcre'] * $property['area'];
        }

        return ResponseFormatter::formatResponse(true, '', [
            'properties' => $properties,
            'sumAreas'   => $sumAreas,
            'sumPrices'  => $sumPrice,
        ]);

    }

}