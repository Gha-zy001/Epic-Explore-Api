<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FavoriteResource extends JsonResource
{
  /**
   * Transform the resource into an array.
   *
   * @return array<string, mixed>
   */
  public function toArray(Request $request): array
  {
    $isFavorite = $request->user() ? $request->user()->favorites->contains($this->id) : false;
    return [
      'id' => $this->favoritable->id,
      'name' => $this->favoritable->name,
      'address' => $this->favoritable->address,
      'type' => $this->favoritable_type,
      'status' => $isFavorite,
      'img_url' => $this->favoritable->images->pluck('data'),
    ];
  }
}
