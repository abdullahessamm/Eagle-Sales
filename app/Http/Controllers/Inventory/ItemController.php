<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\AppConfig;
use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Services\ItemsExcelParser\ItemsExcelParser;
use Shuchkin\SimpleXLSX;

class ItemController extends Controller
{
    /**
     * Create new Item.
     *
     * @param Request $request
     * @throws \App\Exceptions\ValidationError
     * @throws \App\Exceptions\DBException
     * @return \Illuminate\Http\JsonResponse
     * @author Abdullah Essam <abdoessam.2010@gmail.com>
     */
    public function create(Request $request)
    {
        $authUser = auth()->user()->userData;

        if (! $authUser->can('create', Item::class))
            throw new \App\Exceptions\ForbiddenException;

        $rules = [
            'name'                                             => 'required|regex:/^[A-Za-z\d\W]{3,50}$/',
            'ar_name'                                          => 'required|regex:/^[\x{0621}-\x{064A}\d\W]{3,50}$/u',
            'category_id'                                      => 'required|integer|exists:inventory_categories,id',
            'brand'                                            => 'required|regex:/^[A-Za-z\d\W]{3,50}$/',
            'ar_brand'                                         => 'required|regex:/^[\x{0621}-\x{064A}\d\W]{3,50}$/u',
            'barcode'                                          => 'string|max:64',
            'keywords'                                         => 'required|string|max:255',
            'description'                                      => 'required|regex:/^[\w\W\d]+$/|min:10|max:16777215',
            'ar_description'                                   => 'required|regex:/^[\x{0621}-\x{064A}\W\d]+$/u|min:10|max:16777215',
            'total_available_count'                            => 'required|integer|between:1,65535',
            'supplier_id'                                      => $authUser->isAdmin() ? 'required|exists:suppliers,id' : 'exists:suppliers,id',
            'price'                                            => 'required|numeric|between:1,999999.99',
            'uoms'                                             => 'required|array|min:1',
            'uoms.*.name'                                      => 'required|regex:/^[A-Za-z\d\W]{3,30}$/',
            'uoms.*.ar_name'                                   => 'required|regex:/^[\x{0621}-\x{064A}\d\W]{3,30}$/u',
            'uoms.*.description'                               => 'regex:/^[A-Za-z\d\W]{10,255}$/',
            'uoms.*.ar_description'                            => 'regex:/^[\x{0621}-\x{064A}\d\W]{10,255}$/u',
            'uoms.*.weight'                                    => 'numeric|between:0,999999.99',
            'uoms.*.length'                                    => 'numeric|between:0,999999.99',
            'uoms.*.width'                                     => 'numeric|between:0,999999.99',
            'uoms.*.height'                                    => 'numeric|between:0,999999.99',
            'uoms.*.is_default'                                => 'required|boolean',
            'uoms.*.conversion_rule.factor'                    => 'required_if:uoms.*.is_default,false|numeric|between:0,999999.99',
            'uoms.*.conversion_rule.is_multiply'               => 'required_if:uoms.*.is_default,false|bool',
            'vars'                                             => 'array|min:1',
            'vars.*.attribute'                                 => 'required|array|min:2|max:2',
            'vars.*.attribute.name'                            => 'required|regex:/^[A-Za-z]{2,20}$/',
            'vars.*.attribute.ar_name'                         => 'required|regex:/^[\x{0621}-\x{064A}]{2,20}$/u',
            'vars.*.values'                                    => 'required|array|min:1',
            'vars.*.values.*'                                  => 'array',
            'vars.*.values.*.name'                             => 'required|regex:/^[A-Za-z\d\W]{2,20}$/',
            'vars.*.values.*.ar_name'                          => 'required|regex:/^[\x{0621}-\x{064A}\d\W]{2,20}$/u',
            'vars.*.values.*.is_available'                     => 'required|boolean',
            'vars.*.values.*.children'                         => 'array|min:1',
            'vars.*.values.*.children.*.attribute'             => 'required|array|min:2|max:2',
            'vars.*.values.*.children.*.attribute.name'        => 'required|regex:/^[A-Za-z]{2,20}$/',
            'vars.*.values.*.children.*.attribute.ar_name'     => 'required|regex:/^[\x{0621}-\x{064A}]{2,20}$/u',
            'vars.*.values.*.children.*.values'                => 'required|array|min:1',
            'vars.*.values.*.children.*.values.*'              => 'array',
            'vars.*.values.*.children.*.values.*.name'         => 'required|regex:/^[A-Za-z\d\W]{2,20}$/',
            'vars.*.values.*.children.*.values.*.ar_name'      => 'required|regex:/^[\x{0621}-\x{064A}\d\W]{2,20}$/u',
            'vars.*.values.*.children.*.values.*.is_available' => 'required|boolean'
        ];

        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails())
            throw new \App\Exceptions\ValidationError($validation->errors()->all());

        // check if uoms has only one default uom
        $defaultUom = null;
        foreach ($request->get('uoms') as $uom) {
            if ($uom['is_default']) {
                if ($defaultUom)
                    throw new \App\Exceptions\ValidationError(['Only one default uom is allowed']);
                $defaultUom = $uom;
            }
        }

        if (! $defaultUom)
            throw new \App\Exceptions\ValidationError(['Default uom is required']);
        
        $itemParams = [
            'name',
            'ar_name',
            'category_id',
            'brand',
            'ar_brand',
            'barcode',
            'keywords',
            'description',
            'ar_description',
            'total_available_count',
            'price'
        ];

        $item = new Item;
        foreach ($itemParams as $param)
            if ($request->has($param))
                $item->$param = $request->get($param);

        // add vars if any
        if ($request->has('vars')) {
            $vars = $request->get('vars');

            foreach ($vars as $index => $var) {
                $attribute = $var['attribute'];
                $values = $var['values'];

                try {
                    $item->updateOrCreateVariant($attribute, $values);
                } catch (\Exception $e) {
                    throw new \App\Exceptions\ValidationError([$e->getMessage() . " at vars[$index]"]);
                }
            }
        }

        $item->supplier_id = $authUser->isAdmin() ? $request->get('supplier_id') : $authUser->userInfo->id;

        // check auto approve configuration if user not admin
        if ($authUser->isAdmin())
            $item->is_approved = true;
        else {
            $autoApprove = AppConfig::where('key', 'auto_approve_items')->first();

            if (! $autoApprove)
                $item->is_approved = null;
            else
                $item->is_approved = (bool) $autoApprove->value;
        }

        try {
            $item->save();
        } catch (QueryException $e) {
            throw new \App\Exceptions\DBException($e);
        }

        $uoms = $this->sanitizeUoms($request->get('uoms'));
        $item->addUOMs($uoms);

        return response()->json(['success' => true, 'item' => $item->withFullData()], 201);
    }

    /**
     * Sanitize uoms data and return array of uoms
     *
     * @param array $uoms
     * @return array
     * @author Abdullah Essam <abdoessam.2010@gmail.com>
     */
    private function sanitizeUoms(array $uoms): array
    {
        $uomParams = [
            'name',
            'ar_name',
            'description',
            'ar_description',
            'weight',
            'length',
            'width',
            'height',
            'is_default',
            'conversion_rule'
        ];
        
        $handledUoms = [];
        foreach ($uoms as $uom) {
            $uom = collect($uom)->only($uomParams)->toArray();
            

            if (isset($uom['conversion_rule']))
                $uom['conversion_rule'] = collect($uom['conversion_rule'])->only(['factor', 'is_multiply'])->toArray();

            $handledUoms[] = $uom;
        }
            
        return $handledUoms;
    }

    /**
     * Upload items using excel file
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\ValidationError
     * @throws \App\Exceptions\XSSAttackAttempt
     * @throws \App\Exceptions\DBException
     * @author Abdullah Essam <abdoessam.2010@gmail.com>
     */
    public function uploadItemsUsingExcel(Request $request)
    {
        $authUser = auth()->user()->userData;

        if (! $authUser->can('create', Item::class))
            throw new \App\Exceptions\ForbiddenException;

        $rules = [
            'file' => 'required|file|mimes:xlsx'
        ];

        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails())
            throw new \App\Exceptions\ValidationError($validation->errors()->all());

        $file = $request->file('file');
        $filePath = $file->getRealPath();

        try {
            $itemParser = new ItemsExcelParser($filePath);
        } catch (\App\Exceptions\ValidationError $e) {
            throw new \App\Exceptions\ValidationError($e->getErrors());
        } catch (\Exception $e) {
            throw new \App\Exceptions\ValidationError([$e->getMessage()]);
        }
        
        $items = $itemParser->getItems();

        return response()->json(['success' => true, 'items' => $items], 200);

        // $xlsx = SimpleXLSX::parse($filePath);

        // if (! $xlsx)
        //     throw new \App\Exceptions\ValidationError(['Invalid excel file']);

        // $sheets = $xlsx->sheetNames();
        // $sheets = array_map('trim', $sheets);
        // $sheets = array_map('strtolower', $sheets);
        
        // if (! in_array('items', $sheets))
        //     throw new \App\Exceptions\ValidationError(['Excel file must contain sheet named "items"']);

        // $errors = [
        //     "items" => [],
        //     "uoms"  => [],
        //     "vars"  => []
        // ];

        // // Handle items
        // $itemsSheetIndex = array_search('items', $sheets);
        // $itemsRows = $xlsx->rows($itemsSheetIndex);

        // try {
        //     $items = $this->sanitizeExcelItems($itemsRows);
        // } catch (\App\Exceptions\ValidationError $e) {
        //     $errors['items'] = $e->getErrors();
        // }

        // // Handle uoms
        // $uomsSheetIndex = array_search('uoms', $sheets);
        // $uomsRows = $xlsx->rows($uomsSheetIndex);
        
        
        // if (count($errors['items']) > 0 || count($errors['uoms']) > 0 || count($errors['vars']) > 0)
        //     throw new \App\Exceptions\ValidationError($errors);

        // return response()->json([
        //     'items' => $items
        // ]);
    }

    /**
     * approve item
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function approveOrReject(Request $request, int $id)
    {
        $authUser = auth()->user()->userData;
        if (! $authUser->can('approve', Item::class))
            throw new \App\Exceptions\ForbiddenException;

        $validation = Validator::make($request->all(), [
            "approved" => "required|integer|in:0,1"
        ]);

        if ($validation->fails())
            throw new \App\Exceptions\ValidationError($validation->errors()->all());

        try {
            $item = Item::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw new \App\Exceptions\NotFoundException(Item::class, "$id");
        }

        if ($request->get('approved') == 1)
            $item->approve();
        else
            $item->reject();
        
        event(new \App\Events\Items\ItemApprovalResponse($item));

        $response = $request->get('approved') == 1 ? 'approved' : 'rejected';
        return response()->json(['success' => true, 'message' => "Item $response successfully"]);
    }

    /**
     * Update item
     *
     * @param Request $request
     * @param integer $id
     * @return JsonResponse
     * @throws \App\Exceptions\ForbiddenException
     * @throws \App\Exceptions\ValidationError
     * @throws \App\Exceptions\NotFoundException
     * @author Abdullah Essam <abdoessam.2010@gmail.com>
     */
    public function update(Request $request, int $id)
    {
        // rules of update item
        $rules = [
            'name'                                             => 'required|regex:/^[A-Za-z\d\W]{3,50}$/',
            'ar_name'                                          => 'required|regex:/^[\x{0621}-\x{064A}\d\W]{3,50}$/u',
            'category_id'                                      => 'required|integer|exists:inventory_categories,id',
            'brand'                                            => 'required|regex:/^[A-Za-z\d\W]{3,50}$/',
            'ar_brand'                                         => 'required|regex:/^[\x{0621}-\x{064A}\d\W]{3,50}$/u',
            'barcode'                                          => 'string|max:64',
            'keywords'                                         => 'required|string|max:255',
            'description'                                      => 'required|regex:/^[\w\W\d]+$/|min:10|max:16777215',
            'ar_description'                                   => 'required|regex:/^[\x{0621}-\x{064A}\W\d]+$/u|min:10|max:16777215',
            'total_available_count'                            => 'required|integer|between:1,65535',
            'price'                                            => 'required|numeric|between:1,999999.99',
            'vars'                                             => 'array|min:1',
            'vars.*.attribute'                                 => 'required|array|min:2|max:2',
            'vars.*.attribute.name'                            => 'required|regex:/^[A-Za-z]{2,20}$/',
            'vars.*.attribute.ar_name'                         => 'required|regex:/^[\x{0621}-\x{064A}]{2,20}$/u',
            'vars.*.values'                                    => 'required|array|min:1',
            'vars.*.values.*'                                  => 'array',
            'vars.*.values.*.name'                             => 'required|regex:/^[A-Za-z\d\W]{2,20}$/',
            'vars.*.values.*.ar_name'                          => 'required|regex:/^[\x{0621}-\x{064A}\d\W]{2,20}$/u',
            'vars.*.values.*.is_available'                     => 'required|boolean',
            'vars.*.values.*.children'                         => 'array|min:1',
            'vars.*.values.*.children.*.attribute'             => 'required|array|min:2|max:2',
            'vars.*.values.*.children.*.attribute.name'        => 'required|regex:/^[A-Za-z]{2,20}$/',
            'vars.*.values.*.children.*.attribute.ar_name'     => 'required|regex:/^[\x{0621}-\x{064A}]{2,20}$/u',
            'vars.*.values.*.children.*.values'                => 'required|array|min:1',
            'vars.*.values.*.children.*.values.*'              => 'array',
            'vars.*.values.*.children.*.values.*.name'         => 'required|regex:/^[A-Za-z\d\W]{2,20}$/',
            'vars.*.values.*.children.*.values.*.ar_name'      => 'required|regex:/^[\x{0621}-\x{064A}\d\W]{2,20}$/u',
            'vars.*.values.*.children.*.values.*.is_available' => 'required|boolean'
        ];

        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails())
            throw new \App\Exceptions\ValidationError($validation->errors()->all());
        
        // find item
        $item = Item::find($id);

        // throw not found exception if item not found
        if (! $item)
            throw new \App\Exceptions\NotFoundException(Item::class, "$id");

        // throw forbidden exception if user can't update item
        if (! auth()->user()->userData->can('update', $item))
            throw new \App\Exceptions\ForbiddenException;

        // update item
        $item->name = $request->get('name');
        $item->ar_name = $request->get('ar_name');
        $item->category_id = $request->get('category_id');
        $item->barcode = $request->get('barcode');
        $item->keywords = $request->get('keywords');
        $item->description = $request->get('description');
        $item->ar_description = $request->get('ar_description');
        $item->total_available_count = $request->get('total_available_count');
        $item->price = $request->get('price');

        // update item vars
        $item->setItemToNotVariant();

        if ($request->has('vars')) {
            foreach ($request->get('vars') as $index => $var) {
                $attribute = $var['attribute'];
                $values = $var['values'];

                try {
                    $item->updateOrCreateVariant($attribute, $values);
                } catch (\Exception $e) {
                    throw new \App\Exceptions\ValidationError([$e->getMessage() . " at vars[$index]"]);
                }
            }
        }

        // save item
        try {
            $item->save();
        } catch (QueryException $e) {
            throw new \App\Exceptions\DBException($e);
        }

        // return updated item
        return response()->json(['success' => true, 'message' => "Item($id) updated successfully"]);
    }

    /**
     * Change item status to available or unavailable.
     *
     * @param Request $request
     * @param integer $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\NotFoundException
     * @throws \App\Exceptions\ForbiddenException
     * @throws \App\Exceptions\DBException
     * @throws \App\Exceptions\ValidationError
     * @author Abdullah Essam <abdoessam.2010@gmail.com>
     */
    public function changeStatus(Request $request, int $id)
    {
        $rules = [
            'is_available' => 'required|integer|in:0,1',
        ];

        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails())
            throw new \App\Exceptions\ValidationError($validation->errors()->all());

        // find item
        $item = Item::find($id);

        if (! $item)
            throw new \App\Exceptions\NotFoundException(Item::class, "$id");

        // throw forbidden exception if user can't update item
        if (! auth()->user()->userData->can('update', $item))
            throw new \App\Exceptions\ForbiddenException;

        // change item status
        $item->is_available = $request->get('is_available');

        // save item
        try {
            $item->save();
        } catch (QueryException $e) {
            throw new \App\Exceptions\DBException($e);
        }

        // return updated item
        return response()->json(['success' => true, 'message' => "Item($id) updated successfully"]);
    }

    /**
     * get item by id.
     *
     * @param Request $request
     * @return void
     */
    public function getItemById(Request $request, int $id)
    {
        $item = Item::find($id);

        // throw not found exception if item not found
        if (! $item)
            throw new \App\Exceptions\NotFoundException(Item::class, "$id");

        // if item is not active or not approved then throw not found exception if user can't view item
        if (! $item->is_active || ! $item->is_approved) {
            $authUser = auth()->user();
            if ($authUser) {
                if (! $authUser->userData->can('view', $item))
                    throw new \App\Exceptions\NotFoundException(Item::class, "$id");
            } else {
                throw new \App\Exceptions\NotFoundException(Item::class, "$id");
            }
        }

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
            'min_price'         => 'integer|min:0',
            'max_price'         => 'integer|min:0',
            'has_promotions'    => 'integer|in:0,1',
            'is_active'         => 'integer|in:0,1',
            'is_approved'       => 'integer|in:0,1',
            'supplier_id'       => 'integer|exists:suppliers,id',
            'category_id'       => 'integer|exists:inventory_categories,id',
            'search'            => 'string|max:255',
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

            if ($request->has('min_price'))
                $query->where('price', '>=', $request->get('min_price'));

            if ($request->has('max_price'))
                $query->where('price', '<=', $request->get('max_price'));

            // if search is set to true
            if ($request->has('search'))
                $query->where('name', 'like', '%' . $request->get('search') . '%')
                    ->orWhere('ar_name', 'like', '%' . $request->get('search') . '%')
                    ->orWhere('keywords', 'like', '%' . $request->get('search') . '%')
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

        // order items by created_at desc
        $items = $items->orderBy('created_at', 'desc');

        // paginate items
        $items = $items->paginate($request->get('limit'), $fields, 'page', $request->get('page'))->all();

        // get full data of items
        foreach ($items as $item)
            $item->withFullData();
        
        // return items
        return response()->json(['success' => true, 'items' => $items]);

    }
}