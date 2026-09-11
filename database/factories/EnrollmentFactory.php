<?php

namespace Database\Factories;

use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Enrollment>
 */
class EnrollmentFactory extends Factory
{
    protected $model = Enrollment::class;

    public function definition(): array
    {
        $firstName = fake()->firstName();
        $lastName  = fake()->lastName();

        return [
            'user_id' => User::factory()->student(),
            'school_year' => '2026-2027',
            'reference_number' => 'TEST-' . strtoupper(Str::random(8)),
            'status' => 'enrolled',
            'grade_level' => 'grade1',
            'student_data' => [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'grade_level' => 'grade1',
                'student_type' => 'new',
                'gender' => fake()->randomElement(['male', 'female']),
            ],
            'payment_status' => 'pending',
            'payment_type' => 'full',
            'payment_option' => 'A',
            'payment_amount' => 0,
            'total_fee' => 14504,
            'remaining_balance' => 14504,
            'enrolled_at' => now(),
        ];
    }

    public function withInstallment(): static
    {
        return $this->state(fn () => [
            'payment_type' => 'installment',
            'payment_option' => 'B',
            'downpayment_amount' => 3000,
            'monthly_amount' => 1150,
        ]);
    }

    public function fullyPaid(): static
    {
        return $this->state(fn (array $attrs) => [
            'payment_status' => 'paid',
            'payment_amount' => $attrs['total_fee'] ?? 14504,
            'remaining_balance' => 0,
        ]);
    }

    public function pendingApproval(): static
    {
        return $this->state(fn () => ['status' => 'pending']);
    }
}
