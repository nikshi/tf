<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilterRequest;
use App\Http\Services\ContractService;
use App\Http\Services\PropertyService;
use Illuminate\Http\Request;

class FarmerController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createContract(Request $request) {
        $contractService = new ContractService();

        return response()->json($contractService->createContract($request->post()), 200);
    }

    /**
     * @param FilterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function reportRental(FilterRequest $request) {
        $contractService = new ContractService();

        return response()->json($contractService->getReportRental($request->validated()), 200);
    }

    /**
     * @param FilterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function reportOwn(FilterRequest $request) {
        $propertyService = new PropertyService();

        return response()->json($propertyService->getReportOwn($request->validated()), 200);
    }
}
