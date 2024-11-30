<?php

namespace App\Http\Requests\MasterData\Coworking;

use App\Helpers\ResponseModel;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class Store extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return \true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required',
            'address' => 'required',
            'phone' => 'required|unique:coworkings,phone',
            'email' => 'required|unique:coworkings,email',
            'url' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'address.required' => 'The address field is required.',
            'phone.required' => 'The phone field is required.',
            'phone.unique' => 'The phone field must be unique.',
            'email.required' => 'The email field is required.',
            'email.unique' => 'The email field must be unique.',
            'url.required' => 'The url field is required.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(ResponseModel::error(
            'Validation Error',
            400,
            $validator->errors()
        ));
    }
}
