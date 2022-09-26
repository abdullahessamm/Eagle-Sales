<?php

namespace App\Services\Google;

class GoogleGeocode extends GoogleMapsApi
{
    private array $results;

    public function __construct(array $params)
    {
        parent::__construct('geocoding', $params);
        $this->results = $this->decodedResponseToArray()['results'];
    }

    private function searchInComponents($type)
    {
        foreach ($this->results as $result) {
            if (isset($result['address_components'])) {
                foreach ($result['address_components'] as $component) {
                    if (in_array($type, $component['types'])) {
                        return $component;
                    }
                }
            }
        }
        return null;
    }

    public function getCountry()
    {
        return (object) $this->searchInComponents('country');
    }

    public function getGovernorate()
    {
        return (object) $this->searchInComponents('administrative_area_level_1');
    }

    public function getCity()
    {
        return (object) $this->searchInComponents('administrative_area_level_2');
    }

    public function getZone()
    {
        return (object) ($this->searchInComponents('locality') ?? $this->searchInComponents('administrative_area_level_3'));
    }

    public function getStreet()
    {
        return (object) $this->searchInComponents('route');
    }

    public function getBuildingNumber()
    {
        return (object) $this->searchInComponents('street_number');
    }

    public function getFormattedAddress()
    {
        return $this->getBuildingNumber()?->long_name . ', '
        . $this->getStreet()?->long_name . ' '
        . $this->getZone()?->long_name . ' '
        . $this->getCity()?->long_name . ' '
        . $this->getGovernorate()?->long_name . ' '
        . $this->getCountry()?->long_name;
    }
}