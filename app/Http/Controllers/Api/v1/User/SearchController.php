<?php
namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Services\SearchService;
use App\Traits\ApiTrait;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    protected SearchService $searchService;

    public function __construct(SearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    /**
     * Global search across all entities.
     */
    public function search(Request $request)
    {
        $query = $request->query('query');

        if (!$query) {
            return ApiTrait::errorMessage([], 'Search query is required', 400);
        }

        try {
            $results = $this->searchService->globalSearch($query);
            return ApiTrait::data($results, 'Search results fetched successfully');
        } catch (\Throwable $th) {
            return ApiTrait::errorMessage([], 'An error occurred during search', 500);
        }
    }
}
