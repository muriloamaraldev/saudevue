<?php

namespace Database\Factories;

use App\Models\MedicalRecord;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MedicalRecord>
 */
class MedicalRecordFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'patient_id' => Patient::factory(),
            // O prontuário herda o tenant do paciente — nunca podem divergir.
            'user_id' => fn (array $attributes) => Patient::query()
                ->findKey($attributes['patient_id'])?->user_id,
            'occurred_at' => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'chief_complaint' => fake()->sentence(),
            'history' => fake()->paragraph(),
            'diagnosis' => fake()->sentence(),
            'conduct' => fake()->paragraph(),
            'prescription' => fake()->optional()->sentence(),
        ];
    }
}
