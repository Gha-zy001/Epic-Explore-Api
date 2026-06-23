<?php

namespace App\Http\Controllers\Api\Guider;

use App\Http\Controllers\Controller;
use App\Models\Guider;
use Illuminate\Http\Request;

class GuiderController extends Controller
{
  public function guider_data(Request $request)
  {
    $guider = Guider::get(['id', 'name', 'phone_number', 'description', 'email', 'is_verified']);

    $guider_data = $guider->map(function ($guider) use ($request) {
      if ($guider->is_verified == 1) {
        $guider_phone = $guider->phone_number;
        $guider_email = $guider->email;
      } else {
        $guider_phone = "Not Verified Yet";
        $guider_email = "Not Verified Yet";
      }
      return [
        'id' => $guider->id,
        'name' => $guider->name,
        'phone_number' => $guider_phone,
        'email' => $guider_email,
        'description' => $guider->description,
        'is_verified' => $guider->is_verified,
      ];
    })->toArray();
    return response()->json(['guider_data' => $guider_data]);
  }

  public function guider($guider_id)
  {

    $guider = Guider::findOrFail($guider_id);

    if ($guider->is_verified) {
      $guider_phone = $guider->phone_number;
      $guider_email = $guider->email;
    } else {
      $guider_phone = "Not Verified Yet";
      $guider_email = "Not Verified Yet";
    }

    $guider_data = [
      'id' => $guider->id,
      'name' => $guider->name,
      'phone_number' => $guider_phone,
      'email' => $guider_email,
      'description' => $guider->description,
      'is_verified' => $guider->is_verified,
    ];

    return response()->json(['guider_data' => $guider_data]);
  }
}
