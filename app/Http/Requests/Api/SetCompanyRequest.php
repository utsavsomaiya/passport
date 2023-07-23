<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Models\Company;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Exists;

class SetCompanyRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, (ValidationRule | array<int, string | Exists> | string)>
     */
    public function rules(): array
    {
        return [
            'company_id' => ['required', 'string', Rule::exists(Company::class, 'id')],
        ];
    }
}
