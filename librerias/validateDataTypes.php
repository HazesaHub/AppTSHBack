<?php

class validateDataTypes
{
    private $rules;

    private $data;

    public $response;

    public $key;

    public $requierd;

    public function __construct(array $rules, object $data)
    {
        $this->rules = $rules;
        $this->data = $data;
        $this->response = new stdClass();
        $this->key = "";
        $this->requierd = false;
    }

    public function validate(): object
    {
        // lo primero que tenemos que hacer es recorrer las reglas, para identicar los pararemos para cara una

        $this->response->error = false;
        foreach ($this->rules as $ruleName => $ruleParam) {
            $this->key = $ruleName;
            // first I have tho explote de data
            $ruleParams = explode("|", $ruleParam);

            // first, we have to check if the current param rule is required
            $isRequired = in_array("required", $ruleParams);

            // then we have to check if the current param rule is required
            if (!property_exists($this->data, $ruleName)) {
                if ($isRequired) {
                    $this->response->error = true;
                    $this->response->message = "El campo " . $ruleName . " es requerido";
                    // here is requierd but is not set
                    return $this->response;
                }
                continue;
            }

            $this->requierd = $isRequired;

            // here we have the data, now we have to check de data type
            $value = $this->data->{$ruleName};

            // check if the value is null
            if ($value === null || $value === "") {
                if ($this->requierd) {
                    $this->response->error = true;
                    $this->response->message = "El campo " . $this->key . " no puede ser nulo o vacio";
                    return $this->response;
                }
                // here is not required and is null so we continue because its not required
                continue;
            }

            // check if the data is a string
            $isTypeString = in_array("string", $ruleParams);
            if ($isTypeString) {
                // is string, so we have to validate the string
                $valideString = $this->validateString($value, $ruleParams);
                if ($valideString->error === true) {
                    return $valideString;
                }
                continue;
            }

            // check if the dara is integer
            $isInteger = in_array("integer", $ruleParams);
            if ($isInteger) {
                // is integer, so we have to validate the integer
                $valideInteger = $this->validateInteger($value);
                if ($valideInteger->error === true) {
                    return $valideInteger;
                }
                continue;
            }

            // check if the data is a email
            $isEmail = in_array("email", $ruleParams);
            if ($isEmail) {
                $isEmail = $this->validateEmail($value);
                if ($isEmail->error === true) {
                    return $isEmail;
                }
                continue;
            }

            // cehck if the data is a date
            $idDate = in_array("date", $ruleParams);
            if ($idDate === true) {
                $idDate = $this->validateDate($value);
                if ($idDate->error === true) {
                    return $idDate;
                }
                continue;
            }
        }

        return $this->response;
    }

    private function validateString($value, array $ruleParams): object
    {
        // intentar convertir el valor a string
        if (!is_string($value)) {

            // here it cuold be a number like 22, witch i can convert to string, so we have to check if the value is a number
            if (is_numeric($value)) {
                $value = (string) $value;
            } else {
                $this->response->error = true;
                $this->response->message = "El campo " . $this->key . " debe ser un string";
                return $this->response;
            }
        }

        // first we have to check the max length
        $maxLength = array_filter($ruleParams, function ($param) {
            return strpos($param, "max_length") !== false;
        });

        // order array
        $maxLength = array_values($maxLength);

        // If we dont have max length, default is 255
        $maxLength = (empty($maxLength)) ? 255 : (int) explode(":", $maxLength[0])[1];
        $valueLength = strlen($value);
        // now we have to check how many characters have the string value
        if ($valueLength > $maxLength) {
            $this->response->error = true;
            $this->response->data = "El campo " . $this->key . " no puede tener mas de " . $maxLength . " caracteres";
            return $this->response;
        }

        return $this->response;
    }

    private function validateInteger($value): object
    {

        if (!is_numeric($value)) {
            $this->response->error = true;
            $this->response->message = "El campo " . $this->key . " debe ser un numero";
            return $this->response;
        }

        return $this->response;
    }

    private function validateEmail(string $value): object
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->response->error = true;
            $this->response->message = "El campo " . $this->key . " debe ser un email valido";
            return $this->response;
        }

        return $this->response;
    }

    private function validateDate(string $value, string $format = 'Y-m-d'): object
    {
        $date = DateTime::createFromFormat($format, $value);
        if (!$date) {
            $this->response->error = true;
            $this->response->message = "El campo " . $this->key . " debe ser una fecha valida con formato " . $format;
            return $this->response;
        }

        return $this->response;
    }

    private function validateSpecialChars(string $value): object
    {
        if (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $value)) {
            $this->response->error = true;
            $this->response->message = "El campo " . $this->key . " no puede contener caracteres especiales";
            return $this->response;
        }

        return $this->response;
    }
}
