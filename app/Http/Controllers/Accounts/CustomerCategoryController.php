<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Models\CustomerCategory;
use App\Rules\ArabicLetters;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerCategoryController extends Controller
{
    /**
     * Create new category
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        if (! auth()->user()->userData->can('create', CustomerCategory::class))
            throw new \App\Exceptions\ForbiddenException;

        $validation = Validator::make($request->all(), [
            'category_name' => 'required|regex:/^[a-zA-Z]{3,20}$/',
            'category_name_ar' => ['required', new ArabicLetters, 'min:3', 'max:20']
        ]);

        if ($validation->fails())
            throw new \App\Exceptions\ValidationError($validation->errors()->all());

        $category = new CustomerCategory;
        $category->category_name = $request->get('category_name');
        $category->category_name_ar = $request->get('category_name_ar');

        try {
            $category->save();
        } catch (QueryException $e) {
            throw new \App\Exceptions\DBException($e);
        }

        return response()->json(['success' =>true, 'data' => $category]);
    }

    /**
     * get category by id
     *
     * @param [type] $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getById($id)
    {
        $category = CustomerCategory::where('id', $id)->first();

        if (! $category)
            return response()->json(['success' => false], 404);
        
        return response()->json(['success' =>true, 'data'=>$category]);
    }

    /**
     * get category by name
     *
     * @param [type] $name
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByName($name)
    {
        $category = CustomerCategory::where('category_name', $name)->first();

        if (! $category)
            return response()->json(['success' => false], 404);
        
        return response()->json(['success' =>true, 'data'=>$category]);
    }

    /**
     * get category by arabic name
     *
     * @param [type] $name
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByNameAR($name)
    {
        $category = CustomerCategory::where('category_name_ar', $name)->first();

        if (! $category)
            return response()->json(['success' => false], 404);
        
        return response()->json(['success' =>true, 'data'=>$category]);
    }

    /**
     * get all categories
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAll()
    {
        $categories = CustomerCategory::get()->all();
        return response()->json(['success' =>true, 'data'=>$categories]);
    }

    /**
     * Update any category by id
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'id' => 'required|regex:/^[0-9]+$/',
            'category_name' => 'required_without:category_name_ar|regex:/^[a-zA-Z]{3,20}$/',
            'category_name_ar' => ['required_without:category_name', new ArabicLetters, 'min:3', 'max:20']
        ]);

        if ($validation->fails())
            throw new \App\Exceptions\ValidationError($validation->errors()->all());

        $category = CustomerCategory::where('id', $request->get('id'))->first();

        if (! $category)
            return response()->json(['success' => false], 404);

        if ($request->has('category_name'))
            $category->category_name = $request->get('category_name');
        
        if ($request->has('category_name'))
            $category->category_name = $request->get('category_name_ar');

        return response()->json(['success' =>true]);
    }
}
