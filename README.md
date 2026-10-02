# Demanda Clientes

Sistema para registrar **demanda no cubierta** en los locales.

No representa pedidos de clientes ni reservas. Cada registro significa:

> Un cliente buscó una combinación de grupo, color y talle, pero el local no la tenía disponible.

## Objetivo

Convertir consultas perdidas por falta de stock o surtido en información comercial:

- qué grupos están siendo solicitados;
- qué colores faltan;
- qué talles tienen demanda;
- qué combinaciones se repiten;
- qué sucursales registran más oportunidades no cubiertas;
- cuándo y quién realizó cada registro.

## Flujo del local

1. El vendedor inicia sesión con su usuario de tienda, por ejemplo `TIENDA01`.
2. Cuando un cliente quiere comprar algo que no está disponible, abre **Registrar demanda**.
3. Selecciona Grupo, Color y Talle.
4. Puede agregar una observación opcional.
5. Guarda el registro.
6. La sucursal, el usuario, la fecha y la hora se registran automáticamente.

Si varios clientes preguntan por lo mismo, debe registrarse una vez por cada cliente. Esa repetición es justamente la señal de demanda que queremos medir.

## Estados

- **REGISTRADO**: consulta cargada por el local.
- **REVISADO**: administración ya analizó el registro.
- **DESCARTADO**: registro inválido o que no debe considerarse en el análisis.

## Reportes

Los reportes permiten filtrar por:

- fecha;
- sucursal;
- usuario;
- grupo;
- color;
- talle;
- estado.

También se puede exportar el resultado a CSV.

## Dashboard

El dashboard muestra:

- consultas registradas hoy;
- consultas del mes;
- cantidad de grupos con demanda;
- registros revisados;
- combinaciones Grupo + Color + Talle más solicitadas;
- sucursales con mayor demanda no cubierta;
- últimos registros.

## Catálogos

Los importadores de Grupo, Color y Talle se mantienen independientes.

## Actualización

Después de hacer pull:

```bash
git pull origin master
composer install
php artisan migrate
php artisan optimize:clear
php artisan serve
```

La migración conserva los registros anteriores y cambia la terminología de estados automáticamente.
