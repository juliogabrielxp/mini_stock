<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class ProductManager extends Component
{
    use WithPagination;
    use WithFileUploads;

    // Estado dos modais
    public bool $showModal       = false;
    public bool $showDeleteModal = false;
    public bool $showSaleModal   = false;

    public ?int $editingId  = null;
    public ?int $deletingId = null;

    // Dados do produto sendo vendido
    public ?int    $sellingId    = null;
    public string  $sellingName  = '';
    public int     $sellingStock = 0;
    public string  $sellingPrice = '';
    public string  $saleQuantity = '';
    public ?string $saleError    = null;

    // Campos do formulário de produto
    public string $name        = '';
    public string $price       = '';
    public string $quantity    = '';
    public string $description = '';
    public $image              = null;
    public ?string $currentImagePath = null;

    // Busca
    public string $search = '';

    protected function rules(): array
    {
        return [
            'name'        => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'quantity'    => 'required|integer|min:0',
            'description' => 'nullable|string|max:500',
            'image'       => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
                'dimensions:min_width=100,min_height=100,max_width=2000,max_height=2000',
            ],
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required'     => 'O nome do produto é obrigatório.',
            'name.max'          => 'O nome pode ter no máximo 255 caracteres.',
            'price.required'    => 'O preço é obrigatório.',
            'price.numeric'     => 'Informe um preço válido.',
            'price.min'         => 'O preço não pode ser negativo.',
            'quantity.required' => 'A quantidade é obrigatória.',
            'quantity.integer'  => 'A quantidade deve ser um número inteiro.',
            'quantity.min'      => 'A quantidade não pode ser negativa.',
            'image.image'       => 'O arquivo deve ser uma imagem.',
            'image.mimes'       => 'Formatos aceitos: JPG, PNG ou WebP.',
            'image.max'         => 'A imagem deve ter no máximo 2MB.',
            'image.dimensions'  => 'A imagem deve ter entre 100x100 e 2000x2000 pixels.',
        ];
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    // ── CRUD de produto ────────────────────────────────────────

    public function openCreate(): void
    {
        $this->resetForm();
        $this->editingId = null;
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $product = Product::findOrFail($id);

        $this->editingId        = $id;
        $this->name             = $product->name;
        $this->price            = $product->price;
        $this->quantity         = $product->quantity;
        $this->description      = $product->description ?? '';
        $this->currentImagePath = $product->image_path;
        $this->image            = null;
        $this->showModal        = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'name'        => $this->name,
            'price'       => $this->price,
            'quantity'    => $this->quantity,
            'description' => $this->description ?: null,
        ];

        if ($this->image) {
            if ($this->currentImagePath) {
                Storage::disk('public')->delete($this->currentImagePath);
            }
            $data['image_path'] = $this->image->store('products', 'public');
        }

        if ($this->editingId) {
            Product::findOrFail($this->editingId)->update($data);
            session()->flash('success', 'Produto atualizado com sucesso!');
        } else {
            Product::create($data);
            session()->flash('success', 'Produto criado com sucesso!');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function removeCurrentImage(): void
    {
        if ($this->editingId && $this->currentImagePath) {
            Storage::disk('public')->delete($this->currentImagePath);
            Product::findOrFail($this->editingId)->update(['image_path' => null]);
            $this->currentImagePath = null;
        }
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId      = $id;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        if ($this->deletingId) {
            $product = Product::findOrFail($this->deletingId);
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
            $product->delete();
            session()->flash('success', 'Produto removido do estoque.');
        }

        $this->showDeleteModal = false;
        $this->deletingId      = null;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteModal = false;
        $this->deletingId      = null;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    // ── Venda rápida ───────────────────────────────────────────

    public function openSale(int $id): void
    {
        $product = Product::findOrFail($id);

        $this->sellingId     = $id;
        $this->sellingName   = $product->name;
        $this->sellingStock  = $product->quantity;
        $this->sellingPrice  = $product->price;
        $this->saleQuantity  = '1';
        $this->saleError     = null;
        $this->showSaleModal = true;
    }

    public function confirmSale(): void
    {
        $this->saleError = null;
        $qty = (int) $this->saleQuantity;

        if ($qty <= 0) {
            $this->saleError = 'A quantidade deve ser maior que zero.';
            return;
        }

        try {
            $product = Product::findOrFail($this->sellingId);
            $product->sell($qty, auth()->id());

            $total = number_format($product->price * $qty, 2, ',', '.');
            session()->flash('success', "Venda registrada! {$qty}x {$product->name} — Total: R$ {$total}");

            $this->closeSaleModal();

        } catch (\DomainException $e) {
            $this->saleError = $e->getMessage();
        } catch (\InvalidArgumentException $e) {
            $this->saleError = $e->getMessage();
        }
    }

    public function closeSaleModal(): void
    {
        $this->showSaleModal = false;
        $this->sellingId     = null;
        $this->sellingName   = '';
        $this->sellingStock  = 0;
        $this->sellingPrice  = '';
        $this->saleQuantity  = '';
        $this->saleError     = null;
    }

    // ── Helpers ────────────────────────────────────────────────

    private function resetForm(): void
    {
        $this->name             = '';
        $this->price            = '';
        $this->quantity         = '';
        $this->description      = '';
        $this->image            = null;
        $this->currentImagePath = null;
        $this->resetValidation();
    }

    public function render()
    {
        $products = Product::query()
            ->when($this->search, fn($q) =>
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('description', 'like', "%{$this->search}%")
            )
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.product-manager', compact('products'));
    }
}
