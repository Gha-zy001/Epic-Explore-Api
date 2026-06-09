<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;


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
      'name' => ['required','string', 'max:150'],
      'email' => ['required','string', 'max:255', 'unique:users'],
      'password' => ['required', 'confirmed', Password::defaults()],
      'image' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
    ];
  }

  public function bodyParameters(): array
  {
    return [
      'name' => [
        'description' => 'The user\'s name.',
        'example' => 'John Doe',
        'required' => true,
      ],
      'email' => [
        'description' => 'The user\'s email address.',
        'example' => 'john@example.com',
        'required' => true,
      ],
      'password' => [
        'description' => 'The user\'s password.',
        'example' => 'securePassword123',
        'required' => true,
      ],
      'password_confirmation' => [
        'description' => 'Password confirmation (must match password).',
        'example' => 'securePassword123',
        'required' => true,
      ],
    ];
  }
}
