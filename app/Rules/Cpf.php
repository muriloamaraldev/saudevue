<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Valida CPF brasileiro (11 dígitos, dígitos verificadores mod 11).
 *
 * Guarda apenas o dígito — a normalização (máscara -> só números) acontece
 * nos FormRequests, então a regra aceita tanto "529.982.247-25" quanto
 * "52998224725".
 */
class Cpf implements ValidationRule
{
    private const PESOS_DV1 = [10, 9, 8, 7, 6, 5, 4, 3, 2];

    private const PESOS_DV2 = [11, 10, 9, 8, 7, 6, 5, 4, 3, 2];

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || ! self::valido($value)) {
            $fail('O :attribute informado não é um CPF válido.');
        }
    }

    /**
     * Remove a máscara e devolve apenas os dígitos.
     */
    public static function normalizar(string $cpf): string
    {
        return preg_replace('/\D/', '', $cpf) ?? '';
    }

    /**
     * Formata para exibição: 52998224725 -> 529.982.247-25.
     */
    public static function formatar(string $cpf): string
    {
        $digitos = self::normalizar($cpf);

        if (strlen($digitos) !== 11) {
            return $digitos;
        }

        return preg_replace('/^(\d{3})(\d{3})(\d{3})(\d{2})$/', '$1.$2.$3-$4', $digitos)
            ?? $digitos;
    }

    public static function valido(string $cpf): bool
    {
        $digitos = self::normalizar($cpf);

        // CPF tem exatamente 11 dígitos.
        if (strlen($digitos) !== 11) {
            return false;
        }

        // Sequências como 111.111.111-11 passam no módulo 11 por coincidência,
        // então são descartadas explicitamente.
        if (preg_match('/^(\d)\1{10}$/', $digitos)) {
            return false;
        }

        $base = substr($digitos, 0, 9);

        return self::digitoVerificador($base, self::PESOS_DV1) === (int) $digitos[9]
            && self::digitoVerificador($base.($digitos[9]), self::PESOS_DV2) === (int) $digitos[10];
    }

    /**
     * @param  array<int, int>  $pesos
     */
    private static function digitoVerificador(string $base, array $pesos): int
    {
        $soma = 0;

        for ($i = 0, $tamanho = strlen($base); $i < $tamanho; $i++) {
            $soma += (int) $base[$i] * $pesos[$i];
        }

        $resto = $soma % 11;

        return $resto < 2 ? 0 : 11 - $resto;
    }
}
