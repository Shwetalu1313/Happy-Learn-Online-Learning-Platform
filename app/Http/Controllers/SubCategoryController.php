<?php

namespace App\Http\Controllers;

use App\Enums\UserRoleEnums;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\SystemActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        $subcategory = SubCategory::create([
            'name' => $data['name'],
            'img_path' => $img_path ?? 'cate/sample.jpg',
            'category_id' => $data['select'],
        ]);


        if ($subcategory){
            $systemActivity = [
                'table_name' => Category::getModelName(),
                'ip_address' => $request->getClientIp(),
                'user_agent' => $request->userAgent(),
                'user_id' => auth()->id(),
                'short' => 'A new sub-category ('.$subcategory->name.') is created.',
                'about' => 'A new sub-category ('.$subcategory->name.') is created for ('.$subcategory->name.') by '. Auth::user()->name . '('.auth()->id().').',
                'target' => UserRoleEnums::ADMIN,
                'route_name' => $request->route()->getName(),
            ];
            SystemActivity::createActivity($systemActivity);

            return redirect()->back()->with('success', __('nav.crt_alt'));
        }else
        {
            return redirect()->back()->with('error', 'Data input process failed.');
        }


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
    public function edit(SubCategory $sub_category)
    {
        $titlePage = $sub_category->name . ' Modify';
        $categories = Category::all();

        return view('category.sub_category.modify', compact('titlePage','sub_category','categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SubCategory $subCategory)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'category_id' => 'required|integer',
        ]);
        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            $avatarPath = $avatar->store('cate', 'public');
            $validatedData['img_path'] = $avatarPath;
        }
        $updated = $subCategory->update($validatedData);

        if ($updated){
            $systemActivity = [
                'table_name' => Category::getModelName(),
                'ip_address' => $request->getClientIp(),
                'user_agent' => $request->userAgent(),
                'user_id' => auth()->id(),
                'short' => 'A new category ('.$subCategory->name.') is updated.',
                'about' => 'A new category ('.$subCategory->name.') is updated by '. Auth::user()->name . '('.auth()->id().').',
                'target' => UserRoleEnums::ADMIN,
                'route_name' => $request->route()->getName(),
            ];
            SystemActivity::createActivity($systemActivity);

            return redirect()->route('category.sub_category.index')->with('success', __('jobapplication.job_update_alert'));

        }else {
            return redirect()->back()->with('error', 'Data input process failed.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SubCategory $subCategory, Request $request)
    {
        if ($subCategory->delete()){
            $systemActivity = [
                'table_name' => Category::getModelName(),
                'ip_address' => $request->getClientIp() ,
                'user_agent' => $request->userAgent(),
                'user_id' => auth()->id(),
                'short' => 'A new category ('.$subCategory->name.') was deleted.',
                'about' => 'A new category ('.$subCategory->name.') id = ('.$subCategory->id.') was deleted by '. Auth::user()->name . '('.auth()->id().').',
                'target' => UserRoleEnums::ADMIN,
                'route_name' => $request->route()->getName(),
            ];
            SystemActivity::createActivity($systemActivity);

            return redirect()->back()->with('success', 'Deleted successfully.');
        }
        else {
            return redirect()->back()->with('error', 'Deleted deletion fail.');
        }

    }
}
