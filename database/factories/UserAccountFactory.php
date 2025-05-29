<?php

namespace Database\Factories;

use App\Models\UserAccount;
use App\Models\GameAccount;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserAccountFactory extends Factory
{
    protected $model = UserAccount::class;

    public function definition()
    {
        return [
            'username' => $this->faker->unique()->userName,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password123', // Will be hashed by mutator
            'phone' => $this->faker->optional(0.7)->phoneNumber,
            'game_account_id' => null, // Will be set later
            'status' => $this->faker->randomElement(['active', 'active', 'active', 'banned', 'suspended']), // 60% active
            'email_verified_at' => $this->faker->optional(0.8)->dateTimeBetween('-1 year', 'now'),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => now(),
        ];
    }

    public function active()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'active',
            ];
        });
    }

    public function banned()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'banned',
            ];
        });
    }

    public function withGameAccount()
    {
        return $this->state(function (array $attributes) {
            $gameAccount = GameAccount::factory()->create();
            return [
                'game_account_id' => $gameAccount->id,
            ];
        });
    }
}
