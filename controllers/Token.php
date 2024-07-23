<?php
include_once '../class/C_Usuarios.php';
include_once '../class/M_Tokens.php';
class Token extends Controller
{
    private $M_Tokens = null;

    private string|null $token = null;

    public function __construct(string $token = null)
    {
        $this->M_Tokens = new M_Tokens($token);
        $this->token = $token;
    }

   public function ReadToken(): object
    {
        $token = $this->M_Tokens->readToken();
        if($token == null || $token == false){
            return $this->response(401, true, 'Token invalido');
        }
        return $this->response(200, false, 'Token valido', $token);
    }

}
