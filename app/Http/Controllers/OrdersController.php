<?php

namespace App\Http\Controllers;

use App\Events\Orders\OrderStateChanged;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Exceptions\ForbiddenException;
use App\Exceptions\ValidationError;
use App\Models\AppConfig;
use App\Models\Order;
use App\Models\OrderComment;

class OrdersController extends Controller
{
    public function get(Request $request)
    {
        $authUser = auth()->user()->userData;
        if ($authUser->isAdmin() && $authUser->cannot('viewAny', Order::class))
            throw new ForbiddenException;
        
        $validator = Validator::make($request->all(), [
            'id'                                => 'integer',
            'supplier_id'                       => 'integer|exists:suppliers,id',
            'buyer_id'                          => 'integer|exists:users,id',
            'seller_id'                         => 'integer|exists:users,id',
            'state'                             => 'integer|min:0|max:6',
            'is_credit'                         => 'integer|between:0,1',
            'load_items'                        => 'integer|between:0,1',
            'load_billing_address'              => 'integer|between:0,1',
            'min_price'                         => 'numeric|min:0',
            'max_price'                         => 'numeric|min:0',
            'limit'                             => 'integer|min:1|required_with:page',
            'page'                              => 'integer|min:1|required_with:limit',
            'count_only'                        => 'integer|between:0,1'
        ]);

        if ($validator->fails())
            throw new ValidationError($validator->errors()->all());

        $ordersModel = $authUser->isAdmin() ? new Order : $authUser->orders();
        if ($authUser->isSupplier())
            $ordersModel = $authUser->userInfo->relatedOrders();

        $orders = $ordersModel->where(function ($query) use ($request) {
            $requestParams = $request->only(['id', 'supplier_id', 'buyer_id', 'seller_id', 'state', 'is_credit']);
            foreach ($requestParams as $param => $value) {
                $param = $param === 'seller_id' ? 'created_by' : $param;
                $query->where($param, $value);
            }
        });

        if ($request->has('min_price'))
            $orders->where('total_required', '>=', $request->get('min_price'));

        if ($request->has('max_price'))
            $orders->where('total_required', '<=', $request->get('max_price'));

        $orders->orderBy('created_at', 'desc');

        if ($request->get('load_items'))
            $orders->with('items');

        if ($request->get('load_billing_address'))
            $orders->with(['billingAddress']);

        if ($request->has('limit'))
            $orders->paginate($request->get('limit'), ['*'], 'page', $request->get('page'));

        if ($request->get('count_only'))
            $orders = $orders->count();

        else {
            $orders = $orders->get();
    
            if ($request->get('load_billing_address'))
                $orders->each(function ($order) use ($authUser) {
                    if ($authUser->isSupplier() && ! $order->isApproved())
                        return;
    
                    $order->billingAddress?->showHiddens();
                });
        }

        return response()->json(['success' => true, 'orders' => $orders]);
    }

    public function updateState(Request $request)
    {
        $authUser = $authUser = auth()->user()->userData;
        if (
            ! $authUser->isCustomer() &&
            ! $authUser->isOnlineClient() &&
            ! $authUser->isSupplier()
        ) throw new ForbiddenException;

        $validator = Validator::make($request->all(), [
            'order_id'  => 'required',
            'state'     => 'required|integer|between:1,6',
            'comment'   => 'required_if:state,1|string'
        ]);

        if ($validator->fails())
            throw new ValidationError($validator->errors()->all());

        $ordersModel = $authUser->isSupplier() ? $authUser->userInfo->relatedOrders() : $authUser->orders();
        $order = $ordersModel->find($request->get('order_id'));

        if (! $order) throw new ValidationError(['Invalid order id']);

        if ($order->isDelivered() || $order->isRejected() || $order->isCancelled())
            throw new ForbiddenException;

        $supplierActions = [
            Order::STATUS_APPROVED,
            Order::STATUS_REJECTED,
            Order::STATUS_REQUEST_MORE_INFO,
            Order::STATUS_UNDER_SHIPPING,
            Order::STATUS_DELIVERED,
        ];

        if (! $authUser->isSupplier() && in_array($request->get('state'), $supplierActions))
            throw new ForbiddenException;

        if ($order->isApproved() && $request->get('state') == Order::STATUS_CANCELLED) {
            $orderCanCancelAfterApprove = AppConfig::orderCanBeCancelledAfterApproved();
            if (! $orderCanCancelAfterApprove)
                throw new ForbiddenException;
        }

        $order->state = $request->get('state');
        $order->save();

        // create comment on request more info status
        $order->comments()->save(new OrderComment([
            'comment'   => $request->get('comment'),
            'author_id' => $authUser->id,
        ]));

        event(new OrderStateChanged($order));

        return response()->json(['success' => true, 'msg' => 'Order has been updated']);
    }
}
