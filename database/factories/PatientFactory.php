<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Patient>
 */
class PatientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => fake()->name(),
            'cpf' => self::cpfValido(),
            'birth_date' => fake()->dateTimeBetween('-90 years', '-1 year')->format('Y-m-d'),
            'phone' => fake()->numerify('###########'),
            'email' => fake()->unique()->safeEmail(),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Gera um CPF com dígitos verificadores corretos a partir de 9 dígitos
     * aleatórios, para os testes não dependerem de um número fixo.
     */
    public static function cpfValido(): string
    {
        $base = fake()->numerify('#########');

        $dv1 = self::digito($base, [10, 9, 8, 7, 6, 5, 4, 3, 2]);
        $dv2 = self::digito($base.$dv1, [11, 10, 9, 8, 7, 6, 5, 4, 3, 2]);

        return $base.$dv1.$dv2;
    }

    /**
     * @param  array<int, int>  $pesos
     */
    private static function digito(string $base, array $pesos): int
    {
        $soma = 0;

        for ($i = 0, $tamanho = strlen($base); $i < $tamanho; $i++) {
            $soma += (int) $base[$i] * $pesos[$i];
        }

        $resto = $soma % 11;

        return $resto < 2 ? 0 : 11 - $resto;
    }
}
