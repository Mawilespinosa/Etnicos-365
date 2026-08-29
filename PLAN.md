# PLAN.md — Etnicos-365

> Plan de trabajo maestro del proyecto **Etnicos-365**: app web Laravel para una fábrica de jeans
> (producción, inventario, ventas y finanzas). Este archivo es la fuente de verdad operativa del equipo;
> cada subagente debe actualizar la sección **Avance** al terminar sus tareas.

---

## 1. Resumen ejecutivo

Etnicos-365 es una aplicación web modular que administra el ciclo completo de una fábrica de jeans:
desde la compra de materias primas y el proceso productivo (8 etapas en orden fijo) hasta el inventario
de producto terminado, las ventas/facturación y las finanzas (ingresos, egresos, utilidad), con dashboard
de indicadores y exportación de reportes (PDF/Excel).

- **Stack**: Laravel 13.25.0 (PHP 8.3.33 NTS), MySQL 8.0.30 (`etnicos_365`), Blade + Tailwind CSS v4 (Vite), Alpine.js.
- **Entorno dev**: Laragon 6.0 en Windows. Vhost recomendado `http://Etnicos-365.test/`; dev server `php artisan serve` → `http://127.0.0.1:8000`.
- **Convenciones**: código en inglés (PSR-4 `App\` → `app/`), UI/mensajes en español, documentación de negocio en español en `docs/`.
- **Estado actual**: **aplicación completa** — fases 0-9 implementadas y verificadas (147 tests, 534 assertions; 1 test flaky conocido en búsqueda de productos). Fase 10 de documentación completada (README, manual de usuario, AGENTS.md al día).

## 2. Alcance (contrato con el cliente)

1. Autenticación y RBAC (login/logout manual, sin Breeze).
2. CRUD: vendedores, clientes, proveedores, productos, materias primas + lista de materiales (BOM).
3. Producción: órdenes de trabajo (OT) con 8 etapas en orden fijo.
4. Inventario: stock de producto terminado, movimientos, alertas de stock mínimo.
5. Ventas/facturación: registro de ventas con detalle y factura imprimible.
6. Finanzas: ingresos, egresos, utilidad; dashboard con KPIs y reportes exportables.

## 3. Decisiones técnicas (acordadas)

| Decisión | Opción elegida | Justificación |
|---|---|---|
| Autenticación | **Manual** (sin Breeze) | Control total sobre RBAC y middleware propio |
| RBAC | 5 roles + permisos por módulo | admin, producción, inventario/bodega, ventas, finanzas |
| Etapas de producción | Constantes en `config/production.php` + tabla `production_order_stages` | Orden fijo garantizado a nivel de código, avance trazable por OT |
| Stock producto terminado | Tabla `inventory` + `inventory_movements` (morphs) | Trazabilidad de entradas/salidas/ajustes |
| Stock materias primas | Columnas `stock_qty`/`min_stock` en `raw_materials` | Control básico sin complejidad extra (extensible) |
| Reportes | `barryvdh/laravel-dompdf` (PDF) + CSV nativo (Excel) | Ligero; `maatwebsite/excel` como alternativa si se exige `.xlsx` |
| Interactividad | Alpine.js (CDN vía Vite) | Ligero, no cambia el stack Blade+Tailwind |
| Tests | PHPUnit nativo (SQLite `:memory:` ya configurado); Pest opcional | Sin dependencias nuevas obligatorias |
| Moneda | COP con formato colombiano | Según histórico de la propuesta comercial |
| Locale | `APP_LOCALE=es` (cambiar en `.env`) | UI en español |

## 4. Fases del trabajo

### Fase 0 — Setup, modelo de datos y seeders
**Tareas**
1. Cambiar `APP_LOCALE=es` y `APP_FAKER_LOCALE=es_CO` en `.env`.
2. Crear **todas las migraciones** del modelo de datos (sección 5) en orden de dependencias.
3. Crear **factories** para todos los modelos (datos válidos de prueba).
4. Crear **seeders**: `RoleSeeder` (5 roles), `PermissionSeeder` (permisos por módulo), `RolePermissionSeeder` (matriz rol→permisos), `AdminUserSeeder` (admin inicial), `DemoDataSeeder` (catálogos demo: clientes, vendedores, proveedores, productos, materias primas).
5. Crear `config/production.php` con las 8 etapas fijas.
6. (Opcional) Instalar Pest si el equipo lo prefiere.

**Criterios de aceptación**
- `php artisan migrate:fresh --seed` corre sin errores en dev.
- La BD `etnicos_365` contiene las 20 tablas del modelo.
- Los seeders crean roles, permisos, admin y datos demo verificables con `php artisan tinker`.
- `php artisan test` pasa (tests de ejemplo intactos).

### Fase 1 — Autenticación y RBAC
**Tareas**
1. Controlador `AuthController` (login/logout manual) + vistas `auth/login.blade.php`.
2. Middleware `EnsurePermission` (`permission:module.action`) registrado en `bootstrap/app.php`.
3. Modelos `Role`, `Permission` con relaciones; pivotes `role_permission` y `user_role`.
4. Relaciones en `User`: `roles()`, `hasPermission()`, `hasRole()`.
5. CRUD de usuarios (asignación de roles) y CRUD de roles (asignación de permisos) con Form Requests.
6. Seeder de admin + usuarios demo por rol.
7. Tests de autenticación y autorización.

**Criterios de aceptación**
- Usuario no autenticado → redirigido a `/login`.
- Usuario sin permiso → HTTP 403 (vista `errors/403.blade.php`).
- Admin puede crear usuarios, asignar roles y editar permisos por rol; los pivotes persisten.
- `php artisan test` en verde (tests de auth/RBAC).

### Fase 2 — CRUDs de catálogo
**Tareas**
1. CRUD `sellers` (vendedores), `clients` (clientes), `suppliers` (proveedores): index con búsqueda/paginación, create, edit, delete (soft delete opcional).
2. CRUD `products` (productos) y `raw_materials` (materias primas).
3. CRUD de **BOM** (`bill_of_materials`) integrado en la vista show/edit de producto (agregar/quitar materias primas con cantidad).
4. Form Requests de validación para cada entidad.
5. Tests por CRUD (create/update/delete con factories).

**Criterios de aceptación**
- Todos los CRUDs validan entrada en servidor y muestran mensajes flash en español.
- BOM: restricción única `(product_id, raw_material_id)`; no se permiten duplicados.
- `php artisan test` en verde.

### Fase 3 — Producción (OT + 8 etapas)
**Tareas**
1. Modelo `ProductionOrder` con `code` autoincremental (`OT-0001`), `current_stage`, `status`.
2. Modelo `ProductionOrderStage` (una fila por etapa de cada OT).
3. Al crear una OT: generar las 8 etapas en orden fijo (estado `pending`).
4. Acción `advance`: solo permite completar la etapa actual (`current_stage`) y pasar a la siguiente; no se puede saltar etapas.
5. Al completar la etapa 7 (bodega): generar movimiento de entrada al inventario (cantidad de la OT).
6. Al completar la etapa 8 (distribución): OT pasa a `completed`.
7. Vista show con timeline de etapas y botón "Avanzar etapa" (según permisos).
8. Tests del flujo completo 1→8 y de bloqueo de saltos.

**Criterios de aceptación**
- Crear OT genera exactamente 8 etapas en el orden: compra de tela → corte → confección → pulido → lavandería → empaque → bodega → distribución.
- No se puede avanzar a la etapa N+1 sin completar la N.
- Completar etapa 7 incrementa el stock del producto (movimiento `in`).
- `php artisan test` en verde.

### Fase 4 — Inventario
**Tareas**
1. Modelo `Inventory` (stock por producto) sincronizado con `products.stock_qty`.
2. Modelo `InventoryMovement` (type: in/out/adjustment; referencia morph a OT o venta).
3. Registro de movimientos manuales (ajustes) con motivo.
4. Vista de movimientos con filtros (producto, tipo, fecha).
5. Vista de alertas: productos con `stock_qty <= min_stock`.
6. Tests de movimientos y alertas.

**Criterios de aceptación**
- Cada movimiento in/out ajusta el stock del producto de forma consistente.
- La lista de alertas muestra solo productos bajo mínimo.
- `php artisan test` en verde.

### Fase 5 — Ventas y facturación
**Tareas**
1. Modelos `Sale` y `SaleItem`; `invoice_number` autoincremental (`FAC-0001`).
2. Creación de venta con detalle dinámico (Alpine.js): producto, cantidad, precio; cálculo de subtotal, descuento, IVA y total.
3. Al confirmar venta (`draft → confirmed`): generar movimiento `out` de inventario y registro en `incomes` (tipo `sale`).
4. Vista de factura imprimible (`sales/{sale}`) con datos de cliente, vendedor, items y totales.
5. Cancelación de venta (devuelve stock, anula ingreso).
6. Tests de totales, descuento de stock y creación de ingreso.

**Criterios de aceptación**
- Los totales de la venta se calculan correctamente (subtotal − descuento + IVA = total).
- Confirmar venta descuenta stock y crea el ingreso; cancelar revierte ambos.
- `php artisan test` en verde.

### Fase 6 — Finanzas, dashboard y reportes
**Tareas**
1. CRUD de `incomes` (ingresos) y `expenses` (egresos) con categorías.
2. Resumen financiero: total ingresos, total egresos, utilidad (ingresos − egresos) por rango de fechas.
3. Dashboard (`/dashboard`) con KPIs: ventas del día/mes, OT activas, stock bajo, utilidad del mes.
4. Reportes exportables: ventas, inventario, financiero → PDF (dompdf) y CSV (Excel).
5. Tests de cálculos y de descarga de reportes.

**Criterios de aceptación**
- Los cálculos de utilidad son correctos y consistentes con ventas/egresos registrados.
- El dashboard muestra los KPIs con datos reales.
- Los reportes descargan archivos PDF y CSV válidos.
- `php artisan test` en verde.

### Fase 7 — Frontend y pulido
**Tareas**
1. Layout `layouts/app.blade.php`: sidebar responsivo que oculta módulos sin permiso, header con usuario y logout.
2. Revisar todas las vistas: responsive (Tailwind), accesibles (labels, contraste, foco), mensajes en español.
3. Estados vacíos, loading y mensajes flash consistentes.
4. `npm run build` y verificación visual en navegador.

**Criterios de aceptación**
- La app es usable en móvil y escritorio.
- La navegación solo muestra módulos permitidos al rol.
- No quedan textos en inglés en la UI.
- `npm run build` compila sin errores.

### Fase 8 — Seguridad
**Tareas**
1. Form Requests en todas las entradas; autorización con middleware `permission:` y policies donde aplique.
2. Rate limiting en login (`throttle`), CSRF activo, escapado de salidas en Blade.
3. Revisar `$fillable`/`$guarded` en todos los modelos (protección mass assignment).
4. Verificar que no hay secretos en el repo ni en `.env.example`.
5. Tests de seguridad: no autenticado → redirect; sin permiso → 403; entradas inválidas → errores de validación.

**Criterios de aceptación**
- Ninguna ruta protegida es accesible sin autenticación/permiso.
- `php artisan test` en verde (incluye tests de seguridad).

### Fase 9 — Pruebas integrales
**Tareas**
1. Suite completa de tests (unit + feature) por módulo.
2. Flujo end-to-end crítico: login → crear OT → avanzar 8 etapas → stock → venta → finanzas.
3. `php artisan migrate:fresh --seed` + `php artisan test` en verde.
4. Checklist de QA manual (navegador) de los 6 módulos.

**Criterios de aceptación**
- `php artisan test` pasa al 100%.
- El flujo crítico funciona de punta a punta sin errores.

### Fase 10 — Documentación
**Tareas**
1. Actualizar `README.md` (setup, comandos, credenciales demo).
2. Actualizar `docs/` (manual de usuario en español, guía de despliegue).
3. Actualizar la sección **Avance** de este `PLAN.md`.

**Criterios de aceptación**
- README y docs reflejan el estado final del sistema.
- `PLAN.md` con todas las fases marcadas como completadas.

## 5. Modelo de datos (20 tablas, nombres en inglés)

### Auth y RBAC
| Tabla | Columnas clave | Relaciones |
|---|---|---|
| `users` (existe) | id, name, email (unique), password, email_verified_at, remember_token, **is_active** (bool, default true), timestamps | hasMany user_role |
| `roles` | id, name (unique), display_name, description, timestamps | belongsToMany permissions (vía role_permission) |
| `permissions` | id, name (unique, `module.action`), module, display_name, description, timestamps | belongsToMany roles |
| `role_permission` (pivote) | role_id FK, permission_id FK; PK(role_id, permission_id) | — |
| `user_role` (pivote) | user_id FK, role_id FK; PK(user_id, role_id) | — |

### Catálogos
| Tabla | Columnas clave | Relaciones |
|---|---|---|
| `sellers` | id, user_id (FK nullable), name, document_type, document_number (unique), phone, email, address, city, commission_rate, is_active, timestamps | belongsTo users (opcional); hasMany sales |
| `clients` | id, name, document_type, document_number (unique), phone, email, address, city, is_active, timestamps | hasMany sales |
| `suppliers` | id, name, document_type, document_number (unique), phone, email, address, city, contact_name, is_active, timestamps | — |
| `raw_materials` | id, code (unique), name, category, unit (unit/meter/kg/roll), stock_qty, min_stock, cost, is_active, timestamps | belongsToMany products (vía bill_of_materials) |
| `products` | id, code (unique), name, description, size, color, model, category, cost, price, stock_qty, min_stock, is_active, timestamps | belongsToMany raw_materials (BOM); hasMany production_orders, sale_items, inventory |
| `bill_of_materials` | id, product_id FK, raw_material_id FK, quantity, unit, notes, timestamps; **unique(product_id, raw_material_id)** | belongsTo products, raw_materials |

### Producción
| Tabla | Columnas clave | Relaciones |
|---|---|---|
| `production_orders` | id, code (unique `OT-0001`), product_id FK, quantity, current_stage (1–8), status (pending/in_progress/completed/cancelled), notes, created_by FK users, started_at, completed_at, timestamps | belongsTo products, users; hasMany production_order_stages |
| `production_order_stages` | id, production_order_id FK, stage_number (1–8), name (etiqueta en español), status (pending/in_progress/completed), notes, completed_by FK users, completed_at, timestamps; **unique(production_order_id, stage_number)** | belongsTo production_orders |

**Etapas fijas** (constantes en `config/production.php`):
1. fabric_purchase — Compra de tela
2. cutting — Corte
3. sewing — Confección
4. polishing — Pulido
5. laundry — Lavandería
6. packaging — Empaque
7. warehouse — Bodega
8. distribution — Distribución

### Inventario
| Tabla | Columnas clave | Relaciones |
|---|---|---|
| `inventory` | id, product_id FK (unique), location, stock_qty, min_stock, timestamps | belongsTo products |
| `inventory_movements` | id, product_id FK, type (in/out/adjustment), quantity, reference_type (morph), reference_id (morph), reason, user_id FK, timestamps | belongsTo products, users; morphTo reference |

### Ventas
| Tabla | Columnas clave | Relaciones |
|---|---|---|
| `sales` | id, invoice_number (unique `FAC-0001`), client_id FK, seller_id FK (nullable), sale_date, subtotal, discount, tax, total, status (draft/confirmed/cancelled), notes, timestamps | belongsTo clients, sellers; hasMany sale_items |
| `sale_items` | id, sale_id FK, product_id FK, quantity, unit_price, subtotal, timestamps | belongsTo sales, products |

### Finanzas
| Tabla | Columnas clave | Relaciones |
|---|---|---|
| `incomes` | id, type (sale/other), reference_type (morph), reference_id (morph), description, amount, income_date, user_id FK, timestamps | morphTo reference |
| `expenses` | id, category (raw_material/labor/services/other), description, amount, expense_date, user_id FK, timestamps | — |

## 6. Endpoints / rutas principales (web)

Todas las rutas de negocio usan `auth` + middleware `permission:module.action`.

### Auth y dashboard
| Método | Ruta | Permiso | Descripción |
|---|---|---|---|
| GET | `/login` | guest | Formulario login |
| POST | `/login` | guest | Autenticar (throttle) |
| POST | `/logout` | auth | Cerrar sesión |
| GET | `/dashboard` | dashboard.view | KPIs |

### RBAC (solo admin)
| Método | Ruta | Permiso | Descripción |
|---|---|---|---|
| GET/POST | `/users`, `/users/create`, `/users/{user}/edit`, DELETE `/users/{user}` | users.* | CRUD usuarios + roles |
| GET/POST | `/roles`, `/roles/create`, `/roles/{role}/edit`, DELETE `/roles/{role}` | roles.* | CRUD roles + permisos |

### CRUDs de catálogo (resource)
| Módulo | Rutas resource | Permisos |
|---|---|---|
| Vendedores | `/sellers` | sellers.view/create/update/delete |
| Clientes | `/clients` | clients.* |
| Proveedores | `/suppliers` | suppliers.* |
| Productos | `/products` (+ BOM en show/edit) | products.*, bill_of_materials.* |
| Materias primas | `/raw-materials` | raw_materials.* |

### Producción
| Método | Ruta | Permiso | Descripción |
|---|---|---|---|
| GET/POST | `/production/orders`, `/production/orders/create` | production.view/create | Listar/crear OT |
| GET | `/production/orders/{order}` | production.view | Timeline de etapas |
| POST | `/production/orders/{order}/advance` | production.advance | Avanzar etapa actual |
| GET/PUT | `/production/orders/{order}/edit` | production.update | Editar OT |
| POST | `/production/orders/{order}/cancel` | production.update | Cancelar OT |
| DELETE | `/production/orders/{order}` | production.delete | Eliminar OT |

### Inventario
| Método | Ruta | Permiso | Descripción |
|---|---|---|---|
| GET | `/inventory` | inventory.view | Stock por producto |
| GET/POST | `/inventory/movements` | inventory.movements | Historial + registrar movimiento |
| GET | `/inventory/alerts` | inventory.view | Stock bajo mínimo |

### Ventas
| Método | Ruta | Permiso | Descripción |
|---|---|---|---|
| GET/POST | `/sales`, `/sales/create` | sales.view/create | Listar/crear venta |
| GET | `/sales/{sale}` | sales.view | Factura imprimible |
| POST | `/sales/{sale}/confirm` | sales.update | Confirmar (stock + ingreso) |
| POST | `/sales/{sale}/cancel` | sales.update | Cancelar (revierte) |
| DELETE | `/sales/{sale}` | sales.delete | Eliminar |

### Finanzas y reportes
| Método | Ruta | Permiso | Descripción |
|---|---|---|---|
| GET | `/finances` | finances.view | Resumen ingresos/egresos/utilidad |
| GET/POST | `/finances/incomes` | finances.create | Registrar ingreso |
| GET/POST | `/finances/expenses` | finances.create | Registrar egreso |
| GET | `/reports/sales` | reports.export | Reporte ventas (PDF/CSV) |
| GET | `/reports/inventory` | reports.export | Reporte inventario (PDF/CSV) |
| GET | `/reports/financial` | reports.export | Reporte financiero (PDF/CSV) |

## 7. Pantallas principales (Blade)

| Módulo | Vistas |
|---|---|
| Layout | `layouts/app.blade.php` (sidebar por permisos, header, flash) |
| Auth | `auth/login.blade.php` |
| Dashboard | `dashboard/index.blade.php` (KPI cards + gráficas simples) |
| RBAC | `users/index|create|edit`, `roles/index|create|edit` (checkbox de permisos) |
| Catálogos | `sellers/index|create|edit`, `clients/*`, `suppliers/*`, `products/index|create|edit|show` (con BOM), `raw_materials/*` |
| Producción | `production/orders/index|create|edit|show` (timeline de 8 etapas + botón avanzar) |
| Inventario | `inventory/index`, `inventory/movements`, `inventory/alerts` |
| Ventas | `sales/index|create|show` (factura imprimible) |
| Finanzas | `finances/index`, `finances/incomes`, `finances/expenses` |
| Reportes | `reports/index` (filtros + botones exportar) |
| Errores | `errors/403.blade.php` |

## 8. Matriz de permisos por rol (seed)

| Permiso | admin | producción | inventario/bodega | ventas | finanzas |
|---|---|---|---|---|---|
| users.*, roles.* | ✅ | — | — | — | — |
| sellers.* | ✅ | — | — | ✅ (view/create/update) | — |
| clients.* | ✅ | — | — | ✅ (view/create/update) | — |
| suppliers.* | ✅ | ✅ (view) | ✅ (view) | — | — |
| products.* | ✅ | ✅ (view) | ✅ (view/update) | ✅ (view) | ✅ (view) |
| raw_materials.* | ✅ | ✅ (view) | ✅ (view/update) | — | — |
| bill_of_materials.* | ✅ | ✅ (view) | — | — | — |
| production.* | ✅ | ✅ | ✅ (view) | — | — |
| inventory.* | ✅ | — | ✅ | ✅ (view) | — |
| sales.* | ✅ | — | — | ✅ | ✅ (view) |
| finances.* | ✅ | — | — | — | ✅ |
| reports.export | ✅ | — | — | — | ✅ |
| dashboard.view | ✅ | ✅ | ✅ | ✅ | ✅ |

## 9. Criterios de aceptación globales

- `php artisan migrate:fresh --seed` corre sin errores en dev.
- `php artisan test` pasa al 100% tras cada fase.
- Ninguna ruta protegida es accesible sin autenticación o permiso (403).
- UI responsiva, accesible y 100% en español.
- Sin `dd()`, `dump()`, código muerto ni TODOs al cerrar cada fase.
- Sin secretos en código, commits ni `.env.example`.

## 10. Avance

| Fase | Descripción | Estado |
|---|---|---|
| 0 | Setup, modelo de datos y seeders | ✅ Completada (2026-08-19, backend) |
| 1 | Autenticación y RBAC | ✅ Completada (2026-08-19, backend) |
| 2 | CRUDs de catálogo | ✅ Completada (2026-08-20, backend) |
| 3 | Producción (OT + 8 etapas) | ✅ Completada (2026-08-20, backend) |
| 4 | Inventario | ✅ Completada (2026-08-20, backend) |
| 5 | Ventas y facturación | ✅ Completada (2026-08-20, backend) |
| 6 | Finanzas, dashboard y reportes | ✅ Completada (2026-08-20, backend) |
| 7 | Frontend y pulido | ✅ Completada (2026-08-20, frontend) |
| 8 | Seguridad | ✅ Completada (2026-08-20, seguridad) |
| 9 | Pruebas integrales | ✅ Completada (2026-08-20, revisor) |
| 10 | Documentación | ✅ Completada (2026-08-20, documentador) |

> **Nota para subagentes**: al terminar una fase, marcar su estado como ✅ Completada (o 🟡 En curso) y anotar fecha y responsable.