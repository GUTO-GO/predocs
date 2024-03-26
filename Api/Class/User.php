<?php

namespace Predocs\Class;

use Predocs\Model\User as UserModel;

/**
 * Classe de representação do usuário
 *
 * Classe responsável por representar o usuário
 *
 * @package Predocs\Class
 * @author Felipe dos S. Cavalca
 * @version 1.0.0
 * @since 1.0.0
 */

class User
{

    public int $id;
    public string $name;
    public string $email;
    private UserModel $userModel;

    public function __construct(int $id = null)
    {
        $this->userModel = new UserModel();

        if (!is_null($id)) {
            $this->id = $id;
            $this->userModel->getById($id);
        }
    }

    public function __set($name, $value)
    {
        if (property_exists($this, $name)) {
            $this->$name = $value;
        }
    }

    public function setDataUser(array $data)
    {
        foreach ($data as $key => $value) {
            $this->__set($key, $value);
        }
    }
}
