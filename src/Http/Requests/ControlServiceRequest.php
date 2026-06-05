<?php

declare(strict_types=1);

namespace Hamzi\CoreWatch\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class ControlServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'service_key' => 'required|string',
        ];
    }
}
