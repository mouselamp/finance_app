<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;

/**
 * CategoryController - Web Controller
 *
 * This controller only handles VIEW RENDERING.
 * All CRUD operations (store, update, destroy) are handled by ApiCategoryController.
 */
class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of categories.
     */
    public function index()
    {
        return view('categories.index');
    }

    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit($id)
    {
        try {
            $category = Category::where('user_id', Auth::id())->findOrFail($id);
            return view('categories.edit', compact('category'));
        } catch (\Exception $e) {
            return redirect()->route('categories.index')
                ->with('error', 'Kategori tidak ditemukan.');
        }
    }

    // NOTE: store(), update(), destroy(), show() methods are NOT needed here.
    // All CRUD operations are handled via API (ApiCategoryController).
}
