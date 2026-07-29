# Portfolio Nicolás — API

Backend del [portfolio de Nicolás González](https://github.com/nicolasgonzalez98/Portfolio-NicolasGonzalez).
Expone los proyectos del portfolio vía API REST y provee un panel de administración
para gestionarlos (hoy están hardcodeados en el front).

## Stack

- **Laravel 11** (PHP 8.2+)
- **MySQL** en producción (Hostinger) · **SQLite** en desarrollo local
- **Laravel Sanctum** — autenticación por token para el panel admin
- **Cloudflare R2** (S3-compatible, vía Flysystem) — almacenamiento de imágenes
- Arquitectura desacoplada: front estático (Next.js) + esta API + MySQL + R2

## Requisitos

- PHP 8.2+ con las extensiones: `openssl`, `mbstring`, `curl`, `fileinfo`,
  `pdo_mysql`/`pdo_sqlite`, `gd`, `intl`, `zip`
- Composer 2.x

## Puesta en marcha (local)

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve            # http://localhost:8000
```

En dev usa **SQLite** por defecto (`database/database.sqlite`), sin configuración extra.

## Endpoints

| Método | Ruta | Descripción |
|--------|------|-------------|
| `GET`  | `/api/projects` | Lista de proyectos publicados |
| `GET`  | `/api/projects/{slug}` | Detalle de un proyecto |
| `GET`  | `/up` | Health check |

*(El CRUD de administración protegido con Sanctum se agrega en la Fase 3.)*

## Roadmap

- **Fase 0** ✅ Scaffold (Laravel + Sanctum + R2)
- **Fase 1** Migraciones + modelos + seeder (migra los proyectos actuales) + API pública
- **Fase 2** Conectar el front Next a la API
- **Fase 3** Auth + CRUD admin + subida/borrado de imágenes en R2
- **Fase 4** Deploy a Hostinger
