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
- **Cloudflare R2** para imágenes ✅ **LIVE en prod (2026-07-30)**: disco `r2` en
  `config/filesystems.php` (driver `s3`, `region=auto`, `throw=true`), seleccionado por
  `MEDIA_DISK` (dev=`public` local, prod=`r2`). Bucket `portfolio-nicolasng`, URL pública
  r2.dev, token scopeado al bucket (Object R&W). La subida la **intermedia el backend** (las
  credenciales viven solo en el `.env` del server, nunca versionadas — el repo es público) y
  el borrado es **en cascada** (al borrar proyecto o imagen se elimina el objeto en R2; ver
  `MediaStorage` + `ImageController`). Vars: `R2_*` en `.env`.

## Roadmap (fases) — intercalar back y front — ✅ COMPLETO (todo live)
- **Fase 0** ✅ Scaffold: Laravel + Sanctum (`install:api`) + Flysystem S3 + disco R2 + `.env`.
- **Fase 1** ✅ Migraciones + modelos (`Project`, `ProjectLink`, `ProjectImage`) +
  **seeder** (migra los proyectos actuales del front) + endpoints públicos
  (`GET /api/projects`, `GET /api/projects/{slug}`).
- **Fase 2** ✅ Conectar el front Next a la API (reemplazar los proyectos hardcodeados por fetch).
- **Fase 3** ✅ Auth (login Sanctum) + CRUD admin + subida/borrado de imágenes en R2.
- **Fase 4** ✅ Deploy a Hostinger (API + MySQL) + CORS (`FRONTEND_URL`) + env. **LIVE en `https://api.nicolasngonzalez.com`.**

## Modelo de dominio (✅ implementado)
- **Project**: `slug` (unique), `title`, `year` (nullable), `category`
  (enum: `DESTACADO` | `JS_REACT` | `PYTHON` | `TRABAJO_PROFESIONAL`), `status` (nullable),
  `summary`, `description` (text, **texto plano** — decisión del usuario, NO markdown),
  `cover_url` + `cover_key` (portada), `stack` (JSON), `features` (JSON),
  `tech_decisions` (JSON, nullable — lista de `{title, description}`),
  `featured` (bool), `published` (bool), `order` (int).
- **ProjectLink**: `type` (`WEBSITE` | `REPO` | `DEMO`), `label` (nullable), `url`. onDelete cascade.
- **ProjectImage**: `url`, `key` (object key de R2, para borrarlo), `alt` (nullable), `order`. onDelete cascade.
- Ojo: **DecorGlass** (`presupuestos_mauri`) es nombre propio real, NO traducir/renombrar (el
  contenido descriptivo sí está en español).

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

## Deploy ✅ HECHO — API live en `https://api.nicolasngonzalez.com`
Hostinger **Premium** (corre PHP **y** Node vía Passenger). Deploy **MANUAL por SSH**
(usuario `u689345803`, home `/home/u689345803`).

**Estructura** (patrón copiado de `apicamisetas.nicolasngonzalez.com`, que ya andaba):
- App Laravel en `~/domains/api.nicolasngonzalez.com/laravel/` (clon de este repo).
- `~/domains/api.nicolasngonzalez.com/public_html/` = el `public/` de Laravel: su `index.php`
  apunta a `../laravel/vendor` y `../laravel/bootstrap`, + symlink `public_html/storage → laravel/storage/app/public`.
- **DB MySQL**: `u689345803_portfolio` (user `u689345803_portfolio`, host `localhost`), migrada + seedeada.

**⚠️ QUIRK CLAVE — `proc_open` deshabilitado en el PHP CLI de Hostinger:** rompe el auto-deploy
Git de Hostinger y los *scripts* de Composer (`package:discover` "relies on proc_open"). PERO:
`composer install --no-dev` **igual baja el `vendor/`** (solo falla el script final, ignorable), y
los `php artisan` corridos **a mano por SSH andan perfecto**. → **No hay auto-deploy; se hace a mano.**

**Procedimiento de redeploy (cambios de código):**
```bash
cd ~/domains/api.nicolasngonzalez.com/laravel
git pull origin main
composer install --no-dev -o        # solo si cambiaron dependencias; ignorar el error del script final
php artisan migrate --force         # solo si hay migraciones nuevas
php artisan config:clear && php artisan config:cache   # SIEMPRE (ver gotcha)
```

**⚠️ Gotcha `config:cache`:** en prod la config está **cacheada** (`php artisan config:cache`,
perf). Por eso, tras **cualquier** `git pull` o cambio de `.env`, hay que `config:clear` +
`config:cache` de nuevo, o los cambios del `.env` **NO toman efecto**. `route:cache` NO se usa
(hay un closure `Route::get('/', fn)` en `routes/web.php` que lo rompería; win menor, deferido).

**⚠️ NO re-seedear prod NUNCA:** `ProjectSeeder` borra y recrea links+imágenes de cada proyecto
→ borraría las **capturas subidas a R2 por el panel** (y dejaría objetos huérfanos en el bucket).
El seeder ya cumplió su función (migración inicial); de acá en más **el contenido de prod se
gestiona SOLO por el panel admin**.

**CORS** (`config/cors.php`): orígenes = `FRONTEND_URL` (`https://nicolasngonzalez.com`) +
`https://www.nicolasngonzalez.com` + localhost:4000. Auth por token (header), `supports_credentials=false`.

**Front:** live en `https://nicolasngonzalez.com` como app **Next.js bajo Node/Passenger**
(`~/domains/nicolasngonzalez.com/nodejs/`), con **auto-deploy** al pushear a `main` del repo
`Portfolio-NicolasGonzalez`. La URL de la API se hornea vía `.env.production`. (Vercel se
**desconectó** el 2026-07-30 para tener un solo sitio canónico.)
