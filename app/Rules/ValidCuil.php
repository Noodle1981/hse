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
    private $error = 'El CUIL ingresado no es válido.';

    public function passes($attribute, $value)
    {
        if (empty($value)) {
            return true;
        }

        // Formato: XX-XXXXXXXX-X
        if (!preg_match('/^\d{2}-\d{8}-\d$/', $value)) {
            $this->error = 'El formato debe ser XX-XXXXXXXX-X (con guiones).';
            return false;
        }

        // Validar dígito verificador
        $cuil = str_replace('-', '', $value);
        
        if (strlen($cuil) !== 11) {
            $this->error = 'El CUIL debe tener 11 dígitos.';
            return false;
        }

        // Algoritmo de validación
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

        if ((int)$cuil[10] !== $digitoVerificador) {
            $this->error = "Dígito verificador incorrecto. Se esperaba $digitoVerificador.";
            return false;
        }

        return true;
    }

    public function message()
    {
        return $this->error;
    }
}
