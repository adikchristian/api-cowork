<?php

namespace App\Http\Requests\MasterData\CoworkPlan;

use App\Helpers\ResponseModel;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;

class Update extends FormRequest
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
            'name' => 'required',
            'code' => 'required|max:10|unique:cowork_plans,code,'.$this->id,
            'price' => 'required|numeric|max:99999999999999999999',
            'benefit' => 'required',
            'coworking_id' => 'required|exists:coworkings,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'code.required' => 'The code field is required.',
            'code.unique' => 'The code field must be unique.',
            'price.required' => 'The price field is required.',
            'benefit.required' => 'The benefit field is required.',
            'coworking_id.required' => 'The coworking_id field is required.',
            'coworking_id.exists' => 'Coworking not found.',
            'code.max' => 'The code field must not exceed 10 characters.',
            'price.numeric' => 'The price field must be a number.',
            'price.max' => 'The price field must be 20 digits.',
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
