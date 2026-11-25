<?php

namespace App\Http\Requests;

use App\Concerns\ValidatesProperty;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePropertyRequest extends FormRequest
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
        return $this->propertyRules(creating: false);
    }

    /**
     * An unchecked checkbox is absent from form submissions; treat it as false.
     */
    protected function prepareForValidation(): void
    {
        if (! $this->expectsJson() && ! $this->has('featured')) {
            $this->merge(['featured' => false]);
        }
    }
}
