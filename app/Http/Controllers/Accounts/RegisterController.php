<?php

namespace App\Http\Controllers\Accounts;

use App\Exceptions\DBException;
use App\Exceptions\GoogleMapsException;
use App\Http\Controllers\Controller;
use App\Models\AppConfig;
use App\Models\AvailableCity;
use App\Models\AvailableCountry;
use App\Models\BackOfficeUser;
use App\Models\Customer;
use App\Models\Phone;
use App\Models\Seller;
use App\Models\SellerRegisterInvitation;
use App\Models\Supplier;
use App\Models\User;
use App\Models\UsersPlace;
use App\Services\Google\GoogleGeocode;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    private int $job;

    private function userCreator(Request $request, bool $createdByAdmin = false)
    {
        $rules = [
            'f_name'    => 'required|regex:/^[a-zA-Z]+$/|min:2|max:20',
            'l_name'    => 'required|regex:/^[a-zA-Z]+$/|min:2|max:20',
            'email'     => 'required|email|max:50|unique:users,email',
            'phone'     => 'required|regex:/^\+[0-9]{11,14}$/|unique:phones,phone',
            'password'  => 'required|min:8|max:80|regex:/^[\w\d\D\W]+$/',
            'gender'    => 'required|in:male,female',
            'lang'      => 'in:en,ar',
            'coords'    => ['required', 'regex:/^[-+]?([1-8]?\d(\.\d+)?|90(\.0+)?),\s*[-+]?(180(\.0+)?|((1[0-7]\d)|([1-9]?\d))(\.\d+)?)$/'],
        ];

        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails())
            throw new \App\Exceptions\ValidationError($validation->errors()->all());

        $createdUser = new User;
        $createdUser->f_name = $request->get('f_name');
        $createdUser->l_name = $request->get('l_name');
        $createdUser->email = $request->get('email');
        $createdUser->password = Hash::make($request->get('password'));
        $createdUser->job = $request->get('job');
        $createdUser->job = $this->job;
        $createdUser->gender = $request->get('gender');
        $createdUser->lang = $request->get('lang') ?? 'en';
        $createdUser->generateUniqueUsername();

        if ($createdByAdmin) {
            $createdUser->is_approved = true;
            $createdUser->approved_by = auth()->user()->userData->id;
            $createdUser->approved_at = now();
        }

        if ($token = auth()->user())
            $createdUser->created_by = $token->userData->id;

        $createdUser->generateSerialCode();
        try {
            $createdUser->save();
            $this->phoneCreator($request->get('phone'), $createdUser->id, true);
            $this->placeCreator($request->get('coords'), $createdUser);
            return $createdUser;
        } catch (QueryException $e) {
            throw new DBException($e);
        }
    }

    /**
     * check unique fields in users table
     *
     * @param Request $request
     * @return void
     */
    public function checkUniqueFieldInUsers(Request $request)
    {
        $rules = [
            'email' => 'required_without:phone|email|max:50',
            'phone' => 'required_without:email|regex:/^\+[0-9]{11,14}$/',
        ];

        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails())
            throw new \App\Exceptions\ValidationError($validation->errors()->all());

        $responseMsg = [];
        
        if ($request->has('email')) {
            $emailCheck = User::where('email', $request->get('email'))->exists();

            if ($emailCheck)
                $responseMsg['email'] = 'email already exists';
        }
        
        if ($request->has('phone')) {
            $phoneCheck = Phone::where('phone', $request->get('phone'))->exists();

            if ($phoneCheck)
                $responseMsg['phone'] = 'phone already exists';
        }


        return count($responseMsg) > 0 ? response()->json(['status' => 'error', 'message' => $responseMsg], 422) : response()->json(['status' => 'success']);
    }

    private function placeCreator(string $coords, User $user)
    {
        try {
            $geocode_en = new GoogleGeocode([
                'latlng' => $coords,
                'language' => 'en',
            ]);

            $geocode_ar = new GoogleGeocode([
                'latlng' => $coords,
                'language' => 'ar',
            ]);
        } catch (Exception $e) {
            throw new GoogleMapsException($e->getMessage());
        }

        $availableCountries = AvailableCountry::get('iso_code')->pluck('iso_code')->toArray();
        $availableCities = AvailableCity::get('name')->pluck('name')->toArray();
        $country = $geocode_en->getCountry()['short_name'];
        $governorate = $geocode_en->getGovernorate()['short_name'];

        if (! in_array($country, $availableCountries)) {
            $user->delete();
            throw new \App\Exceptions\ValidationError([
                'coords' => 'country not available',
            ]);
        }

        if (! in_array($governorate, $availableCities)) {
            $user->delete();
            throw new \App\Exceptions\ValidationError([
                'coords' => 'city not available',
            ]);
        }

        // init place object
        $place = new UsersPlace;
        $place->coords     = $coords;
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
        $place->setPrimary();
        
        // pass object to addPlace user's method
        $user->addPlace($place);
    }

    /**
     * create new phone
     * @param string $phone
     * @param integer $userID
     * @return void
     */
    public static function phoneCreator(string $phone, int $userID, $isPrimary=false)
    {
        $record = new Phone;
        $record->phone = $phone;
        $record->user_id = $userID;
        $record->is_primary = $isPrimary;
        $record->genVerifyCode();
        try {
            $record->save();
        } catch (QueryException $e) {
            throw new \App\Exceptions\DBException($e);
        }
    }

    /**
     *
     * @param Request $request
     * @return void
     */
    public function registerSupplier(Request $request)
    {
        $createdByAdmin = false;

        if ($authUser = auth()->user()) {
            $user = $authUser->userData;
            if (! $user->job === User::ADMIN_JOB_NUMBER)
                throw new \App\Exceptions\ForbiddenException;
            
            if (! $user->can('create', Supplier::class))
                throw new \App\Exceptions\ForbiddenException;

            $createdByAdmin = true;
        }

        $rules = [
            'shop_name'        => 'required|string|min:3|max:50',
            'vat_no'           => 'regex:/^[A-Z]{2}\d{3,18}$/|unique:suppliers,vat_no',
            'whatsapp_no'      => 'regex:/^\+[0-9]{11,14}$/|unique:suppliers,whatsapp_no',
            'fb_page'          => 'regex:/^(https?:\/\/)?(www\.)?(m\.)?(fb)?(facebook)?(\.com)(\/[\w\D]+\/?)+$/|unique:suppliers,fb_page|max:100',
            'website_domain'   => 'regex:/^(https?:\/\/)?(([\da-z])+\.)?[\d\w\-]+\.[a-z]{2,3}$/|unique:suppliers,website_domain',
        ];

        $validation = Validator::make($request->all(), $rules);

        if ($validation->fails())
            throw new \App\Exceptions\ValidationError($validation->errors()->all());
        
        $this->job = User::SUPPLIER_JOB_NUMBER;
        $createdUser = $this->userCreator($request, $createdByAdmin);

        $record = $request->only([
            'vat_no',
            'shop_name',
            'phone',
            'whatsapp_no',
            'fb_page',
            'website_domain',
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

    // check unique fields in suppliers
    public function checkUniqueFieldInSuppliers(Request $request)
    {
        $rules = [
            'phone' => 'required|string',
            'whatsapp_no' => 'string',
            'fb_page' => 'string',
            'website_domain' => 'string',
        ];

        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails())
            throw new \App\Exceptions\ValidationError($validation->errors()->all());

        $responseMsg = [];
        $phoneCheck = Phone::where('phone', $request->get('phone'))->exists();
        $whatsappCheck = $request->has('whatsapp_no') ? Supplier::where('whatsapp_no', $request->get('whatsapp_no'))->exists() : null;
        $fbPageCheck = $request->has('fb_page') ? Supplier::where('fb_page', $request->get('fb_page'))->exists() : null;
        $websiteDomainCheck = $request->has('website_domain') ? Supplier::where('website_domain', $request->get('website_domain'))->exists() : null;

        if ($phoneCheck)
            $responseMsg['phone'] = 'Phone already exists';
        if ($whatsappCheck)
            $responseMsg['whatsapp_no'] = 'Whatsapp No. already exists';
        if ($fbPageCheck)
            $responseMsg['fb_page'] = 'Facebook Page already exists';
        if ($websiteDomainCheck)
            $responseMsg['website_domain'] = 'Website Domain already exists';

        return count($responseMsg) > 0 ? response()->json(['success' => false, 'message' => $responseMsg], 422) : response()->json(['success' => true], 200);
    }

    /**
     *
     * @param boolean $isFreelancer
     * @param Request $request
     * @return void
     */
    private function registerSeller(bool $isFreelancer, Request $request)
    {
        $rules = [
            'age'                 => 'required|integer|between:16,100',
            'education'           => 'required|string|min:4|max:255',
            'bank_account_number' => 'required_with:bank_name|regex:/^[A-Za-z0-9]{9,50}$/|unique:sellers,bank_account_number',
            'bank_name'           => 'required_with:bank_account_number,!==,null|string|min:3|max:50'
        ];

        $validation = Validator::make($request->all(), $rules);

        if ($validation->fails())
            throw new \App\Exceptions\ValidationError($validation->errors()->all());

        $createdUser = $this->userCreator($request);
        
        $seller = new Seller;
        $seller->user_id = $createdUser->id;
        $seller->is_freelancer = $isFreelancer;
        $seller->age = $request->get('age');
        $seller->education = $request->get('education');

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

    // check unique fields in sellers
    public function checkUniqueFieldInSellers(Request $request)
    {
        $rules = [
            'key' => 'required|string|in:location_coords,l1_address,l1_address_ar,l2_address,l2_address_ar,bank_account_number,bank_name',
            'value' => 'required|string',
        ];

        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails())
            throw new \App\Exceptions\ValidationError($validation->errors()->all());

        $seller = Seller::where($request->get('key'), $request->get('value'))->first();
        if ($seller)
            return response()->json(['status' => 'error', 'message' => $request->get('key') . ' already exists'], 422);

        return response()->json(['status' => 'success']);
    }

    /**
     * register new hierd sellers
     * @param Request $request
     * @return void
     */
    public function registerHiredSeller(Request $request)
    {
        $authUser = auth()->user()->userData;

        if (! $authUser->can('create', Seller::class))
            throw new \App\Exceptions\ForbiddenException;

        $rules = [
            "salary" => "required|numeric|between:0,99999.99",
            "tax"    => "required|numeric|between:0,99.99",
        ];

        $this->job = User::HIERD_SELLER_JOB_NUMBER;

        $requestData = $request->all();
        $validation = Validator::make($requestData, $rules);

        if ($validation->fails())
            throw new \App\Exceptions\ValidationError($validation->errors()->all());
        
        $this->registerSeller(false, $request);
        
        return response()->json(['success' => true]);
    }

    /**
     *
     * @param Request $request
     * @return void
     */
    public function registerFreelancerSeller(Request $request)
    {
        $this->job = User::FREELANCER_SELLER_JOB_NUMBER;
        $createdSeller = $this->registerSeller(true, $request);

        if ($createdSeller instanceof JsonResponse)
            return $createdSeller;
        
        return response()->json(['success' => true]);
    }

    /**
     * register new customer
     *
     * @param Request $request
     * @return void
     */
    public function registerCustomer(Request $request)
    {
        $createdByAdmin = false;

        // check if user is admin
        if ($user = auth()->user()) {
            $authUser = $user->userData;
            if ($authUser->job === User::ADMIN_JOB_NUMBER) {
                if (! $authUser->can('create', Customer::class))
                    throw new \App\Exceptions\ForbiddenException;
                $createdByAdmin = true;
            }
        }

        $this->job = User::CUSTOMER_JOB_NUMBER;

        $rules = [
            'shop_name'           => "required|string|min:3|max:50",
            'vat_no'              => 'required|regex:/^[A-Za-z]{2}\d{3,18}$/|unique:customers,vat_no|unique:suppliers,vat_no',
            'category_id'         => 'required|integer|between:1,255',
            'shop_space'          => 'numeric|between:1,9999.99',
        ];

        $validation = Validator::make($request->all(), $rules);

        if ($validation->fails())
            throw new \App\Exceptions\ValidationError($validation->errors()->all());

        $createdUser = $this->userCreator($request, $createdByAdmin);
        
        $customer = new Customer;
        $customer->shop_name       = $request->get('shop_name');
        $customer->vat_no          = $request->get('vat_no');
        $customer->category_id     = $request->get('category_id');
        $customer->shop_space      = $request->get('shop_space');
        $customer->user_id         = $createdUser->id;

        try {
            $customer->save();
        } catch (QueryException $e) {
            throw new \App\Exceptions\DBException($e);
        }

        return response()->json(['success' => true]);
    }

    public function checkUniqueFieldInCustomers(Request $request)
    {
        $rules = [
            'vat_no' => 'required|string',
            'phone'  => 'required|string',
        ];

        $validation = Validator::make($request->all(), $rules);

        if ($validation->fails())
            throw new \App\Exceptions\ValidationError($validation->errors()->all());

        $responseMsg = [];

        $vatExists = Customer::where('vat_no', $request->get('vat_no'))->exists() || Supplier::where('vat_no', $request->get('vat_no'))->exists();
        $phoneExists = Phone::where('phone', $request->get('phone'))->exists();

        if ($vatExists)
            $responseMsg['vat_no'] = 'VAT No. already exists';
        if ($phoneExists)
            $responseMsg['phone'] = 'Phone already exists';

        return count($responseMsg) > 0 ? response()->json(['success' => false, 'message' => $responseMsg], 422) : response()->json(['success' => true], 200);
    }

    public function registerAdmin(Request $request)
    {
        $authUser = auth()->user()->userData;
        if (! $authUser->can('create', BackOfficeUser::class))
            throw new \App\Exceptions\ForbiddenException;

        $validation = Validator::make($request->all(), [
            'job_title' => 'required|regex:/^[A-Za-z ]+$/|min:3|max:20'
        ]);

        if ($validation->fails())
            throw new \App\Exceptions\ValidationError($validation->errors()->all());

        $this->job = User::ADMIN_JOB_NUMBER;

        $createdUser = $this->userCreator($request, true);

        $backofficeUser = new BackOfficeUser;
        $backofficeUser->user_id = $createdUser->id;
        $backofficeUser->job_title = $request->get('job_title');

        try {
            $backofficeUser->save();
        } catch (QueryException $e) {
            throw new \App\Exceptions\DBException($e);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Record the rewords of registration invitation
     *
     * @param Request $request
     * @return void
     */
    public function registerSellerRewards(Request $request)
    {
        $rewardIsEnabled = AppConfig::where('key', 'sellers_invitation_reward_enabled')->first();
        if (! $rewardIsEnabled)
            return response()->json(['success' => false], 404);
        if (! (bool) $rewardIsEnabled->value)
            return response()->json(['success' => false], 404);
        
        $authUser = auth()->user()->userData;

        if ($authUser->job !== User::FREELANCER_SELLER_JOB_NUMBER)
            throw new \App\Exceptions\ForbiddenException;

        $validation = Validator::make($request->all(), [
            'user_serial' => 'required|regex:/^FS[0-9]+_.+$/|min:25|max:25'
        ]);

        if ($validation->fails())
            throw new \App\Exceptions\ValidationError($validation->errors()->all());

        $invitatorUser = User::where('serial_code', $request->get('user_serial'))->first();

        if (! $invitatorUser)
            return response()->json(['success'=>false], 404);

        $createdSellerId = $authUser->userInfo->id;
        $invitationOwnerId = $invitatorUser->userInfo->id;
        if (SellerRegisterInvitation::where('created_seller_id', $createdSellerId)->first())
            return response()->json(['success' => false], 400);

        $regInvitation = new SellerRegisterInvitation;
        $regInvitation->invitation_owner_id = $invitationOwnerId;
        $regInvitation->created_seller_id = $createdSellerId;
        $regInvitation->save();

        return response()->json(["success" => true]);
    }

    public function registerOnlineClient(Request $request)
    {
        $this->job = User::ONLINE_CLIENT_JOB_NUMBER;
        $this->userCreator($request);

        return response()->json([
            'success' => true
        ]);
    }
}
