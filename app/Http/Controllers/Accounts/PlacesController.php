<?php

namespace App\Http\Controllers\Accounts;

use App\Exceptions\ForbiddenException;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Google\GoogleGeocode;
use App\Exceptions\GoogleMapsException;
use App\Exceptions\NotFoundException;
use App\Exceptions\ValidationError;
use App\Models\AvailableCity;
use App\Models\AvailableCountry;
use App\Models\UsersPlace;
use Illuminate\Support\Facades\Validator;

class PlacesController extends Controller
{

    public function get()
    {
        $authUser = auth()->user()->userData;
        return response()->json([
            'success' => true,
            'places'  => $authUser->places
        ]);
    }

    public function create(Request $request)
    {
        $authUser = auth()->user()->userData;
        if (
            ! $authUser->isCustomer() &&
            ! $authUser->isOnlineClient() &&
            ! $authUser->isSupplier()
        ) throw new ForbiddenException;

        $validator = Validator::make($request->all(), [
            'coords' => ['required', 'regex:/^[-+]?([1-8]?\d(\.\d+)?|90(\.0+)?),\s*[-+]?(180(\.0+)?|((1[0-7]\d)|([1-9]?\d))(\.\d+)?)$/']
        ]);

        if ($validator->fails())
            throw new ValidationError($validator->errors()->all());

        try {
            $geocode_en = new GoogleGeocode([
                'latlng' => $request->get('coords'),
                'language' => 'en',
            ]);

            $geocode_ar = new GoogleGeocode([
                'latlng' => $request->get('coords'),
                'language' => 'ar',
            ]);
        } catch (\Exception $e) {
            throw new GoogleMapsException($e->getMessage());
        }

        $country = $geocode_en->getCountry();
        $governorate = $geocode_en->getGovernorate();

        $availableCountries = AvailableCountry::get('iso_code')->pluck('iso_code')->toArray();
        $availableCities = AvailableCity::get('name')->pluck('name')->toArray();
        $country = $country ? $country['short_name'] : null;
        $governorate = $governorate ? $governorate['short_name'] : null;

        if (! in_array($country, $availableCountries)) {
            throw new ValidationError([
                'coords' => 'country not available',
            ]);
        }

        if (! in_array($governorate, $availableCities)) {
            throw new ValidationError([
                'coords' => 'city not available',
            ]);
        }

        // init place object
        $place = $authUser->places()->where('coords', $request->get('coords'))->first() ?? new UsersPlace;
        $place->user_id = $authUser->id;
        $place->coords     = $request->get('coords');
        $place->country    = $geocode_en->getCountry()['long_name'];
        $place->country_ar = $geocode_ar->getCountry()['long_name'];
        $place->country_code = $geocode_en->getCountry()['short_name'];
        $place->governorate = $geocode_en->getGovernorate()['long_name'];
        $place->governorate_ar = $geocode_ar->getGovernorate()['long_name'];
        $place->city = $geocode_en->getCity() ? $geocode_en->getCity()['long_name'] : null;
        $place->city_ar = $geocode_ar->getCity() ? $geocode_ar->getCity()['long_name'] : null;
        $place->zone = $geocode_en->getZone() ? $geocode_en->getZone()['long_name'] : null;
        $place->zone_ar = $geocode_ar->getZone() ? $geocode_ar->getZone()['long_name'] : null;
        $place->street = $geocode_en->getStreet() ? $geocode_en->getStreet()['long_name'] : null;
        $place->street_ar = $geocode_ar->getStreet() ? $geocode_ar->getStreet()['long_name'] : null;
        $place->building_no = $geocode_en->getBuildingNumber() ? $geocode_en->getBuildingNumber()['long_name'] : null;
        $place->save();

        return response()->json([
            'success' => true,
            'message' => 'Place added successfully'
        ]);
    }

    public function setPrimary(Request $request)
    {
        $authUser = auth()->user()->userData;
        if (
            ! $authUser->isCustomer() &&
            ! $authUser->isOnlineClient() &&
            ! $authUser->isSupplier()
        ) throw new ForbiddenException;

        $validation = Validator::make($request->all(), [
            'id' => 'required|integer',
        ]);

        if ($validation->fails())
            throw new ValidationError($validation->errors()->all());

        $place = $authUser->places()->find($request->get('id'));
        if (! $place)
            throw new NotFoundException(\App\Models\UsersPlace::class, $request->get('id'));

        $place->setPrimary();
        $place->save();

        return response()->json([
            'success' => true,
            'message' => 'Place updated successfully'
        ]);
    }
}
