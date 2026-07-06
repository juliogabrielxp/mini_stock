<?php

namespace Tests\Unit;

use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    private function makeProduct(array $attrs = []): Product
    {
        return Product::create(array_merge([
            'name'     => 'Produto Teste',
            'price'    => 50.00,
            'quantity' => 10,
        ], $attrs));
    }

    private function makeUser(): User
    {
        return User::create([
            'name'     => 'Funcionário',
            'email'    => 'func@loja.com',
            'password' => bcrypt('senha123'),
            'role'     => 'funcionario',
        ]);
    }

    #[Test]
    public function venda_normal_desconta_estoque_corretamente(): void
    {
        $product = $this->makeProduct(['quantity' => 10]);
        $user    = $this->makeUser();

        $product->sell(3, $user->id);

        $this->assertEquals(7, $product->fresh()->quantity);
    }

    #[Test]
    public function venda_registra_na_tabela_sales(): void
    {
        $product = $this->makeProduct(['price' => 100.00, 'quantity' => 5]);
        $user    = $this->makeUser();

        $sale = $product->sell(2, $user->id);

        $this->assertDatabaseHas('sales', [
            'product_id'    => $product->id,
            'user_id'       => $user->id,
            'quantity_sold' => 2,
            'unit_price'    => 100.00,
            'total'         => 200.00,
        ]);

        $this->assertInstanceOf(Sale::class, $sale);
    }

    #[Test]
    public function venda_calcula_total_corretamente(): void
    {
        $product = $this->makeProduct(['price' => 49.90, 'quantity' => 10]);
        $user    = $this->makeUser();

        $sale = $product->sell(3, $user->id);

        $this->assertEquals(149.70, round((float) $sale->total, 2));
    }

    #[Test]
    public function venda_do_ultimo_item_zera_o_estoque(): void
    {
        $product = $this->makeProduct(['quantity' => 1]);
        $user    = $this->makeUser();

        $product->sell(1, $user->id);

        $this->assertEquals(0, $product->fresh()->quantity);
    }

    #[Test]
    public function venda_com_estoque_insuficiente_lanca_excecao(): void
    {
        $product = $this->makeProduct(['quantity' => 3]);
        $user    = $this->makeUser();

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessageMatches('/Estoque insuficiente/');

        $product->sell(5, $user->id);
    }

    #[Test]
    public function venda_com_estoque_insuficiente_nao_altera_estoque(): void
    {
        $product = $this->makeProduct(['quantity' => 3]);
        $user    = $this->makeUser();

        try {
            $product->sell(10, $user->id);
        } catch (\DomainException) {}

        $this->assertEquals(3, $product->fresh()->quantity);
    }

    #[Test]
    public function venda_com_estoque_insuficiente_nao_registra_sale(): void
    {
        $product = $this->makeProduct(['quantity' => 2]);
        $user    = $this->makeUser();

        try {
            $product->sell(5, $user->id);
        } catch (\DomainException) {}

        $this->assertDatabaseCount('sales', 0);
    }

    #[Test]
    public function venda_com_quantidade_zero_lanca_excecao(): void
    {
        $product = $this->makeProduct(['quantity' => 10]);
        $user    = $this->makeUser();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/maior que zero/');

        $product->sell(0, $user->id);
    }

    #[Test]
    public function venda_com_quantidade_negativa_lanca_excecao(): void
    {
        $product = $this->makeProduct(['quantity' => 10]);
        $user    = $this->makeUser();

        $this->expectException(\InvalidArgumentException::class);

        $product->sell(-1, $user->id);
    }

    #[Test]
    public function venda_com_estoque_zerado_lanca_excecao(): void
    {
        $product = $this->makeProduct(['quantity' => 0]);
        $user    = $this->makeUser();

        $this->expectException(\DomainException::class);

        $product->sell(1, $user->id);
    }

    #[Test]
    public function stock_status_retorna_success_quando_quantidade_acima_de_cinco(): void
    {
        $product = $this->makeProduct(['quantity' => 6]);
        $this->assertEquals('success', $product->stock_status);
    }

    #[Test]
    public function stock_status_retorna_warning_quando_quantidade_e_cinco_ou_menos(): void
    {
        $product = $this->makeProduct(['quantity' => 5]);
        $this->assertEquals('warning', $product->stock_status);

        $product->quantity = 1;
        $this->assertEquals('warning', $product->stock_status);
    }

    #[Test]
    public function stock_status_retorna_danger_quando_quantidade_e_zero(): void
    {
        $product = $this->makeProduct(['quantity' => 0]);
        $this->assertEquals('danger', $product->stock_status);
    }

    #[Test]
    public function stock_label_retorna_em_estoque_quando_quantidade_acima_de_cinco(): void
    {
        $product = $this->makeProduct(['quantity' => 10]);
        $this->assertEquals('Em estoque', $product->stock_label);
    }

    #[Test]
    public function stock_label_retorna_estoque_baixo_quando_quantidade_entre_um_e_cinco(): void
    {
        $product = $this->makeProduct(['quantity' => 3]);
        $this->assertEquals('Estoque baixo', $product->stock_label);
    }

    #[Test]
    public function stock_label_retorna_sem_estoque_quando_quantidade_e_zero(): void
    {
        $product = $this->makeProduct(['quantity' => 0]);
        $this->assertEquals('Sem estoque', $product->stock_label);
    }

    #[Test]
    public function image_url_retorna_null_quando_produto_nao_tem_imagem(): void
    {
        $product = $this->makeProduct(['image_path' => null]);
        $this->assertNull($product->image_url);
    }

    #[Test]
    public function image_url_retorna_url_quando_produto_tem_imagem(): void
    {
        $product = $this->makeProduct(['image_path' => 'products/foto.jpg']);
        $this->assertNotNull($product->image_url);
        $this->assertStringContainsString('products/foto.jpg', $product->image_url);
    }

    #[Test]
    public function produto_deletado_nao_aparece_em_consultas_normais(): void
    {
        $product = $this->makeProduct();
        $product->delete();

        $this->assertNull(Product::find($product->id));
        $this->assertNotNull(Product::withTrashed()->find($product->id));
    }

    #[Test]
    public function produto_tem_relacionamento_com_sales(): void
    {
        $product = $this->makeProduct(['quantity' => 10]);
        $user    = $this->makeUser();

        $product->sell(2, $user->id);
        $product->sell(3, $user->id);

        $this->assertCount(2, $product->sales);
    }
}
