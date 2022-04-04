<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\InventoryCategory;
use App\Rules\ArabicLetters;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{

    public function createCategory(Request $request)
    {
        $authUser = auth()->user()->userData;
        // check if user can manage inventory categories
        if (! $authUser->can('manage', InventoryCategory::class))
            throw new \App\Exceptions\ForbiddenException();

        $rules = [
            'category_name'         => 'required|regex:/^\w{3,20}$/',
            'category_name_ar'      => ['required', new ArabicLetters, 'min:3', 'max:20'],
            'description'           => 'regex:/^[\w\W]{3,255}$/',
            'description_ar'        => 'regex:/^[\x{0621}-\x{064A}\W]{3,255}$/u',
            'parent_category_id'    => 'numeric|exists:inventory_categories,id',
        ];

        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails())
            throw new \App\Exceptions\ValidationError($validation->errors()->all());

        $category = new InventoryCategory();
        $category->category_name = $request->get('category_name');
        $category->category_name_ar = $request->get('category_name_ar');
        $category->description = $request->get('description');
        $category->description_ar = $request->get('description_ar');
        $category->parent_category_id = $request->get('parent_category_id');
        $category->created_by = $authUser->id;
        $category->updated_by = $authUser->id;
        // try to save the category
        try {
            $category->save();
        } catch (QueryException $e) {
            throw new \App\Exceptions\DBException($e);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Category created successfully',
            'category' => $category
        ]);
    }
    
    public function updateCategory(Request $request, $id)
    {
        $authUser = auth()->user()->userData;
        // check if user can manage inventory categories
        if (! $authUser->can('manage', InventoryCategory::class))
            throw new \App\Exceptions\ForbiddenException();

        $rules = [
            'category_name'         => 'required|regex:/^\w{3,20}$/',
            'category_name_ar'      => ['required', new ArabicLetters, 'min:3', 'max:20'],
            'description'           => 'regex:/^[\w\W]{3,255}$/',
            'description_ar'        => 'regex:/^[\x{0621}-\x{064A}\W]{3,255}$/u',
            'parent_category_id'    => 'numeric|exists:inventory_categories,id'
        ];

        $validation = Validator::make($request->all(), $rules);
        if ($validation->fails())
            throw new \App\Exceptions\ValidationError($validation->errors()->all());

        $category = InventoryCategory::find($id);
        $category->category_name = $request->get('category_name');
        $category->category_name_ar = $request->get('category_name_ar');
        $category->description = $request->get('description');
        $category->description_ar = $request->get('description_ar');
        $category->parent_category_id = $request->get('parent_category_id');
        $category->updated_by = $authUser->id;
        // try to save the category
        try {
            $category->save();
        } catch (QueryException $e) {
            throw new \App\Exceptions\DBException($e);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Category updated successfully',
            'category' => $category
        ]);
    }

    // get all categories
    public function getCategories()
    {
        $categories = InventoryCategory::where('parent_category_id', null)->get()->all();

        if ($authUser = auth()->user()) {
            $authUser = $authUser->userData;
            if ($authUser->can('manage', InventoryCategory::class)) {
                // show hiddens for each category
                foreach ($categories as $category) {
                    $category->showHiddens();
                }
            }
        }

        // with children for each category
        foreach ($categories as $category)
            $category->withChildren();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Categories retrieved successfully',
            'categories' => $categories
        ]);
    }

    // get a category with his brands
    public function getCategory(int $id)
    {
        $category = InventoryCategory::find($id);
        
        // handle not found category
        if (! $category)
            throw new \App\Exceptions\NotFoundException(InventoryCategory::class, $id);

        if ($authUser = auth()->user()) {
            $authUser = $authUser->userData;
            if ($authUser->can('manage', InventoryCategory::class)) {
                // show hiddens for each category
                $category->showHiddens();
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Category retrieved successfully',
            'category' => $category->withFullData()
        ]);
    }
}
