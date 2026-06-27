<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

class ProductManager extends Component
{
    use WithPagination;

    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public ?int $editingId = null;
    public ?int $deletingId = null;

    public string $name        = '';
    public string $price       = '';
    public string $quantity    = '';
    public string $description = '';

    public string $search = '';

    protected function rules(): array
    {
        return [
            'name'        => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'quantity'    => 'required|integer|min:0',
            'description' => 'nullable|string|max:500',
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
        ];
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->editingId = null;
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $product = Product::findOrFail($id);

        $this->editingId   = $id;
        $this->name        = $product->name;
        $this->price       = $product->price;
        $this->quantity    = $product->quantity;
        $this->description = $product->description ?? '';
        $this->showModal   = true;
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

    public function confirmDelete(int $id): void
    {
        $this->deletingId      = $id;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        if ($this->deletingId) {
            Product::findOrFail($this->deletingId)->delete();
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

    private function resetForm(): void
    {
        $this->name        = '';
        $this->price       = '';
        $this->quantity    = '';
        $this->description = '';
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
