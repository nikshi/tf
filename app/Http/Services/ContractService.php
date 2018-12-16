<?php

namespace App\Http\Services;

use App\Contract;
use App\Letter;
use App\Property;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ContractService
{

    /**
     * @param array $params
     * @return
     */
    public function createContract(array $params)
    {
        $this->validateParams($params);
        $contract = Contract::create($params['contract']);

        try {
            $properties = [];
            foreach ($params['properties'] as $key => $property) {
                $property['contract_id'] = $contract->id;
                $properties[$key] = Property::create($property);
                if (isset($property['letters'])) {
                    foreach ($property['letters'] as $lKey => $letter) {
                        $letter['property_id'] = $properties[$key]->id;
                        $properties[$key]['letters'][$lKey] = Letter::create($letter);
                    }
                }
            }
        } catch (\Exception $e) {
            $props = Property::select('id')->where('contract_id', $contract->id)->pluck('id')->toArray();
            Property::whereIn('id', $props)->delete();
            Letter::whereIn('property_id', $props)->delete();
            Contract::where('id', $contract->id)->delete();
        }

        $contract['properties'] = $properties;

        return ResponseFormatter::formatResponse(true, '', $contract);
    }

    /**
     * @param array $filters
     * @return array
     */
    public function getReportRental(array $filters)
    {
        $contracts = Contract::select('contracts.number as contract_number', 'date_start', 'date_end', 'date_start', 'properties.number as property_number', 'area', 'price', 'name', 'personal_number', 'owned_percentage');
        $contracts->join('properties', 'contracts.id', '=', 'properties.contract_id');
        $contracts->join('letters', 'properties.id', '=', 'letters.property_id');

        $contracts->where('contracts.type', Contract::$CONTRACT_TYPE_RENTAL);
        if(isset($filters['contract_numbers'])) {
            $contracts->whereIn('contracts.number', $filters['contract_numbers']);
        }
        if(isset($filters['property_numbers'])) {
            $contracts->whereIn('properties.number', $filters['property_numbers']);
        }
        if(isset($filters['owner_p_num'])) {
            $contracts->whereIn('letters.personal_number', $filters['owner_p_num']);
        }
        if(isset($filters['date'])) {
            $contracts->whereDate('date_start', '<=', $filters['date']);
            $contracts->whereDate('date_end', '>=', $filters['date']);
        }

        $contractsRes = $contracts->get()->toArray();
        $result = [];
        foreach ($contractsRes as $contract) {
            if (!isset($result[$contract['contract_number']])) {
                $result[$contract['contract_number']] = [
                    'contractNumber' => $contract['contract_number'],
                    'dateStart'      => $contract['date_start'],
                    'dateEnd'        => $contract['date_end'],
                    'properties'     => [],
                ];
            }

            if (!isset($result[$contract['contract_number']]['properties'][$contract['property_number']])) {
                $result[$contract['contract_number']]['properties'][$contract['property_number']] = [
                    'propertyNumber' => $contract['property_number'],
                    'propertyRent'   => 0,
                    'letters'        => [],
                ];
            }

            $result[$contract['contract_number']]['properties'][$contract['property_number']]['letters'][$contract['personal_number']] = [
                'name'           => $contract['name'],
                'personalNumber' => $contract['name'],
                'ownedArea'      => ($contract['area'] / 100) * $contract['owned_percentage'],
                'rent'           => ($contract['price'] / 100) * $contract['owned_percentage'],
            ];

            $result[$contract['contract_number']]['properties'][$contract['property_number']]['propertyRent'] += $result[$contract['contract_number']]['properties'][$contract['property_number']]['letters'][$contract['personal_number']]['rent'];
        }

        return ResponseFormatter::formatResponse(true, '', $result);

    }

    /**
     * @param array $params
     */
    private function validateParams(array $params)
    {
        if (!isset($params['contract'], $params['properties'])) {
            throw new BadRequestHttpException('Invalid request data', null, 400);
        }

        Validator::make($params['contract'], Contract::$rules, Contract::$messages)->validate();
        Validator::make($params['properties'], Property::$rules, Property::$messages)->validate();

        foreach ($params['properties'] as $property) {
            if ($params['contract']['type'] === Contract::$CONTRACT_TYPE_RENTAL && !isset($property['letters']) ||
                $params['contract']['type'] === Contract::$CONTRACT_TYPE_OWN && isset($property['letters'])
            ) {
                throw new BadRequestHttpException('Invalid request data', null, 400);
            }
            $percentSum = 0;
            foreach ($property['letters'] as $letter) {
                $percentSum += (int)$letter['owned_percentage'];
            }

            if ($percentSum > 100) {
                throw new BadRequestHttpException('Invalid request data.', null, 400);
            }

            Validator::make($property['letters'], Letter::$rules, Letter::$messages)->validate();
        }
    }

}