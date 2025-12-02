<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('categories.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // This method handles traditional form submission
        // Most submissions should go through API instead
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'required|in:income,expense'
            ]);

            // Check if category already exists for this user
            $existingCategory = Category::where('user_id', Auth::id())
                ->where('name', $request->name)
                ->where('type', $request->type)
                ->first();

            if ($existingCategory) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['name' => 'Kategori dengan nama dan jenis ini sudah ada.']);
            }

            $category = Category::create([
                'user_id' => Auth::id(),
                'name' => $request->name,
                'type' => $request->type
            ]);

            return redirect()->route('categories.index')
                ->with('success', 'Kategori berhasil ditambahkan!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors($e->errors());
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan kategori. Silakan coba lagi.');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $category = Category::where('user_id', Auth::id())->findOrFail($id);
            return view('categories.show', compact('category'));
        } catch (\Exception $e) {
            return redirect()->route('categories.index')
                ->with('error', 'Kategori tidak ditemukan.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
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

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // This method handles traditional form submission
        // Most updates should go through API instead
        try {
            $category = Category::where('user_id', Auth::id())->findOrFail($id);

            $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'required|in:income,expense'
            ]);

            // Check if another category with same name and type exists
            $existingCategory = Category::where('user_id', Auth::id())
                ->where('name', $request->name)
                ->where('type', $request->type)
                ->where('id', '!=', $id)
                ->first();

            if ($existingCategory) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['name' => 'Kategori dengan nama dan jenis ini sudah ada.']);
            }

            $category->update([
                'name' => $request->name,
                'type' => $request->type
            ]);

            return redirect()->route('categories.index')
                ->with('success', 'Kategori berhasil diperbarui!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors($e->errors());
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal memperbarui kategori. Silakan coba lagi.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // This method handles traditional form submission
        // Most deletions should go through API instead
        try {
            $category = Category::where('user_id', Auth::id())->findOrFail($id);

            // Check if category is being used in transactions
            if ($category->transactions()->count() > 0) {
                return redirect()->route('categories.index')
                    ->with('error', 'Kategori tidak dapat dihapus karena sudah digunakan dalam transaksi.');
            }

            $category->delete();

            return redirect()->route('categories.index')
                ->with('success', 'Kategori berhasil dihapus!');

        } catch (\Exception $e) {
            return redirect()->route('categories.index')
                ->with('error', 'Gagal menghapus kategori. Silakan coba lagi.');
        }
    }
}
