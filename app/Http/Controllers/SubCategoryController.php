<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;

class SubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $titlePage = __('cate.sub_cate');
        $categories = Category::all();
        $sub_categories = SubCategory::all();
        return view('category.sub_category.index', compact('titlePage','categories','sub_categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $titlePage = __('cate.sub_cate');
        $categories = Category::all();
        return view('category.sub_category.entry', compact('titlePage','categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'select' => 'required'
        ]);
        //dd($data);

        // Check if an image has been uploaded
        if ($request->hasFile('avatar')) {
            // Store the uploaded image and get its path
            $img_path = $request->file('avatar')->store('cate/sub_cate','public');
        } else {
            // If no image is uploaded, set the path to null
            $img_path = null;
        }

        SubCategory::create([
            'name' => $data['name'],
            'img_path' => $img_path,
            'category_id' => $data['select'],
        ]);

        return redirect()->back()->with('success', __('nav.crt_alt'));
    }

    /**
     * Display the specified resource.
     */
    public function show(SubCategory $subCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SubCategory $subCategory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SubCategory $subCategory)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SubCategory $subCategory)
    {
        //
    }
}
