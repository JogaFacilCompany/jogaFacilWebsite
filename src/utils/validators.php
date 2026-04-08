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

/**
 * Valida e sanitiza os dados de uma quadra (usado por create e update).
 * Retorna ['valido' => bool, 'mensagem' => string, 'campos' => array].
 */
function validateQuadraData(array $data): array {
    $nome          = trim($data['nome'] ?? '');
    $endereco      = trim($data['endereco'] ?? '');
    $telefone      = trim($data['telefone'] ?? '');
    $cnpj          = preg_replace('/[^0-9]/', '', $data['cnpj'] ?? '');
    $descricao     = trim($data['descricao'] ?? '');
    $modalidades   = trim($data['modalidades'] ?? 'Futebol');
    $funcionamento = trim($data['funcionamento'] ?? '08:00 - 22:00');
    $cancelamento  = (int)($data['cancelamento_horas'] ?? 24);
    $facilidades   = isset($data['facilidades']) ? json_encode($data['facilidades'], JSON_UNESCAPED_UNICODE) : '[]';

    if (empty($nome) || empty($endereco) || empty($telefone) || empty($cnpj)) {
        return ['valido' => false, 'mensagem' => 'Todos os campos obrigatórios (*) devem ser preenchidos.'];
    }

    if (!isValidCnpj($cnpj)) {
        return ['valido' => false, 'mensagem' => 'O CNPJ informado é inválido. Verifique os números.'];
    }

    if (!isValidOperatingHours($funcionamento)) {
        return ['valido' => false, 'mensagem' => 'O formato do horário de funcionamento deve ser HH:MM - HH:MM.'];
    }

    return [
        'valido' => true,
        'campos' => [
            'nome'          => $nome,
            'endereco'      => $endereco,
            'telefone'      => $telefone,
            'cnpj'          => $cnpj,
            'descricao'     => $descricao,
            'modalidades'   => $modalidades,
            'funcionamento' => $funcionamento,
            'cancelamento'  => $cancelamento,
            'facilidades'   => $facilidades,
        ]
    ];
}
