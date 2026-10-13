<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
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
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => 'user',
            'status' => 'active',
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Akun dengan peran dan status tertentu.
     */
    public function role(string $role, string $status = 'active'): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => $role,
            'status' => $status,
        ]);
    }

    /**
     * Akun administrator.
     */
    public function admin(): static
    {
        return $this->role('admin');
    }

    /**
     * Akun reviewer AI Quality Review.
     */
    public function reviewer(): static
    {
        return $this->role('reviewer');
    }

    /**
     * Akun yang dinonaktifkan tetapi riwayatnya tetap tersimpan.
     */
    public function inactive(): static
    {
        return $this->role('user', 'inactive');
    }
}
