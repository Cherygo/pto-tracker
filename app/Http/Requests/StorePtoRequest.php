<?php

namespace App\Http\Requests;

use App\Rules\NoOverlappingPto;
use Illuminate\Foundation\Http\FormRequest;

class StorePtoRequest extends FormRequest
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
     */
    public function rules(): array
    {
        $excludeId = $this->route('ptoRequest')?->id;

        return [
            'start_date' => [
                'required',
                'date',
                'after_or_equal:today',
                new NoOverlappingPto($this->input('end_date'), $excludeId),
            ],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'reason' => ['required', 'string', 'max:255'],
        ];
    }
}
