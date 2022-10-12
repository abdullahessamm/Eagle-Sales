<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Rules\ArabicLetters;
use Illuminate\Support\Facades\Validator;
use App\Models\Item;
use App\Models\Uom;
use App\Models\UomConversionRule;

class UomController extends Controller
{
    // add UOM to item
    public function addUOM(Request $request)
    {
        $rules = [
            'item_id'                                        => 'required|integer|exists:items,id',
            'uoms'                                           => 'required|array|min:1',
            'uoms.*.name'                                    => 'required|regex:/^[a-zA-Z]{3,30}$/',
            'uoms.*.ar_name'                                 => ['required', new ArabicLetters, 'min:3', 'max:30'],
            'uoms.*.description'                             => 'regex:/^\w\W{3,255}$/',
            'uoms.*.ar_description'                          => 'regex:/^[\x{0621}-\x{064A}\W]{3,255}$/u',
            'uoms.*.conversion_rule.factor'                  => 'required|numeric|between:0,9999999.99',
            'uoms.*.conversion_rule.operation_is_multiply'   => 'required|integer|between:0,1'
        ];

        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails())
            throw new \App\Exceptions\ValidationError($validation->errors()->all());

        $item = Item::find($request->get('item_id'));

        $authUser = auth()->user()->userData;
        if (! $authUser->can('update', $item))
            throw new \App\Exceptions\ForbiddenException;
    

        foreach ($request->get('uoms') as $uom) {
            $uoms = new Uom(collect($uom)->except(['conversion_rule'])->toArray());
            $uoms->item_id = $request->get('item_id');
            $uoms->save();

            $uoms->setConversionRule($uom['conversion_rule']['factor'], $uom['conversion_rule']['operation_is_multiply']);
        }

        return response()->json(['success' => true, 'uoms' => $item->uoms]);
    }

    // update uom of item
    public function updateUOM(Request $request)
    {
        $rules = [
            'item_id'                               => 'required|integer|exists:items,id',
            'uom_id'                                => 'required|integer|exists:uoms,id',
            'name'                                  => 'required|regex:/^\w{3,30}$/',
            'ar_name'                               => ['required', new ArabicLetters, 'min:3', 'max:30'],
            "is_default"                            => 'required|integer|between:0,1',
            'description'                           => 'regex:/^\w\W{3,255}$/',
            'ar_description'                        => 'regex:/^[\x{0621}-\x{064A}\W]{3,255}$/u',
            'conversion_rule.factor'                => 'numeric|between:0.01,9999999.99|required_with:conversion_rule.operation_is_multiply',
            'conversion_rule.operation_is_multiply' => 'integer|between:0,1|required_with:conversion_rule.factor'
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
            $request->get('ar_description'),
            $request->get('is_default')
        );

        if ($request->has('conversion_rule')) {
            $rule = UomConversionRule::where('uom_id', $request->get('uom_id'))->first();
            if ($rule) {
                $rule->factor = $request->get('conversion_rule')['factor'];
                $rule->operation_is_multiply = $request->get('conversion_rule')['operation_is_multiply'];
                $rule->save();
            }
        }

        return response()->json(['success' => true]);
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
