<?php

namespace App\Services\Google;

class GoogleMapsApi
{
    protected $module;
    protected string $response;

    public function __construct(string $requiredModule, array $params)
    {
        $this->module = \GoogleMaps::load($requiredModule);
        $this->module->setParam($params);
        $this->response = $this->module->get();
        $this->checkIfApiKeyIsValid();
    }

    public function decodedResponseToJson(bool $assoc = false)
    {
        return json_decode($this->response, $assoc);
    }

    public function decodedResponseToArray()
    {
        return $this->decodedResponseToJson(true);
    }

    private function checkIfApiKeyIsValid()
    {
        $response = $this->decodedResponseToJson();
        
        if ($response->status === 'REQUEST_DENIED') {
            throw new \Exception('Google Maps API request denied');
        }
    }
}