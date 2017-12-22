<?php

namespace SampleApp;

class Route
{

    /**
     *  @var string
     */
    protected $method;

    /**
     * @var string
     */
    protected $path;


    /**
     * @var string
     */
    protected $regex;

    /**
     * @var string
     */
    protected $controllerClass;

    /**
     * @var string
     */
    protected $action;

    /**
     * @var array
     */
    protected $params;

    /**
     * Route constructor.
     * @param string $method
     * @param string $path
     * @param string $controllerClass
     * @param string $action
     */
    public function __construct(string $method, string $path, string $controllerClass, string $action)
    {
        $this->method = strtoupper($method);
        $this->path = $path;
        $this->controllerClass = $controllerClass;
        $this->action = $action;
        $this->buildRegex();
    }

    protected function buildRegex()
    {
        $regex  = '#^'. str_replace('/', '\/', $this->path). "\/?$#";

        if (($pos = strpos($this->path, '{'))) {
            for ($i = $pos; $i < strlen($this->path); $i++) {
                $nextBrace = strpos($this->path, '}', $pos);
                if (false === $nextBrace) {
                    throw new \Exception("Missing closing brace '}' in routes", $this->path);
                }

                $param = substr($this->path, $pos, ($nextBrace - $pos) + 1);
                $regex = str_replace($param, '([\.\w_-]+)', $regex);
                $pos = strpos($this->path, '{', $nextBrace);
                if (false === $pos) {
                    break;
                }
            }
        }

        $this->regex = $regex;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @return string
     */
    public function getRegex(): string
    {
        return $this->regex;
    }

    /**
     * @return string
     */
    public function getControllerClass(): string
    {
        return $this->controllerClass;
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * @param array $params
     */
    public function setParams(array $params)
    {
        $this->params = $params;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

}