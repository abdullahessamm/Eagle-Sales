<?php

namespace App\Http\Controllers\Accounts\Backoffice;

use App\Http\Controllers\Accounts\UserUpdater;
use App\Http\Controllers\Controller;
use App\Models\BackOfficeUser;
use App\Models\Permission;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Updaters extends Controller
{
    /**
     * Update job title
     *
     * @param Request $request
     * @return void
     */
    public function update(Request $request)
    {

        $validation = Validator::make($request->all(), [
            'id' => 'required|regex:/^[1-9][0-9]*$/',
            'job_title' => 'required|regex:/^[a-zA-Z]{3,20}$/'
        ]);

        if ($validation->fails())
            throw new \App\Exceptions\ValidationError($validation->errors()->all());

        $authUser = auth()->user()->userData;

        if (
            ! $authUser->can('update', BackOfficeUser::class) ||
            (int) $request->get('id') === (int) $authUser->userInfo->id
        ) throw new \App\Exceptions\ForbiddenException;

        $backofficeUser = BackOfficeUser::where('user_id', $request->get('id'))->first();

        if (! $backofficeUser)
            return response()->json(['success' =>false], 404);

        $backofficeUser->job_title = $request->get('job_title');

        try {
            $backofficeUser->save();
        } catch (QueryException $e) {
            throw new \App\Exceptions\DBException($e);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Update permissions
     *
     * @param Request $request
     * @return void
     */
    public function updatePermissions(Request $request)
    {
        $authUser = auth()->user()->userData->userInfo;
        
        if ((int) $authUser->id !== 1)
            throw new \App\Exceptions\ForbiddenException;

        $rules = [
            'backoffice_user_id' => 'required|integer|between:2,255',
            'suppliers_access_level' => 'regex:/^[0-1]{4}$/',
            'customers_access_level' => 'regex:/^[0-1]{4}$/',
            'sellers_access_level' => 'regex:/^[0-1]{4}$/',
            'categorys_access_level' => 'regex:/^[0-1]{4}$/',
            'items_access_level' => 'regex:/^[0-1]{4}$/',
            'backoffice_emps_access_level' => 'regex:/^[0-1]{4}$/',
            'orders_access_level' => 'regex:/^[0-1]{4}$/',
            'commissions_access_level' => 'regex:/^[0-1]{4}$/',
            'journey_plan_access_level' => 'regex:/^[0-1]{4}$/',
            'pricelists_access_level' => 'regex:/^[0-1]{4}$/',
            'app_config_access' => 'regex:/^[0-1]$/'
        ];

        $validation = Validator::make($request->all(), $rules);

        if ($validation->fails())
            throw new \App\Exceptions\ValidationError($validation->errors()->all());

        if (count($request->all()) <= 2)
            return response()->json(['success' =>false, 'msg' => 'No changes to be updated'], 400);

        $userPermissions = Permission::where('backoffice_user_id', $request->get('backoffice_user_id'))->first();

        if (! $userPermissions)
            return response()->json(['success' =>false], 404);

        unset($rules['backoffice_user_id']);

        foreach ($rules as $param => $rule)
            $userPermissions->$param = $request->get($param) ?? $userPermissions->$param;

        try {
            $userPermissions->save();
        } catch (QueryException $e) {
            throw new \App\Exceptions\DBException($e);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Update account info
     *
     * @param Request $request
     * @return void
     */
    public function accountInfo(Request $request)
    {
        $authUser = auth()->user()->userData->userInfo;
        if ((int) $authUser->id !== 1)
            throw new \App\Exceptions\ForbiddenException;
        
        $rules = [
            'id' => 'required|integer|between:2,255',
        ];
        $validation = Validator::make($request->only(['id']), $rules);

        if ($validation->fails())
            throw new \App\Exceptions\ValidationError($validation->errors()->all());
        
        $backofficeUser = BackOfficeUser::where('user_id', $request->get('id'))->first();

        if (! $backofficeUser)
            return response()->json(['success' => false], 404);

        $userInfo = $backofficeUser->getUser();

        return UserUpdater::updateInfo($request, $userInfo);
    }
}
