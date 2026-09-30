@props([
    'name' => 'product_id',
    'products' => [],
    'value' => null,
    'required' => false,
    'placeholder' => 'Seleccionar producto',
    'allowClear' => false,
])

@php
    $products = collect($products);
    $productsJson = $products
        ->map(fn ($p) => [
            'id' => $p->id,
            'code' => $p->code,
            'name' => $p->name,
            'description' => (string) $p->description,
            'image' => $p->image ? asset('storage/' . $p->image) : null,
            'price' => (float) $p->price,
        ])
        ->values()
        ->toJson();
    $selectedProduct = $value ? $products->firstWhere('id', $value) : null;
    $selectedId = $selectedProduct?->id;
    $selectedLabel = $selectedProduct ? ($selectedProduct->code . ' — ' . $selectedProduct->name) : null;
    $selectedImage = $selectedProduct?->image ? asset('storage/' . $selectedProduct->image) : null;
@endphp

<div x-data="{
    open: false,
    search: '',
    selectedId: {{ $selectedId ?? 'null' }},
    selectedLabel: {{ json_encode($selectedLabel) }},
    selectedImage: {{ json_encode($selectedImage) }},
    products: {{ $productsJson }},
    get filtered() {
        const q = this.search.trim().toLowerCase();
        if (!q) return this.products;
        return this.products.filter(p =>
            (p.name || '').toLowerCase().includes(q)
            || (p.code || '').toLowerCase().includes(q)
            || (p.description || '').toLowerCase().includes(q)
        );
    },
    money(p) {
        return new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP', minimumFractionDigits: 0 }).format(p.price || 0);
    },
    select(p) {
        this.selectedId = p.id;
        this.selectedLabel = (p.code ? p.code + ' — ' : '') + p.name;
        this.selectedImage = p.image;
        this.open = false;
    },
    clear() {
        this.selectedId = null;
        this.selectedLabel = null;
        this.selectedImage = null;
        this.open = false;
    }
}">
    <input type="hidden" name="{{ $name }}" :value="selectedId ?? ''" {{ $required ? 'required' : '' }}>

    {{-- Trigger --}}
    <button type="button" @click="open = true"
            class="group flex w-full items-center gap-3 rounded-xl border border-gray-300 bg-white px-3 py-2 text-left shadow-sm transition hover:border-brand-400 hover:ring-2 hover:ring-brand-100 focus-visible:ring-2 focus-visible:ring-brand-500">
        <span class="flex h-9 w-9 shrink-0 items-center justify-center overflow-hidden rounded-lg bg-gray-100 text-gray-400">
            <template x-if="selectedImage">
                <img :src="selectedImage" :alt="selectedLabel" class="h-full w-full object-cover">
            </template>
            <template x-if="!selectedImage">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                </svg>
            </template>
        </span>
        <span class="min-w-0 flex-1">
            <span class="block truncate text-sm font-medium text-gray-900" x-text="selectedLabel || '{{ $placeholder }}'"></span>
            <span class="block truncate text-xs text-gray-400" x-text="selectedId ? 'Producto seleccionado' : 'Haz clic para elegir'"></span>
        </span>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
             class="h-5 w-5 shrink-0 text-gray-400 transition-transform group-hover:translate-y-0.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
        </svg>
    </button>

    {{-- Modal --}}
    <div x-show="open" x-cloak @keydown.escape.window="open = false"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6">
        <div x-show="open" x-cloak x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150"
             x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="open = false" aria-hidden="true"></div>

        <div x-show="open" x-cloak @click.outside="open = false"
             x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100" x-transition:leave="ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100" x-transition:leave-end="opacity-0 translate-y-4 scale-95"
             class="relative z-10 flex max-h-[92vh] w-full max-w-[44.8rem] flex-col overflow-hidden rounded-2xl bg-white shadow-2xl ring-1 ring-black/5"
             role="dialog" aria-modal="true" aria-label="Seleccionar producto">

            {{-- Header --}}
            <div class="flex items-start gap-4 border-b border-gray-100 px-6 py-5">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-brand-600 to-brand-800 text-white shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                    </svg>
                </div>
                <div class="min-w-0 flex-1">
                    <h3 class="text-lg font-bold text-gray-900">Seleccionar producto</h3>
                    <p class="text-sm text-gray-500">Elige el producto que deseas usar.</p>
                </div>
                <span class="hidden shrink-0 rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600 sm:inline-block"
                      x-text="filtered.length + ' producto' + (filtered.length !== 1 ? 's' : '')"></span>
                <button type="button" @click="open = false"
                        class="shrink-0 rounded-lg p-1.5 text-gray-400 transition hover:bg-gray-100 hover:text-gray-600"
                        aria-label="Cerrar">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Search --}}
            <div class="border-b border-gray-100 px-6 py-4">
                <div class="relative">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                         class="pointer-events-none absolute left-3.5 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                    <input type="text" x-model="search" placeholder="Buscar por nombre, código o descripción..."
                           class="w-full rounded-xl border border-gray-200 bg-gray-50 py-2.5 pl-11 pr-10 text-sm text-gray-900 placeholder-gray-400 transition focus:border-brand-400 focus:bg-white focus:ring-2 focus:ring-brand-100">
                    <button x-show="search" x-cloak type="button" @click="search = ''" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600" aria-label="Limpiar búsqueda">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                @if ($allowClear)
                    <button type="button" @click="clear()" x-show="selectedId" x-cloak
                            class="mt-3 inline-flex items-center gap-1 text-sm font-medium text-brand-700 hover:text-brand-800">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Limpiar selección
                    </button>
                @endif
            </div>

            {{-- Grid --}}
            <div class="flex-1 overflow-y-auto overscroll-contain bg-gray-50/70 p-4 sm:p-6">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <template x-for="p in filtered" :key="p.id">
                        <button type="button" @click="select(p)"
                                class="group relative flex flex-col overflow-hidden rounded-xl border bg-white text-left shadow-sm transition-all duration-200 hover:-translate-y-0.5 hover:shadow-lg focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-2"
                                :class="selectedId == p.id ? 'border-brand-600 ring-2 ring-brand-500' : 'border-gray-200 hover:border-brand-400'">
                            <div class="relative aspect-[4/3] w-full overflow-hidden bg-gray-100">
                                <template x-if="p.image">
                                    <img :src="p.image" :alt="p.name" class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105">
                                </template>
                                <template x-if="!p.image">
                                    <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-brand-50 to-gray-100 text-brand-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-12 w-12">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                        </svg>
                                    </div>
                                </template>

                                <div x-show="selectedId == p.id" x-cloak
                                     class="absolute right-2 top-2 flex h-6 w-6 items-center justify-center rounded-full bg-brand-600 text-white shadow-md">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="h-4 w-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                    </svg>
                                </div>
                            </div>

                            <div class="flex flex-1 flex-col p-3">
                                <div class="flex items-start justify-between gap-2">
                                    <h4 class="line-clamp-1 font-semibold text-gray-900" x-text="p.name"></h4>
                                    <span class="whitespace-nowrap rounded-md bg-gray-100 px-1.5 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-gray-500"
                                          x-text="p.code"></span>
                                </div>
                                <p class="mt-1 line-clamp-2 flex-1 text-xs leading-relaxed text-gray-500" x-text="p.description"></p>
                                <div class="mt-3 flex items-center justify-between border-t border-gray-100 pt-2.5">
                                    <span class="text-sm font-bold text-brand-800" x-text="money(p)"></span>
                                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-brand-700">
                                        <span x-show="selectedId == p.id" x-cloak>Seleccionado</span>
                                        <span x-show="selectedId != p.id">Seleccionar</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                             class="h-3.5 w-3.5 transition-transform group-hover:translate-x-0.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                                        </svg>
                                    </span>
                                </div>
                            </div>
                        </button>
                    </template>
                </div>

                <div x-show="filtered.length === 0" class="flex flex-col items-center justify-center py-16 text-center">
                    <div class="flex h-16 w-16 items-center justify-center rounded-full bg-gray-100 text-gray-300">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-8 w-8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                    </div>
                    <h4 class="mt-4 text-base font-semibold text-gray-900">No se encontraron productos</h4>
                    <p class="mt-1 text-sm text-gray-500">Intenta con otro término de búsqueda.</p>
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-between border-t border-gray-100 bg-white px-6 py-3">
                <p class="text-xs text-gray-400">Haz clic en una tarjeta para seleccionar el producto.</p>
                <button type="button" @click="open = false"
                        class="rounded-lg px-3 py-1.5 text-sm font-medium text-gray-600 transition hover:bg-gray-100">
                    Cancelar
                </button>
            </div>
        </div>
    </div>
</div>
