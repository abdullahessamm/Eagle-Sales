<?php

namespace App\Http\Controllers\Inventory;

use App\Events\Items\ItemRated;
use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\ItemsRate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RateController extends Controller
{
    // add rate to item
    public function addRate(Request $request)
    {
        $authUser = auth()->user()->userData;
        if (! $authUser->isCustomer() && $authUser->isOnlineClient())
            throw new \App\Exceptions\ForbiddenException;

        $rules = [
            'item_id'                       => 'required|integer|exists:items,id',
            'rate'                          => 'required|integer|min:1|max:5',
        ];

        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails())
            throw new \App\Exceptions\ValidationError($validation->errors()->all());

        $customerRate = ItemsRate::where('item_id', $request->get('item_id'))
            ->where('user_id', $authUser->id)
            ->first();

        if ($customerRate)
            throw new \App\Exceptions\ForbiddenException();

        $item = Item::find($request->get('item_id'));

        // handle not foun items
        if (! $item)
            throw new \App\Exceptions\NotFoundException(Item::class, $request->get('item_id'));

        $item->addRate($request->get('rate'));
        return response()->json(['success' => true]);
    }
}
