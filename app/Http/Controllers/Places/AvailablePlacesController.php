<?php

namespace App\Http\Controllers\Places;

use App\Exceptions\InternalError;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AvailableCountry;
use App\Models\AvailableCity;
use App\Rules\ArabicLettersWithSpaces;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;

class AvailablePlacesController extends Controller
{
    /**
     * get all available countries
     *
     * @return void
     */
    public function getPlaces()
    {
        try {
            $countries = AvailableCountry::all();

            foreach ($countries as $country)
                $country->withCities();

            return response()->json([
                'success' => true,
                'message' => $countries
            ], 200);
        } catch (QueryException $exception) {
            throw new InternalError($exception);
        }
    }

    public function addCountry(Request $request)
    {
        $user = auth()->user()->userData;

        if (! $user->can('change', AvailableCountry::class))
            throw new \App\Exceptions\ForbiddenException;

        $validation = Validator::make($request->all(), [
            'name' => 'required|regex:/^[A-Za-z\s]+$/|max:255|unique:available_countries',
            'name_ar' => ['required', new ArabicLettersWithSpaces, 'max:50', 'unique:available_countries'],
            'code' => 'required|regex:/^\+[0-9]{1,3}$/|unique:available_countries',
            'currency' => 'required|regex:/^[A-Z]{3}$/|unique:available_countries',
            'cities' => 'required|array',
            'cities.*.name' => 'required|regex:/^[A-Za-z\s]+$/|max:50|unique:available_cities',
            'cities.*.name_ar' => ['required', new ArabicLettersWithSpaces, 'max:50', 'unique:available_cities'],
        ]);

        if ($validation->fails())
            throw new \App\Exceptions\ValidationError($validation->errors()->all());

        $geoCode = \GoogleMaps::load('geocoding');
        $geoCodeRequest = json_decode($geoCode->setParam([ 'address' => $request->get('name') ])->get());

        if ($geoCodeRequest->status === 'REQUEST_DENIED')
            throw new \App\Exceptions\GoogleMapsException('Google Maps API request denied');    

        else if ( $geoCodeRequest->status === 'ZERO_RESULTS' )
            throw new \App\Exceptions\ValidationError(['name' => 'Country name is not valid']);

        $geoCodeResult = $geoCodeRequest->results[0];
        $geoCodeCountry = end($geoCodeResult->address_components);
        $geoCodeCountryShort = $geoCodeCountry->short_name;

        try {
            $country = AvailableCountry::create([
                'name' => $request->get('name'),
                'name_ar' => $request->get('name_ar'),
                'iso_code' => $geoCodeCountryShort,
                'code' => $request->get('code'),
                'currency' => $request->get('currency'),
            ]);
        } catch (QueryException $exception) {
            throw new InternalError($exception);
        }

        $cities = $country->addCities($request->get('cities'));
        $country->cities = $cities;

        return response()->json([
            'success' => true,
            'message' => $country
        ], 200);
    }

    /**
     * add city to country
     *
     * @param Request $request
     * @throws ValidationError|InternalError
     * @return JsonResponse
     */
    public function addCity(Request $request)
    {
        $user = auth()->user()->userData;

        if (! $user->can('change', AvailableCity::class))
            throw new \App\Exceptions\ForbiddenException;

        $validation = Validator::make($request->all(), [
            'name' => 'required|regex:/^[A-Za-z\s]+$/|max:50|unique:available_cities',
            'name_ar' => ['required', new ArabicLettersWithSpaces, 'max:50', 'unique:available_cities'],
            'country_id' => 'required|exists:available_countries,id',
        ]);

        if ($validation->fails())
            throw new \App\Exceptions\ValidationError($validation->errors()->all());

        try {
            $city = AvailableCity::create([
                'name' => $request->get('name'),
                'name_ar' => $request->get('name_ar'),
                'country_id' => $request->get('country_id'),
            ]);
        } catch (QueryException $exception) {
            throw new InternalError($exception);
        }

        return response()->json([
            'success' => true,
            'message' => $city
        ]);
    }

    public function deleteCountry(Request $request)
    {
        $user = auth()->user()->userData;

        if (! $user->can('change', AvailableCountry::class))
            throw new \App\Exceptions\ForbiddenException;
        
        $validation = Validator::make($request->all(), [
            'id' => 'required|exists:available_countries,id',
        ]);

        if ($validation->fails())
            throw new \App\Exceptions\ValidationError($validation->errors()->all());

        try {
            AvailableCountry::destroy($request->get('id'));
        } catch (QueryException $exception) {
            throw new InternalError($exception);
        }

        return response()->json([
            'success' => true,
            'message' => 'Country deleted successfully'
        ]);
    }

    public function deleteCity(Request $request)
    {
        $user = auth()->user()->userData;

        if (! $user->can('change', AvailableCity::class))
            throw new \App\Exceptions\ForbiddenException;
        
        $validation = Validator::make($request->all(), [
            'id' => 'required|exists:available_cities,id',
        ]);

        if ($validation->fails())
            throw new \App\Exceptions\ValidationError($validation->errors()->all());

        try {
            AvailableCity::destroy($request->get('id'));
        } catch (QueryException $exception) {
            throw new InternalError($exception);
        }

        return response()->json([
            'success' => true,
            'message' => 'City deleted successfully'
        ]);
    }

    public function updateCountry(Request $request)
    {
        $user = auth()->user()->userData;

        if (! $user->can('change', AvailableCountry::class))
            throw new \App\Exceptions\ForbiddenException;
        
        $validation = Validator::make($request->all(), [
            'id' => 'required|exists:available_countries,id',
            'name' => 'regex:/^[A-Za-z\s]+$/|max:50|unique:available_countries',
            'name_ar' => [new ArabicLettersWithSpaces, 'max:50', 'unique:available_countries'],
            'code' => 'regex:/^\+[0-9]{1,3}$/|unique:available_countries',
            'currency' => 'regex:/^[A-Z]{3}$/|unique:available_countries',
        ]);

        if ($validation->fails())
            throw new \App\Exceptions\ValidationError($validation->errors()->all());

        if ($request->has('name'))
        {
            $geoCode = \GoogleMaps::load('geocoding');
            $geoCodeRequest = json_decode($geoCode->setParam([ 'address' => $request->get('name') ])->get());

            if ($geoCodeRequest->status === 'REQUEST_DENIED')
                throw new \App\Exceptions\GoogleMapsException('Google Maps API request denied');    

            else if ( $geoCodeRequest->status === 'ZERO_RESULTS' )
                throw new \App\Exceptions\ValidationError(['name' => 'Country name is not valid']);

            $geoCodeResult = $geoCodeRequest->results[0];
            $geoCodeCountry = end($geoCodeResult->address_components);
            $geoCodeCountryShort = $geoCodeCountry->short_name;
        }
            

        try {
            $country = AvailableCountry::find($request->get('id'));
            
            $country->update([
                'name' => $request->get('name') ?? $country->name,
                'name_ar' => $request->get('name_ar') ?? $country->name_ar,
                'code' => $request->get('code') ?? $country->code,
                'iso_code' => $geoCodeCountryShort ?? $country->iso_code,
                'currency' => $request->get('currency') ?? $country->currency,
            ]);
        
        } catch (QueryException $exception) {
            throw new InternalError($exception);
        }

        return response()->json([
            'success' => true,
            'message' => $country
        ]);
    }

    public function updateCity(Request $request)
    {
        $user = auth()->user()->userData;

        if (! $user->can('change', AvailableCity::class))
            throw new \App\Exceptions\ForbiddenException;
        
        $validation = Validator::make($request->all(), [
            'id' => 'required|exists:available_cities,id',
            'name' => 'regex:/^[A-Za-z\s]+$/|max:50|unique:available_cities',
            'name_ar' => [new ArabicLettersWithSpaces, 'max:50', 'unique:available_cities'],
        ]);

        if ($validation->fails())
            throw new \App\Exceptions\ValidationError($validation->errors()->all());

        try {
            $city = AvailableCity::find($request->get('id'));
            $city->update([
                'name' => $request->get('name') ?? $city->name,
                'name_ar' => $request->get('name_ar') ?? $city->name_ar,
            ]);
        } catch (QueryException $exception) {
            throw new InternalError($exception);
        }

        return response()->json([
            'success' => true,
            'message' => $city
        ]);
    }
    
}
