<x-layouts.app title="Painel — MiniStock">

    <aside class="sidebar">
        <div class="sidebar-brand">
            <i class="bi bi-bag-heart-fill"></i> Mini<span>Stock</span>
        </div>

        <nav class="sidebar-nav">
            <ul class="nav flex-column gap-1">
                <li class="nav-item">
                    <a href="{{ route('painel') }}" class="nav-link active">
                        <i class="bi bi-boxes"></i> Produtos
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('home') }}" class="nav-link">
                        <i class="bi bi-shop"></i> Ver vitrine
                    </a>
                </li>
            </ul>
        </nav>

        <div class="sidebar-bottom">
            <div class="px-2 mb-2" style="color: #64748B; font-size: 0.8rem;">
                <i class="bi bi-person-circle me-1"></i>
                {{ auth()->user()->name }}
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-sm w-100 text-start"
                    style="color: #94A3B8; background: transparent; border: none; padding: 0.5rem 0.75rem;">
                    <i class="bi bi-box-arrow-left me-1"></i> Sair
                </button>
            </form>
        </div>
    </aside>

    <main class="painel-content">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h2 class="fw-bold mb-0" style="font-size: 1.5rem;">Gerenciar Estoque</h2>
                <p class="text-muted small mb-0">Adicione, edite ou remova produtos.</p>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @livewire('product-manager')
    </main>

</x-layouts.app>
