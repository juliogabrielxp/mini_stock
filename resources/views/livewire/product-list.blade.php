<div>

    <div class="mb-4" style="max-width: 400px; margin: 0 auto;">
        <div class="input-group">
            <span class="input-group-text bg-white border-end-0">
                <i class="bi bi-search text-muted"></i>
            </span>
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                class="form-control border-start-0"
                placeholder="Buscar produto..."
            >
        </div>
    </div>

    @if ($products->isEmpty())
        <div class="text-center py-5 text-muted">
            <i class="bi bi-search" style="font-size: 2rem;"></i>
            <p class="mt-2">Nenhum produto encontrado.</p>
        </div>
    @else
        <div class="row g-3">
            @foreach ($products as $product)
                <div class="col-sm-6 col-md-4 col-lg-3">
                    <div class="product-card p-3 h-100 d-flex flex-column">
                        <div class="rounded-3 mb-3 d-flex align-items-center justify-content-center"
                            style="height: 100px; background: #F1F5F9;">
                            <i class="bi bi-box-seam text-secondary" style="font-size: 2.5rem;"></i>
                        </div>

                        <div class="flex-grow-1">
                            <h6 class="fw-semibold mb-1">{{ $product->name }}</h6>
                            @if ($product->description)
                                <p class="text-muted small mb-2">{{ $product->description }}</p>
                            @endif
                        </div>

                        <div class="d-flex align-items-center justify-content-between mt-2">
                            <span class="product-price">R$ {{ number_format($product->price, 2, ',', '.') }}</span>
                            <span class="badge-stock bg-{{ $product->stock_status }}-subtle text-{{ $product->stock_status }}-emphasis">
                                {{ $product->stock_label }}
                            </span>
                        </div>

                        <div class="mt-2 text-muted" style="font-size: 0.78rem;">
                            <i class="bi bi-archive me-1"></i>
                            {{ $product->quantity }} unidade{{ $product->quantity != 1 ? 's' : '' }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4 d-flex justify-content-center">
            {{ $products->links() }}
        </div>
    @endif
</div>
