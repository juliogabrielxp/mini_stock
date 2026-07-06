<div>
    {{-- Barra de ações --}}
    <div class="d-flex gap-3 mb-4 painel-actions">
        <div class="input-group" style="max-width: 320px;">
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

        <button wire:click="openCreate" class="btn btn-accent ms-auto">
            <i class="bi bi-plus-lg me-1"></i> Novo produto
        </button>
    </div>

    {{-- Flash messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show py-2 small mb-3" role="alert">
            <i class="bi bi-check-circle-fill me-1"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Tabela com scroll horizontal no mobile --}}
    <div class="table-painel">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width: 60px;" class="col-hide-mobile">Foto</th>
                        <th>Produto</th>
                        <th class="col-hide-mobile">Preço</th>
                        <th>Qtd</th>
                        <th class="col-hide-mobile">Situação</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr>
                            <td class="align-middle col-hide-mobile">
                                @if ($product->image_url)
                                    <img
                                        src="{{ $product->image_url }}"
                                        alt="{{ $product->name }}"
                                        class="rounded-2"
                                        style="width: 48px; height: 48px; object-fit: cover;"
                                    >
                                @else
                                    <div class="rounded-2 d-flex align-items-center justify-content-center"
                                        style="width: 48px; height: 48px; background: #F1F5F9;">
                                        <i class="bi bi-image text-secondary" style="font-size: 1.2rem;"></i>
                                    </div>
                                @endif
                            </td>
                            <td class="align-middle">
                                <div class="fw-semibold">{{ $product->name }}</div>
                                {{-- No mobile mostra preço e badge aqui --}}
                                <div class="d-flex d-md-none align-items-center gap-2 mt-1 flex-wrap">
                                    <span class="small fw-semibold" style="color: #6366F1;">
                                        R$ {{ number_format($product->price, 2, ',', '.') }}
                                    </span>
                                    <span class="badge-stock bg-{{ $product->stock_status }}-subtle text-{{ $product->stock_status }}-emphasis">
                                        {{ $product->stock_label }}
                                    </span>
                                </div>
                                @if ($product->description)
                                    <div class="text-muted small d-none d-md-block">{{ Str::limit($product->description, 50) }}</div>
                                @endif
                            </td>
                            <td class="align-middle fw-semibold col-hide-mobile" style="color: #6366F1;">
                                R$ {{ number_format($product->price, 2, ',', '.') }}
                            </td>
                            <td class="align-middle">{{ $product->quantity }}</td>
                            <td class="align-middle col-hide-mobile">
                                <span class="badge rounded-pill bg-{{ $product->stock_status }}-subtle text-{{ $product->stock_status }}-emphasis badge-stock">
                                    @if ($product->stock_status === 'danger')
                                        <i class="bi bi-x-circle me-1"></i>
                                    @elseif ($product->stock_status === 'warning')
                                        <i class="bi bi-exclamation-triangle me-1"></i>
                                    @else
                                        <i class="bi bi-check-circle me-1"></i>
                                    @endif
                                    {{ $product->stock_label }}
                                </span>
                            </td>
                            <td class="align-middle text-end">
                                <button
                                    wire:click.stop="openSale({{ $product->id }})"
                                    class="btn btn-sm btn-success me-1"
                                    title="Venda rápida"
                                    @if($product->quantity === 0) disabled @endif
                                >
                                    <i class="bi bi-cart-plus"></i>
                                </button>
                                <button
                                    wire:click.stop="openEdit({{ $product->id }})"
                                    class="btn btn-sm btn-outline-secondary me-1"
                                    title="Editar"
                                >
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button
                                    wire:click.stop="confirmDelete({{ $product->id }})"
                                    class="btn btn-sm btn-outline-danger"
                                    title="Remover"
                                >
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                <p class="mt-2 mb-0">Nenhum produto cadastrado ainda.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Paginação --}}
    <div class="mt-3">
        {{ $products->links() }}
    </div>

    {{-- ==================== Modal: Venda rápida ==================== --}}
    @if ($showSaleModal)
        <div class="livewire-modal-backdrop" wire:click.self="closeSaleModal">
            <div class="livewire-modal" style="max-width: 420px;">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h5 class="fw-bold mb-0">
                        <i class="bi bi-cart-plus me-2 text-success"></i>Venda rápida
                    </h5>
                    <button wire:click="closeSaleModal" class="btn-close"></button>
                </div>

                <div class="rounded-3 p-3 mb-4" style="background: #F8FAFC; border: 1px solid #E2E8F0;">
                    <div class="fw-semibold mb-1">{{ $sellingName }}</div>
                    <div class="d-flex gap-3 text-muted small flex-wrap">
                        <span><i class="bi bi-tag me-1"></i>R$ {{ number_format((float)$sellingPrice, 2, ',', '.') }}</span>
                        <span><i class="bi bi-archive me-1"></i>{{ $sellingStock }} em estoque</span>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold text-secondary">Quantidade a vender</label>
                    <input
                        type="number"
                        wire:model.live="saleQuantity"
                        min="1"
                        max="{{ $sellingStock }}"
                        class="form-control form-control-lg text-center @if($saleError) is-invalid @endif"
                        placeholder="0"
                    >
                    @if($saleError)
                        <div class="invalid-feedback d-block">
                            <i class="bi bi-exclamation-triangle me-1"></i>{{ $saleError }}
                        </div>
                    @endif
                </div>

                @if((int)$saleQuantity > 0 && !$saleError)
                    <div class="rounded-3 p-3 mb-4 text-center" style="background: #F0FDF4; border: 1px solid #BBF7D0;">
                        <div class="text-muted small mb-1">Total da venda</div>
                        <div class="fw-bold" style="font-size: 1.5rem; color: #16A34A;">
                            R$ {{ number_format((float)$sellingPrice * (int)$saleQuantity, 2, ',', '.') }}
                        </div>
                        <div class="text-muted small mt-1">
                            Estoque restante: <strong>{{ $sellingStock - (int)$saleQuantity }}</strong> unidade(s)
                        </div>
                    </div>
                @endif

                <div class="d-flex gap-2 justify-content-end">
                    <button wire:click="closeSaleModal" class="btn btn-outline-secondary">Cancelar</button>
                    <button
                        wire:click="confirmSale"
                        class="btn btn-success"
                        @if((int)$saleQuantity <= 0) disabled @endif
                    >
                        <span wire:loading wire:target="confirmSale" class="spinner-border spinner-border-sm me-1"></span>
                        <i class="bi bi-check-lg me-1"></i> Confirmar venda
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ==================== Modal: Criar / Editar ==================== --}}
    @if ($showModal)
        <div class="livewire-modal-backdrop" wire:click.self="closeModal">
            <div class="livewire-modal" style="max-width: 520px;">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h5 class="fw-bold mb-0">{{ $editingId ? 'Editar produto' : 'Novo produto' }}</h5>
                    <button wire:click="closeModal" class="btn-close"></button>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold text-secondary">Nome</label>
                    <input type="text" wire:model="name"
                        class="form-control @error('name') is-invalid @enderror"
                        placeholder="Ex: Camiseta Básica">
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-semibold text-secondary">Preço (R$)</label>
                        <input type="number" step="0.01" min="0" wire:model="price"
                            class="form-control @error('price') is-invalid @enderror" placeholder="0,00">
                        @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold text-secondary">Quantidade</label>
                        <input type="number" min="0" wire:model="quantity"
                            class="form-control @error('quantity') is-invalid @enderror" placeholder="0">
                        @error('quantity') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold text-secondary">
                        Descrição <span class="fw-normal text-muted">(opcional)</span>
                    </label>
                    <textarea wire:model="description" class="form-control" rows="2"
                        placeholder="Uma breve descrição do produto..."></textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-semibold text-secondary">
                        Imagem <span class="fw-normal text-muted">(opcional)</span>
                    </label>

                    @if ($currentImagePath && !$image)
                        <div class="d-flex align-items-center gap-3 mb-2 p-2 rounded-2" style="background: #F8FAFC; border: 1px solid #E2E8F0;">
                            <img src="{{ Storage::url($currentImagePath) }}" alt="Imagem atual"
                                class="rounded-2" style="width: 56px; height: 56px; object-fit: cover;">
                            <div class="flex-grow-1">
                                <div class="small fw-semibold">Imagem atual</div>
                                <div class="text-muted" style="font-size: 0.75rem;">Envie uma nova para substituir</div>
                            </div>
                            <button wire:click="removeCurrentImage" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </div>
                    @endif

                    @if ($image)
                        <div class="d-flex align-items-center gap-3 mb-2 p-2 rounded-2" style="background: #F0FDF4; border: 1px solid #BBF7D0;">
                            <img src="{{ $image->temporaryUrl() }}" alt="Preview"
                                class="rounded-2" style="width: 56px; height: 56px; object-fit: cover;">
                            <div class="flex-grow-1">
                                <div class="small fw-semibold text-success">Nova imagem selecionada</div>
                                <div class="text-muted" style="font-size: 0.75rem;">{{ $image->getClientOriginalName() }}</div>
                            </div>
                        </div>
                    @endif

                    <input type="file" wire:model="image"
                        class="form-control @error('image') is-invalid @enderror"
                        accept="image/jpeg,image/png,image/webp">
                    @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    <div class="form-text text-muted" style="font-size: 0.75rem;">
                        <i class="bi bi-info-circle me-1"></i>JPG, PNG ou WebP · Máximo 2MB · Entre 100x100 e 2000x2000 pixels
                    </div>
                    <div wire:loading wire:target="image" class="mt-2 small text-muted">
                        <span class="spinner-border spinner-border-sm me-1"></span> Carregando imagem...
                    </div>
                </div>

                <div class="d-flex gap-2 justify-content-end">
                    <button wire:click="closeModal" class="btn btn-outline-secondary">Cancelar</button>
                    <button wire:click="save" class="btn btn-accent">
                        <span wire:loading wire:target="save" class="spinner-border spinner-border-sm me-1"></span>
                        {{ $editingId ? 'Salvar alterações' : 'Criar produto' }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ==================== Modal: Confirmar exclusão ==================== --}}
    @if ($showDeleteModal)
        <div class="livewire-modal-backdrop">
            <div class="livewire-modal" style="max-width: 380px; text-align: center;">
                <div class="mb-3" style="font-size: 2.5rem; color: #EF4444;">
                    <i class="bi bi-trash3-fill"></i>
                </div>
                <h5 class="fw-bold mb-2">Remover produto?</h5>
                <p class="text-muted small mb-4">
                    Essa ação remove o produto e sua imagem do estoque. Você pode restaurá-lo posteriormente se necessário.
                </p>
                <div class="d-flex gap-2 justify-content-center">
                    <button wire:click="cancelDelete" class="btn btn-outline-secondary">Cancelar</button>
                    <button wire:click="delete" class="btn btn-danger">
                        <span wire:loading wire:target="delete" class="spinner-border spinner-border-sm me-1"></span>
                        Sim, remover
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
