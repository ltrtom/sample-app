<?php

namespace SampleApp;

use SampleApp\Exception\BadRequestException;

class Controller
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var int
     */
    protected $statusCode = 200;
    /**
     * @param Application $app
     */
    public function setApp(Application $app)
    {
        $this->app = $app;
    }

    protected function getRequestBody()
    {
        $json = file_get_contents('php://input');

        if (!$json) {
            return null;
        }

        return json_decode($json, true);
    }

    protected function validateForm(array $mandatoryKeys)
    {
        $data = $this->getRequestBody();

        if (!$data) {
            throw new BadRequestException('Missing correct JSON in request body');
        }

        foreach ($mandatoryKeys as $mandatoryKey) {
            if (!isset($data[$mandatoryKey])) {
                throw new BadRequestException(sprintf('Missing "%s" parameter in body', $mandatoryKey));
            }
            $value = $data[$mandatoryKey];

            if ('' === trim($value)) {
                throw new BadRequestException(sprintf('Value should not be blank for paramater %s', $mandatoryKey));
            }

            $data[$mandatoryKey] = $value;
        }

        return $data;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }
}