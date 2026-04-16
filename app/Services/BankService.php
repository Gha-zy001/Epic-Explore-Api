<?php

namespace App\Services;

use App\Models\Bank;
use App\Models\State;

class BankService extends BaseService
{
    protected string $cachePrefix = 'banks';

    /**
     * Get all banks with pagination.
     */
    public function getAllBanks(int $perPage = 10)
    {
        return Bank::paginate($perPage);
    }

    /**
     * Get bank by ID.
     */
    public function getBankById(int $id)
    {
        return Bank::find($id);
    }

    /**
     * Get banks by state name.
     */
    public function getBanksByState(string $stateName)
    {
        return $this->remember("state.{$stateName}", function () use ($stateName) {
            $state = State::where('name', $stateName)->first();
            if (!$state) {
                return null;
            }

            return Bank::where('state_id', $state->id)->get();
        });
    }
}
