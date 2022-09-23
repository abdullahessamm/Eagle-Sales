<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\ItemsComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    // add comment to item
    public function addComment(Request $request)
    {
        // if user is not customer throw forbidden exception
        $authUser = auth()->user()->userData;
        if (! $authUser->isCustomer() || ! $authUser->isOnlineClient())
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
        $comment->user_id = $authUser->id;

        $item->addComment($comment);

        return response()->json(['success' => true, 'comment' => $comment]);
    }

    public function deleteComment(Request $request, int $commentId)
    {
        $comment = ItemsComment::find($commentId);

        if (! $comment)
            throw new \App\Exceptions\NotFoundException(ItemsComment::class, $commentId);

        $authUser = auth()->user()->userData;
        $item = $comment->item()->first();
        
        if ((int) $authUser->id !== (int) $comment->user_id && $authUser->cannot('update-comment', $item))
            throw new \App\Exceptions\ForbiddenException;

        $comment->delete();
        return response()->json(['success' => true]);
    }
}
