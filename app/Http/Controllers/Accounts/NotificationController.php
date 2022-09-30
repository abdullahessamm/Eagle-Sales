<?php

namespace App\Http\Controllers\Accounts;

use App\Exceptions\ValidationError;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    public function get(Request $request)
    {
        $authUser = auth()->user()->userData;

        $validation = Validator::make($request->all(), [
            'unread_only' => 'integer|between:0,1',
            'limit'       => 'integer|min:1|required_with:page',
            'page'        => 'integer|min:1|required_with:limit'
        ]);

        if ($validation->fails())
            throw new ValidationError($validation->errors()->all());

        $notifications = $authUser->notifications();

        if ($request->get('unread_only'))
            $notifications->where('read_at', null);

        $notifications->orderBy('created_at', 'desc');
        
        $notifications = $notifications->paginate($request->get('limit'), ['*'], 'page', $request->get('page'));
        return response()->json([
            'success' => true,
            'notifications' => $notifications
        ]);
    }

    public function read() {
        $authUser = auth()->user()->userData;
        $authUser->notifications()->where('read_at', null)->update(['read_at' => now()]);
        return response()->json(['success' => true]);
    }
}
