<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PromotionController extends Controller
{
    // add promotion to item
    public function addPromotion(Request $request)
    {
        $rules = [
            'item_id'                       => 'required|integer|exists:items,id',
            'description'                   => 'required|regex:/^\w\W{3,255}$/',
            'ar_description'                => 'required|regex:/^[\x{0621}-\x{064A}\W]{3,255}$/u',
            'start_date'                    => 'required|date',
            'expire_date'                   => 'required|date',
            'is_discount'                   => 'required|boolean',
            'has_promocodes'                => 'boolean',
            'conditional_quantity'          => 'required|integer|between:1,255',
            'number_of_free_elements'       => 'integer|between:1,255|required_if:is_discount,0',
            'amount_of_discount'            => 'numeric|between:1,99999999.99,required_if:is_discount,1',
            'is_active'                     => 'required|boolean',
        ];

        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails())
            throw new \App\Exceptions\ValidationError($validation->errors()->all());

        $item = Item::find($request->get('item_id'));

        $authUser = auth()->user()->userData;
        if (! $authUser->can('update', $item))
            throw new \App\Exceptions\ForbiddenException;

        $promotion = new Promotion;
        $promotion->description = $request->get('description');
        $promotion->ar_description = $request->get('ar_description');
        $promotion->start_date = $request->get('start_date');
        $promotion->expire_date = $request->get('expire_date');
        $promotion->is_discount = $request->get('is_discount');
        $promotion->has_promocodes = $request->get('has_promocodes') ?? false;
        $promotion->conditional_quantity = $request->get('conditional_quantity');
        $promotion->number_of_free_elements = $request->get('number_of_free_elements') ?? null;
        $promotion->amount_of_discount = $request->get('amount_of_discount') ?? null;
        $promotion->is_active = $request->get('is_active');

        $item->addPromotion($promotion);

        return response()->json(['success' => true, 'item' => $item->withFullData()]);
    }

    // deactivate promotion
    public function deactivatePromotion(Request $request)
    {
        $rules = [
            'promotion_id' => 'required|integer|exists:promotions,id',
        ];

        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails())
            throw new \App\Exceptions\ValidationError($validation->errors()->all());

        $promotion = Promotion::find($request->get('promotion_id'));

        $authUser = auth()->user()->userData;
        if (! $authUser->can('update', $promotion->item()))
            throw new \App\Exceptions\ForbiddenException;

        // deactivate the promotion
        $promotion->activate(false);

        return response()->json(['success' => true]);
    }

    // activate promotion
    public function activatePromotion(Request $request)
    {
        $rules = [
            'promotion_id' => 'required|integer|exists:promotions,id',
        ];

        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails())
            throw new \App\Exceptions\ValidationError($validation->errors()->all());

        $promotion = Promotion::find($request->get('promotion_id'));

        $authUser = auth()->user()->userData;
        if (! $authUser->can('update', $promotion->item()))
            throw new \App\Exceptions\ForbiddenException;

        // activate the promotion
        $promotion->activate(true);

        return response()->json(['success' => true]);
    }

    // update promotion only description
    public function updatePromotion(Request $request)
    {
        $rules = [
            'promotion_id' => 'required|integer|exists:promotions,id',
            'description'  => 'regex:/^\w\W{3,255}$/|required_without:ar_description',
            'ar_description' => 'regex:/^[\x{0621}-\x{064A}\W]{3,255}$/u|required_without:description',
        ];

        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails())
            throw new \App\Exceptions\ValidationError($validation->errors()->all());

        $promotion = Promotion::find($request->get('promotion_id'));

        $authUser = auth()->user()->userData;
        if (! $authUser->can('update', $promotion->item()))
            throw new \App\Exceptions\ForbiddenException;

        $promotion->updateDescription($request->get('description'), $request->get('ar_description'));

        return response()->json(['success' => true]);
    }
}
