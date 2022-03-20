<?php

namespace App\Http\Controllers\Accounts;

use App\Exceptions\DBException;
use App\Http\Controllers\Controller;
use App\Models\AppConfig;
use App\Models\BackOfficeUser;
use App\Models\Customer;
use App\Models\Phone;
use App\Models\Seller;
use App\Models\SellerRegisterInvitation;
use App\Models\Supplier;
use App\Models\User;
use App\Rules\ArabicLetters;
use App\Rules\ArabicLettersWithSpaces;
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
    private function userCreator(Request $request, bool $createdByAdmin = false)
    {
        $rules = [
            'f_name'    => 'required|regex:/^[a-zA-Z]+$/|min:2|max:50',
            'f_name_ar' => ['required', 'min:2', 'max:50', new ArabicLetters],
            'l_name'    => 'required|regex:/^[a-zA-Z]+$/|min:2|max:50',
            'l_name_ar' => ['required', 'min:2', 'max:50', new ArabicLetters],
            'email'     => 'required|email|max:255|unique:users,email',
            'phone'     => 'required|regex:/^\+[0-9]{11,14}$/|unique:phones,phone',
            'username'  => 'required|regex:/^\w[\w\.]+$/|min:5|max:80|unique:users,username',
            'password'  => 'required|min:8|max:255|regex:/^[\w\d\D\W]+$/',
            'country'   => 'required|regex:/^[A-Z]{2}$/',
            'city'      => 'required|string|min:3|max:10'
        ];
        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails())
            throw new \App\Exceptions\ValidationError($validation->errors()->all());

        $createdUser = new User;
        $createdUser->f_name = $request->get('f_name');
        $createdUser->f_name_ar = $request->get('f_name_ar');
        $createdUser->l_name = $request->get('l_name');
        $createdUser->l_name_ar = $request->get('l_name_ar');
        $createdUser->email = $request->get('email');
        $createdUser->username = $request->get('username');
        $createdUser->password = Hash::make($request->get('password'));
        $createdUser->job = $request->get('job');
        $createdUser->country = $request->get('country');
        $createdUser->city = $request->get('city');
        $createdUser->job = $this->job;

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
            return $createdUser;
        } catch (QueryException $e) {
            throw new DBException($e);
        }
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
            'vat_no'           => 'required|regex:/^[A-Za-z]{2}\d{3,18}$/|unique:suppliers,vat_no',
            'shop_name'        => 'required|string|min:3|max:50',
            'whatsapp_no'      => 'required|regex:/^\+966[0-9]{8,11}$/|unique:suppliers,whatsapp_no',
            'fb_page'          => 'regex:/^(https?:\/\/)?(www\.)?(m\.)?(fb)?(facebook)?(\.com)(\/[\w\D]+\/?)+$/|unique:suppliers,fb_page',
            'website_domain'   => 'regex:/^(https?:\/\/)?(([\da-z])+\.)?[\d\w\-]+\.[a-z]{2,3}$/|unique:suppliers,website_domain',
            'location_coords'  => 'regex:/^[-+]?([1-8]?\d(\.\d+)?|90(\.0+)?),\s*[-+]?(180(\.0+)?|((1[0-7]\d)|([1-9]?\d))(\.\d+)?)$/',
            'l1_address'       => 'required|regex:/^[a-zA-Z\d]+[\w\d\ \-]+$/|min:4|max:255',
            'l1_address_ar'    => ['required', new ArabicLettersWithSpaces, 'min:4', 'max:255'],
            'l2_address'       => 'regex:/^[a-zA-Z\d]+[\w\d\ \-]+$/|min:4|max:255',
            'l2_address_ar'    => [new ArabicLettersWithSpaces, 'min:4', 'max:255'],
        ];

        $validation = Validator::make($request->all(), $rules);

        if ($validation->fails())
            throw new \App\Exceptions\ValidationError($validation->errors()->all());
        
        $this->job = User::SUPPLIER_JOB_NUMBER;
        $createdUser = $this->userCreator($request, $createdByAdmin);

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

    /**
     *
     * @param boolean $isFreelancer
     * @param Request $request
     * @return void
     */
    private function registerSeller(bool $isFreelancer, Request $request)
    {
        $rules = [
            'age'                 => 'required|regex:/^[0-9]{2,3}$/',
            'education'           => 'required|string|min:4|max:255',
            'l1_address'          => 'required|regex:/^[a-zA-Z\d]+[\w\d\ \-]+$/|min:4|max:255',
            'l1_address_ar'       => ['required', new ArabicLettersWithSpaces, 'min:4', 'max:255'],
            'l2_address'          => 'regex:/^[a-zA-Z\d]+[\w\d\ \-]+$/|min:4|max:255',
            'l2_address_ar'       => [new ArabicLettersWithSpaces, 'min:4', 'max:255'],
            'location_coords'     => 'regex:/^[-+]?([1-8]?\d(\.\d+)?|90(\.0+)?),\s*[-+]?(180(\.0+)?|((1[0-7]\d)|([1-9]?\d))(\.\d+)?)$/',
            'bank_account_number' => 'required_with:bank_name|regex:/^[A-Za-z0-9]{9,50}$/',
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
            'l1_address'          => 'required|regex:/^[a-zA-Z\d]+[\w\d\ \-]+$/|min:4|max:255',
            'l1_address_ar'       => ['required', new ArabicLettersWithSpaces, 'min:4', 'max:255'],
            'l2_address'          => 'regex:/^[a-zA-Z\d]+[\w\d\ \-]+$/|min:4|max:255',
            'l2_address_ar'       => [new ArabicLettersWithSpaces, 'min:4', 'max:255'],
            'location_coords'     => 'regex:/^[-+]?([1-8]?\d(\.\d+)?|90(\.0+)?),\s*[-+]?(180(\.0+)?|((1[0-7]\d)|([1-9]?\d))(\.\d+)?)$/',
            'vat_no'              => 'required|regex:/^[A-Za-z]{2}\d{3,18}$/|unique:suppliers,vat_no',
            'category_id'         => 'required|integer|between:1,255',
            'shop_space'          => 'numeric|between:1,9999.99',
        ];

        $validation = Validator::make($request->all(), $rules);

        if ($validation->fails())
            throw new \App\Exceptions\ValidationError($validation->errors()->all());

        $createdUser = $this->userCreator($request, $createdByAdmin);
        
        $customer = new Customer;
        $customer->shop_name       = $request->get('shop_name');
        $customer->l1_address      = $request->get('l1_address');
        $customer->l1_address_ar   = $request->get('l1_address_ar');
        $customer->l2_address      = $request->get('l2_address');
        $customer->l2_address_ar   = $request->get('l2_address_ar');
        $customer->location_coords = $request->get('location_coords');
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
}