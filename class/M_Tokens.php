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

    private int $exp = 28800;

    public function __construct($token = null)
    {
        $this->token = $token;
        $this->privateKey = $this->getPrivateKey();
        $this->publicKey = $this->getPublicKey();
        $this->encryptionKey = $this->getEncryptionKey();
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

    private function encryptPayload(array $payload): string
    {
        $plaintext = json_encode($payload);
        $cipher = "aes-256-cbc";
        $key = substr(hash('sha256', $this->encryptionKey, true), 0, 32);
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher));

        $ciphertext = openssl_encrypt($plaintext, $cipher, $key, 0, $iv);

        return base64_encode($ciphertext . '::' . $iv);
    }

    private function decryptPayload(string $encryptedPayload): array
    {
        $cipher = "aes-256-cbc";
        $key = substr(hash('sha256', $this->encryptionKey, true), 0, 32);

        list($ciphertext, $iv) = explode('::', base64_decode($encryptedPayload), 2);
        
        $plaintext = openssl_decrypt($ciphertext, $cipher, $key, 0, $iv);
        
        return json_decode($plaintext, true);
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
        return JWT::encode(array('data' =>$encryptedData), $this->privateKey, 'RS256');
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
        if($decoded === false) {
            return false;
        }

        return $this->decryptPayload($decoded['data']);
    }

    public function updateToken(array $data): string | false
    {
        $decoded = $this->readToken();
        if($decoded === false) {
            return false;
        }
        $decoded = array_merge($decoded, $data);
        return $this->createToken($decoded);
    }
}
?>
