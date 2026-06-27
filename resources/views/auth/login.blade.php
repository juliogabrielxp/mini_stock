<x-layouts.app title="Login — MiniStock">

<div class="min-vh-100 d-flex align-items-center justify-content-center" style="background: #F1F5F9;">
    <div class="w-100" style="max-width: 400px; padding: 1rem;">

        <div class="bg-white rounded-4 p-4 shadow-sm border" style="border-color: #E2E8F0 !important;">

            <div class="text-center mb-4">
                <div class="mb-2" style="font-size: 2rem; color: #6366F1;">
                    <i class="bi bi-bag-heart-fill"></i>
                </div>
                <h1 class="fw-bold mb-0" style="font-size: 1.4rem;">MiniStock</h1>
                <p class="text-muted small">Área restrita — funcionários</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger py-2 small">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-3">
                    <label for="email" class="form-label small fw-semibold text-secondary">
                        E-mail
                    </label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-control @error('email') is-invalid @enderror"
                        value="{{ old('email') }}"
                        placeholder="funcionario@loja.com"
                        autofocus
                        required
                    >
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label small fw-semibold text-secondary">
                        Senha
                    </label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-control @error('password') is-invalid @enderror"
                        placeholder="••••••••"
                        required
                    >
                </div>

                <div class="mb-4 d-flex align-items-center">
                    <input type="checkbox" id="remember" name="remember" class="form-check-input me-2">
                    <label for="remember" class="form-check-label small text-muted">
                        Manter conectado
                    </label>
                </div>

                <button type="submit" class="btn btn-accent w-100">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Entrar no painel
                </button>
            </form>

            <div class="text-center mt-3">
                <a href="{{ route('home') }}" class="text-muted small text-decoration-none">
                    <i class="bi bi-arrow-left me-1"></i>Voltar para a vitrine
                </a>
            </div>
        </div>

    </div>
</div>

</x-layouts.app>
