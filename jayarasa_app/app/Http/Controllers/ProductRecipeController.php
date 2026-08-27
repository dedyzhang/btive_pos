<?php

namespace App\Http\Controllers;

use App\Models\ProductRecipe;
use App\Models\Products;
use App\Models\SupplyItem;
use Illuminate\Http\Request;

class ProductRecipeController extends Controller
{
    /**
     * Recipe manager: pick a product, define which supplies one serving consumes.
     */
    public function index(Request $request)
    {
        $products = Products::with('category')->orderBy('name', 'asc')->get();
        $supplyItems = SupplyItem::where('is_active', true)->orderBy('name', 'asc')->get();

        // Selected product (defaults to the first one so the page is never empty-handed).
        $selectedProduct = null;
        if ($request->filled('product')) {
            $selectedProduct = Products::with(['recipes.supplyItem', 'category'])->find($request->product);
        }
        if (!$selectedProduct && $products->isNotEmpty()) {
            $selectedProduct = Products::with(['recipes.supplyItem', 'category'])->find($products->first()->uuid);
        }

        // Which products already have a recipe — shown as a badge in the picker.
        $productsWithRecipe = ProductRecipe::distinct()->pluck('product_id')->all();

        return view('product_recipe.index', compact('products', 'supplyItems', 'selectedProduct', 'productsWithRecipe'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,uuid',
            'supply_item_id' => 'required|exists:supply_items,uuid',
            'qty' => 'required|numeric|min:0.001',
        ]);

        // updateOrCreate rather than create: re-adding an existing ingredient is an edit,
        // and the table has a unique constraint that would otherwise throw.
        ProductRecipe::updateOrCreate(
            [
                'product_id' => $request->product_id,
                'supply_item_id' => $request->supply_item_id,
            ],
            ['qty' => $request->qty]
        );

        return redirect()->route('product-recipe.index', ['product' => $request->product_id])
            ->with('success_recipe', 'Bahan resep disimpan.');
    }

    public function update(Request $request, String $uuid)
    {
        $request->validate([
            'qty' => 'required|numeric|min:0.001',
        ]);

        $recipe = ProductRecipe::findOrFail($uuid);
        $recipe->update(['qty' => $request->qty]);

        return redirect()->route('product-recipe.index', ['product' => $recipe->product_id])
            ->with('success_recipe', 'Jumlah bahan diperbarui.');
    }

    public function destroy(String $uuid)
    {
        $recipe = ProductRecipe::findOrFail($uuid);
        $productId = $recipe->product_id;
        $recipe->delete();

        return redirect()->route('product-recipe.index', ['product' => $productId])
            ->with('success_recipe', 'Bahan dihapus dari resep.');
    }
}
