<?php

namespace App\Http\Requests;

use App\Concerns\ValidatesProperty;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Closure;

class StorePropertyRequest extends FormRequest
{
    use ValidatesProperty;

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
     * @return array<string, ValidationRule|array<mixed>|string|Closure>
     */
    public function rules(): array
    {
        return $this->propertyRules(creating: true);
    }
}
