<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidCuil implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (empty($value)) {
            return true; // CUIL es opcional
        }

        // Formato: XX-XXXXXXXX-X
        if (!preg_match('/^\d{2}-\d{8}-\d$/', $value)) {
            return false;
        }

        // Validar dígito verificador
        $cuil = str_replace('-', '', $value);
        
        if (strlen($cuil) !== 11) {
            return false;
        }

        // Algoritmo de validación de CUIL/CUIT argentino
        $multiplicadores = [5, 4, 3, 2, 7, 6, 5, 4, 3, 2];
        $suma = 0;

        for ($i = 0; $i < 10; $i++) {
            $suma += (int)$cuil[$i] * $multiplicadores[$i];
        }

        $resto = $suma % 11;
        $digitoVerificador = 11 - $resto;

        if ($digitoVerificador == 11) {
            $digitoVerificador = 0;
        } elseif ($digitoVerificador == 10) {
            $digitoVerificador = 9;
        }

        return (int)$cuil[10] === $digitoVerificador;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'El CUIL ingresado no es válido. Formato esperado: XX-XXXXXXXX-X';
    }
}
