GOTITAS - DEMANDA NO CUBIERTA

Este sistema registra productos que los clientes buscan y que el local no tiene disponible.

REVISION PRACTICA
- REGISTRADO: cargado por el local.
- REVISADO: administración ya controló el registro.
- DESCARTADO: no debe considerarse.

ADMIN/SUPERVISOR pueden:
- marcar un registro como revisado con un solo botón;
- seleccionar varios registros de la página y marcarlos revisados juntos;
- ver quién revisó y cuándo;
- reabrir un registro revisado;
- descartar registros incorrectos.

LOGOS
Copiar dentro de public/images:
- logo-gotitas.png
- logo-gotitas-blanco.png
- favicon-gotitas.png

Ver public/images/README_LOGOS.txt para tamaños recomendados.

ACTUALIZAR
git pull origin master
composer install
php artisan migrate
php artisan optimize:clear
php artisan serve
