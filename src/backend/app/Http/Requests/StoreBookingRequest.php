<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'eventTypeId' => ['required', 'integer', 'exists:event_types,id'],
            'guestName' => ['required', 'string', 'max:255'],
            'guestEmail' => ['required', 'email'],
            'startTime' => ['required', 'date'],
        ];
    }
}
