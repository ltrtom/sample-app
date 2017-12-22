<?php
require  '../vendor/autoload.php';
opcache_reset();
use App\Controller\TaskRestController;
use App\Controller\UserRestController;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use SampleApp\Application;
use SampleApp\Exception\NotFoundException;

$app = new Application($_SERVER);

$app->initDatabase('localhost', 'sample', 'root', 'root');

// setup routing for user part
$app->addRoute('GET', '/users', UserRestController::class, 'listUsersAction');
$app->addRoute('GET', '/users/{id}', UserRestController::class, 'getUserAction');
$app->addRoute('DELETE', '/users/{id}', UserRestController::class, 'deleteUserAction');
$app->addRoute('POST', '/users', UserRestController::class, 'addUserAction');

// for user tasks part
$app->addRoute('GET', '/users/{userId}/tasks', TaskRestController::class, 'listUserTasksAction');
$app->addRoute('POST', '/users/{userId}/tasks', TaskRestController::class, 'addUserTaskAction');
$app->addRoute('GET', '/users/{userId}/tasks/{id}', TaskRestController::class, 'getUserTaskAction');
$app->addRoute('DELETE', '/users/{userId}/tasks/{id}', TaskRestController::class, 'deleteUserTaskAction');

// setup repositories
$app->registerSharedService('user.repository', new UserRepository($app->getService('db')));
$app->registerSharedService('task.repository', new TaskRepository($app->getService('db')));

try {
    $app->run();
} catch (NotFoundException $e) {
    echo $e->getMessage();
}

