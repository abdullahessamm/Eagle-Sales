<?php

namespace App\Http\Controllers;

use App\Exceptions\ForbiddenException;
use App\Exceptions\ValidationError;
use App\Models\Invoice;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class InvoicesController extends Controller
{
    
    public function create(Request $request)
    {
        $authUser = auth()->user()->userData;

        if ($authUser->cannot('create', Order::class))
            throw new ForbiddenException;

        $validation = Validator::make($request->all(), [
            'billing_address_id'                            => 'required|integer',
            'buyer_id'                                      => Rule::requiredIf($authUser->isSeller()),
            'items'                                         => 'required|array|min:1',
            'items.*'                                       => 'array',
            'items.*.id'                                    => 'required|integer|exists:items,id',
            'items.*.uom_id'                                => 'required|integer',
            'items.*.quantity'                              => 'required|integer|min:1',
            'items.*.promotion_id'                          => 'integer',
            'items.*.variant'                               => 'array',
            'items.*.variant.attribute.name'                => 'required_with:items.*.variant|string',
            'items.*.variant.attribute.ar_name'             => 'required_with:items.*.variant|string',
            'items.*.variant.value.name'                    => 'required_with:items.*.variant|string',
            'items.*.variant.value.ar_name'                 => 'required_with:items.*.variant|string',
            'items.*.variant.value.child'                   => 'array',
            'items.*.variant.value.child.attribute.name'    => 'required_with:items.*.variant.value.child|string',
            'items.*.variant.value.child.attribute.ar_name' => 'required_with:items.*.variant.value.child|string',
            'items.*.variant.value.child.value.name'        => 'required_with:items.*.variant.value.child|string',
            'items.*.variant.value.child.value.ar_name'     => 'required_with:items.*.variant.value.child|string',
            'is_credit'                                     => 'boolean',
        ]);

        if ($validation->fails())
            throw new ValidationError($validation->errors()->all());

        $buyer = $authUser;
        if ($authUser->isSeller())
            $buyer = $authUser->linkedCustomers()->find($request->get('buyer_id'));

        if (! $buyer)
            throw new ValidationError(['Invalid buyer id']);

        // validate billing address
        $address =  $buyer->places()->find($request->get('billing_address_id'));
        if (!$address)
            throw new ValidationError(['Invalid billing address id']);

        $items = collect($request->get('items'));

        // check for duplicate
        $items->each(function ($item) use ($items) {
            if ($items->where('id', $item['id'])->count() > 1)
                throw new ValidationError(['duplicated entries ' . $item['id']]);
        });

        $ordersItems = $items->map(function ($item) {
            $itemModel = Item::find($item['id'])->load(['promotions', 'uoms']);
            $uom = $itemModel->uoms->firstWhere('id', $item['uom_id']);

            if (! $uom)
                throw new ValidationError(['uom is invalid'], true);

            $promotion = null;
            if (isset($item['promotion_id'])) {
                $promotion = $itemModel->promotions->firstWhere('id', $item['promotion_id']);
                if (! $promotion)
                    throw new ValidationError(['promotion is invalid'], true);
            }
            $orderItem = $itemModel->sell($uom, $item['quantity'], $promotion);
            if (! $orderItem)
                throw new ValidationError(['Invalid quantity'], true);
            $orderItem['variant'] = isset($item['variant']) ? json_encode($item['variant']) : null;
            $orderItem['supplier_id'] = $itemModel->supplier()->id;

            return $orderItem;
        });

        $invoice = new Invoice();
        $invoice->buyer_id = $buyer->id;
        $invoice->generateSerialCode();
        $invoice->save();

        $groupedItemsBySupplier = $ordersItems->groupBy('supplier_id')->map(function ($items) {
            return $items->map(fn ($item) => new OrderItem(collect($item)->except('supplier_id')->toArray()));
        });

        foreach ($groupedItemsBySupplier as $supplierId => $items) {
            $order = new Order([
                'invoice_id' => $invoice->id,
                'supplier_id' => $supplierId,
                'buyer_id' => $buyer->id,
                'required' => collect($items)->sum('total_before_discount'),
                'discount' => collect($items)->sum('discount'),
                'total_required' => collect($items)->sum('total'),
                'is_credit' => $request->has('is_credit') ? $request->get('is_credit') : false,
                'billing_address_id' => $request->get('billing_address_id'),
            ]);

            $order->save();
            $order->items()->saveMany($items);
        }

        $invoice->load('orders');
        $invoice->orders->map(fn ($order) => $order->load('items'));

        return response()->json(['invoice' => $invoice]);
    }

    public function get(Request $request)
    {
        $authUser = auth()->user()->userData;
        if (! $authUser->isAdmin() && ! $authUser->isCustomer() && ! $authUser->isOnlineClient())
            throw new ForbiddenException;

        if ($authUser->isAdmin() && $authUser->cannot('viewAny', Order::class))
            throw new ForbiddenException;

        $validation = Validator::make($request->all(), [
            'id'                => 'integer',
            'serial_number'     => 'string',
            'buyer_id'          => 'integer|exists:users,id',
            'created_at'        => 'date_format:d/m/Y',
            'limit'             => 'integer|min:1|required_with:page',
            'page'              => 'integer|min:1|required_with:limit',
            'load_orders'       => 'integer|between:0,1',
        ]);

        if ($validation->fails())
            throw new ValidationError($validation->errors()->all());

        $invoicesModel = $authUser->isAdmin() ? new Invoice : $authUser->invoices();

        $invoices = $invoicesModel->where(function ($query) use ($request) {
            $requestParams = $request->only(['id', 'serial_number', 'buyer_id', 'created_at']);
            foreach ($requestParams as $param => $value)
                $query->where($param, $value);
        });

        $invoices->orderBy('created_at', 'desc');

        if ($request->get('load_orders'))
            $invoices->with('orders');

        if ($request->has('limit'))
            $invoices->paginate($request->get('limit'), ['*'], 'page', $request->get('page'));

        return response()->json(['success' => true, 'invoices' => $invoices->get()]);
    }
}
