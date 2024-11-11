<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "name" => fake()->name(),
            "email" => fake()->unique()->safeEmail(),
            "phone" => [
                "country_code" => fake()->countryCode(),
                "number" => fake()->phoneNumber(),
            ],
            "balance" => [
                "available" => fake()->randomFloat(null, 1000.0),
                "currency" => "USD",
            ],
            "rules" => [
                [
                    "name" => "Send email if not logged in for 5 days",
                    "type" => "inactivity",
                    "conditions" => [
                        "days_inactive" => 5,
                    ],
                    "action" => [
                        "type" => "email",
                        "priority" => 1,
                    ],
                ],
            ],
            "last_login" => null,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(
            fn(array $attributes) => [
                // "email_verified_at" => null,
            ]
        );
    }
}
