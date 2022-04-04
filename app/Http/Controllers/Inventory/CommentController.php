<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\ItemsComment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    // add comment to item
    public function addComment(Request $request)
    {
        // if user is not customer throw forbidden exception
        $authUser = auth()->user()->userData;
        if ($authUser->job !== User::CUSTOMER_JOB_NUMBER)
            throw new \App\Exceptions\ForbiddenException;

        $rules = [
            'item_id'                       => 'required|integer|exists:items,id',
            'comment'                       => 'required|string|min:3|max:255',
        ];

        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails())
            throw new \App\Exceptions\ValidationError($validation->errors()->all());

        $item = Item::find($request->get('item_id'));
        // handle not found items
        if (! $item)
            throw new \App\Exceptions\NotFoundException(Item::class, $request->get('item_id'));
        
        $comment = new ItemsComment;
        $comment->comment = $request->get('comment');

        $item->addComment($comment);

        return response()->json(['success' => true, 'item' => $item->withFullData()]);
    }
}
