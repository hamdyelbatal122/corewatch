<?php

declare(strict_types=1);

namespace Hamzi\CoreWatch\Http\Requests;

use Hamzi\CoreWatch\Application\DTOs\LogFilterDto;
use Illuminate\Foundation\Http\FormRequest;

final class LogsRequest extends FormRequest
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
            'file' => 'required|string',
            'page' => 'integer|min:1',
            'level' => 'nullable|string',
            'ip' => 'nullable|string',
            'status' => 'nullable|integer',
            'search' => 'nullable|string',
            'date_start' => 'nullable|date',
            'date_end' => 'nullable|date',
        ];
    }

    public function toFilterDto(): LogFilterDto
    {
        return LogFilterDto::fromArray([
            'level' => $this->input('level'),
            'ip' => $this->input('ip'),
            'status' => $this->input('status'),
            'search' => $this->input('search'),
            'date_start' => $this->input('date_start'),
            'date_end' => $this->input('date_end'),
        ]);
    }
}
