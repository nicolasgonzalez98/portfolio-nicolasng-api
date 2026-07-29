# Portfolio Nicolás — API (contexto para agentes Claude)

> Backend en **Laravel 11** del portfolio de Nicolás. Repo separado del front
> (`portfolio-nicolasng`), igual que el split front/back de Nico Camisetas.
> Idioma de trabajo: **español (Argentina), informal ("vos")**. Metodología: ver
> `../CLAUDE.md` (Tech Lead, incremental, parar cada 1-3 componentes).

## Objetivo
Hoy los proyectos del portfolio están **hardcodeados en JSX** en el front. Este
back los mueve a una **base de datos** + expone una **API pública** para el front
y un **panel admin** (CRUD) para gestionarlos sin tocar código.

## Stack
- Laravel 11 (PHP 8.2+), Eloquent.
- **DB: SQLite en dev / MySQL en prod (Hostinger).** Esquema portable (columnas
  JSON + tipos estándar → andan igual en ambos). Mismo patrón que Nico Camisetas.
- **Sanctum** (token) para el admin. El `User` ya tiene `HasApiTokens`.
- **Cloudflare R2** para imágenes: disco `r2` en `config/filesystems.php` (driver
  `s3`, `region=auto`, `throw=true`). La subida la **intermedia el backend** (las
  credenciales viven solo en el server) y el borrado es **en cascada** (al borrar
  proyecto o imagen se elimina el objeto en R2). Vars: `R2_*` en `.env`.

## Roadmap (fases) — intercalar back y front
- **Fase 0** ✅ Scaffold: Laravel + Sanctum (`install:api`) + Flysystem S3 + disco R2 + `.env`.
- **Fase 1** Migraciones + modelos (`Project`, `ProjectLink`, `ProjectImage`) +
  **seeder** (migra los proyectos actuales del front) + endpoints públicos
  (`GET /api/projects`, `GET /api/projects/{slug}`).
- **Fase 2** Conectar el front Next a la API (reemplazar los proyectos hardcodeados por fetch).
- **Fase 3** Auth (login Sanctum) + CRUD admin + subida/borrado de imágenes en R2.
- **Fase 4** Deploy a Hostinger (API + MySQL) + CORS (`FRONTEND_URL`) + env.

## Modelo de dominio (a implementar en Fase 1, viene del diseño validado en Prisma)
- **Project**: `slug` (unique), `title`, `year` (nullable), `category`
  (enum: `DESTACADO` | `JS_REACT` | `PYTHON` | `TRABAJO_PROFESIONAL`), `summary`,
  `description` (text, **texto plano** — decisión del usuario, NO markdown),
  `stack` (JSON), `features` (JSON), `featured` (bool), `published` (bool), `order` (int).
- **ProjectLink**: `type` (`WEBSITE` | `REPO` | `DEMO`), `label` (nullable), `url`. onDelete cascade.
- **ProjectImage**: `url`, `key` (object key de R2, para borrarlo), `alt` (nullable), `order`. onDelete cascade.
- Ojo: **DecorGlass** es nombre propio real, NO traducir/renombrar.

## Quirks del entorno (Windows 10 + Laragon) — IMPORTANTE
- **PHP y Composer NO están en el PATH.** Usar rutas completas:
  - PHP: `/c/laragon/bin/php/php-8.3.30-Win32-vs16-x64/php.exe`
  - Composer: `/c/composer/composer.phar` → `"$PHP" "$COMPOSER" ...`
- **El PHP de Laragon no traía `php.ini`** (solo las plantillas) → se creó copiando
  `php.ini-development` y activando: openssl, mbstring, curl, fileinfo, pdo_mysql,
  pdo_sqlite, sqlite3, gd, intl, zip, sodium, exif, mysqli. Sin esto Composer falla
  por `openssl`.
- **`php artisan install:api` falla parcialmente**: intenta `composer require` invocando
  `php` desde PATH (no existe) → no instala Sanctum aunque diga "installed". Solución:
  instalar los paquetes a mano (`composer require laravel/sanctum league/flysystem-aws-s3-v3`)
  y publicar (`vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"`).
- **MySQL de Laragon (8.4.3) no conecta**: el server pide `auth_gssapi` como plugin por
  defecto (config rota) → ni el CLI ni PDO autentican. Por eso **dev = SQLite**. Arreglarlo
  requeriría levantar mysqld con `--skip-grant-tables` y cambiar el plugin del user root.
- Para archivos temporales usar el scratchpad de la sesión, no `/tmp`.
- `git commit -F <archivo>` (las comillas en `-m` rompen en esta PC).

## Deploy (Fase 4, pendiente)
Hostinger compartido corre PHP → Laravel va nativo. Falta confirmar si el plan es
**Premium o Business** (afecta detalles del deploy, no la viabilidad). DB: MySQL de
Hostinger (descomentar el bloque `mysql` en `.env`). Front ya hosteado y funcional en
Hostinger bajo `nicolasngonzalez.com`.
