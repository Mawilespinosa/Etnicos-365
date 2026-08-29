# Etnicos-365

Sistema web para la gestión integral de una **fábrica de jeans**: producción (8 etapas), inventario, ventas/facturación y finanzas, con dashboard de indicadores y reportes exportables (PDF/CSV).

Aplicación **completa y funcional** construida con Laravel. Documentación de negocio y plan maestro en [`docs/`](docs/) y [`PLAN.md`](PLAN.md).

---

## Características

| Módulo | Funcionalidad |
|---|---|
| **Autenticación y RBAC** | Login manual (sin Breeze), 5 roles (admin, producción, inventario/bodega, ventas, finanzas) y 51 permisos `module.action` con middleware `permission:`. |
| **Catálogos (CRUD)** | Vendedores, clientes, proveedores, productos (con lista de materiales BOM integrada) y materias primas. Búsqueda, paginación, mensajes flash en español. |
| **Producción** | Órdenes de trabajo (OT) con código autoincremental `OT-0001` y **8 etapas fijas** en orden: compra de tela → corte → confección → pulido → lavandería → empaque → bodega → distribución. Avance secuencial; al completar la etapa 7 el producto entra al inventario. |
| **Inventario** | Stock por producto, movimientos `in/out/adjustment` con trazabilidad (morphs) y alertas de stock mínimo. |
| **Ventas** | Contado y crédito con **pagos parciales** (`paid`/`partial`/`pending`), factura imprimible `FAC-0001`, confirmación (descuenta stock + registra ingreso) y cancelación (revierte ambos). |
| **Finanzas** | Ingresos (morph, tipo `sale`/`other`), egresos (categorías), utilidad por rango de fechas. |
| **Dashboard y reportes** | KPIs (ventas del día/mes, OT activas, stock bajo, utilidad del mes) y reportes de ventas/inventario/financiero en **PDF** (dompdf) y **CSV** compatible con Excel (UTF-8 BOM, `;`). |
| **Pruebas** | 147 tests PHPUnit (534 assertions) sobre SQLite `:memory:`. |

## Stack

- **Backend**: Laravel 13.25.0 (PHP ≥ 8.3)
- **Base de datos**: MySQL 8 (en dev: `etnicos_365`, usuario `root`, sin contraseña; tests usan SQLite `:memory:`)
- **Frontend**: Blade + Tailwind CSS v4 (Vite) + Alpine.js
- **PDF**: `barryvdh/laravel-dompdf` ^3.1 (CSV nativos, sin maatwebsite/excel)

## Requisitos

- PHP **8.3+** (verificado con 8.3.33 NTS), extensiones: `pdo_mysql`, `sqlite3`, `mbstring`, `gd`/`dom` (para dompdf)
- Composer 2.x
- Node.js 20+/npm 10 (para compilar assets)
- MySQL 8 (o SQLite si solo se quiere probar)
- Recomendado: **Laragon 6** en Windows (documentado y verificado)

## Setup paso a paso

> Ruta de ejemplo en Laragon: `C:\laragon\www\Etnicos-365`

```bash
# 1. Clonar / ubicarse en el proyecto
git clone <repo> Etnicos-365
cd Etnicos-365

# 2. Instalar dependencias de PHP
composer install

# 3. Crear el archivo .env (si no existe)
copy .env.example .env          # Windows
# cp .env.example .env          # Linux/macOS

# 4. Configurar la base de datos en .env (para MySQL)
#    DB_CONNECTION=mysql
#    DB_HOST=127.0.0.1
#    DB_PORT=3306
#    DB_DATABASE=etnicos_365
#    DB_USERNAME=root
#    DB_PASSWORD=

# 5. Generar APP_KEY y crear/rellenar la base de datos
php artisan key:generate
php artisan migrate:fresh --seed

# 6. Instalar dependencias frontend y compilar assets
npm install
npm run build

# 7. Levantar el servidor de desarrollo
php artisan serve
```

Abrir **http://127.0.0.1:8000** (o el vhost `http://Etnicos-365.test/` si se usa Laragon con DocumentRoot = `public/`).

### Comando todo-en-uno

```bash
composer run setup   # composer install + .env + key:generate + migrate + npm install + npm run build
```

## Credenciales demo

> ⚠️ Solo desarrollo local. En cualquier otro entorno define `ADMIN_PASSWORD` en `.env` (el seeder lo respeta).

| Rol | Correo | Contraseña (demo) |
|---|---|---|
| **Administrador** | `admin@etnicos365.com` | `Admin123!` |

El seeder `AdminUserSeeder` usa `env('ADMIN_PASSWORD') ?: 'Admin123!'`. Usuarios adicionales por rol pueden crearse desde **Usuarios** (módulo de administración).

## Estructura de carpetas (clave)

```
app/Http/Controllers/     # Controladores por módulo (15)
app/Http/Requests/        # Form Requests de validación (27)
app/Http/Middleware/      # EnsurePermission (permission:module.action)
app/Models/               # 18 modelos (atributos #[Fillable([...])])
app/Policies/             # RolePolicy, UserPolicy
config/production.php     # 8 etapas fijas de producción
config/sales.php          # Prefijo de factura y tasa de IVA (0.19)
database/migrations/      # 20 tablas de negocio + framework
database/factories/       # Factories para tests/seeders
database/seeders/         # Roles, permisos, admin, datos demo
resources/views/          # Vistas Blade en español
routes/web.php            # Rutas web (auth + permission:)
tests/Feature/            # 15 archivos de tests
docs/                     # Plan, propuesta comercial, manual de usuario
PLAN.md                   # Plan maestro (fases y avance)
```

## Comandos útiles

```bash
php artisan serve                 # servidor dev → http://127.0.0.1:8000
php artisan migrate               # aplicar migraciones
php artisan migrate:fresh --seed  # recrear BD y sembrar (SOLO dev)
php artisan db:seed               # sembrar sobre BD existente
php artisan test                  # suite completa de tests
composer run test                 # idem (limpia config antes)
php artisan route:list            # listar rutas con middleware
php artisan tinker                # consola interactiva
npm run dev                       # Vite hot reload
npm run build                     # compilar assets de producción
```

## Notas de entorno (Windows/Laragon)

- Si Composer reporta `platform_check.php: Composer detected issues`, la terminal abierta conserva un PATH con PHP 8.2. **Cierra y abre terminal nueva** y verifica `php -v`.
- Rutas de acceso: vhost `http://Etnicos-365.test/` (recomendado), por IP `http://<IP>/Etnicos-365/public/` (la IP cambia, ver `ipconfig`), o `php artisan serve`.
- Tests usan SQLite `:memory:` (`phpunit.xml`) y **no tocan** MySQL.

## Solución de problemas (resumen)

| Problema | Causa / Solución |
|---|---|
| `Composer detected issues` | Terminal con PATH viejo → abrir terminal nueva |
| Página en blanco al compilar CSS | Falta `npm run build` (o usar `npm run dev`) |
| `Base de datos no encontrada` | Crear `etnicos_365` en MySQL o cambiar `DB_*` en `.env` |
| Test de búsqueda de productos falla | Flaky conocido (factory con `model` aleatorio) → re-ejecutar |

## Documentación relacionada

- [`PLAN.md`](PLAN.md) — plan maestro, modelo de datos (20 tablas), rutas, matriz de permisos y avance de fases.
- [`docs/manual-usuario.md`](docs/manual-usuario.md) — manual de uso para el usuario final.
- [`docs/Documento_de_despliegue_Gestion_Produccion_Jean.docx`](docs/Documento_de_despliegue_Gestion_Produccion_Jean.docx) — guía de despliegue (cliente).
- [`docs/Propuesta_Comercial_Gestion_Produccion_Jean.docx`](docs/Propuesta_Comercial_Gestion_Produccion_Jean.docx) — propuesta comercial.
- [`AGENTS.md`](AGENTS.md) — contexto y reglas para agentes de IA.

## Licencia

Proyecto interno de la fábrica de jeans **Etnicos-365** (código basado en Laravel, MIT).