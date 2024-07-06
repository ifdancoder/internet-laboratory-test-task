<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use \Auth;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->id == $this->id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id' => 'numeric|exists:users,id',
            'email' => 'email|unique:users',
        ];
    }

    public function messages(): array
    {
        return [
            'id.numeric' => 'ID должен быть числом',
            'id.exists' => 'Пользователь не найден',
            'email.email' => 'Неверный формат почты',
            'email.unique' => 'Пользователь с таким email уже существует',
        ];
    }

    public function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new HttpResponseException(
            new JsonResponse([
                'success' => false,
                'errors' => $validator->errors()
            ], 422));
    }
}
