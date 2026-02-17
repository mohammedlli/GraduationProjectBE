<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
        //      'stage_id'     => 'required|exists:stages,id',
        //      'user_id'     => 'required|exists:users,id',
        // 'question'  => 'required|string|max:500',
        //     'language'  => 'required|string|max:500',
        //     'description'  => 'nullable|string|max:500',
         ];
    }
}