<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Rules\ArabicLetters;
use Illuminate\Support\Facades\Validator;
use App\Models\Item;

class UomController extends Controller
{
    // add UOM to item
    public function addUOM(Request $request)
    {
        $rules = [
            'item_id'                       => 'required|integer|exists:items,id',
            'name'                          => 'required|regex:/^[a-zA-Z]{3,30}$/',
            'ar_name'                       => ['required', new ArabicLetters, 'min:3', 'max:30'],
            'description'                   => 'regex:/^\w\W{3,255}$/',
            'ar_description'                => 'regex:/^[\x{0621}-\x{064A}\W]{3,255}$/u',
        ];

        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails())
            throw new \App\Exceptions\ValidationError($validation->errors()->all());

        $item = Item::find($request->get('item_id'));

        $authUser = auth()->user()->userData;
        if (! $authUser->can('update', $item))
            throw new \App\Exceptions\ForbiddenException;
        
        $item->addUOM(
            $request->get('name'),
            $request->get('ar_name'),
            $request->get('description'),
            $request->get('ar_description')
        );

        return response()->json(['success' => true, 'item' => $item->withFullData()]);
    }

    // update uom of item
    public function updateUOM(Request $request)
    {
        $rules = [
            'item_id'                       => 'required|integer|exists:items,id',
            'uom_id'                        => 'required|integer|exists:uoms,id',
            'name'                          => 'required|regex:/^\w{3,30}$/',
            'ar_name'                       => ['required', new ArabicLetters, 'min:3', 'max:30'],
            'description'                   => 'regex:/^\w\W{3,255}$/',
            'ar_description'                => 'regex:/^[\x{0621}-\x{064A}\W]{3,255}$/u',
        ];

        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails())
            throw new \App\Exceptions\ValidationError($validation->errors()->all());

        $item = Item::find($request->get('item_id'));

        $authUser = auth()->user()->userData;
        if (! $authUser->can('update', $item))
            throw new \App\Exceptions\ForbiddenException;
        
            $item->updateUOM(
            $request->get('uom_id'),
            $request->get('name'),
            $request->get('ar_name'),
            $request->get('description'),
            $request->get('ar_description')
        );

        return response()->json(['success' => true, 'item' => $item->withFullData()]);
    }

    // delete uom of item
    public function deleteUOM(Request $request)
    {
        $rules = [
            'item_id'                       => 'required|integer|exists:items,id',
            'uom_id'                        => 'required|integer|exists:uoms,id',
        ];

        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails())
            throw new \App\Exceptions\ValidationError($validation->errors()->all());

        $item = Item::find($request->get('item_id'));

        $authUser = auth()->user()->userData;
        if (! $authUser->can('update', $item))
            throw new \App\Exceptions\ForbiddenException;
        
        $item->deleteUOM($request->get('uom_id'));

        return response()->json(['success' => true, 'item' => $item->withFullData()]);
    }
}
