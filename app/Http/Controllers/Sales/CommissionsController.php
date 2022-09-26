<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Exceptions\ForbiddenException;
use App\Exceptions\ValidationError;
use App\Models\DuesOfSeller;
use App\Models\OurCommission;
use App\Models\Seller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class CommissionsController extends Controller
{
    public function getOurCommissions(Request $request)
    {
        $authUser = auth()->user()->userData;
        if (! $authUser->isAdmin() && ! $authUser->isSupplier())
            throw new ForbiddenException;

        if ($authUser->isAdmin() && $authUser->cannot('viewAny', OurCommission::class))
            throw new ForbiddenException;

        $validation = Validator::make($request->all(), [
            'supplier_id'   => 'integer|min:0',
            'obtained'      => 'integer|between:0,1',
            'start_date'    => 'date_format:Y-m-d',
            'end_date'      => 'date_format:Y-m-d',
        ]);

        if ($validation->fails())
            throw new ValidationError($validation->errors()->all());
        
        $startDate = $request->has('start_date') ? Carbon::create($request->get('start_date')) : Carbon::create(1990);
        $endDate   = $request->has('end_date') ? Carbon::create($request->get('end_date')) : now();
        
        $ourCommissionModel = $authUser->isAdmin() ? new OurCommission : $authUser->userInfo->commissions();
        $commissions = $ourCommissionModel->whereBetween('created_at', [$startDate, $endDate]);

        if ($request->has('supplier_id'))
            $commissions->where('supplier_id', $request->get('supplier_id'));

        if ($request->has('obtained'))
            $commissions->where('obtained', $request->get('obtained'));
            
        $commissions = $commissions->get();
        $commissions->each(function ($commission) {
            $commission->load(['order', 'supplier']);
            $commission->supplier->load('user');
        });

        $commissions = [
            'total'   => $commissions->sum('amount'),
            'details' => $commissions->groupBy('supplier_id')
        ];

        return response()->json([
            'success' => true,
            'commissions' => $commissions
        ]);
    }

    public function obtainSupplierCommission(Request $request, int $id)
    {
        $authUser = auth()->user()->userData;
        if ($authUser->cannot('obtain', OurCommission::class))
            throw new ForbiddenException;

        $commission = OurCommission::find($id);
        if (! $commission)
            throw new ValidationError(['Commission record not found']);

        $commission->obtained = true;
        $commission->obtained_by = $authUser->id;
        $commission->save();

        return response()->json([
            'success' => true,
            'message' => 'Commission obtained successfully'
        ]);
    }

    public function getSellersCommissions(Request $request)
    {
        $authUser = auth()->user()->userData;
        if (! $authUser->isAdmin() && ! $authUser->isSeller())
            throw new ForbiddenException;

        if ($authUser->isAdmin() && $authUser->cannot('viewAny', DuesOfSeller::class))
            throw new ForbiddenException;

        $validation = Validator::make($request->all(), [
            'seller_id'     => 'integer|min:0',
            'withdrawn'     => 'integer|between:0,1',
            'start_date'    => 'date_format:Y-m-d',
            'end_date'      => 'date_format:Y-m-d',
        ]);

        if ($validation->fails())
            throw new ValidationError($validation->errors()->all());

        $startDate = $request->has('start_date') ? Carbon::create($request->get('start_date')) : Carbon::create(1990);
        $endDate = $request->has('end_date') ? Carbon::create($request->get('end_date')) : now();

        $duesModel = $authUser->isAdmin() ? new DuesOfSeller : $authUser->userInfo->dues();
        $commissions = $duesModel->with('seller')
        ->where('is_commission', true)
        ->whereBetween('created_at', [$startDate, $endDate]);

        if ($request->has('seller_id'))
            $commissions->where('seller_id', $request->get('seller_id'));

        if ($request->has('withdrawn'))
            $commissions->where('was_withdrawn', $request->get('withdrawn'));

        $commissions = $commissions->get();
        $commissions->each(function ($commission) {
            $commission->seller->load('user');
        });

        $commissions = [
            'total' => $commissions->sum('cash'),
            'details' => $commissions->groupBy('seller_id')
        ];

        return response()->json([
            'success' => true,
            'commissions' => $commissions
        ]);
    }

    public function withdrawSellerCommissions(Request $request, int $id)
    {
        $authUser = auth()->user()->userData;
        if ($authUser->cannot('withdraw', DuesOfSeller::class))
            throw new ForbiddenException;

        $seller = Seller::find($id);

        if (! $seller)
            throw new ValidationError(['seller id is invalid']);
        
        $seller->dues()->where('was_withdrawn', false)->update(['was_withdrawn' => true]);
        return response()->json([
            'success' => true
        ]);
    }
}
