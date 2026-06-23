<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Traits\ApiTrait;

class RankingController extends Controller
{
    public function index()
    {
        $topUsers = User::orderBy('exp', 'desc')
            ->limit(10)
            ->get(['name', 'image', 'exp', 'level']);

        return ApiTrait::data(compact('topUsers'));
    }
}
