<?php

namespace App\Http\Requests\BookingDetail;

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
            'date' => 'required|date',
            'file' => 'required|file|mimes:jpg,png,pdf|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'file.required' => 'File is required',
            'file.file' => 'File must be a file',
            'file.mimes' => 'File must be a jpg, png, or pdf',
            'file.max' => 'File size must not exceed 2MB',
            'date.required' => 'Date is required',
            'date.date' => 'Date must be a valid date',
            'booking_id.required' => 'Booking ID is required',
            'booking_id.exists' => 'Booking ID Not Found',
        ];
    }

    public function failedvalidation(Validator $validator){
        throw new HttpResponseException(
            ResponseModel::error(
                'Validation Error',
                400,
                $validator->errors()
            )
        );
    }
}
