# PedidoClientes

Sistema web para registrar pedidos/solicitudes de clientes por **Grupo + Color + Talle**, identificando automáticamente **qué usuario** y **qué sucursal** originaron cada registro.

## Flujo

1. El administrador crea las sucursales.
2. El administrador crea usuarios y asigna cada uno a una sucursal.
3. Un ADMIN o SUPERVISOR importa el catálogo CSV con columnas `grupo;color;talle`.
4. El operador LOCAL inicia sesión.
5. En **Nuevo pedido** elige Grupo → Color → Talle y puede escribir una observación.
6. Al guardar, el servidor toma el `user_id` y `sucursal_id` desde la sesión autenticada. El navegador **no envía ni elige la sucursal**.
7. ADMIN/SUPERVISOR pueden ver el origen de todos los pedidos, cambiar estados y generar reportes. LOCAL ve solamente su sucursal.

## Roles

- **ADMIN**: usuarios, sucursales, catálogo, pedidos y reportes globales.
- **SUPERVISOR**: catálogo, pedidos, estados y reportes globales.
- **LOCAL**: carga pedidos y consulta únicamente los registros de su sucursal.

## Requisitos

- PHP 8.1+
- Composer
- PostgreSQL
- Extensiones PHP habituales de Laravel: pdo_pgsql, mbstring, openssl, tokenizer, xml, ctype, json.

## Instalación

```bash
git clone https://github.com/EliasBenitez04/PedidoClientes.git
cd PedidoClientes
composer install
copy .env.example .env
php artisan key:generate
```

Crear la base PostgreSQL:

```sql
CREATE DATABASE pedido_clientes;
```

Configurar en `.env`:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=pedido_clientes
DB_USERNAME=postgres
DB_PASSWORD=TU_CLAVE
```

Luego:

```bash
php artisan migrate --seed
php artisan serve
```

Abrir `http://127.0.0.1:8000`.

## Usuario inicial

El seeder toma estas variables del `.env`:

```env
ADMIN_NAME=Administrador
ADMIN_EMAIL=admin@pedidos.local
ADMIN_PASSWORD=Cambiar123!
```

**Cambiar la contraseña antes de usar en producción.**

## Importación de catálogo

Archivo CSV/TXT, máximo 10 MB. Se detecta automáticamente separador coma, punto y coma o tabulación.

Cabecera obligatoria:

```csv
grupo;color;talle
REMERA ECO;NEGRO;M
REMERA ECO;NEGRO;L
REMERA ECO;BLANCO;M
```

También se aceptan alias comunes de cabecera como `grupo_plan`, `talla` o `size`. El ejemplo está en `docs/catalogo_ejemplo.csv`.

## Datos guardados por pedido

- usuario que cargó
- sucursal asociada al usuario
- grupo
- color
- talle
- observación
- estado
- fecha y hora de creación

La sucursal se resuelve en backend desde el usuario autenticado; no hay un campo editable de local en la pantalla de carga.

## Reportes

Filtros por fecha, sucursal, usuario, grupo, color, talle y estado. Exportación en CSV UTF-8 compatible con Excel.

## Estados

- PENDIENTE
- PROCESADO
- CANCELADO
