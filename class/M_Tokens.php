<?php
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;

class M_Tokens
{
    public string|null $token;
    private string $privateKey;
    private string $publicKey;
    private string $encryptionKey;

    private string $IvEncryptPayload;

    private int $exp = 28800;

    public function __construct($token = null)
    {
        $this->token = $token;
        $this->privateKey = $this->getPrivateKey();
        $this->publicKey = $this->getPublicKey();
        $this->encryptionKey = $this->getEncryptionKey();
        $this->IvEncryptPayload = $this->getIvEncryptPayload();
    }

    private function getPrivateKey(): string | false
    {
        return file_get_contents(KeyPrivateEncryptToken);
    }

    private function getPublicKey(): string | false
    {
        return file_get_contents(KeyPublicEncryptToken);
    }

    private function getEncryptionKey(): string
    {
        return file_get_contents(KeyEncryptPayload);
    }

    private function getIvEncryptPayload(): string
    {
        return file_get_contents(KeyIVEncryptPayload);
    }

    private function encryptPayload(array $payload): string
    {
        $plaintext = json_encode($payload);
        $cipher = "aes-256-cbc";

        // Decodifica la clave desde Base64
        $key = base64_decode($this->encryptionKey);
        if (strlen($key) !== 32) {
            throw new Exception('Invalid key length. Must be 256 bits (32 bytes) for AES-256.');
        }

        // Decodifica el IV desde Base64
        $iv = base64_decode($this->IvEncryptPayload); // Usa el IV previamente generado
        if (strlen($iv) !== 16) {
            throw new Exception('Invalid IV length. Must be 128 bits (16 bytes) for AES.');
        }

        // Cifra el texto plano
        $ciphertext = openssl_encrypt($plaintext, $cipher, $key, OPENSSL_RAW_DATA, $iv);

        if ($ciphertext === false) {
            throw new Exception('Encryption failed.');
        }

        // Codifica el texto cifrado en Base64
        return base64_encode($ciphertext);
    }

    private function decryptPayload(string $encryptedPayload): array | false
    {
        $cipher = "aes-256-cbc";

        // Decodifica la clave desde Base64
        $key = base64_decode($this->encryptionKey);
        if (strlen($key) !== 32) {
            return false;
        }

        // Decodifica el IV desde Base64
        $iv = base64_decode($this->IvEncryptPayload);
        if (strlen($iv) !== 16) {
            return false;
        }

        // Decodifica el texto cifrado desde Base64
        $ciphertext = base64_decode($encryptedPayload);

        // Descifra el texto cifrado
        $plaintext = openssl_decrypt($ciphertext, $cipher, $key, OPENSSL_RAW_DATA, $iv);
        if ($plaintext === false) {
            return false;
        }

        // Decodifica el JSON
        $data = json_decode($plaintext, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return false;
        }

        return $data;
    }

    public function createToken(array $data): string | false
    {
        $data['iss'] = 'Hazesa';
        $data['aud'] = 'AppTSH';
        $data['exp'] = time() + $this->exp;
        $data['nbf'] = time();
        $data['iat'] = time();
        $data['timezone'] = 'America/Mexico_City';

        $encryptedData = $this->encryptPayload($data);
        return JWT::encode(array('data' => $encryptedData), $this->privateKey, 'RS256');
    }

    private function verifyToken(): array | false
    {
        try {
            return (array) JWT::decode($this->token, new Key($this->publicKey, 'RS256'));
        } catch (SignatureInvalidException $e) {
            // Signature is invalid
        } catch (BeforeValidException $e) {
            // Token is not yet valid
        } catch (ExpiredException $e) {
            // Token has expired
        } catch (UnexpectedValueException $e) {
            // Token is malformed
        } catch (Exception $e) {
            // General error
        }
        return false;
    }

    public function readToken(): array | false
    {
        $decoded = $this->verifyToken();
        if ($decoded === false) {
            return false;
        }

        return $this->decryptPayload($decoded['data']);
    }

    public function updateToken(array $data): string | false
    {
        $decoded = $this->readToken();
        if ($decoded === false) {
            return false;
        }
        $decoded = array_merge($decoded, $data);
        return $this->createToken($decoded);
    }
}
