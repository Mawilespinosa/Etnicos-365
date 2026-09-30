# AGENTS.md

## Proyecto

**Etnicos-365** — app web Laravel para una fábrica de jeans: producción (8 etapas), inventario, ventas y finanzas. El código vive aquí; la planeación/documentos de cliente están en `docs/` (plan, propuesta comercial, diagrama de despliegue, manual de usuario).

## Estado real (fases 0-9 completadas y verificadas)

Aplicación **completa y funcional**:

- **Auth manual + RBAC**: 5 roles (admin, producción, inventario/bodega, ventas, finanzas) y **51 permisos** (`module.action`, ver `database/seeders/PermissionSeeder.php`). Middleware `EnsurePermission` (`permission:module.action`), throttle en login.
- **CRUDs**: vendedores, clientes, proveedores, productos (con BOM integrada en show), materias primas, usuarios y roles. Form Requests en todas las entradas.
- **Producción**: órdenes de trabajo (OT, código `OT-0001`…) con **8 etapas fijas** definidas en `config/production.php` (compra de tela → corte → confección → pulido → lavandería → empaque → bodega → distribución). Avance secuencial obligatorio; al completar la etapa 7 (bodega) se genera el movimiento `in` al inventario; la 8 (distribución) cierra la OT.
- **Inventario**: stock por producto (tabla `inventory` + `products.stock_qty` sincronizados), movimientos `in/out/adjustment` (morphs), alertas de stock mínimo.
- **Ventas/facturación**: contado y crédito (pagos parciales), `payment_status` (paid/partial/pending), factura imprimible, confirmación (descuenta stock + registra ingreso) y cancelación (revierte ambos).
- **Finanzas**: ingresos (morph, tipo `sale`/`other`), egresos (categorías raw_material/labor/services/other), utilidad por rango de fechas.
- **Dashboard + reportes**: KPIs (ventas del día/mes, OT activas, stock bajo, utilidad del mes); reportes de ventas/inventario/financiero en **PDF (barryvdh/laravel-dompdf)** y **CSV compatible con Excel** (UTF-8 BOM, `;`).
- **Tienda pública (storefront)**: `PublicStoreController` + rutas `/tienda/*` **sin auth** (`routes/web.php:158-169`): catálogo (solo productos `is_active` y `stock_qty > 0`), carrito en sesión, checkout invitado (crea `Sale` `confirmed`/`pending`, `client_id` y `seller_id` nulos), pago **PSE simulado** (`pse/pay` + `pse/callback`: al pagar crea el `Income` y marca `paid`). Usa los mismos modelos de ventas/inventario, no crea tablas nuevas.
- **Tests**: 177 tests (PHPUnit, SQLite `:memory:`, 647 assertions) — pero **3 fallan** actualmente (ver nota siguiente).

**Nota sobre tests (¡importante!)**:
- `ProductFactory` asigna `model`, `name`, `category` y `code` aleatorios. Toda búsqueda que incluya la columna `model` (o `category`) es **flaky**: `ProductCrudTest::test_admin_can_search_products` y `PublicStoreTest::test_catalog_search_works` fallan si un producto "Jean Slim" recibe `model='Clásico'` aleatorio. Si tocas tests/factory de productos, fija `model`/`category` explícitamente en los productos creados dentro del test.
- **3 tests fallando al día de hoy** (feature `tienda` aún en progreso / expectativas desincronizadas de la implementación), todos en `tests/Feature/PublicStoreTest.php`:
  - `test_catalog_search_works` — flaky por `model` aleatorio (ver arriba).
  - `test_process_checkout_fails_when_insufficient_stock` — el controller **recorta** la cantidad al stock disponible (`min()` en `PublicStoreController::processCheckout`), así que nunca falla por stock; sí se crea la venta, y el test espera que NO se cree.
  - `test_unauthenticated_user_can_access_store_routes` — `/tienda/checkout` redirige (302) con carrito vacío, pero el test espera `assertOk()`.

## Stack y entorno (verificado)

- **Laravel 13.25.0** (PHP minimum 8.3) + **MySQL 8.0.30** + Blade + Tailwind CSS v4 (Vite) + Alpine.js (dependencia npm, no CDN).
- PHP activo: **8.3.33 NTS** en `C:\laragon\bin\php\php-8.3.33-nts-Win32-vs16-x64`. Composer 2.10.2, Node v22/npm 10.
- Base de datos: **`etnicos_365`**, usuario `root`, **sin contraseña** (configurado en `.env`).
- Paquete clave: `barryvdh/laravel-dompdf` ^3.1 (reportes PDF). No se usa maatwebsite/excel (los CSV son nativos). `laravel/pint` está en `require-dev` (formato PHP, sin `pint.json`; `vendor/bin/pint`).
- Modelo de datos: **21 tablas de negocio** (migraciones `2026_08_19_*` + 2 de `2026_08_21_*`) + tablas del framework (`users`, `cache`, `jobs`). Incluye `sale_payments` (pagos parciales de ventas a crédito). Las migraciones `2026_08_21_*` agregan `image` a `products` y hacen `client_id`/`seller_id` **nullable** en `sales` (checkout invitado de la tienda).

### Gotchas críticos del entorno

- **Terminales viejas ven PHP 8.2.30** (PATH de máquina corregido a 8.3, pero procesos/terminales abiertos antes conservan el PATH viejo) → Composer lanza `platform_check.php: Composer detected issues`. Si un comando falla así, **cierra y abre terminal nueva** y verifica `php -v`.
- Rutas de acceso:
  - Vhost (recomendado): `http://Etnicos-365.test/` (DocumentRoot = `public/`)
  - Por IP: `http://<IP>/Etnicos-365/public/` — las IPs las asigna DHCP y **cambian** (consultar con `ipconfig`).
  - Dev server: `php artisan serve` → `http://127.0.0.1:8000`
- Apache usa **FastCGI** (`php-cgi.exe`), config de PHP activa en `C:\laragon\etc\apache2\fcgid.conf` y `C:\laragon\usr\laragon.ini`.
- Los tests usan **SQLite `:memory:`** (`phpunit.xml`), no tocan MySQL.

## Comandos

```bash
php artisan serve                 # servidor dev (nueva terminal) → http://127.0.0.1:8000
php artisan migrate               # ejecutar migraciones
php artisan migrate:fresh --seed  # recrear BD + seeders (solo dev)
php artisan db:seed               # ejecutar seeders sobre BD existente
php artisan make:model -mcr X     # modelo + migración + controller + factory
npm install && npm run build      # compilar Tailwind/Vite (build de assets para producción)
npm run dev                       # Vite dev server (hot reload)
php artisan test                  # suite completa (PHPUnit; config en phpunit.xml)
php artisan route:list            # listar rutas
php artisan tinker                # consola interactiva
composer run dev                  # server + queue + pail + vite (concurrently)
composer setup                    # install + .env + key + migrate + npm build (fresh install)
```

Sin config extra de lint/typecheck — PHP 8.3 nativo. `composer run test` ejecuta `config:clear` + `php artisan test`. `vendor/bin/pint` (formato) está disponible. Para un solo test: `php artisan test --filter=NombreDelTest`.

## Estructura (rutas clave)

- `app/Http/Controllers/` — controladores por módulo (Auth, User, Role, Seller, Client, Supplier, Product, RawMaterial, ProductionOrder, Inventory, Sale, Finance, Dashboard, Report, PublicStore).
- `app/Http/Requests/` — Form Requests de validación (una por operación).
- `app/Http/Middleware/EnsurePermission.php` — autorización `permission:module.action`.
- `app/Models/` — 18 modelos (ver lista en `app/Models/`); atributos con `#[Fillable([...])]` (PHP 8 attributes).
- `app/Policies/` — `RolePolicy`, `UserPolicy` (protección: admin no se elimina a sí mismo, rol admin no se borra).
- `config/production.php` — las 8 etapas fijas de producción.
- `config/sales.php` — prefijo de factura (`FAC-`) y tasa de IVA (0.19).
- `database/migrations/`, `database/factories/`, `database/seeders/` — modelo de datos completo.
- `resources/views/` — vistas Blade en español (layout con sidebar por permisos, módulos por carpeta; `resources/views/store/` para la tienda pública).
- `routes/web.php` — rutas auth+permission; la tienda pública va **sin auth** al final (`/tienda/*`, prefijo `store.`).
- `tests/Feature/` — 17 archivos de tests de módulos + seguridad + tienda (`PublicStoreTest`).

## Convenciones

- Código en **inglés** (PSR-4 `App\` → `app/`); la **UI/mensajes en español** (clientes).
- Documentación de negocio/documentos en español en `docs/`.
- No documentar en .docx a menos que se pida: requiere PHP vía Laragon (sin Pandoc/Python). Usar Markdown.
- Moneda en **COP** con formato colombiano; IVA 19% (`config/sales.php`).
- Códigos autoincrementales: OT `OT-0001`, facturas `FAC-0001`.

## Reglas de desarrollo (obligatorias para todos los agentes)

> Este es el archivo de reglas. **Todo agente (backend, frontend, revisor, seguridad, planificador) DEBE leer y cumplir esta sección antes de crear o modificar cualquier archivo.** Para agregar una regla nueva, se añade como un nuevo punto al final con su número correlativo.

1. **Seguir el plan y los docs**: antes de implementar algo, consultar `docs/` (plan, propuesta). No inventar alcance.
2. **No romper lo existente**: correr `php artisan test` después de cada cambio relevante; verificar que las migraciones corren (`php artisan migrate:fresh --seed` solo en dev).
3. **Convenciones del proyecto**: código en inglés (PSR-4), UI/mensajes en español, respetar las convenciones ya escritas arriba.
4. **No registrar secretos**: nunca poner credenciales/contraseñas en código, commits ni `.env.example`. Usar variables de entorno y `config/`.
5. **Migraciones**: definir el modelo de datos en migraciones y factories primero; no crear tablas a mano en la BD.
6. **Validación y seguridad**: validar toda entrada de usuario en el servidor (Form Requests), usar `auth`, escapar salidas en Blade, prevenir inyección SQL y XSS.
7. **Responsive y accesible**: las vistas (frontend) deben ser responsivas y accesibles; seguir el estilo Tailwind existente.
8. **Calidad**: no dejar `dd()`, `dump()`, código muerto ni TODO sin resolver al finalizar una tarea.
9. **Commits**: solo commitear cuando el usuario lo pida; mensajes claros en el estilo del repo; no subir `vendor/`, `.env` ni `node_modules`.
10. **Ante duda**: preguntar al usuario antes de tomar decisiones de diseño/alcance que no estén definidas en `docs/`.

## Alcance implementado (de docs/plan, fases 0-9)

1. Autenticación manual + RBAC (5 roles, 51 permisos) — sin Breeze.
2. CRUD: vendedores, clientes, proveedores, productos (+ BOM), materias primas, usuarios, roles.
3. Producción: órdenes de trabajo con **8 etapas en orden fijo**: compra de tela → corte → confección → pulido → lavandería → empaque → bodega → distribución.
4. Inventario (stock terminado, movimientos, alertas stock mínimo).
5. Ventas/facturación (contado y crédito con pagos parciales); finanzas (ingresos, egresos, utilidad); dashboard + reportes PDF/CSV.
6. Tienda pública (storefront) con catálogo, carrito y checkout invitado + PSE simulado.

## Git

Repo en rama **`main`**, 4 commits (fases 0-9 y tienda ya commiteadas; working tree limpio). No subir `vendor/`, `.env` ni `node_modules` (Laravel ya trae `.gitignore`).

## Nota: `docs/AGENTS.md`

`docs/` tiene su propio `AGENTS.md` (contexto de planeación/documentos de cliente, en su mayoría histórico y algo desactualizado — por ej. dice "aún NO es un repo git"). Para la aplicación, esta es la fuente vigente.