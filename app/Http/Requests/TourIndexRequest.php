<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TourIndexRequest extends FormRequest
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
            'priceFrom' => ['numeric'],
            'priceTo' => ['numeric'],
            'dateFrom' => ['date', 'date_format:Y-m-d', 'after_or_equal:today' ],
            'dateTo' => ['date', 'date_format:Y-m-d', 'after_or_equal:today'],
            'sortBy' => [Rule::in(['price'])],
            'sortOrder' => [Rule::in(['asc', 'desc'])],
        ];
    }
}
