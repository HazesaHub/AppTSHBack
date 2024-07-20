<?php
include_once '../class/C_Usuarios.php';
class Login extends Controller
{
    private $C_Usuarios = null;
    public array $rules = [
        'username' => 'string|max_length:255|required',
        'password' => 'string|max_length:255|required',
    ];

    public function __construct()
    {
        $this->C_Usuarios = new C_Usuarios();
    }

    public function Login(object $body): object
    {
        $validate = new validateDataTypes($this->rules, $body);
        $validarTypes = $validate->validate();
        if ($validarTypes->error) {
            return $this->response(400, true, $validarTypes->message);
        }

        $usuario = $this->C_Usuarios->Login((array) $body);
        if (!is_array($usuario)) {
            return $this->response(400, true, $usuario);
        }

        // aqui es un array hay que ver si esta vacio o no
        if (empty($usuario)) {
            return $this->response(400, true, 'Usuario o contraseña incorrectos');
        }

        // aqui hay usuario, hay que ver si ya tengo que actualizar o no la password_secundaria

        if ($usuario[0]['password_secondary'] == null) {
            $this->C_Usuarios->id_usuario = $usuario[0]['id_usuario'];
            $login = $this->C_Usuarios->UpdateNewPassword($body->password);
            if ($login !== true) {
                return $this->response(200, true, 'Error al actualizar la contraseña secundaria');
            }
        }

        /// si esta el id entonces lo obtengo
        return $this->response(200, false, 'Login correcto', $usuario);
    }

    
    public function LoginSecondary(object $body): object
    {
        $validate = new validateDataTypes($this->rules, $body);
        $validarTypes = $validate->validate();
        if ($validarTypes->error) {
            return $this->response(400, true, $validarTypes->message);
        }

        $usuario = $this->C_Usuarios->LoginSecondary((array) $body);
        if (!is_array($usuario)) {
            return $this->response(400, true, $usuario);
        }

        // aqui es un array hay que ver si esta vacio o no
        if (empty($usuario)) {
            return $this->response(400, true, 'Usuario o contraseña incorrectos');
        }

        // aqui hay usuario, hay que ver si ya tengo que actualizar o no la password_secundaria

        if ($usuario[0]['password_secondary'] == null) {
            $this->C_Usuarios->id_usuario = $usuario[0]['id_usuario'];
            $login = $this->C_Usuarios->UpdateNewPassword($body->password);
            if ($login !== true) {
                return $this->response(200, true, 'Error al actualizar la contraseña secundaria');
            }
        }

        /// si esta el id entonces lo obtengo
        return $this->response(200, false, 'Login correcto', $usuario);
    }

}
