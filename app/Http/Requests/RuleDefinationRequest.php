<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RuleDefinationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "name" => ["required", "string", "max:255"],
            "type" => [
                "required",
                "string",
                "in:inactivity,transaction_threshold,transaction_limit",
            ],

            // Conditions based on the rule type
            "conditions.days_inactive" => [
                "required_if:type,inactivity",
                "integer",
                "min:1",
            ],
            "conditions.min_transaction_amount" => [
                "required_if:type,transaction_threshold",
                "numeric",
                "min:1",
            ],
            "conditions.transaction_count" => [
                "required_if:type,transaction_limit",
                "numeric",
                "min:2",
            ],

            // Action fields
            "action.type" => ["required", "string", "in:email,sms"],
            "action.priority" => ["required", "string", "min:1", "max:3"],
        ];
    }
}
