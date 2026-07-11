<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class ValidCuil implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value)) {
            return;
        }

        // Formato: XX-XXXXXXXX-X
        if (! preg_match('/^\d{2}-\d{8}-\d$/', $value)) {
            $fail('El formato del CUIL debe ser XX-XXXXXXXX-X (con guiones).');

            return;
        }

        // Validar dígito verificador
        $cuil = str_replace('-', '', $value);

        if (strlen($cuil) !== 11) {
            $fail('El CUIL debe tener 11 dígitos.');

            return;
        }

        // Algoritmo de validación
        $multiplicadores = [5, 4, 3, 2, 7, 6, 5, 4, 3, 2];
        $suma = 0;

        for ($i = 0; $i < 10; $i++) {
            $suma += (int) $cuil[$i] * $multiplicadores[$i];
        }

        $resto = $suma % 11;
        $digitoVerificador = 11 - $resto;

        if ($digitoVerificador == 11) {
            $digitoVerificador = 0;
        } elseif ($digitoVerificador == 10) {
            $digitoVerificador = 9;
        }

        if ((int) $cuil[10] !== $digitoVerificador) {
            $fail("El CUIL ingresado no es válido. Dígito verificador incorrecto (se esperaba $digitoVerificador).");

            return;
        }
    }
}
