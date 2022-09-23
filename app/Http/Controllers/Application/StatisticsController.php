<?php

namespace App\Http\Controllers\Application;

use App\Exceptions\ForbiddenException;
use App\Exceptions\ValidationError;
use App\Http\Controllers\Controller;
use App\Models\InventoryCategory;
use App\Models\Order;
use App\Models\Seller;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class StatisticsController extends Controller
{
    public function salesBySellers(Request $request)
    {
        $authUser = auth()->user()->userData;
        if (! $authUser->isAdmin())
            throw new ForbiddenException;

        if (! $authUser->userInfo->permissions->hasAccessToStatistics())
            throw new ForbiddenException;

        $validation = Validator::make($request->all(), [
            'country'       => 'required|in:eg,sa|string',
            'start_date'    => 'date_format:Y-m-d',
            'end_date'      => 'date_format:Y-m-d',
            'state'         => 'integer|between:0,6'
        ]);

        if ($validation->fails())
            throw new ValidationError($validation->errors()->all());

        $sellers = Seller::with('user:id,f_name,l_name,avatar_uri')->get(['id', 'user_id', 'is_freelancer']);
        $sellers = $sellers->map(function ($seller) use ($request) {
            $state = $request->get('state');
            $startDate = $request->has('start_date') ? Carbon::create($request->get('start_date')) : null;
            $endDate = $request->has('end_date') ? Carbon::create($request->get('end_date')) : null;
            $seller->salesAmount = $seller->ordersAmount($state, $startDate, $endDate);
            return $seller;
        });


        return response()->json([
            'success' => true,
            'sellers' => $sellers->where('salesAmount', '>', '0')->sortByDesc('salesAmount')
        ]);
    }

    public function salesBySuppliers(Request $request)
    {
        $authUser = auth()->user()->userData;
        if (! $authUser->isAdmin() && ! $authUser->isCustomer())
            throw new ForbiddenException;

        if ($authUser->isAdmin()) {
            if (! $authUser->userInfo->permissions->hasAccessToStatistics())
                throw new ForbiddenException;
        }

        $validation = Validator::make($request->all(), [
            'country'       => [Rule::requiredIf($authUser->isAdmin()), 'in:eg,sa', 'string'],
            'start_date'    => 'date_format:Y-m-d',
            'end_date'      => 'date_format:Y-m-d',
            'state'         => 'integer|between:0,6'
        ]);

        if ($validation->fails())
            throw new ValidationError($validation->errors()->all());

        $orderModel = $authUser->isAdmin() ? new Order : $authUser->orders();

        $startDate = $request->get('start_date') ?? '1990-01-01';
        $endDate = $request->get('end_date') ??  date('Y-m-d');
        
        $orders = $orderModel->whereBetween('created_at', [$startDate, $endDate]);
        if ($request->has('country')) {
            $currency = $request->get('country') == 'eg' ? 'EGP' : 'SAR';
            $orders->where('currency', $currency);
        }

        if ($request->has('state'))
            $orders->where('state', $request->get('state'));

        $ordersBySupplier = $orders->get([
            'id',
            'supplier_id',
            'state',
            'currency',
            'total_required',
            'created_at',
            'updated_at'
        ])->groupBy('supplier_id');

        $salesAmount = $ordersBySupplier->map(function ($orders, $supplierId) {
            $supplier = Supplier::find($supplierId)->load(['user:id,f_name,l_name,avatar_uri'])->user;
            $supplier->salesAmount = collect($orders)->sum('total_required');
            return $supplier;
        });
        
        return response()->json([
            'success' => true,
            'orders' => $salesAmount
        ]);
    }

    // TODO create sales by categories statistics for Suppliers
    public function salesByCategories(Request $request)
    {
        $authUser = auth()->user()->userData;
        if (! $authUser->isAdmin())
            throw new ForbiddenException;

        if (! $authUser->userInfo->permissions->hasAccessToStatistics())
            throw new ForbiddenException;

        $validation = Validator::make($request->all(), [
            'country'       => 'required|in:eg,sa|string',
            'start_date'    => 'date_format:Y-m-d',
            'end_date'      => 'date_format:Y-m-d'
        ]);

        if ($validation->fails())
            throw new ValidationError($validation->errors()->all());

        $categories = InventoryCategory::get(['id', 'category_name', 'category_name_ar'])
        ->map(function ($category) use ($request) {
            $startDate = $request->has('start_date') ? Carbon::create($request->get('start_date')) : null;
            $endDate = $request->has('end_date') ? Carbon::create($request->get('end_date')) : null;
            $category->salesAmount = $category->salesAmount($startDate, $endDate);
            return $category;
        });

        return response()->json([
            'success' => true,
            'categories' => $categories
        ]);
    }

    public function salesByOrderState(Request $request)
    {
        $authUser = auth()->user()->userData;
        if ($authUser->isAdmin()) {
            if (! $authUser->userInfo->permissions->hasAccessToStatistics())
            throw new ForbiddenException;
        }

        $validation = Validator::make($request->all(), [
            'country'       => [Rule::requiredIf($authUser->isAdmin()), 'in:eg,sa', 'string'],
            'start_date'    => 'date_format:Y-m-d',
            'end_date'      => 'date_format:Y-m-d',
        ]);

        if ($validation->fails())
            throw new ValidationError($validation->errors()->all());

        $startDate = Carbon::create(1990);
        $endDate = Carbon::now();

        if ($request->has('start_date'))
            $startDate = Carbon::create($request->get('start_date'));

        if ($request->has('end_date'))
            $endDate = Carbon::create($request->get('end_date'));

        $orders = $authUser->isAdmin() ? new Order : $authUser->orders();
        $orders = $orders
        ->whereBetween('created_at', [$startDate, $endDate])
        ->where(function ($query) {
            $query->where('state', Order::STATUS_OPEN)
            ->orWhere('state', Order::STATUS_APPROVED)
            ->orWhere('state', Order::STATUS_DELIVERED);
        })
        ->get()
        ->groupBy('state');

        $salesAmount = $orders->map(function ($orders) {
            return $orders->sum('total_required');
        });

        return response()->json([
            'success' => true,
            'salesAmount' => $salesAmount
        ]);
    }

    public function salesByMonth(Request $request)
    {
        $authUser = auth()->user()->userData;
        if ($authUser->isAdmin()) {
            if (! $authUser->userInfo->permissions->hasAccessToStatistics())
                throw new ForbiddenException;
        }

        $validation = Validator::make($request->all(), [
            'country'       => [Rule::requiredIf($authUser->isAdmin()), 'in:eg,sa', 'string'],
            'state'         => 'required|integer|between:0,6',
            'month'         => 'required|date_format:Y-m'
        ]);

        if ($validation->fails())
            throw new ValidationError($validation->errors()->all());

        $orderModel = $authUser->isAdmin() ? new Order : $authUser->orders();
        if ($authUser->isSupplier())
            $orderModel = $authUser->userInfo->relatedOrders();

        $startOfMonth = Carbon::create($request->get('month'));
        $endOfMonth = clone $startOfMonth;
        $endOfMonth->addMonth()->subDay();
        
        $orders = $orderModel->where('state', $request->get('state'))
        ->whereBetween('created_at', [$startOfMonth, $endOfMonth]);

        if ($request->has('country')) {
            $currency = $request->get('country') === 'eg' ? 'EGP' : 'SAR';
            $orders->where('currency', $currency);
        }

        $orders = $orders->get(['total_required', 'created_at', 'state']);
        $ordersByDay = $orders->each(function ($order) {
            $order->day = $order->created_at->format('Y_m_d');
        })->groupBy('day');

        $salesAmount = $ordersByDay->map(function ($orders, $day) {
            return $orders->sum('total_required');
        });

        return response()->json([
            'success' => true,
            'salesAmount' => $salesAmount
        ]);
    }

    public function salesByProduct(Request $request)
    {
        $authUser = auth()->user()->userData;
        if (! $authUser->isSupplier())
            throw new ForbiddenException;

        $validation = Validator::make($request->all(), [
            'start_date' => 'date_format:Y-m-d',
            'end_date' => 'date_format:Y-m-d'
        ]);

        if ($validation->fails())
            throw new ValidationError($validation->errors()->all());

        $startDate = Carbon::create(1990);
        $endDate = now();

        if ($request->has('start_date'))
            $startDate = Carbon::create($request->get('start_date'));

        if ($request->has('end_date'))
            $endDate = Carbon::create($request->get('end_date'));

        $items = $authUser->userInfo->relatedItems()
        ->get(['id', 'supplier_id', 'name', 'ar_name'])
        ->map(function ($item) use ($startDate, $endDate) {
            $item->salesAmount = $item->salesAmount($startDate, $endDate);
            return $item;
        })->where('salesAmount', '>', 0);

        return response()->json([
            'success' => true,
            'items' => $items
        ]);
    }

    public function sellerCommissionByYear(Request $request)
    {
        $authUser = auth()->user()->userData;
        if (! $authUser->isFreelancerSeller())
            throw new ForbiddenException;

        $validation = Validator::make($request->all(), [
            'year'  => 'required|date_format:Y'
        ]);

        if ($validation->fails())
            throw new ValidationError($validation->errors()->all());

        $startDate = Carbon::create($request->get('year'));
        $endDate   = clone $startDate;
        $endDate   = $endDate->addYear()->subDay();

        $commissions = $authUser->userInfo->dues()
        ->where('is_commission', true)
        ->whereBetween('created_at', [$startDate, $endDate])
        ->get(['id', 'seller_id', 'cash', 'created_at'])
        ->map(function ($commission) {
            $commission->month = $commission->created_at->month;
            return $commission;
        })
        ->groupBy('month')
        ->map(fn ($commissions) => $commissions->sum('cash'));

        return response()->json([
            'success' => true,
            'commissions' => $commissions
        ]);
    }
}
