<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\AppConfig;
use Illuminate\Http\Request;
use App\Models\Item;
use App\Rules\ArabicLetters;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;
use App\Models\InventoryCategory;
use App\Models\Supplier;
use App\Models\User;

class ItemController extends Controller
{
    public function create(Request $request)
    {
        $authUser = auth()->user()->userData;
        if (! $authUser->can('create', Item::class))
            throw new \App\Exceptions\ForbiddenException;

        $rules = [
            'name'                          => 'required|regex:/^[A-Za-z\d\W]{3,50}$/',
            'ar_name'                       => 'required|regex:/^[\x{0621}-\x{064A}\d\W]{3,50}$/u',
            'category_id'                   => 'required|integer|exists:inventory_categories,id',
            'brand'                         => 'required|regex:/^[A-Za-z\d\W]{3,50}$/',
            'ar_brand'                      => 'required|regex:/^[\x{0621}-\x{064A}\d\W]{3,50}$/u',
            'barcode'                       => 'string|max:64',
            'keywards'                      => 'required|string|max:255',
            'description'                   => 'required|regex:/^[\w\W\d]+$/|min:10|max:16777215',
            'ar_description'                => 'required|regex:/^[\x{0621}-\x{064A}\W\d]+$/u|min:10|max:16777215',
            'total_available_count'         => 'required|integer|between:1,65535',
            'supplier_id'                   => 'exists:suppliers,id',
            'price'                         => 'required|numeric|between:1,999999.99',
            'default_uom_name'              => 'required|regex:/^\w{3,30}$/',
            'default_uom_ar_name'           => ['required', new ArabicLetters, 'min:3', 'max:30'],
            'default_uom_description'       => 'regex:/^\w{3,255}$/',
            'default_uom_ar_description'    => 'regex:/^[\x{0621}-\x{064A}\W]{3,50}$/u',
        ];

        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails())
            throw new \App\Exceptions\ValidationError($validation->errors()->all());
        
        $itemParams = [
            'name',
            'ar_name',
            'category_id',
            'barcode',
            'keywards',
            'description',
            'ar_description',
            'total_available_count',
            'price'
        ];

        $item = new Item;
        foreach ($itemParams as $param)
            if ($request->has($param))
                $item->$param = $request->get($param);

        $item->supplier_id = $authUser->userInfo->id;
        $item->currency = $authUser->currency;

        // check auto approve configuration
        $autoApprove = AppConfig::where('key', 'auto_approve_items')->first();

        if (! $autoApprove)
            $item->is_approved = false;
        else
            $item->is_approved = (bool) $autoApprove->value;
        
        try {
            $item->save();
        } catch (QueryException $e) {
            throw new \App\Exceptions\DBException($e);
        }

        $item->addUOM(
            $request->get('default_uom_name'),
            $request->get('default_uom_ar_name'),
            $request->get('default_uom_description'),
            $request->get('default_uom_ar_description'),
            true
        );

        return response()->json(['success' => true, 'item' => $item->withFullData()]);
    }

    /**
     * approve item
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function approve(Request $request)
    {
        $authUser = auth()->user()->userData;
        if (! $authUser->can('approve', Item::class))
            throw new \App\Exceptions\ForbiddenException;

        $rules = [
            'id' => 'required|integer|exists:items,id',
        ];

        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails())
            throw new \App\Exceptions\ValidationError($validation->errors()->all());

        $item = Item::find($request->get('id'));
        $item->approve();
        
        event(new \App\Events\Items\ItemApprovalResponse($item));

        return response()->json(['success' => true, 'item' => $item->withFullData()]);
    }

    /**
     * reject item
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function reject(Request $request)
    {
        $authUser = auth()->user()->userData;
        if (! $authUser->can('approve', Item::class))
            throw new \App\Exceptions\ForbiddenException;

        $rules = [
            'id' => 'required|integer|exists:items,id',
        ];

        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails())
            throw new \App\Exceptions\ValidationError($validation->errors()->all());

        $item = Item::find($request->get('id'));
        $item->reject();

        event(new \App\Events\Items\ItemApprovalResponse($item));

        return response()->json(['success' => true, 'item' => $item->withFullData()]);
    }

    // update item
    public function update(Request $request)
    {
        // rules of update item
        $rules = [
            'id'                          => 'required|integer|exists:items,id',
            'name'                        => 'required|regex:/^[A-Za-z\d\W]{3,50}$/',
            'ar_name'                     => 'required|regex:/^[\x{0621}-\x{064A}\d\W]{3,50}$/u',
            'category_id'                 => 'required|integer|exists:inventory_categories,id',
            'brand'                       => 'required|regex:/^[A-Za-z\d\W]{3,50}$/',
            'ar_brand'                    => 'required|regex:/^[\x{0621}-\x{064A}\d\W]{3,50}$/u',
            'barcode'                     => 'string|max:64',
            'keywards'                    => 'required|string|max:255',
            'description'                 => 'required|regex:/^[\w\W\d]+$/|min:10|max:16777215',
            'ar_description'              => 'required|regex:/^[\x{0621}-\x{064A}\W\d]+$/u|min:10|max:16777215',
            'total_available_count'       => 'required|integer|between:1,65535',
            'supplier_id'                 => 'exists:suppliers,id',
            'price'                       => 'required|numeric|between:1,999999.99',
        ];

        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails())
            throw new \App\Exceptions\ValidationError($validation->errors()->all());

        // find item
        $item = Item::find($request->get('id'));

        // throw not found exception if item not found
        if (! $item)
            throw new \App\Exceptions\NotFoundException(Item::class, $request->get('id'));

        // throw forbidden exception if user can't update item
        if (! auth()->user()->userData->can('update', $item))
            throw new \App\Exceptions\ForbiddenException;

        // update item
        $item->name = $request->get('name');
        $item->ar_name = $request->get('ar_name');
        $item->category_id = $request->get('category_id');
        $item->barcode = $request->get('barcode');
        $item->keywards = $request->get('keywards');
        $item->description = $request->get('description');
        $item->ar_description = $request->get('ar_description');
        $item->total_available_count = $request->get('total_available_count');
        $item->supplier_id = $request->get('supplier_id');
        $item->price = $request->get('price');

        // save item
        try {
            $item->save();
        } catch (QueryException $e) {
            throw new \App\Exceptions\DBException($e);
        }

        // return updated item
        return response()->json(['success' => true, 'item' => $item->withFullData()]);
    }

    // change item status is available or not
    public function changeStatus(Request $request)
    {
        $rules = [
            'id' => 'required|integer|exists:items,id',
        ];

        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails())
            throw new \App\Exceptions\ValidationError($validation->errors()->all());

        // find item
        $item = Item::find($request->get('id'));

        // throw not found exception if item not found
        if (! $item)
            throw new \App\Exceptions\NotFoundException(Item::class, $request->get('id'));

        // throw forbidden exception if user can't update item
        if (! auth()->user()->userData->can('update', $item))
            throw new \App\Exceptions\ForbiddenException;

        // change item status
        $item->is_available = ! $item->is_available;

        // save item
        try {
            $item->save();
        } catch (QueryException $e) {
            throw new \App\Exceptions\DBException($e);
        }

        // return updated item
        return response()->json(['success' => true, 'item' => $item->withFullData()]);
    }

    // get item by id
    public function getItemById(Request $request)
    {
        $rules = [
            'id' => 'required|integer',
        ];

        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails())
            throw new \App\Exceptions\ValidationError($validation->errors()->all());

        // find item
        $item = Item::find($request->get('id'));

        // throw not found exception if item not found
        if (! $item)
            throw new \App\Exceptions\NotFoundException(Item::class, $request->get('id'));

        // if item is not active or not approved then throw not found exception if user can't view item
        if (! $item->is_active || ! $item->is_approved)
            if (! auth()->user()->userData->can('view', $item))
                throw new \App\Exceptions\NotFoundException(Item::class, $request->get('id'));

        // return item
        return response()->json(['success' => true, 'item' => $item->withFullData()]);
    }

    /**
     * get items by optional filters
     *
     * @param Request $request
     * @return void
     */
    function getItems(Request $request)
    {
        $rules = [
            'brand'             => 'string|max:255',
            'order_by'          => 'string|in:name,ar_name,created_at,updated_at|required_with:order_type',
            'order_type'        => 'string|in:asc,desc|required_with:order_by',
            'limit'             => 'integer|between:1,100',
            'page'              => 'integer|required_with:limit|min:1',
            'min_price'         => 'integer|min:0|required_with:max_price',
            'max_price'         => 'integer|min:0|required_with:min_price',
            'has_promotions'    => 'boolean',
            'is_active'         => 'boolean',
            'is_approved'       => 'boolean',
            'supplier_id'       => 'integer',
            'category_id'       => 'integer',
            'is_search'         => 'boolean',
            'search'            => 'string|max:255|required_if:is_search',
            'required_fields'   => 'string',
        ];

        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails())
            throw new \App\Exceptions\ValidationError($validation->errors()->all());

        if (auth()->user()) {
            // if user not admin or not supplier then set is approved to true and is active to true
            if (! auth()->user()->userData->job === User::ADMIN_JOB_NUMBER && ! auth()->user()->userData->job === User::SUPPLIER_JOB_NUMBER) {
                $request->merge(['is_approved' => true]);
                $request->merge(['is_active' => true]);
            }

            // if user is supplier then set supplier id to his user info id
            if (auth()->user()->userData->job === User::SUPPLIER_JOB_NUMBER)
                $request->merge(['supplier_id' => auth()->user()->userData->userInfo->id]);
        } else {
            // if user not logged in then set is approved to true and is active to true
            $request->merge(['is_approved' => true]);
            $request->merge(['is_active' => true]);
        }

        // get items
        $items = Item::where(function($query) use ($request) {
            // if brand is set
            if ($request->has('brand'))
                $query->where('brand', $request->get('brand'));

            // if has promotions is set
            if ($request->has('has_promotions'))
                $query->where('has_promotions', $request->get('has_promotions'));

            // if is active is set
            if ($request->has('is_active'))
                $query->where('is_active', $request->get('is_active'));

            // if is approved is set
            if ($request->has('is_approved'))
                $query->where('is_approved', $request->get('is_approved'));

            // if supplier id is set
            if ($request->has('supplier_id'))
                $query->where('supplier_id', $request->get('supplier_id'));

            // if category id is set
            if ($request->has('category_id'))
                $query->where('category_id', $request->get('category_id'));

            // if price range is set
            if ($request->has('min_price') && $request->has('max_price'))
                $query->whereBetween('price', [$request->get('min_price'), $request->get('max_price')]);

            // if search is set to true
            if ($request->get('is_search'))
                $query->where('name', 'like', '%' . $request->get('search') . '%')
                    ->orWhere('ar_name', 'like', '%' . $request->get('search') . '%')
                    ->orWhere('keywards', 'like', '%' . $request->get('search') . '%')
                    ->orWhere('description', 'like', '%' . $request->get('search') . '%')
                    ->orWhere('ar_description', 'like', '%' . $request->get('search') . '%');
        });

        // if order by is set
        if ($request->has('order_by'))
            $items = $items->orderBy($request->get('order_by'), $request->get('order_type'));

        if ($request->has('required_fields')) {
            $required_fields = explode(',', $request->get('required_fields'));
            $fields = [];
            foreach ($required_fields as $field) {
                $field = trim($field);
                if (in_array($field, Item::AVAILABLE_COLUMNS))
                    $fields[] = $field;
            }
        } else
            $fields = ['*'];

        // paginate items
        $items = $items->paginate($request->get('limit'), $fields, 'page', $request->get('page'))->all();

        // get full data of items
        foreach ($items as $item)
            $item->withFullData();
        
        // return items
        return response()->json(['success' => true, 'items' => $items]);

    }
}