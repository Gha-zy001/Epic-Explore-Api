<?php

namespace App\Http\Controllers\Api\v1\User\Search;

use App\Actions\Search\GlobalSearchAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SearchController extends Controller
{
  public function __invoke(Request $request, GlobalSearchAction $action)
  {
    return $action->execute($request->query('query'));
  }
}
