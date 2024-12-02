<?php

namespace App\Http\Requests\Booking;

use App\Helpers\ResponseModel;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class Store extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'code' => 'required|max:10|unique:bookings,code',
            'cowork_plan_id' => 'required|exists:cowork_plans,id',
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'price' => 'required|numeric',
            'status' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'The code field is required.',
            'code.max' => 'The code field must not exceed 10 characters.',
            'code.unique' => 'The code field must be unique.',
            'cowork_plan_id.required' => 'The cowork_plan_id field is required.',
            'cowork_plan_id.exists' => 'Cowork plan not found.',
            'user_id.required' => 'The user_id field is required.',
            'user_id.exists' => 'User not found.',
            'date.required' => 'The date field is required.',
            'date.date' => 'The date field must be a valid date.',
            'price.required' => 'The price field is required.',
            'price.numeric' => 'The price field must be a number.',
            'status.required' => 'The status field is required.',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            ResponseModel::error(
                'Validation Error',
                400,
                $validator->errors(),
            )
        );
    }
}
