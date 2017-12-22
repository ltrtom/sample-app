<?php

namespace App\Controller;


use App\Repository\TaskRepository;
use SampleApp\Controller;
use SampleApp\Exception\BadRequestException;
use SampleApp\Exception\NotFoundException;

class TaskRestController extends Controller
{

    public function listUserTasksAction($userId)
    {
        return $this->getRepository()->findAll((int) $userId);
    }

    public function getUserTaskAction($userId, $id)
    {
        $task = $this->getRepository()->findOne((int) $userId, (int) $id);

        if (!$task) {
            throw new NotFoundException('Task not found');
        }

        return $task;
    }

    public function deleteUserTaskAction($userId, $id)
    {
        $task = $this->getRepository()->findOne((int) $userId, (int) $id);
        if (!$task) {
            throw new NotFoundException('Task not found');
        }

        $this->getRepository()->delete((int) $userId, (int) $id);
    }

    public function addUserTaskAction($userId)
    {
        $data = $this->validateForm(['description', 'status']);

        $taskId = $this->getRepository()->insertTask(
            (int) $userId,
            $data['description'],
            (bool) $data['status']
        );

        $task = $this->getRepository()->findOne((int) $userId, $taskId);
        $this->statusCode = 201;

        return $task;
    }

    /**
     * @return TaskRepository
     */
    protected function getRepository()
    {
        return $this->app->getService('task.repository');
    }


}