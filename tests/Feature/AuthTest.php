<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(array $attrs = []): User
    {
        return User::create(array_merge([
            'name'     => 'Funcionário',
            'email'    => 'func@loja.com',
            'password' => bcrypt('senha123'),
            'role'     => 'funcionario',
        ], $attrs));
    }

    #[Test]
    public function vitrine_publica_acessivel_sem_login(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    #[Test]
    public function pagina_de_login_acessivel_sem_autenticacao(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    #[Test]
    public function painel_redireciona_usuario_nao_autenticado_para_login(): void
    {
        $response = $this->get('/painel');
        $response->assertRedirect('/login');
    }

    #[Test]
    public function painel_acessivel_para_usuario_autenticado(): void
    {
        $user = $this->makeUser();

        $response = $this->actingAs($user)->get('/painel');
        $response->assertStatus(200);
    }

    #[Test]
    public function login_com_credenciais_corretas_redireciona_para_painel(): void
    {
        $this->makeUser(['email' => 'func@loja.com', 'password' => bcrypt('senha123')]);

        $response = $this->post('/login', [
            'email'    => 'func@loja.com',
            'password' => 'senha123',
        ]);

        $response->assertRedirect('/painel');
        $this->assertAuthenticated();
    }

    #[Test]
    public function login_com_senha_errada_retorna_erro(): void
    {
        $this->makeUser(['email' => 'func@loja.com']);

        $response = $this->post('/login', [
            'email'    => 'func@loja.com',
            'password' => 'senha_errada',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    #[Test]
    public function login_com_email_inexistente_retorna_erro(): void
    {
        $response = $this->post('/login', [
            'email'    => 'naoexiste@loja.com',
            'password' => 'qualquercoisa',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    #[Test]
    public function login_sem_email_retorna_erro_de_validacao(): void
    {
        $response = $this->post('/login', [
            'email'    => '',
            'password' => 'senha123',
        ]);

        $response->assertSessionHasErrors('email');
    }

    #[Test]
    public function login_com_email_invalido_retorna_erro_de_validacao(): void
    {
        $response = $this->post('/login', [
            'email'    => 'nao-e-um-email',
            'password' => 'senha123',
        ]);

        $response->assertSessionHasErrors('email');
    }

    #[Test]
    public function login_sem_senha_retorna_erro_de_validacao(): void
    {
        $response = $this->post('/login', [
            'email'    => 'func@loja.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors('password');
    }

    #[Test]
    public function usuario_ja_autenticado_e_redirecionado_da_pagina_de_login(): void
    {
        $user = $this->makeUser();

        $response = $this->actingAs($user)->get('/login');
        $response->assertRedirect('/painel');
    }

    #[Test]
    public function logout_encerra_sessao_e_redireciona_para_vitrine(): void
    {
        $user = $this->makeUser();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect('/');
        $this->assertGuest();
    }

    #[Test]
    public function logout_sem_autenticacao_redireciona_para_login(): void
    {
        $response = $this->post('/logout');
        $response->assertRedirect('/login');
    }
}
