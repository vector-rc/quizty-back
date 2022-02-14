<?php

namespace Quizty\User;

use Quizty\Utils\MysqlRepository;

class UserRepository
{
    private $repository;
    public function __construct()
    {
        $this->repository = new MysqlRepository();
    }

    public function findAll()
    {
        return $this->repository->select('User', null, 'enable=1');
    }
    public function findByEmail($email)
    {
        $data = $this->repository->select('User', null, 'email=:email', ['email' => $email]);
        return $data ? $data[0] : $data;
    }
    public function findById($id)
    {
        $data = $this->repository->select('User', null, 'id=:id', ['id' => $id]);
        return $data ? $data[0] : $data;
    }

    public function save(User|array $user)
    {
        $user = (array)$user;
        $encrypt_password = new EncryptPassword($user['password']);
        $user['password'] = $encrypt_password();
        return $this->repository->insert('User', $user);
    }

    public function edit(User $user)
    {
        $encrypt_password = new EncryptPassword($user->password);
        $user->password = $encrypt_password();
        $response = $this->repository->update("User", (array)$user, 'id=:id');
        return $response;
    }

    public function delete($id)
    {
        return $this->repository->delete('User', $id);
    }

    public function exist($email)
    {
        return $this->repository->select('User', null, 'email=:email', ['email' => $email]);
    }
}
