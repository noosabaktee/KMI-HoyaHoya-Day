<?php

namespace App\Http\Controllers\Umkm;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\LogActivity;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $merchant = Auth::user()->merchant;
        if (!$merchant) abort(403);

        $search = $request->input('search');
        $categoryId = $request->input('category_id');

        $query = Product::where('intMerchant_ID', $merchant->intMerchant_ID)
            ->where('bitActive', 1)
            ->with('category');

        if (!empty($search)) {
            $query->where('txtProductName', 'LIKE', "%{$search}%");
        }

        if (!empty($categoryId)) {
            $query->where('intCategory_ID', $categoryId);
        }

        $products = $query->orderBy('txtProductName')->paginate(12)->withQueryString();

        $categories = Category::where('intMerchant_ID', $merchant->intMerchant_ID)
            ->where('bitActive', 1)
            ->get();

        return view('umkm.products.index', compact('products', 'categories', 'merchant', 'search', 'categoryId'));
    }

    public function store(Request $request)
    {
        $merchant = Auth::user()->merchant;
        if (!$merchant) abort(403);

        if ($request->has('price')) {
            $rawPrice = preg_replace('/\D/', '', (string) $request->input('price'));
            $request->merge(['price' => $rawPrice !== '' ? (float) $rawPrice : null]);
        }
        if ($request->has('stock')) {
            $rawStock = preg_replace('/\D/', '', (string) $request->input('stock'));
            $request->merge(['stock' => $rawStock !== '' ? (int) $rawStock : null]);
        }

        $validated = $request->validate([
            'product_name' => 'required|string|max:150',
            'category_id' => 'nullable|integer',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'unit' => 'nullable|string|max:30',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'new_category' => 'nullable|string|max:100',
        ]);

        $categoryId = $validated['category_id'] ?? null;
        if (!empty($validated['new_category'])) {
            $cat = Category::firstOrCreate([
                'intMerchant_ID' => $merchant->intMerchant_ID,
                'txtCategoryName' => trim($validated['new_category']),
            ], [
                'txtCategorySlug' => Str::slug($validated['new_category']),
                'bitActive' => 1,
            ]);
            $categoryId = $cat->intCategory_ID;
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $code = 'PRD-' . strtoupper(Str::random(5));

        Product::create([
            'intMerchant_ID' => $merchant->intMerchant_ID,
            'intCategory_ID' => $categoryId,
            'txtProductCode' => $code,
            'txtProductName' => $validated['product_name'],
            'floatPrice' => $validated['price'],
            'intStock' => $validated['stock'],
            'txtUnit' => $validated['unit'] ?? 'Pcs',
            'txtProductImage' => $imagePath,
            'txtDescription' => $validated['description'] ?? null,
            'bitActive' => 1,
        ]);

        LogActivity::record('CREATE_PRODUCT', "Menambahkan produk {$validated['product_name']}.");

        return redirect()->route('umkm.products.index')->with('success', "Produk '{$validated['product_name']}' berhasil ditambahkan!");
    }

    public function update(Request $request, int $id)
    {
        $merchant = Auth::user()->merchant;
        $product = Product::where('intProduct_ID', $id)
            ->where('intMerchant_ID', $merchant->intMerchant_ID)
            ->firstOrFail();

        if ($request->has('price')) {
            $rawPrice = preg_replace('/\D/', '', (string) $request->input('price'));
            $request->merge(['price' => $rawPrice !== '' ? (float) $rawPrice : null]);
        }
        if ($request->has('stock')) {
            $rawStock = preg_replace('/\D/', '', (string) $request->input('stock'));
            $request->merge(['stock' => $rawStock !== '' ? (int) $rawStock : null]);
        }

        $validated = $request->validate([
            'product_name' => 'required|string|max:150',
            'category_id' => 'nullable|integer',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'unit' => 'nullable|string|max:30',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        $imagePath = $product->txtProductImage;
        if ($request->hasFile('image')) {
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $product->update([
            'intCategory_ID' => $validated['category_id'] ?? null,
            'txtProductName' => $validated['product_name'],
            'floatPrice' => $validated['price'],
            'intStock' => $validated['stock'],
            'txtUnit' => $validated['unit'] ?? 'Pcs',
            'txtProductImage' => $imagePath,
            'txtDescription' => $validated['description'] ?? null,
        ]);

        LogActivity::record('UPDATE_PRODUCT', "Memperbarui produk {$product->txtProductName}.");

        return redirect()->route('umkm.products.index')->with('success', "Produk '{$product->txtProductName}' berhasil diperbarui.");
    }

    public function updateStock(Request $request, int $id)
    {
        $merchant = Auth::user()->merchant;
        $product = Product::where('intProduct_ID', $id)
            ->where('intMerchant_ID', $merchant->intMerchant_ID)
            ->firstOrFail();

        if ($request->has('stock')) {
            $rawStock = preg_replace('/\D/', '', (string) $request->input('stock'));
            $request->merge(['stock' => $rawStock !== '' ? (int) $rawStock : null]);
        }

        $validated = $request->validate([
            'stock' => 'required|integer|min:0',
        ]);

        $product->update(['intStock' => $validated['stock']]);

        return redirect()->route('umkm.products.index')->with('success', "Stok produk '{$product->txtProductName}' diubah menjadi {$validated['stock']}.");
    }

    public function destroy(int $id)
    {
        $merchant = Auth::user()->merchant;
        $product = Product::where('intProduct_ID', $id)
            ->where('intMerchant_ID', $merchant->intMerchant_ID)
            ->firstOrFail();

        $product->update(['bitActive' => 0]);

        LogActivity::record('DELETE_PRODUCT', "Menghapus produk {$product->txtProductName}.");

        return redirect()->route('umkm.products.index')->with('success', "Produk '{$product->txtProductName}' berhasil dihapus.");
    }
}

