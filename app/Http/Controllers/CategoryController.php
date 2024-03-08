<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $titlePage = __('cate.cate');
        $categories = Category::all();
        return view('category.index', compact('titlePage','categories'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Check if an image has been uploaded
        if ($request->hasFile('avatar')) {
            // Store the uploaded image and get its path
            $img_path = $request->file('avatar')->store('cate','public');
        }

        Category::create([
            'name' => $data['name'],
            'img_path' => $img_path,
        ]);

        return redirect()->back()->with('success', __('nav.crt_alt'));
    }
    /**
     * Displaying Category list in Admin Side
     */
    public function listingV1() {
        $titlePage = __('cate.cate_lst');
        $categories = Category::all();
        return view('category.list_V1', compact('titlePage', 'categories'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * This function aims to show specific resource in modify page.
     *  @param Category $category
     */
    public function showV2(Category $category){
        $titlePage = __('cate.cate_mod');
        $category = Category::findOrFail($category->id);
        return view('category.modify', compact('titlePage', 'category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            $avatarPath = $avatar->store('cate', 'public');
            $validatedData['img_path'] = $avatarPath;
        }
        $category->update($validatedData);

        return redirect()->route('category.lst_V1')->with('success', __('jobapplication.job_update_alert'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->back()->with('success', 'Deleted successfully.');
    }
}
