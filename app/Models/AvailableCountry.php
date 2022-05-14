<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AvailableCountry extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_ar',
        'iso_code',
        'code',
        'flag',
        'currency',
        'currency_ar',
    ];

    public function cities()
    {
        return $this->hasMany(AvailableCity::class, 'country_id', 'id');
    }

    public function getCities()
    {
        return $this->cities()->get()->all();
    }

    public function addCities($cities)
    {
        $allCities = [];

        foreach ($cities as $city) {
            $newCity = new AvailableCity();
            $newCity->name = $city['name'];
            $newCity->name_ar = $city['name_ar'];
            $newCity->country_id = $this->id;
            $newCity->save();
            $allCities[] = $newCity;
        }

        return $allCities;
    }

    public function withCities()
    {
        $this->cities = $this->getCities();
        return $this;
    }
}
