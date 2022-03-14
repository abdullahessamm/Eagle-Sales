<?php

namespace App\Http\Controllers\Accounts;

use App\Exceptions\DBException;
use App\Http\Controllers\Controller;
use App\Models\Phone;
use App\Models\Seller;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    private int $job;

    // supplier  --> 0
    // hierd seller  --> 1
    // freelancer seller --> 2
    // customer  --> 3
    // admins  --> 4
    private function userCreator(Request $request)
    {
        $rules = [
            'f_name'    => 'required|string|min:2|max:50',
            'l_name'    => 'required|string|min:2|max:50',
            'email'     => 'required|email|max:255|unique:users,email',
            'phone'     => 'required|regex:/^\+966[0-9]{8,11}$/|unique:phones,phone',
            'username'  => 'required|regex:/^\w+$/|min:5|max:80|unique:users,username',
            'password'  => 'required|min:8|max:255|regex:/^[\w\d\D\W]+$/',
            'country'   => 'required|regex:/^[A-Z]{2}$/',
            'city'      => 'required|string|min:3|max:10'
        ];
        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails())
            throw new \App\Exceptions\ValidationError($validation->errors()->all());

        $createdUser = new User;
        $createdUser->f_name = $request->get('f_name');
        $createdUser->l_name = $request->get('l_name');
        $createdUser->email = $request->get('email');
        $createdUser->username = $request->get('username');
        $createdUser->password = Hash::make($request->get('password'));
        $createdUser->job = $request->get('job');
        $createdUser->country = $request->get('country');
        $createdUser->city = $request->get('city');
        $createdUser->job = $this->job;

        if ($token = auth()->user())
            $createdUser->created_by = $$token->getUser()->id;

        $createdUser->generateSerialCode();
        try {
            $createdUser->save();
            $this->phoneCreator($request->get('phone'), $createdUser->id);
            return $createdUser;
        } catch (QueryException $e) {
            throw new DBException($e);
        }
    }

    public static function phoneCreator(string $phone, int $userID)
    {
        $record = new Phone;
        $record->phone = $phone;
        $record->user_id = $userID;
        $record->genVerifyCode();
        try {
            $record->save();
        } catch (QueryException $e) {
            throw new \App\Exceptions\DBException($e);
        }
    }

    public function registerSupplier(Request $request)
    {
        $rules = [
            'vat_no'           => 'required|regex:/^[A-Za-z]{2}\d{3,18}$/|unique:suppliers,vat_no',
            'shop_name'        => 'required|string|min:3|max:50',
            'whatsapp_no'      => 'required|regex:/^\+966[0-9]{8,11}$/|unique:suppliers,whatsapp_no',
            'fb_page'          => 'regex:/^(https?:\/\/)?(www\.)?(m\.)?(fb)?(facebook)?(\.com)(\/[\w\D]+\/?)+$/|unique:suppliers,fb_page',
            'website_domain'   => 'regex:/^(https?:\/\/)?(([\da-z])+\.)?[\d\w\-]+\.[a-z]{2,3}$/|unique:suppliers,website_domain',
            'location_coords'  => 'regex:/^[-+]?([1-8]?\d(\.\d+)?|90(\.0+)?),\s*[-+]?(180(\.0+)?|((1[0-7]\d)|([1-9]?\d))(\.\d+)?)$/',
            'l1_address'       => 'required|regex:/^[a-zA-Z\d]+[\w\d\ \-]+$/|min:4|max:255',
            'l1_address_ar'    => 'required|string|min:4|max:255',
            'l2_address'       => 'regex:/^[a-zA-Z\d]+[\w\d\ \-]+$/|min:4|max:255',
            'l2_address_ar'    => 'regex:/^[\u0600-\u06ff\d]+[\u0600-\u06ff\d\ \-]+$/|min:4|max:255',
        ];

        $validation = Validator::make($request->all(), $rules);

        if ($validation->fails())
            throw new \App\Exceptions\ValidationError($validation->errors()->all());
        
        $this->job = 0;
        $createdUser = $this->userCreator($request);

        if ($createdUser instanceof JsonResponse)
            return $createdUser;

        $record = $request->only([
            'vat_no',
            'shop_name',
            'phone',
            'whatsapp_no',
            'fb_page',
            'website_domain',
            'location_coords',
            'l1_address',
            'l1_address_ar',
            'l2_address',
            'l2_address_ar'
        ]);

        $record['user_id'] = $createdUser->id;

        try {
            Supplier::create($record);
        } catch (QueryException $e) {
            $createdUser->delete();
            throw new \App\Exceptions\DBException($e);
        }
        
        return response()->json(['success' => true], 200);
    }

    private function registerSeller(bool $isFreelancer, Request $request)
    {
        $rules = [
            'age'                 => 'required|regex:/^[0-9]{2,3}$/',
            'education'           => 'required|string|min:4|max:255',
            'l1_address'          => 'required|regex:/^[a-zA-Z\d]+[\w\d\ \-]+$/|min:4|max:255',
            'l1_address_ar'       => 'required|string|min:4|max:255',
            'l2_address'          => 'regex:/^[a-zA-Z\d]+[\w\d\ \-]+$/|min:4|max:255',
            'l2_address_ar'       => 'regex:/^[\u0600-\u06ff\d]+[\u0600-\u06ff\d\ \-]+$/|min:4|max:255',
            'location_coords'     => 'regex:/^[-+]?([1-8]?\d(\.\d+)?|90(\.0+)?),\s*[-+]?(180(\.0+)?|((1[0-7]\d)|([1-9]?\d))(\.\d+)?)$/',
            'bank_account_number' => 'required_with:bank_name|regex:/^[0-9]{9,50}$/',
            'bank_name'           => 'required_with:bank_account_number,!==,null|string|min:3|max:50'
        ];

        $validation = Validator::make($request->all(), $rules);

        if ($validation->fails())
            throw new \App\Exceptions\ValidationError($validation->errors()->all());

        

        $createdUser = $this->userCreator($request);

        if ($createdUser instanceof JsonResponse)
            return $createdUser;
        
        $seller = new Seller;
        $seller->user_id = $createdUser->id;
        $seller->age = $request->get('age');
        $seller->education = $request->get('education');
        $seller->l1_address = $request->get('l1_address');
        $seller->l1_address_ar = $request->get('l1_address_ar');
        if ($request->has('l2_address'))
            $seller->l2_address = $request->get('l2_address');
        
        if ($request->has('l2_address_ar'))
            $seller->l2_address_ar = $request->get('l2_address_ar');

        if ($request->has('location_coords'))
            $seller->location_coords = $request->get('location_coords');

        if ($request->has('bank_account_number'))
            $seller->bank_account_number = $request->get('bank_account_number');

        if ($request->has('bank_name'))
            $seller->bank_name = $request->get('bank_name');

        try {
            $seller->save();
            return $seller;
        } catch (QueryException $e) {
            throw new \App\Exceptions\DBException($e);
        }
    }

    public function registerHiredSeller(Request $request)
    {
        $rules = [
            "salary" => "required|numeric|between:0,99999.99",
            "tax"    => "required|numeric|between:0,99.99",
        ];

        $this->job = 1;

        $requestData = $request->all();
        $validation = Validator::make($requestData, $rules);

        if ($validation->fails())
            throw new \App\Exceptions\ValidationError($validation->errors()->all());
        
        $createdSeller = $this->registerSeller(false, $request);

        if ($createdSeller instanceof JsonResponse)
            return $createdSeller;
        
        return response()->json(['success' => true]);
    }

    public function registerFreelancerSeller(Request $request)
    {
        $this->job = 2;
        $createdSeller = $this->registerSeller(true, $request);

        if ($createdSeller instanceof JsonResponse)
            return $createdSeller;
        
        return response()->json(['success' => true]);
    }

    # TODO complete customers registration
    public function registerCustomer(Request $request)
    {

        $this->job = 3;

        $rules = [
            'shop_name'           => "required|string|min:3|max:50",
            'l1_address'          => 'required|regex:/^[a-zA-Z\d]+[\w\d\ \-]+$/|min:4|max:255',
            'l1_address_ar'       => 'required|string|min:4|max:255',
            'l2_address'          => 'regex:/^[a-zA-Z\d]+[\w\d\ \-]+$/|min:4|max:255',
            'l2_address_ar'       => 'regex:/^[\u0600-\u06ff\d]+[\u0600-\u06ff\d\ \-]+$/|min:4|max:255',
            'location_coords'     => 'regex:/^[-+]?([1-8]?\d(\.\d+)?|90(\.0+)?),\s*[-+]?(180(\.0+)?|((1[0-7]\d)|([1-9]?\d))(\.\d+)?)$/',
            'vat_no'              => 'required|regex:/^[A-Za-z]{2}\d{3,18}$/|unique:suppliers,vat_no',
            'category_id'         => 'required|integer|between:1,255'
        ];

        $validation = Validator::make($request->all(), $rules);

        if ($validation->fails())
            throw new \App\Exceptions\ValidationError($validation->errors()->all());

        $createdUser = $this->userCreator($request);

        if ($createdUser instanceof JsonResponse)
            return $createdUser;
        

        try {
            
        } catch (QueryException $e) {

        }
    }

    public function registerAdmin(Request $request)
    {
        try {
            
        } catch (QueryException $e) {

        }
    }
}
