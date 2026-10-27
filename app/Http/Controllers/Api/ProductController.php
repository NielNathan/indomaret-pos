<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    // GET /api/products?q=indomie&category_id=3&per_page=15
    public function index(Request $request)
    {
        return Product::with('category:id,name')
            ->when($request->q, fn ($q, $s) => $q->where(fn ($w) => $w->where('name', 'like', "%$s%")->orWhere('sku', 'like', "%$s%")))
            ->when($request->category_id, fn ($q, $c) => $q->where('category_id', $c))
            ->orderBy('name')
            ->paginate(min((int) $request->get('per_page', 15), 100));
    }

    public function show(Product $product)
    {
        return response()->json(['data' => $product->load('category:id,name')]);
    }

    public function store(Request $request)
    {
        $this->adminOnly($request);
        $product = Product::create($this->validated($request));
        return response()->json(['data' => $product], 201);
    }

    public function update(Request $request, Product $product)
    {
        $this->adminOnly($request);
        $product->update($this->validated($request, $product));
        return response()->json(['data' => $product]);
    }

    public function destroy(Request $request, Product $product)
    {
        $this->adminOnly($request);
        $product->delete();
        return response()->json(['message' => 'Produk dihapus.']);
    }

    private function adminOnly(Request $request): void
    {
        abort_unless($request->user()->isAdmin(), 403, 'Hanya admin yang dapat mengubah produk.');
    }

    private function validated(Request $request, ?Product $product = null): array
    {
        return $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'sku' => ['required', 'string', 'max:30', Rule::unique('products', 'sku')->ignore($product)],
            'name' => ['required', 'string', 'max:120'],
            'price' => ['required', 'integer', 'min:0'],
        ]);
    }
}
