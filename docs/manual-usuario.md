# Manual de usuario — Etnicos-365

Sistema de gestión para la fábrica de jeans **Etnicos-365**: producción, inventario, ventas y finanzas en una sola aplicación.

---

## 1. Introducción

Etnicos-365 es una aplicación web que le permite a la fábrica controlar el ciclo completo de su operación:

1. **Catálogos**: registrar vendedores, clientes, proveedores, productos y materias primas.
2. **Producción**: crear órdenes de trabajo (OT) y avanzarlas por 8 etapas hasta que el producto llega a bodega.
3. **Inventario**: conocer el stock disponible y las alertas de productos por debajo del mínimo.
4. **Ventas**: registrar ventas de contado o a crédito, imprimir facturas y registrar pagos.
5. **Finanzas**: llevar el control de ingresos y egresos y conocer la utilidad.
6. **Reportes**: exportar reportes de ventas, inventario y finanzas en PDF o Excel.

---

## 2. Cómo ingresar al sistema

1. Abra el navegador y vaya a la dirección del sistema:
   - Desarrollo: `http://127.0.0.1:8000` (o `http://Etnicos-365.test/` en Laragon).
   - Producción: la URL que su administrador de sistemas le indique.
2. En la pantalla de **Iniciar sesión** escriba su correo y contraseña.
3. Presione **Ingresar**.

> **Credenciales demo (solo desarrollo local)**: `admin@etnicos365.com` / `Admin123!`.
> En el servidor real, el administrador le asignará un usuario y una contraseña propios.

Si olvida la contraseña, contacte al administrador del sistema (no hay recuperación automática).

### Roles y qué puede ver cada usuario

| Rol | Qué puede hacer |
|---|---|
| **Administrador** | Todo: usuarios, roles, catálogos, producción, inventario, ventas, finanzas y reportes. |
| **Producción** | Ver catálogos (productos, materias primas, proveedores), crear y avanzar órdenes de producción por sus etapas. |
| **Inventario / Bodega** | Ver proveedores y producción, gestionar inventario y movimientos, editar productos y materias primas. |
| **Ventas** | Gestionar clientes y vendedores, ver productos e inventario, registrar ventas y pagos. |
| **Finanzas** | Ver productos y ventas, registrar ingresos/egresos, consultar finanzas y exportar reportes. |

> El menú lateral muestra **solo los módulos permitidos** para su rol. Si no ve una opción, su rol no tiene permiso para ella.

---

## 3. Navegación general

- **Menú lateral (izquierda)**: agrupa los módulos por categoría (Principal, Administración, Catálogos, Producción, Inventario, Ventas, Finanzas).
- **Botón ☰**: en pantallas pequeñas (móvil/tablet) abre y cierra el menú.
- **Cabecera**: muestra su nombre y rol; el botón **Cerrar sesión** lo saca del sistema.
- Los mensajes de confirmación (verde), advertencia (amarillo) y error (rojo) aparecen al inicio del contenido y se cierran solos o con la ✕.

---

## 4. Dashboard (pantalla de inicio)

Al ingresar verá los indicadores principales:

- **Ventas del día** y **ventas del mes** (total en pesos).
- **Órdenes de producción activas** (OT en curso).
- **Productos con stock bajo** (cantidad).
- **Utilidad del mes** (ingresos − egresos).

Los valores se actualizan automáticamente con la información registrada en los módulos.

---

## 5. Catálogos

### 5.1 Vendedores
- **Listar**: menú **Catálogos → Vendedores**.
- **Crear**: botón **Nuevo vendedor**; diligencie nombre, tipo y número de documento (único), teléfono, correo, dirección, ciudad y % de comisión.
- **Editar / Eliminar**: botones en la columna *Acciones* de cada fila.
- Use el cuadro de **buscar** para filtrar por nombre o documento.

### 5.2 Clientes
Igual que vendedores (menú **Catálogos → Clientes**). El número de documento es único. Los clientes se usan al registrar ventas.

### 5.3 Proveedores
Menú **Catálogos → Proveedores**. Incluye un campo de **contacto** (persona encargada). Los proveedores se usan como referencia para la compra de materias primas.

### 5.4 Materias primas
Menú **Catálogos → Materias primas**. Registre código (único), nombre, categoría (Telas, Insumos, Químicos…), unidad (metro, kg, rollo, unidad), **stock actual**, **stock mínimo** (para alertas) y costo.

### 5.5 Productos y lista de materiales (BOM)
- Menú **Catálogos → Productos**: registre código (único), nombre, talla, color, modelo, categoría, costo, precio y stock.
- En la vista **Ver** de un producto (botón *Ver* de cada fila) se administra la **lista de materiales (BOM)**: agregue las materias primas necesarias con su cantidad y unidad. Ejemplo: 1.5 metros de tela denim + 5 botones + 1 cierre por cada jean.
- La BOM le sirve de referencia para saber qué insumos requiere cada producto al producir.

---

## 6. Producción — Órdenes de trabajo (OT)

### 6.1 Crear una orden de trabajo
1. Menú **Producción → Órdenes de producción**.
2. Botón **Nueva orden de trabajo**.
3. Seleccione el **producto**, la **cantidad** a producir y agregue observaciones si lo desea.
4. Guarde. El sistema genera el código automáticamente (`OT-0001`, `OT-0002`, …) y crea las **8 etapas** en orden.

### 6.2 Las 8 etapas del proceso
1. Compra de tela
2. Corte
3. Confección
4. Pulido
5. Lavandería
6. Empaque
7. **Bodega** — al completar esta etapa, el producto entra al **inventario** (stock + cantidad de la OT)
8. **Distribución** — al completarla, la OT se cierra

### 6.3 Avanzar una etapa
1. Abra la OT (botón **Ver**).
2. Revise la línea de tiempo con las 8 etapas (pendiente / en curso / completada).
3. Presione **Avanzar etapa** para completar la etapa actual y pasar a la siguiente.

> ⚠️ **No se puede saltar etapas**: el sistema solo permite avanzar la etapa actual en orden. Si necesita corregir, cancele la OT y cree una nueva (o edite la OT si tiene permiso).

### 6.4 Estados de una OT
- **En curso**: está en alguna de las etapas 1–7.
- **Completada**: terminó la etapa 8 (distribución). El stock ya fue registrado.
- **Cancelada**: se canceló antes de terminar.

---

## 7. Inventario

### 7.1 Consultar stock
Menú **Inventario → Inventario**: tabla con producto, ubicación, stock disponible, stock mínimo y estado.

### 7.2 Movimientos
Menú **Inventario → Movimientos**: historial completo de entradas (`in`), salidas (`out`) y ajustes (`adjustment`), con el motivo y el usuario que los realizó.
- Para registrar un **ajuste manual** (ej. conteo físico, merma, devolución): botón **Registrar movimiento**, seleccione tipo, producto, cantidad y motivo. El stock se actualiza al instante.
- Los movimientos por producción (etapa 7) y por ventas se generan automáticamente: no los registre a mano.

### 7.3 Alertas de stock
Menú **Inventario → Alertas de stock**: muestra los productos cuyo stock está **por debajo del mínimo** configurado. Revise esta pantalla periódicamente para planear producción o compras.

---

## 8. Ventas y facturación

### 8.1 Registrar una venta
1. Menú **Ventas → Ventas**, botón **Nueva venta**.
2. Seleccione **cliente** y (opcional) **vendedor**, y la **fecha**.
3. En el detalle, agregue **productos**: cantidad y precio de venta (el sistema calcula el subtotal).
4. Aplique **descuento** si aplica (no puede superar el subtotal).
5. El sistema calcula: subtotal − descuento + **IVA 19%** = **total**.
6. Indique el **pago inicial**:
   - Si paga el total → la venta queda **Pagada**.
   - Si paga una parte → queda **Parcial** (saldo pendiente).
   - Si no paga → queda **Pendiente** (venta a crédito).
7. Guarde. La venta queda en estado **borrador**.

### 8.2 Confirmar la venta (importante)
La venta guardada debe **confirmarse** para que:
- Se **descuente el stock** de los productos vendidos, y
- Se registre el **ingreso** en finanzas.

> Si el stock no alcanza, el sistema muestra el error y la venta NO se confirma.

### 8.3 Factura imprimible
En la vista de la venta verá la **factura** (`FAC-0001`, `FAC-0002`, …) con datos del cliente, vendedor, productos, totales y pagos. Use **Imprimir** de su navegador (Ctrl+P) para guardarla en PDF o papel.

### 8.4 Pagos parciales (ventas a crédito)
En la vista de la venta, use **Registrar pago** para abonar al saldo:
- El **saldo pendiente** se calcula como total − pagos.
- Al cubrir el total, la venta pasa a **Pagada**.
- No se puede pagar más del saldo pendiente.

### 8.5 Cancelar o eliminar una venta
- **Cancelar** (solo ventas confirmadas): revierte el stock y anula el ingreso registrado.
- **Eliminar** (solo ventas en borrador): borra la venta sin efectos en stock ni finanzas.

---

## 9. Finanzas

Menú **Finanzas → Finanzas**: resumen con filtro por **fechas**.

- **Ingresos**: se registran automáticamente al confirmar ventas (tipo *venta*). También puede registrar ingresos manuales (tipo *otro*, ej. inyección de capital, venta de chatarra).
- **Egresos**: registre gastos por categoría: **materia prima**, **mano de obra**, **servicios** u **otro**.
- **Utilidad** = total ingresos − total egresos en el rango de fechas seleccionado.

Los ingresos por venta cancelada se **anulan automáticamente**.

---

## 10. Reportes

Menú **Finanzas → Reportes**:

| Reporte | Contenido |
|---|---|
| **Ventas** | Facturas confirmadas por rango de fechas: número, fecha, cliente, vendedor, subtotal, descuento, IVA y total. |
| **Inventario** | Stock por producto: código, nombre, ubicación, stock, mínimo, costo unitario y valor total. |
| **Financiero** | Ingresos, egresos y utilidad por rango de fechas. |

Para descargar:
1. Seleccione las **fechas** (opcional; por defecto toma el mes actual, excepto inventario que siempre es global).
2. Elija formato **PDF** (imprimible) o **CSV** (Excel).
3. Presione el botón de descarga.

> Los CSV abren directamente en Excel (separador `;`, codificación UTF-8). En Excel: si los números no se interpretan bien, use *Datos → Desde texto/CSV* y seleccione separador `;`.

---

## 11. Administración (solo rol administrador)

### 11.1 Usuarios
Menú **Administración → Usuarios**:
- **Crear**: nombre, correo (único), contraseña y uno o más **roles**.
- **Editar**: cambiar datos, contraseña, activar/desactivar (`is_active`) o cambiar roles.
- **Eliminar**: no puede eliminarse a sí mismo.

### 11.2 Roles y permisos
Menú **Administración → Roles**:
- Cree roles con un **display name** descriptivo y marque los **permisos** que tendrá.
- El rol **admin** no se puede eliminar.

---

## 12. Solución de problemas comunes

| Problema | Solución |
|---|---|
| **No recuerda la contraseña** | Pídale al administrador que cree un usuario nuevo o que edite el suyo. |
| **"No autorizado (403)"** | Su rol no tiene permiso para ese módulo. Contacte al administrador para ampliar permisos. |
| **No puede avanzar una etapa de producción** | Debe completar la etapa actual primero; el avance es estrictamente secuencial. |
| **La venta no se confirma: stock insuficiente** | Produzca más unidades (OT) o registre un ajuste de inventario. |
| **El menú no muestra un módulo** | Ese módulo está fuera de los permisos de su rol. |
| **Los reportes CSV abren raros en Excel** | Abra con *Datos → Desde texto/CSV* y use separador `;`. |
| **El sistema va lento** | Cierre sesión si termina; contacte al administrador si persiste. |
| **Pantalla de "mantenimiento"** | El administrador activó `php artisan down`; espere o avise al administrador. |

---

## 13. Preguntas frecuentes

**¿El stock se descuenta al guardar la venta?**
No. El stock se descuenta al **confirmar** la venta (paso 8.2). Hasta entonces la venta es un borrador sin efectos.

**¿Puedo vender un producto sin stock?**
Puede crear la venta, pero **no podrá confirmarla** si el stock no alcanza.

**¿Cómo entran los productos al inventario?**
Al completar la **etapa 7 (bodega)** de una OT, la cantidad producida entra al inventario automáticamente. También con ajustes manuales en **Inventario → Movimientos**.

**¿Puedo editar una venta ya confirmada?**
No. Para corregirla, cancele la venta (revierte stock e ingreso) y registre una nueva.

**¿El IVA está incluido?**
El sistema calcula IVA del **19%** sobre (subtotal − descuento) y lo muestra por separado en la factura.

---

*Documento de apoyo de la aplicación Etnicos-365. Para dudas técnicas consulte `README.md`; para el plan del proyecto, `PLAN.md`.*