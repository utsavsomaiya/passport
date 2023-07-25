<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use App\Models\Hierarchy;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Exists;
use Illuminate\Validation\Rules\Unique;

class HierarchyRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, (ValidationRule | array<int, string | Exists | Unique> | string)>
     */
    public function rules(): array
    {
        return [
            'parent_hierarchy_id' => ['sometimes', 'string', 'uuid', Rule::exists(Hierarchy::class, 'id')->where('company_id', app('company_id'))],
            'name' => ['required', 'string', 'max:255', Rule::unique(Hierarchy::class)
                ->ignore($this->route()?->parameter('id'))
                ->where('parent_hierarchy_id', $this->get('parent_hierarchy_id'))
                ->where('name', $this->get('name')),
            ],
            'description' => ['nullable', 'string'],
            'slug' => ['sometimes', 'string', 'max:255'],
        ];
    }
}
