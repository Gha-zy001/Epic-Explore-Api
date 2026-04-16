<?php

namespace App\Http\Controllers;

use App\Http\Resources\BankResource;
use App\Traits\ApiTrait;
use App\Services\BankService;
use Illuminate\Http\Request;

class BankController extends Controller
{
  protected BankService $bankService;

  public function __construct(BankService $bankService)
  {
    $this->bankService = $bankService;
  }

  public function getBanks()
  {
    try {
      $banks = $this->bankService->getAllBanks(10);
      if ($banks->count() > 0) {
        $allBanks = BankResource::collection($banks);
        return ApiTrait::data(compact('allBanks'), 'Banks Fetched Successfully', 200);
      }
      return ApiTrait::errorMessage([], 'No Banks Yet', 404);
    } catch (\Throwable $th) {
      return ApiTrait::errorMessage([], 'Something went wrong', 500);
    }
  }

  public function getBank($id)
  {
    if (!is_numeric($id)) {
      return ApiTrait::errorMessage([], 'Invalid Bank Id', 400);
    }
    try {
      $bank = $this->bankService->getBankById($id);
      if (!$bank) {
        return ApiTrait::errorMessage([], 'Bank Not Found', 404);
      }
      return BankResource::collection([$bank]);
    } catch (\Throwable $th) {
      return ApiTrait::errorMessage([], 'Something went wrong', 500);
    }
  }

  public function getBanksByState($stateName)
  {
    try {
      $banks = $this->bankService->getBanksByState($stateName);
      
      if ($banks === null) {
          return response()->json(['error' => 'State not found'], 404);
      }

      return compact('banks');
    } catch (\Throwable $th) {
      return ApiTrait::errorMessage([], 'An error occurred', 422);
    }
  }
}

