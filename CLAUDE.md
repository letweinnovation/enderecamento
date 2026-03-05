# CLAUDE.md — Contexto do Projeto
## O que é este projeto?

GTI Endereçamento é uma **ferramenta** da Letwe Innovation, construído em **Laravel 12** com arquitetura de **monólito modular** (`nwidart/laravel-modules`). Gerencia aumento, redução, bloqueio, dados dos endereços de um armazem.

**Produção**: `dsv-enderecamento.gtiplug.com.br`

## Stack

- **Backend**: PHP 8.2+, Laravel 12, Eloquent ORM

- **Frontend**: Blade, TailwindCSS v4, Vite 7, Vanilla JS / Alpine.js

- **DB**: SQLite (dev) / MySQL (prod)

- **Fila**: `database` driver

- **Linting**: Laravel Pint (PSR-12)

- **Testes**: PHPUnit 11+

## Comandos essenciais

```bash

composer setup # Instala tudo (deps, .env, key, migrate, build)

composer dev # Inicia servidor + queue + logs + vite em paralelo

php artisan test # Roda testes

./vendor/bin/pint # Formata código (PSR-12)

```

## Autenticação

- **Google OAuth 2.0** (restrito a `@letwe.com.br`).

- **Dev login**: `GET /auth/dev-login` → loga como `admin@letwe.com.br` (apenas env `local`).


## Regras para codificação

1. **Sempre em módulo**: Lógica de negócio vai dentro de `Modules/<Nome>/`. Nunca no `app/` a não ser para services compartilhados.

2. **Scaffolding**: Use `php artisan module:make-controller|model|migration NomeDoArtefato NomeDoModulo`.

3. **Namespaces**: `Modules\<Nome>\Http\Controllers\...`, `Modules\<Nome>\Models\...`.

4. **Views**: Referenciadas como `'modulo::view'` (ex: `view('clients::index')`).

5. **PSR-12**: Obrigatório. Type hints em argumentos e retornos.

6. **Migrations**: Sempre forneça `down()` funcional.

7. **IDs de elementos HTML**: `id="btn-nome-acao"` para compatibilidade com testes automatizados.

8. **Encapsulamento**: Módulos não acessam banco de outro módulo diretamente.

9. **TailwindCSS v4**: Use classes utilitárias com moderação. Prefira componentes Blade.



## Testes

- **Unitários/Feature**: `tests/` + `Modules/<Nome>/tests/`

- **UI automatizado**: `testsprite_tests/` (TestSprite)

- **Dev login**: `GET /auth/dev-login` para testes locais

- **Rodar**: `php artisan test`


## Banco de dados

- **Dev**: `database/database.sqlite`

- **Prod**: MySQL (config em `.env`)

- **Migrations globais**: `database/migrations/` 

- **Migrations por módulo**: `Modules/<Nome>/database/migrations/`

- **Session driver**: `database`

- **Cache/Queue driver**: `database`