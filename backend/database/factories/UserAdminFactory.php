<?php

namespace Database\Factories;

use App\Models\UserAdmin;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserAdmin>
 */
class UserAdminFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserAdmin::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'admin_name' => $this->faker->name(),
            'admin_phone' => '+81' . $this->faker->numerify('##########'),
            'admin_password' => Hash::make('admin123'),
            'admin_is_verified' => true,
            'admin_verified_at' => now(),
        ];
    }

    /**
     * Indicate that the admin is not verified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'admin_is_verified' => false,
            'admin_verified_at' => null,
        ]);
    }
}
