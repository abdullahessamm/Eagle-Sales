<?php

namespace App\Http\Controllers\Media;

use App\Events\Accounts\NewUserArrived;
use App\Exceptions\ForbiddenException;
use App\Exceptions\NotFoundException;
use App\Exceptions\ValidationError;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class Users extends Controller
{
    public function uploadProfilePicture(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg|max:30720',
            'pos_x' => 'numeric',
            'pos_y' => 'numeric',
            'scale' => 'numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = clone $request->user()->userData;
        unset($user->userInfo);
        
        if ($user->avatar_uri)
            Storage::disk('public')->delete($user->avatar_uri);
        
        $imagePath = $request->file('profile_picture')->store('users/profile_pictures', 'public');
        $image = \Image::make(storage_path('app/public/' . $imagePath));
        $image->save(null, 50);
        $user->setAvatar($imagePath, 0, 0, 1);
        $user->save();

        return response()->json([
            'message' => 'Profile picture uploaded',
            'image_uri' => $imagePath
        ]);
    }

    public function deleteProfilePicture(Request $request)
    {
        $user = null;

        $validator = Validator::make($request->all(), [
            'user_id' => 'integer',
        ]);
        
        if ($validator->fails())
            throw new ValidationError($validator->errors()->all());

        if ($request->has('user_id')) {
            $authUser = auth()->user()->userData;
            if (! $authUser->isAdmin())
                throw new ForbiddenException();
            
            $user = User::find($request->get('user_id'));

            if (! $user)
                throw new NotFoundException(User::class, $request->get('user_id'));
            else {
                $clonedUser = clone $user;
                $clonedUser->withFullInfo();
            }

            if (! $authUser->can('update', $clonedUser->userInfo))
                throw new ForbiddenException();
        
        } else {
            $user = clone $request->user()->userData;
            unset($user->userInfo);
        } // end if
        
        if ($user->avatar_uri)
            Storage::disk('public')->delete($user->avatar_uri);
        
        $user->deleteAvatar();
        $user->save();
        
        return response()->json([
            'message' => 'Profile picture deleted'
        ]);
    }

    public function uploadVatCertificate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:pdf|max:10000'
        ]);

        if ($validator->fails())
            throw new ValidationError($validator->errors()->all(), true);

        $authUser = auth()->user()->userData;
        if (! $authUser->isCustomer())
            throw new ForbiddenException;

        $customer = $authUser->userInfo;
        $customer->vat_uri = $request->file('file')->store('users/vat_certificates', 'public');
        $customer->save();

        event(new NewUserArrived($authUser));

        return response()->json([
            'success' => true,
            'message' => 'vat upload successfully',
            'uri'     => $customer->vat_uri
        ]);
    }
}
