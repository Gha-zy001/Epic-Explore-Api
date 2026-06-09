<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password as RulesPassword;

class ResetPasswordRequest extends FormRequest
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
      'email' => 'required|email',
      'otp' => 'required|max:6',
      'password' => ['required', 'confirmed', RulesPassword::defaults()],
    ];
  }

  public function bodyParameters(): array
  {
    return [
      'email' => [
        'description' => 'The user\'s email address.',
        'example' => 'john@example.com',
        'required' => true,
      ],
      'otp' => [
        'description' => 'One-time password sent to email.',
        'example' => '123456',
        'required' => true,
      ],
      'password' => [
        'description' => 'New password.',
        'example' => 'newSecurePassword123',
        'required' => true,
      ],
      'password_confirmation' => [
        'description' => 'Password confirmation (must match password).',
        'example' => 'newSecurePassword123',
        'required' => true,
      ],
    ];
  }
}
