<?php

namespace Database\Factories;

use App\Models\GameAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

class GameAccountFactory extends Factory
{
    protected $model = GameAccount::class;

    public function definition()
    {
        return [
            'username' => $this->faker->unique()->userName,
            'email' => $this->faker->unique()->safeEmail,
            'current_balance' => $this->faker->numberBetween(0, 1000000),
            'total_recharge' => $this->faker->numberBetween(0, 50000000),
            'status' => $this->faker->randomElement(['active', 'active', 'active', 'banned']), // 75% active
            'last_login_at' => $this->faker->optional(0.8)->dateTimeBetween('-1 month', 'now'),
            'created_at' => $this->faker->dateTimeBetween('-2 years', '-1 month'),
            'updated_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
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

    public function withHighBalance()
    {
        return $this->state(function (array $attributes) {
            return [
                'current_balance' => $this->faker->numberBetween(500000, 2000000),
                'total_recharge' => $this->faker->numberBetween(10000000, 100000000),
            ];
        });
    }
}
