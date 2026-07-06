<x-layouts.app title="MiniStock — Vitrine">

    {{-- Navbar pública --}}
    <nav class="navbar navbar-public px-3 py-3">
        <div class="container-fluid">
            <span class="navbar-brand-name">
                <i class="bi bi-bag-heart-fill me-2"></i>MiniStock
            </span>
            <div class="d-flex align-items-center gap-2">
                @auth
                    <a href="{{ route('painel') }}" class="btn btn-sm btn-accent">
                        <i class="bi bi-speedometer2 me-1"></i>
                        <span class="d-none d-sm-inline">Painel</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-person-lock me-1"></i>
                        <span class="d-none d-sm-inline">Acesso de funcionário</span>
                        <span class="d-inline d-sm-none">Entrar</span>
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <div class="container mt-4 mt-md-5 mb-4">
        <div class="text-center mb-4 mb-md-5 vitrine-hero">
            <h1 class="fw-bold" style="font-size: 2.2rem;">Nossos Produtos</h1>
            <p class="text-muted">Confira o que temos disponível no estoque hoje.</p>
        </div>

        @livewire('product-list')
    </div>

</x-layouts.app>
