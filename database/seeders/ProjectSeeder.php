<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

/**
 * Migra los proyectos que hoy están hardcodeados en el front (portfolio-nicolasng).
 * Idempotente: se puede correr las veces que haga falta (updateOrCreate por slug).
 *
 * Notas:
 *  - Las imágenes/portadas apuntan por ahora a assets locales del front
 *    (key = null). En la Fase 3 se migran a Cloudflare R2 vía el panel admin.
 *  - El texto de DecorGlass queda tal cual está hoy en el front (nombre real, no se toca).
 */
class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->projects() as $data) {
            $links = $data['links'] ?? [];
            $images = $data['images'] ?? [];
            unset($data['links'], $data['images']);

            $project = Project::updateOrCreate(['slug' => $data['slug']], $data);

            // Reescribe links e imágenes desde cero para mantener el seeder idempotente.
            $project->links()->delete();
            foreach ($links as $i => $link) {
                $project->links()->create(array_merge($link, ['order' => $i + 1]));
            }

            $project->images()->delete();
            foreach ($images as $i => $image) {
                $project->images()->create(array_merge($image, ['order' => $i + 1]));
            }
        }
    }

    private function projects(): array
    {
        return [
            // ---------------------------------------------------------------
            // DESTACADOS
            // ---------------------------------------------------------------
            [
                'slug' => 'turnoya',
                'title' => 'TurnoYa',
                'year' => 2026,
                'category' => 'DESTACADO',
                'status' => 'En desarrollo',
                'summary' => 'SaaS de reservas de turnos multi-rubro: marketplace público + panel de administración.',
                'description' => 'TurnoYa es un SaaS de reservas de turnos para profesionales y comercios (barberías, peluquerías, estética, salud), genérico para cualquier rubro basado en turnos. Tiene dos lados: un marketplace público donde los clientes buscan un negocio y reservan sin necesidad de registrarse, y un panel privado donde el dueño administra su negocio, servicios, horarios y turnos.',
                'cover_url' => '/images/TurnoYa/cover.png',
                'cover_key' => null,
                'stack' => ['NestJS', 'React 19', 'TypeScript', 'Prisma', 'PostgreSQL', 'JWT', 'PrimeReact', 'Vite', 'Jest'],
                'features' => [
                    'Marketplace público: los clientes buscan negocios por nombre o dirección y reservan sin login.',
                    'Reserva pública multi-paso (negocio → servicio → día → horario disponible).',
                    'Panel del dueño con login: gestión de negocio, servicios, editor de horarios semanales (multi-rango) y turnos.',
                    'Turno walk-in: el dueño agenda un cliente presencial, con contacto opcional.',
                    'Modo claro/oscuro persistente que respeta la preferencia del sistema.',
                ],
                'tech_decisions' => [
                    ['title' => 'Anti-doble-reserva en 2 capas', 'description' => 'chequeo de disponibilidad a nivel app + índice único parcial en PostgreSQL. Si dos personas reservan el mismo turno a la vez, la base rechaza el segundo INSERT y la API responde 409.'],
                    ['title' => 'Multi-tenant por propiedad', 'description' => 'cada dueño opera únicamente sobre su negocio; la autorización se resuelve desde el token JWT, nunca desde el body.'],
                    ['title' => 'Motor de disponibilidad como función pura', 'description' => 'testeada con Jest: genera los slots del día excluyendo los turnos ocupados.'],
                    ['title' => 'Modelado de dominio cuidado', 'description' => 'plata en centavos enteros, horarios en minutos desde medianoche y timezone del negocio (Argentina).'],
                ],
                'featured' => true,
                'published' => true,
                'order' => 10,
                'links' => [
                    ['type' => 'REPO', 'label' => 'GitHub — monorepo API + Web', 'url' => 'https://github.com/nicolasgonzalez98/turnoya'],
                ],
                'images' => [],
            ],
            [
                'slug' => 'nico_camisetas',
                'title' => 'Nico Camisetas',
                'year' => 2026,
                'category' => 'DESTACADO',
                'status' => 'En producción',
                'summary' => 'E-commerce de camisetas de fútbol + panel de gestión a medida (Vue 3 + Laravel 11).',
                'description' => 'Nico Camisetas es una tienda online de camisetas de fútbol (re-ediciones y originales) pensada para el mercado argentino. La parte pública es un catálogo curado donde el comprador arma un pedido y lo comparte por Instagram o WhatsApp (sin pasarela de pago online, por decisión de negocio). La verdadera diferencia está en el back-office a medida: un panel que digitaliza los flujos que el dueño llevaba en Excel — control de stock con sugerencia de reposición por urgencia, un registro interno de ventas (Remitos) y el seguimiento de cobros de Mercado Libre pendientes de acreditación.',
                'cover_url' => '/images/NicoCamisetas/cover.png',
                'cover_key' => null,
                'stack' => ['Vue 3', 'Laravel 11', 'PrimeVue', 'Tailwind CSS', 'Pinia', 'Sanctum', 'Cloudflare R2', 'SQLite', 'Docker', 'Fly.io', 'Netlify'],
                'features' => [
                    'Catálogo público con landing curada, vidrieras (Destacados, Novedades, Retro, Más vendidos), categorías en árbol con megamenú, buscador y filtros.',
                    'Productos con talles/variantes: re-ediciones multi-talle y originales como pieza única, con stock por talle.',
                    'Carrito compartible por Instagram/WhatsApp, con tope de cantidad según stock.',
                    'Panel de administración completo con Design System propio: ABM de productos, categorías, banners y testimonios.',
                    'Tablero de Stock/Reposición que reemplaza un Excel: vista "Qué comprar" ordenada por urgencia + acción "Recibí".',
                    'Remitos + seguimiento de cobros de Mercado Libre pendientes de acreditación, con recordatorios de "separar la plata".',
                ],
                'tech_decisions' => [
                    ['title' => 'Design System a medida sobre PrimeVue', 'description' => 'primitivas reutilizables (AdminPage, StatTile, EmptyState, UploadDropzone) + composables, con dos shells (público vs. admin) seleccionados por ruta y tokens de color unificados.'],
                    ['title' => 'Seguridad por capas', 'description' => 'middleware de rol admin sobre todas las mutaciones + guards de router; la subida de imágenes la intermedia el backend hacia R2 (las credenciales viven solo en el server); el total de la orden se calcula en el backend, nunca se confía en el cliente.'],
                    ['title' => 'Deploy end-to-end', 'description' => 'SPA en Netlify + API Laravel dockerizada en Fly.io con SQLite en volumen persistente, incluyendo una migración de datos MySQL→SQLite y CORS por patrón de origen.'],
                    ['title' => 'Ingesta de datos legacy desde Excel', 'description' => 'seeders idempotentes con openpyxl (parseo de booleanos y formato numérico argentino) para importar 262 remitos históricos sin duplicar.'],
                ],
                'featured' => true,
                'published' => true,
                'order' => 20,
                'links' => [
                    ['type' => 'REPO', 'label' => 'GitHub — Frontend', 'url' => 'https://github.com/nicolasgonzalez98/nico-camisetas-frontt'],
                    ['type' => 'REPO', 'label' => 'GitHub — Backend', 'url' => 'https://github.com/nicolasgonzalez98/nico-camisetas-back'],
                ],
                'images' => [],
            ],

            // ---------------------------------------------------------------
            // JAVASCRIPT Y REACT
            // ---------------------------------------------------------------
            [
                'slug' => 'presupuestos_mauri',
                'title' => 'Presupuestos DecorGlass',
                'year' => 2023,
                'category' => 'JS_REACT',
                'status' => null,
                'summary' => 'Este es mi primer trabajo profesional.',
                'description' => 'En esta aplicación vas a poder crear tus propios presupuestos y organizar tus clientes y tu inventario. Todo con la ventaja de poder usarla desde donde estés.',
                'cover_url' => '/images/presupuestosMauri/portada.jpg',
                'cover_key' => null,
                'stack' => ['React.js', 'JavaScript', 'Redux', 'CSS', 'HTML', 'Passport.js', 'React Bootstrap'],
                'features' => [
                    'Creá tu usuario, guardá tus propios presupuestos y clientes, y gestioná tu inventario.',
                ],
                'tech_decisions' => null,
                'featured' => false,
                'published' => true,
                'order' => 30,
                'links' => [
                    ['type' => 'WEBSITE', 'label' => null, 'url' => 'https://front-presupuestos.vercel.app/'],
                    ['type' => 'REPO', 'label' => 'GitHub (BackEnd)', 'url' => 'https://github.com/nicolasgonzalez98/back-presupuestos-MPGlass'],
                    ['type' => 'REPO', 'label' => 'GitHub (FrontEnd)', 'url' => 'https://github.com/nicolasgonzalez98/Front-Presupuestos-Glass'],
                ],
                'images' => [
                    ['url' => '/images/presupuestosMauri/create_budget.jpg', 'alt' => null],
                    ['url' => '/images/presupuestosMauri/login.jpg', 'alt' => null],
                    ['url' => '/images/presupuestosMauri/clients.jpg', 'alt' => null],
                    ['url' => '/images/presupuestosMauri/articles.jpg', 'alt' => null],
                    ['url' => '/images/presupuestosMauri/add_client.jpg', 'alt' => null],
                    ['url' => '/images/presupuestosMauri/create_article.jpg', 'alt' => null],
                ],
            ],
            [
                'slug' => 'rock_paper_scissors',
                'title' => 'Piedra, Papel o Tijera',
                'year' => 2022,
                'category' => 'JS_REACT',
                'status' => null,
                'summary' => 'Mi propia versión del clásico, con animaciones y estilos complejos.',
                'description' => 'En esta aplicación podés jugar a Piedra, Papel o Tijera contra la máquina. Si ganás sumás un punto; si perdés, restás uno. ¡A ver si le ganás!',
                'cover_url' => '/images/RockPaperScissors/portada.jpg',
                'cover_key' => null,
                'stack' => ['React.js', 'JavaScript', 'CSS', 'HTML', 'styled-components', 'Context API'],
                'features' => [
                    'Jugá Piedra, Papel o Tijera contra la computadora.',
                    'El puntaje se mantiene aunque refresques el navegador (opción futura).',
                    'Diseño adaptado al tamaño de pantalla de cada dispositivo.',
                ],
                'tech_decisions' => null,
                'featured' => false,
                'published' => true,
                'order' => 40,
                'links' => [
                    ['type' => 'WEBSITE', 'label' => null, 'url' => 'https://rock-scissors-papers-react.vercel.app/'],
                    ['type' => 'REPO', 'label' => null, 'url' => 'https://github.com/nicolasgonzalez98/rockScissorsPapers-react-'],
                ],
                'images' => [
                    ['url' => '/images/RockPaperScissors/home.jpg', 'alt' => 'home'],
                    ['url' => '/images/RockPaperScissors/in-game.jpg', 'alt' => 'en juego'],
                    ['url' => '/images/RockPaperScissors/result.jpg', 'alt' => 'resultado'],
                    ['url' => '/images/RockPaperScissors/rules.jpg', 'alt' => 'reglas'],
                    ['url' => '/images/RockPaperScissors/home-responsive.jpg', 'alt' => 'home responsive'],
                    ['url' => '/images/RockPaperScissors/in-game-responsive.jpg', 'alt' => 'en juego responsive'],
                    ['url' => '/images/RockPaperScissors/result-responsive.jpg', 'alt' => 'resultado responsive'],
                ],
            ],

            // ---------------------------------------------------------------
            // PYTHON
            // ---------------------------------------------------------------
            [
                'slug' => 'forumNicolas',
                'title' => 'Foro de Nicolás',
                'year' => 2023,
                'category' => 'PYTHON',
                'status' => null,
                'summary' => 'Foro desarrollado con Django. Mi primer proyecto con este framework.',
                'description' => 'En esta aplicación podés crear foros y comentar en ellos. Es una app simple hecha con Django, y fue mi primer proyecto con este framework.',
                'cover_url' => '/images/forumNicolas/portada.png',
                'cover_key' => null,
                'stack' => ['Python', 'Django'],
                'features' => [
                    'Podés crear y guardar foros.',
                    'Podés crear discusiones dentro de cada foro.',
                ],
                'tech_decisions' => null,
                'featured' => false,
                'published' => true,
                'order' => 50,
                'links' => [
                    ['type' => 'WEBSITE', 'label' => null, 'url' => 'http://forumnicolas.pythonanywhere.com'],
                    ['type' => 'REPO', 'label' => null, 'url' => 'https://github.com/nicolasgonzalez98/forum-django'],
                ],
                'images' => [
                    ['url' => '/images/forumNicolas/home.jpg', 'alt' => 'home'],
                    ['url' => '/images/forumNicolas/agregarForo.jpg', 'alt' => 'agregar foro'],
                    ['url' => '/images/forumNicolas/addDiscuss.jpg', 'alt' => 'agregar discusión'],
                ],
            ],
            [
                'slug' => 'calorie_calc',
                'title' => 'Calculadora de Calorías',
                'year' => 2023,
                'category' => 'PYTHON',
                'status' => null,
                'summary' => 'Registro y estimación de calorías diarias, hecha con Django.',
                'description' => "“La salud es lo primero.” Lo escuchaste mil veces, y con los años te das cuenta de que es verdad. Esta app es una forma simple de arrancar con algo tan tedioso como cuidar la alimentación, partiendo de la base de que todo empieza por lo que comemos.\n\nCalorie Calc permite registrar y estimar las calorías que necesitás consumir por día, y sirve de guía para subir o bajar de peso.",
                'cover_url' => '/images/CalorieCalc/portada.jpg',
                'cover_key' => null,
                'stack' => ['Python', 'Django', 'django-filter'],
                'features' => [
                    'Creá tus propios alimentos.',
                    'Asigná alimentos a cada usuario.',
                    'Registrá y estimá las calorías que necesitás por día.',
                ],
                'tech_decisions' => null,
                'featured' => false,
                'published' => true,
                'order' => 60,
                'links' => [
                    ['type' => 'REPO', 'label' => null, 'url' => 'https://github.com/nicolasgonzalez98/CalorieCalc-dj-py-'],
                ],
                'images' => [
                    ['url' => '/images/CalorieCalc/home.jpg', 'alt' => 'home'],
                    ['url' => '/images/CalorieCalc/signIn.jpg', 'alt' => 'inicio de sesión'],
                    ['url' => '/images/CalorieCalc/addFoodItem.jpg', 'alt' => 'agregar alimento'],
                    ['url' => '/images/CalorieCalc/foodItems.jpg', 'alt' => 'alimentos'],
                ],
            ],
        ];
    }
}
