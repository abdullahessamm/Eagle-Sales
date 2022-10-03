<?php

namespace App\Http\Controllers\Accounts;

use App\Events\UserHasBeenApproved;
use App\Http\Controllers\Controller;
use App\Models\BackOfficeUser;
use App\Models\Customer;
use App\Models\Seller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Rules\ArabicLetters;
use App\Models\User;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;

class UserUpdater extends Controller
{
    public static function updateInfo(Request $request, User $user)
    {
        $rules = [
            'f_name'      => 'regex:/^[a-zA-Z\x{0621}-\x{064A}]{2,20}$/u',
            'l_name'      => 'regex:/^[a-zA-Z\x{0621}-\x{064A}]{2,20}$/u',
            'email'       => 'email|max:50|unique:users,email',
            'username'   => 'regex:/^[a-zA-Z]+$/|min:4|max:50|unique:users,username',
            'lang'        => 'in:en,ar,in',
        ];
        $validation = Validator::make($request->all(), $rules);

        if ($validation->fails())
            throw new \App\Exceptions\ValidationError($validation->errors()->all());

        foreach ($rules as $param => $rule)
        $user->$param = request()->get($param) ?? $user->$param;

        try {
            $user->save();
        } catch (QueryException $e) {
            throw new \App\Exceptions\DBException($e);
        }

        return response()->json(['success' => true]);
    }

    private function checkUserPermissions(User $authUser, User $user, string $action): void
    {
        switch ($user->job) {
            case User::ADMIN_JOB_NUMBER:
                $authUser->can($action, BackOfficeUser::class) ? null : throw new \App\Exceptions\ForbiddenException;
                break;
            
            case User::SUPPLIER_JOB_NUMBER:
                $authUser->can($action, Supplier::class) ? null : throw new \App\Exceptions\ForbiddenException;
                break;

            case User::FREELANCER_SELLER_JOB_NUMBER:
                $authUser->can($action, Seller::class) ? null : throw new \App\Exceptions\ForbiddenException;
                break;

            case User::HIERD_SELLER_JOB_NUMBER:
                $authUser->can($action, Seller::class) ? null : throw new \App\Exceptions\ForbiddenException;
                break;

            case User::CUSTOMER_JOB_NUMBER:
                $authUser->can($action, Customer::class) ? null : throw new \App\Exceptions\ForbiddenException;
                break;

            default:
                throw new Exception;
                break;
        }
    }

    public function ban(int $id)
    {
        $authUser = auth()->user()->userData;
        if ($authUser->job !== User::ADMIN_JOB_NUMBER)
            throw new \App\Exceptions\ForbiddenException;

        $user = User::where('id', $id)->first();
        if (! $user)
            return response()->json(['success' => false, 'msg' => 'user not found'], 404);

        $this->checkUserPermissions($authUser, $user, 'ban');

        if (! $user->banUser())
            return response()->json(['success' => false, 'msg' => 'Some thing went wrong'], 500);
        return response()->json(['success' => true, 'msg' => 'user has been banned']);
    }

    public function reactivate(int $id)
    {
        $authUser = auth()->user()->userData;
        if ($authUser->job !== User::ADMIN_JOB_NUMBER)
            throw new \App\Exceptions\ForbiddenException;

        $user = User::where('id', $id)->first();
        if (! $user)
            return response()->json(['success' => false, 'msg' => 'user not found'], 404);

        $this->checkUserPermissions($authUser, $user, 'ban');

        if (! $user->reactivateUser())
            return response()->json(['success' => false, 'msg' => 'Some thing went wrong'], 500);
        return response()->json(['success' => true, 'user has been reactivated']);
    }

    public function approve(int $id)
    {
        $authUser = auth()->user()->userData;
        if ($authUser->job !== User::ADMIN_JOB_NUMBER)
            throw new \App\Exceptions\ForbiddenException;

        $user = User::where('id', $id)->first();
        if (! $user)
            return response()->json(['success' => false, 'msg' => 'user not found'], 404);

        $this->checkUserPermissions($authUser, $user, 'approve');
        $user->approve();
        event(new UserHasBeenApproved($user));
        return response()->json(['success' => true, 'msg', 'user has been approved']);
    }

    public function decline(int $id)
    {
        $authUser = auth()->user()->userData;
        if ($authUser->job !== User::ADMIN_JOB_NUMBER)
            throw new \App\Exceptions\ForbiddenException;

        $user = User::where('id', $id)->first();
        if (! $user)
            return response()->json(['success' => false, 'msg' => 'user not found'], 404);

        $this->checkUserPermissions($authUser, $user, 'approve');
        $user->approve(false);
        return response()->json(['success' => true, 'msg', 'user has been declined']);
    }

    /**
     * Change user password
     *
     * @param Request $request
     * @return void
     */
    public function changePassword(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'old_password'  => 'required|string|min:8|max:80',
            'new_password'  => 'required|string|min:8|max:80'
        ]);

        if ($validation->fails())
            throw new \App\Exceptions\ValidationError($validation->errors()->all());

        $user = auth()->user()->userData;
        
        $hashedPassword = $user->password;
        if (! Hash::check($request->get('old_password'), $hashedPassword))
            throw new \App\Exceptions\ForbiddenException;

        $newPassword = $request->get('new_password');
        $user->password = Hash::make($newPassword);

        try {
            $user->save();
        } catch (QueryException $e) {
            throw new \App\Exceptions\DBException($e);
        }
        
        return response()->json(['success' =>true]);
    }
}
