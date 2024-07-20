<?php

class C_Usuarios extends database
{
    public int $id_usuario;
    public function __construct($id_usuario = 0)
    {
        parent::__construct();
        $this->id_usuario = $id_usuario;
    }

    public function Login(array $body): array|string
    {
        $body['password'] = sha1($body['password']);

        // con PDF crea una setencia que inserte el cmapo nombre y el campo apellido, usando parametros
        $sql = "SELECT * FROM C_Usuarios WHERE usuario = :username AND password = :password";
        return $this->Select($sql, $body);
    }

    private function Get_AES_PEM_KEY(): string | false
    {
        return file_get_contents(Base_URL . 'clave_aes.pem');
    }

    public function UpdateNewPassword(string $password): bool | string
    {
        $pepper = $this->Get_AES_PEM_KEY();
        if ($pepper === false) {
            return 'No se pudo obtener la clave de cifrado';
        }

        $pepperedPassword = hash_hmac("sha256", $password, $pepper);

        // Hashear la contraseña mezclada con Argon2
        $options = [
            'memory_cost' => 1 << 17, // 128MB
            'time_cost' => 4,
            'threads' => 2,
        ];

        $hashedPassword = password_hash($pepperedPassword, PASSWORD_ARGON2ID, $options);

        $sql = "UPDATE C_Usuarios SET password_secondary = :password_secondary WHERE id_usuario = :id_usuario";
        $params = [
            'password_secondary' => $hashedPassword,
            'id_usuario' => $this->id_usuario,
        ];

        return $this->Update($sql, $params);
    }

    public function LoginSecondary(array $body): array|string
    {
        $pepper = $this->Get_AES_PEM_KEY();
        if ($pepper === false) {
            return false;
        }

        $pepperedPassword = hash_hmac("sha256", $body['password'], $pepper);

        // con PDF crea una setencia que inserte el cmapo nombre y el campo apellido, usando parametros
        $sql = "SELECT * FROM C_Usuarios WHERE usuario = :username";
        unset($body['password']);
        $user = $this->Select($sql, $body);
        if (!is_array($user)) {
            return 'Usuario o contraseña incorrectos';
        }

        // aqui es un array hay que ver si esta vacio o no
        if (empty($user)) {
            return 'Usuario o contraseña incorrectos';
        }

        // aqui si esta el usuario
        $passwordSecondaryDB = $user[0]['password_secondary'];

        // Verificar la contraseña
        if (password_verify($pepperedPassword, $passwordSecondaryDB)) {
            return $user;
        } else {
            return 'Usuario o contraseña incorrectos';
        }

    }
}
