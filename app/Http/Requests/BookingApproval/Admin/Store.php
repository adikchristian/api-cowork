<?php

namespace App\Http\Requests\BookingApproval\Admin;

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
            'booking_id' => 'required|exists:bookings,id',
            'status' => 'required|in:success,pending,cancled',
        ];
    }

    public function messages()
    {
        return [
            'booking_id.required' => 'Booking ID is required',
            'booking_id.exists' => 'Booking ID Not Found',
            'status.required' => 'Status is required',
            'status.in' => 'Status must be success, pending, or cancled',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            ResponseModel::error(
                'Validation Error',
                400,
                $validator->errors()
            )
        );
    }
}
