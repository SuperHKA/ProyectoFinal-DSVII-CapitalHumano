<?php

declare(strict_types=1);

namespace App\Servicios;

use App\Contratos\CryptographicServiceInterface;
use RuntimeException;

final class RsaSignatureService implements CryptographicServiceInterface
{
    private string $privateKeyPath;
    private string $publicKeyPath;

    public function __construct(?string $privateKeyPath = null, ?string $publicKeyPath = null)
    {
        $keyDirectory = ROOT_PATH . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'keys';
        $privateFromEnvironment = trim((string) env_value('SIGNING_PRIVATE_KEY', ''));
        $publicFromEnvironment = trim((string) env_value('SIGNING_PUBLIC_KEY', ''));
        $this->privateKeyPath = $privateKeyPath
            ?? ($privateFromEnvironment !== '' ? $privateFromEnvironment : $keyDirectory . DIRECTORY_SEPARATOR . 'private.pem');
        $this->publicKeyPath = $publicKeyPath
            ?? ($publicFromEnvironment !== '' ? $publicFromEnvironment : $keyDirectory . DIRECTORY_SEPARATOR . 'public.pem');
    }

    public function protect(string $plainText): string
    {
        $signature = '';
        if (!openssl_sign($plainText, $signature, $this->privateKey(), OPENSSL_ALGO_SHA256)) {
            throw new RuntimeException('No fue posible firmar los datos con OpenSSL.');
        }

        return base64_encode($signature);
    }

    public function verify(string $plainText, string $protectedValue): bool
    {
        $signature = base64_decode($protectedValue, true);
        if ($signature === false) {
            return false;
        }

        $result = openssl_verify($plainText, $signature, $this->publicKey(), OPENSSL_ALGO_SHA256);
        if ($result === -1) {
            throw new RuntimeException('OpenSSL no pudo verificar la firma digital.');
        }

        return $result === 1;
    }

    public function algorithm(): string
    {
        return 'RSA-SHA256';
    }

    private function privateKey(): \OpenSSLAsymmetricKey
    {
        if (!is_file($this->privateKeyPath) || !is_readable($this->privateKeyPath)) {
            throw new RuntimeException('La llave privada de firma no esta disponible.');
        }

        $key = openssl_pkey_get_private((string) file_get_contents($this->privateKeyPath));
        if (!$key instanceof \OpenSSLAsymmetricKey) {
            throw new RuntimeException('La llave privada de firma no es valida.');
        }

        return $key;
    }

    private function publicKey(): \OpenSSLAsymmetricKey
    {
        if (!is_file($this->publicKeyPath) || !is_readable($this->publicKeyPath)) {
            throw new RuntimeException('La llave publica de firma no esta disponible.');
        }

        $key = openssl_pkey_get_public((string) file_get_contents($this->publicKeyPath));
        if (!$key instanceof \OpenSSLAsymmetricKey) {
            throw new RuntimeException('La llave publica de firma no es valida.');
        }

        return $key;
    }
}
