<div>
  
    <div class="d-flex align-items-center gap-3 mb-4">
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

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show py-2 small mb-3" role="alert">
            <i class="bi bi-check-circle-fill me-1"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="table-painel">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Preço</th>
                    <th>Quantidade</th>
                    <th>Situação</th>
                    <th class="text-end">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $product->name }}</div>
                            @if ($product->description)
                                <div class="text-muted small">{{ Str::limit($product->description, 50) }}</div>
                            @endif
                        </td>
                        <td class="align-middle fw-semibold" style="color: #6366F1;">
                            R$ {{ number_format($product->price, 2, ',', '.') }}
                        </td>
                        <td class="align-middle">{{ $product->quantity }}</td>
                        <td class="align-middle">
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
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                            <p class="mt-2 mb-0">Nenhum produto cadastrado ainda.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $products->links() }}
    </div>

    @if ($showModal)
        <div class="livewire-modal-backdrop" wire:click.self="closeModal">
            <div class="livewire-modal">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h5 class="fw-bold mb-0">
                        {{ $editingId ? 'Editar produto' : 'Novo produto' }}
                    </h5>
                    <button wire:click="closeModal" class="btn-close"></button>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-semibold text-secondary">Nome</label>
                    <input
                        type="text"
                        wire:model="name"
                        class="form-control @error('name') is-invalid @enderror"
                        placeholder="Ex: Camiseta Básica"
                    >
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-semibold text-secondary">Preço (R$)</label>
                        <input
                            type="number"
                            step="0.01"
                            min="0"
                            wire:model="price"
                            class="form-control @error('price') is-invalid @enderror"
                            placeholder="0,00"
                        >
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-semibold text-secondary">Quantidade</label>
                        <input
                            type="number"
                            min="0"
                            wire:model="quantity"
                            class="form-control @error('quantity') is-invalid @enderror"
                            placeholder="0"
                        >
                        @error('quantity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-semibold text-secondary">
                        Descrição <span class="fw-normal text-muted">(opcional)</span>
                    </label>
                    <textarea
                        wire:model="description"
                        class="form-control"
                        rows="2"
                        placeholder="Uma breve descrição do produto..."
                    ></textarea>
                </div>

                <div class="d-flex gap-2 justify-content-end">
                    <button wire:click="closeModal" class="btn btn-outline-secondary">
                        Cancelar
                    </button>
                    <button wire:click="save" class="btn btn-accent">
                        <span wire:loading wire:target="save" class="spinner-border spinner-border-sm me-1"></span>
                        {{ $editingId ? 'Salvar alterações' : 'Criar produto' }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if ($showDeleteModal)
        <div class="livewire-modal-backdrop">
            <div class="livewire-modal" style="max-width: 380px; text-align: center;">
                <div class="mb-3" style="font-size: 2.5rem; color: #EF4444;">
                    <i class="bi bi-trash3-fill"></i>
                </div>
                <h5 class="fw-bold mb-2">Remover produto?</h5>
                <p class="text-muted small mb-4">
                    Essa ação remove o produto do estoque. Você pode restaurá-lo posteriormente se necessário.
                </p>
                <div class="d-flex gap-2 justify-content-center">
                    <button wire:click="cancelDelete" class="btn btn-outline-secondary">
                        Cancelar
                    </button>
                    <button wire:click="delete" class="btn btn-danger">
                        <span wire:loading wire:target="delete" class="spinner-border spinner-border-sm me-1"></span>
                        Sim, remover
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
