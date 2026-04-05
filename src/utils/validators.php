<?php
// utils/validators.php

/**
 * Valida o formato e os dígitos verificadores de um CPF.
 */
function isValidCpf(string $cpfInput): bool {
    $cpfDigits = preg_replace('/[^0-9]/', '', $cpfInput);

    if (strlen($cpfDigits) !== 11 || preg_match('/^(\d)\1{10}$/', $cpfDigits)) {
        return false;
    }

    for ($i = 9; $i < 11; $i++) {
        $expectedDigit = 0;
        for ($j = 0; $j < $i; $j++) {
            $expectedDigit += (int)$cpfDigits[$j] * (($i + 1) - $j);
        }
        $expectedDigit = (($expectedDigit % 11) < 2) ? 0 : 11 - ($expectedDigit % 11);
        if ((int)$cpfDigits[$i] !== $expectedDigit) return false;
    }

    return true;
}

/**
 * Valida o formato e os dígitos verificadores de um CNPJ.
 */
function isValidCnpj(string $cnpjInput): bool {
    $cnpjDigits = preg_replace('/[^0-9]/', '', $cnpjInput);

    if (strlen($cnpjDigits) !== 14 || preg_match('/^(\d)\1{13}$/', $cnpjDigits)) {
        return false;
    }

    // Validação dos dígitos verificadores
    for ($t = 12; $t < 14; $t++) {
        $d = 0;
        $c = 0;
        for ($m = ($t - 7), $i = 0; $i < $t; $i++) {
            $d += (int)$cnpjDigits[$i] * $m;
            $m = ($m == 2 ? 9 : --$m);
        }
        $d = ((10 * $d) % 11) % 10;
        if ((int)$cnpjDigits[$t] != $d) return false;
    }

    return true;
}

/**
 * Valida o formato básico de horário HH:MM - HH:MM
 */
function isValidOperatingHours(string $hours): bool {
    return preg_match('/^([01][0-9]|2[0-3]):[0-5][0-9] - ([01][0-9]|2[0-3]):[0-5][0-9]$/', $hours);
}
