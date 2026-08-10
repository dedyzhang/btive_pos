<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\Products;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() : View
    {
        $categories = Categories::with('products')->orderBy('sort','asc')->get();
        $products = Products::orderBy('created_at','asc')->get();
        return view('products.index', compact('categories','products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() : View
    {
        $categories = Categories::OrderBy('sort','asc')->get();
        return view('products.create',compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'categories' => 'required',
            'price' => 'required',
            'cost_price' => 'required',
            'stock' => 'required',
        ]);

        $filename = "";
        $croppedData = $request->input('cropped_image_data');
        if ($croppedData) {
            $imageParts = explode(";base64,", $croppedData);
            $imageBase64 = base64_decode($imageParts[1]);
            $filename = \Illuminate\Support\Str::random(40) . '.png';
            $path = storage_path('app/public/products/' . $filename);
            if (!file_exists(dirname($path))) {
                mkdir(dirname($path), 0777, true);
            }
            file_put_contents($path, $imageBase64);
        } else {
            $file = $request->file('picture');
            if($file != null ) {
                $filename = $file->hashName();
                $file->storeAs('products', $filename);
            }
        }

        Products::create([
            'name' => $request->name,
            'category_id' => $request->categories,
            'price' => $request->price,
            'cost_price' => $request->cost_price,
            'stock' => $request->stock,
            'description' => $request->description,
            'picture' => $filename,
            'is_active' => 0,
        ]);

        return redirect()->route('products.index')->with('success','Sucessfully Add Product');
    }

    /**
     * Display the specified resource.
     */
    public function show(String $uuid)
    {
        $products = Products::with('category')->findOrFail($uuid);

        return response()->json([
            'success' => true,
            'product' => $products
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(String $uuid) : View
    {
        $product = Products::findOrFail($uuid);
        $categories = Categories::OrderBy('sort','asc')->get();

        return view('products.edit',compact('product','categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, String $uuid)
    {
        $product = Products::findOrFail($uuid);

        $request->validate([
            'name' => 'required',
            'categories' => 'required',
            'price' => 'required',
            'cost_price' => 'required',
            'stock' => 'required',
        ]);
        
        $filename = $product->picture;
        $croppedData = $request->input('cropped_image_data');

        if ($croppedData) {
            if ($product->picture != "") {
                $oldPath = storage_path('app/public/products/') . $product->picture;
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }
            $imageParts = explode(";base64,", $croppedData);
            $imageBase64 = base64_decode($imageParts[1]);
            $filename = \Illuminate\Support\Str::random(40) . '.png';
            $path = storage_path('app/public/products/' . $filename);
            if (!file_exists(dirname($path))) {
                mkdir(dirname($path), 0777, true);
            }
            file_put_contents($path, $imageBase64);
        } else if ($file = $request->file('picture')) {
            if ($product->picture != "") {
                $oldPath = storage_path('app/public/products/') . $product->picture;
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }
            $filename = $file->hashName();
            $file->storeAs('products', $filename);
        } else if ($request->is_deleted == 1) {
            if ($product->picture != "") {
                $oldPath = storage_path('app/public/products/') . $product->picture;
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }
            $filename = "";
        }

        $product->update([
            'name' => $request->name,
            'category_id' => $request->categories,
            'price' => $request->price,
            'cost_price' => $request->cost_price,
            'stock' => $request->stock,
            'description' => $request->description,
            'picture' => $filename
        ]);

        return redirect()->route('products.index')->with('success','Successfully Edit Product');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(String $uuid)
    {
        $product = Products::findOrFail($uuid);

        if($product->picture != "") {
            $oldPath = storage_path('app/public/products/') . $product->picture;
            unlink($oldPath);
        }
        $product->delete();

        return response()->json(['success' => true, 'message' => 'Successfully Delete Product']);
    }
    /**
     * Toggle products is active or not
     */
    public function activeToggle(String $uuid,Request $request) {
        $product = Products::findOrFail($uuid);

        $active = $request->active;
        if($active == "true") {
            $active = 1;
        } else {
            $active = 0;
        }

        $product->update([
            'is_active' => $active,
        ]);

        return response()->json(['success'=> true]);
    }
}
