# AGENTS.md

## Proyecto

Repositorio de planeación/documentos del **Sistema de Gestión de Producción de Jean**: web app para una fábrica de jeans (producción, inventario, ventas, finanzas). Contiene solo planificación, no código de aplicación aún.

- Stack de destino: **Laravel 13 + MySQL 8 + Blade + Tailwind CSS** (PHP 8.3+), responsive.
- Entorno dev local: **Laragon 6.0** en Windows. PHP activo: **8.3.33 NTS** (`C:\laragon\bin\php\php-8.3.33-nts-Win32-vs16-x64`); también existe 8.1.10 TS y 8.2.30 NTS. Composer 2.10.2. (No hay Python ni Node; Pandoc/soffice NO están instalados).
- Apache usa **FastCGI** (`php-cgi.exe`) vía `C:\laragon\etc\apache2\fcgid.conf`; la versión de PHP activa se define en `C:\laragon\usr\laragon.ini` (`[php] Version=...`). PHP está en el PATH de usuario, no el de máquina.
- **Servidor por IP**: Apache escucha en `0.0.0.0:80/443` (todas las interfaces). Al activar Laragon, cualquier proyecto de `C:\laragon\www` es accesible desde la red vía `http://<IP-máquina>/<proyecto>/` (IPs típicas: Ethernet `192.168.1.8`, Wi-Fi `192.168.1.7`). El `C:\laragon\www\index.php` lista todos los proyectos. El firewall de Windows ya permite Apache (reglas `Apache HTTP Server` en perfil **Public**); crear reglas nuevas requiere elevación de admin.
- Aún NO es un repo git; no hay comandos lint/test/build definidos.

## Alcance del sistema (contrato con el cliente)

- Usuarios con roles **admin / usuario** (Laravel auth).
- CRUD de **vendedores, clientes, proveedores, productos y materias primas**.
- Producción: órdenes de trabajo con **8 etapas en orden fijo**: compra de tela → corte → confección → pulido → lavandería → empaque → bodega → distribución.
- Inventario de producto terminado, movimientos y alertas de stock mínimo.
- Ventas y facturación; finanzas (ingresos, egresos, utilidad); dashboard con reportes (PDF/Excel).
- Hosting previsto: Hostinger Plan Unlimited; certificado SSL Let's Encrypt; backups diarios.

## Documentos y convenciones

- Toda la documentación es en **español**, con portada + tabla de contenidos + numeración de secciones (structure `x.y`).
- Los `.docx` actuales (`Documento_de_despliegue_*.docx`, `Propuesta_Comercial_*.docx`) son las entregas principales; el diagrama de despliegue vive en `recursos-diagrama/Figura1_diagrama_despliegue.png`.
- Deben rellenar campos con marcadores tipo `Complete aquí ...` y actualizar `Fecha`/`Versión`.
- `plan-app-gesti-n-producci-n-jean.json` es log de una sesión OpenCode (`fabrica-jeans`); contiene el plan y decisiones (stack, roles, nivel de detalle) como referencia histórica.

## Generar docx

Para crear/editar documentos Word no uses Pandoc ni Python (no disponibles). Usa **PHP de Laragon** (zlib/COM disponibles) con librerías que no requieran Composer, o pide instalar tooling. El proyecto original se generó vía sesión OpenCode en `C:\laragon\www\fabrica-jeans` (directorio vacío de referencia).