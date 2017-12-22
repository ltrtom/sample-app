<?php

namespace SampleApp;

use PDO;
use SampleApp\Exception\BadRequestException;
use SampleApp\Exception\NotFoundException;

class Application
{
    /**
     * the current variable of $_SERVER['PATH_INFO']
     * @var string
     */
    protected $serverPathInfo;

    /**
     * the current HTTP method used
     * @var string
     */
    private $serverMethod;

    /**
     * @var Route[]
     */
    protected $routes;

    /**
     * @var array
     */
    protected $services;

    /**
     * Application constructor.
     * @param array $server
     */
    public function __construct(array $server)
    {
        if (!isset($server['PATH_INFO'])) {
            $this->serverPathInfo = '/';
        } else {
            $this->serverPathInfo = $server['PATH_INFO'];
        }

        if (!isset($server['REQUEST_METHOD'])) {
            throw new \LogicException('Unable to get the REQUEST_METHOD from server variables');
        }

        $this->serverMethod = $server['REQUEST_METHOD'];
    }

    /**
     * Register a route to the app
     * this route will be called if it matched http verb and path
     *
     * @param string $method
     * @param string $path
     * @param string $controller
     * @param string $action
     */
    public function addRoute(string $method, string $path, string $controller, string $action)
    {
        $this->routes[] = new Route(
            $method,
            $path,
            $controller,
            $action
        );
    }

    public function run()
    {
        // first step we found the corresponding route
        $route = $this->findMatchingRoute();

        if (!$route) {
            http_response_code(404);
            echo sprintf('No route matching for %s %s', $this->serverMethod, $this->serverPathInfo);
            return;
        }

        // instanciate the controller given by the route object
        $controllerClass = $route->getControllerClass();
        $controller = new $controllerClass;

        if (!($controller instanceof Controller)) {
            throw new \Exception('Controller must extends from SampleApp\\Controller class');
        }
        $controller->setApp($this);

        $this->addContentType();

        try {
            // call the action given by the route
            $response = call_user_func_array([$controller, $route->getAction()], $route->getParams());
        } catch (NotFoundException $e) {
            http_response_code(404);
            echo json_encode(['error' => $e->getMessage()]);
            return;
        } catch (BadRequestException $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
            return;
        }

        if (null === $response) {
            http_response_code(204);
        } else {
            if (!is_array($response)) {
                throw new \Exception('Controller response must be an array');
            }


            http_response_code($controller->getStatusCode());
            echo json_encode($response);
        }
    }

    public function initDatabase(string $server, string $databaseName, string $user, string $password)
    {
        $pdo = new PDO(
            sprintf("mysql:host=%s;dbname=%s", $server, $databaseName),
            $user,
            $password
        );
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->registerSharedService('db', $pdo);
    }

    public function getService(string $key)
    {
        if (!isset($this->services[$key])) {
            throw new \Exception('Missing service '.$key);
        }

        return $this->services[$key];
    }

    public function registerSharedService(string $key, $instance)
    {
        $this->services[$key] = $instance;
    }

    /**
     * @return null|Route
     */
    private function findMatchingRoute()
    {
        foreach ($this->routes as $route) {
            if (preg_match($route->getRegex(), $this->serverPathInfo, $matches) &&
                $this->serverMethod === $route->getMethod()) {
                // remove the full path match
                array_shift($matches);
                $route->setParams($matches);

                return $route;
            }
        }

        return null;
    }

    public function addContentType()
    {
        if (!headers_sent()) {
            header('Content-Type: application/json');
        }
    }
}