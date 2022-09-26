<?php

namespace App\Http\Controllers\Sales;

use App\Exceptions\ForbiddenException;
use App\Exceptions\NotFoundException;
use App\Exceptions\ValidationError;
use App\Http\Controllers\Controller;
use App\Models\AppConfig;
use App\Models\JourneyPlan;
use App\Models\Seller;
use Ayeo\Geo\Coordinate\Decimal;
use Ayeo\Geo\DistanceCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class JourneyPlansController extends Controller
{
    public function create(Request $request)
    {
        $authUser = auth()->user()->userData;
        if ($authUser->cannot('create', JourneyPlan::class))
            throw new ForbiddenException;

        $validation = Validator::make($request->all(), [
            'seller_id'                 => 'required|integer|exists:sellers,id',
            'journey_plan'              => 'required|array|min:1',
            'journey_plan.*.date'       => 'required|date_format:Y-m-d',
            'journey_plan.*.coords'     => 'required|regex:/^[0-9\.]+\,[0-9\.]+$/'
        ]);

        if ($validation->fails())
            throw new ValidationError($validation->errors()->all());

        $seller = Seller::find($request->get('seller_id'));
        if ($seller->is_freelancer)
            throw new ValidationError(['seller is freelancer']);

        $journeyPlans = collect($request->get('journey_plan'))
        ->map(function ($day) {
            $journeyPlan = new JourneyPlan;
            $journeyPlan->date = $day['date'];
            $journeyPlan->location_coords = $day['coords'];
            return $journeyPlan;
        });

        $journeyPlans = $seller->journeyPlans()->saveMany($journeyPlans);

        return response()->json([
            'success' => true,
            'journey_plans' => $journeyPlans
        ]);
    }

    public function visit(Request $request)
    {
        $authUser = auth()->user()->userData;
        if (! $authUser->isSeller())
            throw new ForbiddenException;

        $validation = Validator::make($request->all(), [
            'id'     => 'required|integer',
            'coords' => ['required', 'regex:/^[-+]?([1-8]?\d(\.\d+)?|90(\.0+)?),\s*[-+]?(180(\.0+)?|((1[0-7]\d)|([1-9]?\d))(\.\d+)?)$/'],
        ]);

        if ($validation->fails())
            throw new ValidationError($validation->errors()->all());

        $journeyPlan = $authUser->userInfo->journeyPlans()->find($request->get('id'));
        if (! $journeyPlan)
            throw new NotFoundException(JourneyPlan::class, $request->get('id'));

        $sellerCoords = explode(',', $request->get('coords'));
        $placeCoords  = explode(',', $journeyPlan->location_coords);

        $sellerCoords = new Decimal($sellerCoords[0], $sellerCoords[1]);
        $placeCoords  = new Decimal($placeCoords[0], $placeCoords[1]);

        $distanceCalculator = new DistanceCalculator();
        $currentDistance = $distanceCalculator->getDistance($sellerCoords, $placeCoords);
        $allowedDistance = AppConfig::journyPlanDistanceToMarkVisit();

        if ($currentDistance > $allowedDistance)
            throw new ForbiddenException('You can\'t visit from current location');
            
        $journeyPlan->has_been_visited = true;
        $journeyPlan->visited_at = now();

        return response()->json([
            'success' => true,
            'message' => 'Place has been visited'
        ]);
    }

    public function get(Request $request)
    {
        $authUser = auth()->user()->userData;
        if (! $authUser->isAdmin() && ! $authUser->isSeller())
            throw new ForbiddenException;

        if ($authUser->isAdmin() && $authUser->cannot('viewAny', JourneyPlan::class))
            throw new ForbiddenException;

        $validation = Validator::make($request->all(), [
            'seller_id'     => 'integer|exists:sellers,id',
            'start_date'    => 'date_format:Y-m-d',
            'end_date'      => 'date_format:Y-m-d',
            'visited'       => 'integer|between:0,1'
        ]);

        if ($validation->fails())
            throw new ValidationError($validation->errors()->all());

        $startDate = $request->has('start_date') ? Carbon::create($request->get('start_date')) : Carbon::create(1990);
        $endDate = $request->has('end_date') ? Carbon::create($request->get('end_date')) : Carbon::now();

        $sellers = $authUser->isAdmin() ? Seller::get() : collect([$authUser->userInfo]);
        
        if ($request->has('seller_id'))
            $sellers = $sellers->where('id', $request->get('seller_id'));

        $sellers = $sellers->map(function ($seller) use ($request, $startDate, $endDate) {
            $seller->load([
                'user',
                'journeyPlans' => function ($query) use ($request, $startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                    if ($request->has('visited'))
                        $query->where('has_been_visited', $request->get('visited'));
                },
            ]);

            return $seller;
        });

        return response()->json([
            'success' => true,
            'sellers' => $sellers
        ]);
    }

    public function delete(Request $request)
    {
        $authUser = auth()->user()->userData;
        if ($authUser->cannot('delete', JourneyPlan::class))
            throw new ForbiddenException;

        $validation = Validator::make($request->all(), [
            'ids' => 'array|min:1',
            'ids.*' => 'integer|min:0'
        ]);

        if ($validation->fails())
            throw new ValidationError($validation->errors()->all());

        JourneyPlan::whereIn('id', $request->get('ids'))->delete();
        return response()->json([
            'success' => true
        ]);
    }
}
