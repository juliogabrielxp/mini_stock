# 📦 MiniStock

Sistema de gerenciamento de estoque desenvolvido com Laravel e Livewire, como projeto de portfólio para demonstrar habilidades em desenvolvimento backend com PHP.

---

## 🚀 Funcionalidades

- **Vitrine pública** — listagem de produtos acessível sem autenticação
- **Painel administrativo** — área protegida para cadastro, edição e remoção de produtos
- **Autenticação nativa Laravel** — sistema de login com session-based auth, sem pacotes externos
- **Modal de detalhes** — visualização de produto com Livewire, sem recarregar a página
- **Interface responsiva** — layout construído com Bootstrap 5

---

## 🛠️ Stack

| Tecnologia | Uso |
|---|---|
| PHP / Laravel | Backend e roteamento |
| Livewire 3 | Componentes reativos (modal) |
| MySQL | Banco de dados |
| Bootstrap 5 | Interface e responsividade |
| Hostinger | Hospedagem compartilhada |

---

## ⚙️ Como rodar localmente

```bash
# Clone o repositório
git clone https://github.com/juliogabrielxp/ministock.git
cd ministock

# Instale as dependências
composer install
npm install && npm run build

# Configure o ambiente
cp .env.example .env
php artisan key:generate

# Configure o banco de dados no .env e rode as migrations
php artisan migrate

# Inicie o servidor
php artisan serve
```

Acesse em: `http://localhost:8000`

---

## 🌐 Deploy

O projeto está hospedado em **Hostinger (shared hosting)**, sem acesso SSH.

O deploy exigiu soluções alternativas ao fluxo padrão do Laravel:

- Arquivos PHP auxiliares para executar comandos Artisan via browser
- Geração de hash bcrypt via arquivo temporário
- Inserção direta do usuário administrador pelo phpMyAdmin

Essas restrições foram um aprendizado valioso sobre como adaptar o fluxo de deploy a ambientes limitados.

---

## 📁 Estrutura resumida

```
app/
├── Http/
│   ├── Controllers/     # Lógica de produtos e autenticação
│   └── Livewire/        # Componente do modal de detalhes
├── Models/
│   └── Product.php
resources/
├── views/
│   ├── products/        # Vitrine pública
│   ├── admin/           # Painel administrativo
│   └── livewire/        # Views dos componentes Livewire
routes/
├── web.php              # Rotas públicas e protegidas
```

---

## 📌 Decisões técnicas

**Por que autenticação nativa e não Breeze/Jetstream?**
A escolha foi intencional — queria entender o ciclo completo de autenticação com sessão, sem abstrações prontas. O login, o middleware de proteção de rotas e o logout foram implementados manualmente.

**Por que Livewire para o modal?**
Para explorar componentes reativos sem JavaScript puro, aproveitando a integração nativa com o ecossistema Laravel.

---

## 👤 Autor

**Julio Gabriel**
Backend Developer — PHP / Laravel

- LinkedIn: [linkedin.com/in/juliogabrielxp](https://linkedin.com/in/juliogabrielxp)

- Projeto ao vivo: [lightcoral-wren-471724.hostingersite.com/](https://lightcoral-wren-471724.hostingersite.com/)

---


