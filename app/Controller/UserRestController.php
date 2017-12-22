<?php

namespace App\Controller;

use App\Repository\UserRepository;
use SampleApp\Controller;
use SampleApp\Exception\NotFoundException;

class UserRestController extends Controller
{
    public function listUsersAction()
    {
        return $this->getRepository()->findAll();
    }

    public function getUserAction($id)
    {
        $user = $this->getRepository()->findOne((int) $id);

        if (!$user) {
            throw new NotFoundException('User not found');
        }

        return $user;
    }

    public function deleteUserAction($id)
    {
        $user = $this->getRepository()->findOne((int) $id);

        if (!$user) {
            throw new NotFoundException('User not found');
        }

        $this->getRepository()->delete($id);

        $this->statusCode = 204;
    }

    public function addUserAction()
    {
        $data = $this->validateForm(['email', 'name']);

        $id = $this->getRepository()->insertUser($data['email'], $data['name']);

        $user = $this->getRepository()->findOne($id);
        $this->statusCode = 201;

        return $user;
    }

    /**
     * @return UserRepository
     */
    protected function getRepository()
    {
        return $this->app->getService('user.repository');
    }
}