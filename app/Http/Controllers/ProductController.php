<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBillOfMaterialRequest;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\BillOfMaterial;
use App\Models\Product;
use App\Models\RawMaterial;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $products = Product::query()
            ->when($request->search, function ($query, string $search): void {
                $query->where(function ($q) use ($search): void {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                        ->orWhere('model', 'like', "%{$search}%")
                        ->orWhere('category', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('products.index', compact('products'));
    }

    public function create(): View
    {
        return view('products.create');
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $this->storeImage($request->file('image'));
        }

        Product::create($data);

        return redirect()->route('products.index')
            ->with('success', 'Producto creado correctamente.');
    }

    public function show(Product $product): View
    {
        $product->load('rawMaterials');
        $availableMaterials = RawMaterial::whereNotIn('id', $product->rawMaterials->pluck('id'))
            ->orderBy('name')
            ->get();

        return view('products.show', compact('product', 'availableMaterials'));
    }

    public function edit(Product $product): View
    {
        return view('products.edit', compact('product'));
    }

    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $this->storeImage($request->file('image'));
        }

        $product->update($data);

        return redirect()->route('products.index')
            ->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Producto eliminado correctamente.');
    }

    /**
     * Store the uploaded image and return the relative path.
     */
    private function storeImage($file): string
    {
        $extension = $file->getClientOriginalExtension();
        $filename = (string) \Illuminate\Support\Str::uuid() . '.' . $extension;
        $path = $file->storeAs('products', $filename, 'public');

        return $path;
    }

    public function addMaterial(StoreBillOfMaterialRequest $request, Product $product): RedirectResponse
    {
        BillOfMaterial::create([
            'product_id' => $product->id,
            'raw_material_id' => $request->validated('raw_material_id'),
            'quantity' => $request->validated('quantity'),
            'unit' => $request->validated('unit'),
            'notes' => $request->validated('notes'),
        ]);

        return redirect()->route('products.show', $product)
            ->with('success', 'Materia prima agregada a la lista de materiales.');
    }

    public function removeMaterial(Product $product, RawMaterial $material): RedirectResponse
    {
        BillOfMaterial::where('product_id', $product->id)
            ->where('raw_material_id', $material->id)
            ->delete();

        return redirect()->route('products.show', $product)
            ->with('success', 'Materia prima eliminada de la lista de materiales.');
    }
}