<?php

namespace App\Http\Requests;

use Doctrine\Inflector\Rules\English\Rules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

use function Laravel\Prompts\password;

class UserData extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   */
  public function authorize(): bool
  {
    return true;
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
   */
  public function rules(): array
  {
    return [
      'name' => ['string', 'max:150'],
      'email' => ['string', 'max:255', 'unique:users'],
      'password' => ['required', 'confirmed', Password::defaults()],
      'image' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
    ];
  }
}
