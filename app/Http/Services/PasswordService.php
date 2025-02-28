<?php

namespace App\Http\Services;

class PasswordService
{
    /**
     * Gera uma senha segura com caracteres aleatórios.
     *
     * @param int $tamanho
     * @return string
     */
    public function generateSecurePassword(int $tamanho = 15): string
    {
        $caracteres = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_=+';
        $senha = '';
        $comprimento = strlen($caracteres);

        for ($i = 0; $i < $tamanho; $i++) {
            $senha .= $caracteres[random_int(0, $comprimento - 1)];
        }

        return $senha;
    }
}
