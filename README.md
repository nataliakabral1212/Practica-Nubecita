# Practica Nube

Práctica de balanceo de carga con Laravel 12, PHP 8.3 y el [starter kit oficial Livewire](https://github.com/laravel/livewire-starter-kit/tree/be93d73522270d9a659dd72acb7fb102a8b83485). El dashboard y el indicador de servidor identifican el nodo que responde. El tema de concierto usa morado y fucsia si el último dígito del hostname es impar, y azul y cian en los demás casos.

## Despliegue

En cada nodo, usa PHP 8.3 con PDO MySQL y configura la raíz web en `public/`. Da al proceso PHP permiso de escritura en `storage/` y `bootstrap/cache/`.

```sh
composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction
cp -n .env.example .env
```

Verifica `DB_CONNECTION=mysql`, completa `DB_PASSWORD` y agrega `APP_KEY` a `.env`. Genera la clave una sola vez con `php artisan key:generate --show` y compártela entre los nodos. En un despliegue existente, conserva la clave para mantener acceso a los datos cifrados de 2FA. Configura un servicio de correo para enviar los enlaces de recuperación y verificación.

Con la conexión web de MariaDB limitada a `SELECT, INSERT, UPDATE, DELETE`, ejecuta la migración una sola vez por despliegue desde un entorno con `DB_MIGRATE_USERNAME` y `DB_MIGRATE_PASSWORD` disponibles para el comando.

```sh
php artisan migrate --database=migraciones --force
```

En cada nodo, con las credenciales de la aplicación:

```sh
php artisan optimize
```

Las migraciones incorporan tablas y columnas existentes. Para revertirlas, restaura una copia de seguridad de la base de datos.

Nginx en `10.124.0.3` debe enviar los encabezados `X-Forwarded-For`, `X-Forwarded-Proto`, `X-Forwarded-Host`, `X-Forwarded-Port` y `X-Forwarded-Prefix` con los valores de la solicitud pública cuando correspondan. Los nodos comparten sesiones y caché en MariaDB.

Los archivos de `public/build` se incluyen en Git. Si cambias el frontend, ejecuta `npm ci` y `npm run build` en tu equipo y sube los archivos generados.
