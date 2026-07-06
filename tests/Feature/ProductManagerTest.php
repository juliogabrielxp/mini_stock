<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use App\Livewire\ProductManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProductManagerTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(): User
    {
        return User::create([
            'name'     => 'Funcionário',
            'email'    => 'func@loja.com',
            'password' => bcrypt('senha123'),
            'role'     => 'funcionario',
        ]);
    }

    private function makeProduct(array $attrs = []): Product
    {
        return Product::create(array_merge([
            'name'     => 'Produto Teste',
            'price'    => 50.00,
            'quantity' => 10,
        ], $attrs));
    }

    #[Test]
    public function funcionario_pode_criar_produto(): void
    {
        $user = $this->makeUser();

        Livewire::actingAs($user)
            ->test(ProductManager::class)
            ->call('openCreate')
            ->set('name', 'Camiseta Azul')
            ->set('price', '79.90')
            ->set('quantity', '20')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('products', [
            'name'     => 'Camiseta Azul',
            'price'    => 79.90,
            'quantity' => 20,
        ]);
    }

    #[Test]
    public function criar_produto_sem_nome_retorna_erro_de_validacao(): void
    {
        $user = $this->makeUser();

        Livewire::actingAs($user)
            ->test(ProductManager::class)
            ->call('openCreate')
            ->set('name', '')
            ->set('price', '10.00')
            ->set('quantity', '5')
            ->call('save')
            ->assertHasErrors(['name' => 'required']);
    }

    #[Test]
    public function criar_produto_com_preco_negativo_retorna_erro_de_validacao(): void
    {
        $user = $this->makeUser();

        Livewire::actingAs($user)
            ->test(ProductManager::class)
            ->call('openCreate')
            ->set('name', 'Produto X')
            ->set('price', '-5')
            ->set('quantity', '10')
            ->call('save')
            ->assertHasErrors(['price' => 'min']);
    }

    #[Test]
    public function criar_produto_com_quantidade_negativa_retorna_erro_de_validacao(): void
    {
        $user = $this->makeUser();

        Livewire::actingAs($user)
            ->test(ProductManager::class)
            ->call('openCreate')
            ->set('name', 'Produto X')
            ->set('price', '10.00')
            ->set('quantity', '-1')
            ->call('save')
            ->assertHasErrors(['quantity' => 'min']);
    }

    #[Test]
    public function criar_produto_fecha_modal_apos_salvar(): void
    {
        $user = $this->makeUser();

        Livewire::actingAs($user)
            ->test(ProductManager::class)
            ->call('openCreate')
            ->set('name', 'Produto Y')
            ->set('price', '25.00')
            ->set('quantity', '5')
            ->call('save')
            ->assertSet('showModal', false);
    }

    #[Test]
    public function funcionario_pode_editar_produto(): void
    {
        $user    = $this->makeUser();
        $product = $this->makeProduct(['name' => 'Nome Antigo', 'price' => 30.00]);

        Livewire::actingAs($user)
            ->test(ProductManager::class)
            ->call('openEdit', $product->id)
            ->assertSet('name', 'Nome Antigo')
            ->set('name', 'Nome Novo')
            ->set('price', '45.00')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('products', [
            'id'    => $product->id,
            'name'  => 'Nome Novo',
            'price' => 45.00,
        ]);
    }

    #[Test]
    public function abrir_edicao_preenche_campos_com_dados_do_produto(): void
    {
        $user    = $this->makeUser();
        $product = $this->makeProduct([
            'name'        => 'Calça Jeans',
            'price'       => 129.90,
            'quantity'    => 8,
            'description' => 'Slim fit',
        ]);

        Livewire::actingAs($user)
            ->test(ProductManager::class)
            ->call('openEdit', $product->id)
            ->assertSet('name', 'Calça Jeans')
            ->assertSet('description', 'Slim fit')
            ->assertSet('editingId', $product->id);
    }

    #[Test]
    public function funcionario_pode_deletar_produto(): void
    {
        $user    = $this->makeUser();
        $product = $this->makeProduct();

        Livewire::actingAs($user)
            ->test(ProductManager::class)
            ->call('confirmDelete', $product->id)
            ->assertSet('showDeleteModal', true)
            ->call('delete');

        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }

    #[Test]
    public function cancelar_exclusao_mantem_produto_no_banco(): void
    {
        $user    = $this->makeUser();
        $product = $this->makeProduct();

        Livewire::actingAs($user)
            ->test(ProductManager::class)
            ->call('confirmDelete', $product->id)
            ->call('cancelDelete')
            ->assertSet('showDeleteModal', false);

        $this->assertDatabaseHas('products', ['id' => $product->id, 'deleted_at' => null]);
    }

    #[Test]
    public function criar_produto_com_imagem_valida_salva_arquivo(): void
    {
        Storage::fake('public');
        $user = $this->makeUser();

        $file = UploadedFile::fake()->image('produto.jpg', 200, 200)->size(500);

        Livewire::actingAs($user)
            ->test(ProductManager::class)
            ->call('openCreate')
            ->set('name', 'Produto com Foto')
            ->set('price', '99.90')
            ->set('quantity', '5')
            ->set('image', $file)
            ->call('save')
            ->assertHasNoErrors();

        $product = Product::where('name', 'Produto com Foto')->first();
        $this->assertNotNull($product->image_path);
        Storage::disk('public')->assertExists($product->image_path);
    }

    #[Test]
    public function imagem_acima_de_2mb_retorna_erro_de_validacao(): void
    {
        Storage::fake('public');
        $user = $this->makeUser();

        $file = UploadedFile::fake()->image('grande.jpg', 500, 500)->size(3000);

        Livewire::actingAs($user)
            ->test(ProductManager::class)
            ->call('openCreate')
            ->set('name', 'Produto X')
            ->set('price', '10.00')
            ->set('quantity', '1')
            ->set('image', $file)
            ->call('save')
            ->assertHasErrors(['image' => 'max']);
    }

    #[Test]
    public function arquivo_nao_imagem_retorna_erro_de_validacao(): void
    {
        Storage::fake('public');
        $user = $this->makeUser();

        $file = UploadedFile::fake()->create('documento.pdf', 100, 'application/pdf');

        Livewire::actingAs($user)
            ->test(ProductManager::class)
            ->call('openCreate')
            ->set('name', 'Produto X')
            ->set('price', '10.00')
            ->set('quantity', '1')
            ->set('image', $file)
            ->call('save')
            ->assertHasErrors(['image']);
    }

    #[Test]
    public function venda_rapida_abre_modal_com_dados_corretos(): void
    {
        $user    = $this->makeUser();
        $product = $this->makeProduct(['name' => 'Tênis', 'price' => 199.90, 'quantity' => 5]);

        Livewire::actingAs($user)
            ->test(ProductManager::class)
            ->call('openSale', $product->id)
            ->assertSet('showSaleModal', true)
            ->assertSet('sellingName', 'Tênis')
            ->assertSet('sellingStock', 5);
    }

    #[Test]
    public function venda_rapida_com_quantidade_valida_desconta_estoque(): void
    {
        $user    = $this->makeUser();
        $product = $this->makeProduct(['quantity' => 10]);

        Livewire::actingAs($user)
            ->test(ProductManager::class)
            ->call('openSale', $product->id)
            ->set('saleQuantity', '3')
            ->call('confirmSale')
            ->assertSet('showSaleModal', false)
            ->assertSet('saleError', null);

        $this->assertEquals(7, $product->fresh()->quantity);
    }

    #[Test]
    public function venda_rapida_com_estoque_insuficiente_exibe_erro(): void
    {
        $user    = $this->makeUser();
        $product = $this->makeProduct(['quantity' => 2]);

        $component = Livewire::actingAs($user)
            ->test(ProductManager::class)
            ->call('openSale', $product->id)
            ->set('saleQuantity', '10')
            ->call('confirmSale');

        $this->assertNotNull($component->get('saleError'));
        $this->assertEquals(2, $product->fresh()->quantity);
    }

    #[Test]
    public function venda_rapida_com_quantidade_zero_exibe_erro(): void
    {
        $user    = $this->makeUser();
        $product = $this->makeProduct(['quantity' => 10]);

        $component = Livewire::actingAs($user)
            ->test(ProductManager::class)
            ->call('openSale', $product->id)
            ->set('saleQuantity', '0')
            ->call('confirmSale');

        $this->assertNotNull($component->get('saleError'));
    }

    #[Test]
    public function venda_rapida_registra_sale_no_banco(): void
    {
        $user    = $this->makeUser();
        $product = $this->makeProduct(['price' => 50.00, 'quantity' => 10]);

        Livewire::actingAs($user)
            ->test(ProductManager::class)
            ->call('openSale', $product->id)
            ->set('saleQuantity', '2')
            ->call('confirmSale');

        $this->assertDatabaseHas('sales', [
            'product_id'    => $product->id,
            'quantity_sold' => 2,
            'total'         => 100.00,
        ]);
    }

    #[Test]
    public function busca_filtra_produtos_por_nome(): void
    {
        $user = $this->makeUser();
        $this->makeProduct(['name' => 'Camiseta Azul']);
        $this->makeProduct(['name' => 'Calça Jeans']);

        $component = Livewire::actingAs($user)
            ->test(ProductManager::class)
            ->set('search', 'Camiseta');

        $products = $component->get('products');
        $this->assertCount(1, $products->items());
        $this->assertEquals('Camiseta Azul', $products->items()[0]->name);
    }

    #[Test]
    public function busca_vazia_retorna_todos_os_produtos(): void
    {
        $user = $this->makeUser();
        $this->makeProduct(['name' => 'Produto A']);
        $this->makeProduct(['name' => 'Produto B']);
        $this->makeProduct(['name' => 'Produto C']);

        $component = Livewire::actingAs($user)
            ->test(ProductManager::class)
            ->set('search', '');

        $this->assertEquals(3, $component->get('products')->total());
    }
}
