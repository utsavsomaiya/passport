<?php

declare(strict_types=1);

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;
use Illuminate\Validation\Rules\RequiredIf;

class ProductMediaRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string|File|RequiredIf|null>>
     */
    public function rules(): array
    {
        return [
            'image_url' => [Rule::requiredIf($this->get('images') === null), 'url'],
            'images' => [Rule::requiredIf($this->get('image_url') === null), 'array'],
            'images.*' => ['required_with:images', 'file', File::defaults()],
        ];
    }
}
