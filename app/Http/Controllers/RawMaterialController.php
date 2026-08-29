<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRawMaterialRequest;
use App\Http\Requests\UpdateRawMaterialRequest;
use App\Models\RawMaterial;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RawMaterialController extends Controller
{
    public function index(Request $request): View
    {
        $rawMaterials = RawMaterial::query()
            ->when($request->search, function ($query, string $search): void {
                $query->where(function ($q) use ($search): void {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                        ->orWhere('category', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('raw_materials.index', compact('rawMaterials'));
    }

    public function create(): View
    {
        return view('raw_materials.create');
    }

    public function store(StoreRawMaterialRequest $request): RedirectResponse
    {
        RawMaterial::create($request->validated());

        return redirect()->route('raw-materials.index')
            ->with('success', 'Materia prima creada correctamente.');
    }

    public function edit(RawMaterial $rawMaterial): View
    {
        return view('raw_materials.edit', compact('rawMaterial'));
    }

    public function update(UpdateRawMaterialRequest $request, RawMaterial $rawMaterial): RedirectResponse
    {
        $rawMaterial->update($request->validated());

        return redirect()->route('raw-materials.index')
            ->with('success', 'Materia prima actualizada correctamente.');
    }

    public function destroy(RawMaterial $rawMaterial): RedirectResponse
    {
        $rawMaterial->delete();

        return redirect()->route('raw-materials.index')
            ->with('success', 'Materia prima eliminada correctamente.');
    }
}