<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSellerRequest;
use App\Http\Requests\UpdateSellerRequest;
use App\Models\Seller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SellerController extends Controller
{
    public function index(Request $request): View
    {
        $sellers = Seller::query()
            ->when($request->search, function ($query, string $search): void {
                $query->where(function ($q) use ($search): void {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('document_number', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('city', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('sellers.index', compact('sellers'));
    }

    public function create(): View
    {
        $users = User::orderBy('name')->get();

        return view('sellers.create', compact('users'));
    }

    public function store(StoreSellerRequest $request): RedirectResponse
    {
        Seller::create($request->validated());

        return redirect()->route('sellers.index')
            ->with('success', 'Vendedor creado correctamente.');
    }

    public function edit(Seller $seller): View
    {
        $users = User::orderBy('name')->get();

        return view('sellers.edit', compact('seller', 'users'));
    }

    public function update(UpdateSellerRequest $request, Seller $seller): RedirectResponse
    {
        $seller->update($request->validated());

        return redirect()->route('sellers.index')
            ->with('success', 'Vendedor actualizado correctamente.');
    }

    public function destroy(Seller $seller): RedirectResponse
    {
        $seller->delete();

        return redirect()->route('sellers.index')
            ->with('success', 'Vendedor eliminado correctamente.');
    }
}