# SENA SmartCourse — Contexto completo del proyecto
> Documento de contextualización para continuar el desarrollo desde cero entendimiento.
> Última actualización: Mayo 2026

---

## ¿Qué es el proyecto?

**SENA SmartCourse** es una plataforma web integral para la gestión y control académico de los cursos complementarios del SENA en Caldas. Reemplaza el uso de formatos físicos, correos y hojas de cálculo por un sistema digital con acceso diferenciado por roles.

Desarrollado como proyecto de investigación formativa del tecnólogo en **Análisis y Desarrollo de Software — Grupo 2613934**, SENA Caldas.

---

## Stack tecnológico

| Capa | Tecnología |
|------|-----------|
| Backend | Laravel 12 + PHP 8.2 |
| Autenticación | Laravel Sanctum (tokens) |
| Base de datos | MySQL (Railway) |
| Frontend | Nuxt 4 + Vue 3 |
| Estado global | Pinia |
| Estilos | Tailwind CSS |
| Reportes PDF | barryvdh/laravel-dompdf |
| Deploy backend | Railway |
| Deploy frontend | Local (localhost:3000) — pendiente Vercel |
| CI/CD | GitHub → Railway auto-deploy |

---

## URLs en producción

```
Backend API:   https://sena-smart-course-back-production.up.railway.app/api
Test endpoint: https://sena-smart-course-back-production.up.railway.app/api/test
GitHub repo:   https://github.com/pablangas28/sena-smart-course-back
Railway:       https://railway.app (proyecto: observant-nature)
```

---

## Usuarios de prueba (seeders)

| Rol | Email | Password |
|-----|-------|----------|
| Coordinador | coordinador@sena.edu.co | Sena2025* |
| Instructor | instructor@sena.edu.co | Sena2025* |
| Aliado | aliado@sena.edu.co | Sena2025* |
| Estudiante | Se crea vía formulario de inscripción | La que el estudiante defina al registrarse |

**Estudiantes en BD (Railway):**
- Juan Pablo Erira Vargas — user_id: 17 — curso_id: 1
- Juan Perez — user_id: 18 — curso_id: 2
- Andres Andica — user_id: 19 — curso_id: 2

---

## Estructura de base de datos (8 tablas)

```
regionales          id, nombre, departamento
users               id, nombre, apellidos, email, telefono, ocupacion,
                    rol (coordinador|instructor|aliado|estudiante),
                    regional_id (FK nullable), password, activo
cursos              id, nombre, descripcion, creado_por (FK users),
                    regional_id (FK), horas_requeridas (default 40),
                    horas_cumplidas (default 0), fecha_inicio, fecha_fin,
                    estado (activo|finalizado|cancelado)
clases              id, curso_id (FK), tema, fecha_hora,
                    tipo (presencial|virtual), duracion_horas (default 2)
formularios_inscripcion  id, curso_id (FK), creado_por (FK users),
                         token (UUID único), activo, expira_en
registro_estudiantes     id, user_id (FK), curso_id (FK), nombre, apellidos,
                         fecha_nacimiento, genero, telefono, celular,
                         tel_contacto_emergencia, cel_contacto_emergencia,
                         pantallazo_sofia, estado (activo|desertado|graduado)
asistencias         id, clase_id (FK), estudiante_id (FK users),
                    asistio (boolean), observacion
calificaciones      id, clase_id (FK), estudiante_id (FK users),
                    nota (decimal 3,1), observacion
```

**Nota crítica:** `Regional.php` tiene `protected $table = 'regionales'` y `FormularioInscripcion.php` tiene `protected $table = 'formularios_inscripcion'` — sin esto Laravel pluraliza mal en inglés.

---

## Arquitectura del backend (Laravel 12)

### Carpeta del proyecto
```
sena-smartcourse-back/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php
│   │   │   ├── UserController.php
│   │   │   ├── RegionalController.php
│   │   │   ├── CursoController.php
│   │   │   ├── ClaseController.php
│   │   │   ├── FormularioInscripcionController.php
│   │   │   ├── RegistroEstudianteController.php
│   │   │   ├── AsistenciaController.php
│   │   │   ├── CalificacionController.php
│   │   │   └── ReporteController.php
│   │   └── Middleware/
│   │       └── RoleMiddleware.php
│   └── Models/
│       ├── User.php
│       ├── Regional.php
│       ├── Curso.php
│       ├── Clase.php
│       ├── FormularioInscripcion.php
│       ├── RegistroEstudiante.php
│       ├── Asistencia.php
│       └── Calificacion.php
├── database/
│   ├── migrations/          (11 migraciones en orden correcto)
│   └── seeders/
│       ├── DatabaseSeeder.php
│       ├── RegionalSeeder.php      (4 regionales de Caldas)
│       ├── CoordinadorSeeder.php
│       └── PruebaSeeder.php        (instructor + aliado + cursos)
├── routes/
│   └── api.php
├── resources/views/reportes/
│   ├── curso.blade.php
│   ├── asistencia.blade.php
│   └── calificaciones.blade.php
├── nixpacks.toml               (configuración Railway)
└── config/cors.php
```

### Middleware de roles registrado en bootstrap/app.php
```php
$middleware->alias(['role' => \App\Http\Middleware\RoleMiddleware::class]);
```

### Custom Start Command en Railway
```
php artisan migrate --force && php -S 0.0.0.0:$PORT -t public
```

---

## Mapa completo de endpoints (api.php)

### Públicas (sin token)
```
GET  /api/test
POST /api/login
GET  /api/inscripcion/{token}
POST /api/inscripcion/{token}
```

### Protegidas — Todos los roles autenticados
```
POST  /api/logout
GET   /api/me
POST  /api/cambiar-password
PATCH /api/usuarios/{user}          ← perfil propio
```

### Coordinador + Instructor + Aliado (lectura)
```
GET /api/cursos
GET /api/cursos/{curso}
GET /api/cursos/{curso}/clases
GET /api/cursos/{curso}/clases/{clase}
GET /api/cursos/{curso_id}/estudiantes
GET /api/estudiantes/{registroEstudiante}
GET /api/cursos/{curso_id}/estudiantes/{estudiante_id}/asistencia
GET /api/cursos/{curso_id}/estudiantes/{estudiante_id}/calificaciones
GET /api/clases/{clase}/asistencia
GET /api/clases/{clase}/calificaciones
```

### Solo Instructor y Aliado (escritura)
```
POST   /api/cursos
PATCH  /api/cursos/{curso}
DELETE /api/cursos/{curso}
POST   /api/cursos/{curso}/clases
PATCH  /api/cursos/{curso}/clases/{clase}
DELETE /api/cursos/{curso}/clases/{clase}
GET    /api/cursos/{curso}/formularios
POST   /api/cursos/{curso}/formularios
PATCH  /api/formularios/{formulario}/toggle-activo
PATCH  /api/estudiantes/{registroEstudiante}/estado
POST   /api/clases/{clase}/asistencia
POST   /api/clases/{clase}/calificaciones
```

### Solo Coordinador
```
GET    /api/usuarios
POST   /api/usuarios
GET    /api/usuarios/{user}
PATCH  /api/usuarios/{user}/toggle-activo
GET    /api/regionales         (apiResource completo)
POST   /api/regionales
GET    /api/regionales/{id}
PATCH  /api/regionales/{id}
DELETE /api/regionales/{id}
GET    /api/reportes/cursos
GET    /api/reportes/cursos/{curso}/pdf
GET    /api/reportes/cursos/{curso}/asistencia-pdf
GET    /api/reportes/cursos/{curso}/calificaciones-pdf
```

### Solo Estudiante
```
GET /api/mi-progreso            ← cursos inscritos con estado
```

---

## Mapa de permisos por rol

| Acción | Coordinador | Instructor | Aliado | Estudiante |
|--------|:-----------:|:----------:|:------:|:----------:|
| Ver TODOS los cursos | ✅ | ❌ | ❌ | ❌ |
| Ver SUS cursos | ❌ | ✅ | ✅ | ❌ |
| Crear curso | ❌ | ✅ | ✅ | ❌ |
| Editar/eliminar curso | ❌ | ✅ solo suyos | ✅ solo suyos | ❌ |
| Gestionar usuarios | ✅ | ❌ | ❌ | ❌ |
| Gestionar regionales | ✅ | ❌ | ❌ | ❌ |
| Reportes PDF | ✅ | ❌ | ❌ | ❌ |
| Registrar asistencia | ❌ | ✅ | ✅ | ❌ |
| Registrar calificaciones | ❌ | ✅ | ✅ | ❌ |
| Generar formularios | ❌ | ✅ | ✅ | ❌ |
| Ver mi progreso | ❌ | ❌ | ❌ | ✅ |

---

## Flujo de inscripción de estudiantes

El estudiante **nunca se crea directamente** — siempre nace desde el flujo público:

```
1. Instructor/Aliado → POST /api/cursos/{id}/formularios
   └── Backend genera UUID único (token)
   └── Devuelve link: {APP_URL}/api/inscripcion/{UUID}

2. Instructor comparte el link por WhatsApp/correo

3. Frontend muestra formulario en: localhost:3000/inscripcion/{UUID}
   └── GET /api/inscripcion/{UUID} → datos del curso

4. Estudiante llena el form y envía
   └── POST /api/inscripcion/{UUID} con form-data
   └── Backend crea User (rol: estudiante)
   └── Crea RegistroEstudiante
   └── Devuelve token para login inmediato
```

**IMPORTANTE:** El link que se comparte debe apuntar al FRONTEND, no al backend:
```javascript
// ✅ Correcto (en formulario.vue)
const uuid = res.formulario.token
linkGenerado.value = `${window.location.origin}/inscripcion/${uuid}`

// ❌ Incorrecto — esto muestra JSON crudo
linkGenerado.value = res.link  // apunta al backend
```

---

## Variables de entorno en Railway

```env
APP_NAME=SENA-SmartCourse
APP_ENV=production
APP_KEY=base64:ZQNurzyzIuSAEYBcJpmC3PirNS499PXG+gc8Z4kQsRg=
APP_DEBUG=false
APP_URL=https://sena-smart-course-back-production.up.railway.app

DB_CONNECTION=mysql
DB_HOST=mysql.railway.internal
DB_PORT=3306
DB_DATABASE=railway
DB_USERNAME=root
DB_PASSWORD=XCrBFTCnvwBPGHELQxZrDqRYSkaxsHOY

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
FILESYSTEM_DISK=public
LOG_LEVEL=error
```

---

## Configuración CORS (config/cors.php)

```php
'allowed_origins' => [
    'http://localhost:3000',        // frontend local
    // 'https://tu-frontend.vercel.app'  // agregar cuando se publique
],
```

---

## Frontend Nuxt 4 — estructura clave

### nuxt.config.ts
```typescript
export default defineNuxtConfig({
  runtimeConfig: {
    public: {
      apiBase: 'https://sena-smart-course-back-production.up.railway.app/api'
    }
  }
})
```

### composables/useApi.js
```javascript
export const useApi = () => {
  const config = useRuntimeConfig()
  const token  = useCookie('token')

  const apiFetch = async (url, options = {}, retries = 2) => {
    const isFormData = options.body instanceof FormData
    try {
      return await $fetch(config.public.apiBase + url, {
        ...options,
        timeout: 10000,
        headers: {
          'Accept': 'application/json',
          ...(!isFormData && { 'Content-Type': 'application/json' }),
          ...(token.value ? { Authorization: `Bearer ${token.value}` } : {}),
          ...options.headers,
        },
      })
    } catch (err) {
      const status = err?.response?.status
      if (status === 401) {
        token.value = null
        const auth = useAuthStore()
        auth.user = null
        await navigateTo('/login')
        return
      }
      if (status === 403) {
        const e = new Error('Sin permisos para esta acción.')
        e.status = 403
        throw e
      }
      const isNetworkError = err?.name === 'FetchError' ||
        err?.message?.includes('Failed to fetch')
      if (retries > 0 && isNetworkError) {
        await new Promise(res => setTimeout(res, 1000))
        return apiFetch(url, options, retries - 1)
      }
      if (!navigator.onLine) throw new Error('SIN_INTERNET')
      throw err
    }
  }
  return { apiFetch }
}
```

### Estructura de páginas del frontend
```
pages/
├── login.vue
├── inscripcion/
│   └── [token].vue          ← página pública de inscripción
├── instructor/
│   ├── cursos/
│   │   ├── [id]/
│   │   │   └── clases/
│   │   │       └── [claseId].vue
│   │   ├── [id].vue
│   │   ├── nuevo.vue
│   │   ├── estudiantes.vue
│   │   ├── formulario.vue
│   │   ├── index.vue
│   │   └── perfil.vue
```

---

## Problemas resueltos en el historial

| Problema | Solución aplicada |
|----------|-------------------|
| Login devolvía 422 revelando si el email existía | Reescribir AuthController para retornar siempre 401 con mensaje genérico |
| Tabla `regionals` no encontrada | Agregar `protected $table = 'regionales'` en Regional.php |
| Tabla `formulario_inscripcions` no encontrada | Agregar `protected $table = 'formularios_inscripcion'` en FormularioInscripcion.php |
| Laravel respondía HTML en vez de JSON en Postman | Agregar header `Accept: application/json` en cada petición |
| CursoController.php tenía código de RegionalController | Reemplazar contenido completo del archivo |
| Railway 502 Bad Gateway | Cambiar puerto de dominio de 80 a 8080 en Settings → Networking |
| Seeders fallaban en redeploy por email duplicado | Quitar `db:seed` del start command, dejar solo `migrate --force` |
| Coordinador recibía 403 en GET /cursos | Mover lectura de cursos al grupo `role:coordinador,instructor,aliado` en api.php |
| Link de inscripción apuntaba al backend | Cambiar `linkGenerado.value = res.link` por `window.location.origin + '/inscripcion/' + uuid` |
| `ERR_INTERNET_DISCONNECTED` en el front | Error de red intermitente del ISP — no es error de código |
| APP_DEBUG=true en producción | Cambiar a `false` en variables de Railway |

---

## Problemas pendientes / en curso

| Problema | Estado |
|----------|--------|
| Estudiante recibe 403 al consultar info del curso | **EN INVESTIGACIÓN** — el endpoint `/api/mi-progreso` existe pero puede faltar endpoint para ver detalle del curso como estudiante |
| El aliado también recibe 403 en algún endpoint | **EN INVESTIGACIÓN** — revisar si el token se está enviando correctamente desde el front |
| Formulario de inscripción al hacer POST devolvía 500 | **RESUELTO** — era por nombre de tabla incorrecto en el modelo |
| Link de inscripción llevaba a vista de cursos en local | **EN INVESTIGACIÓN** — verificar que `pages/inscripcion/[token].vue` existe y que el middleware no redirige al estudiante |
| Descarga de PDFs da error | **PENDIENTE** — agregar pruebas en el manual y diagnosticar |

---

## Comandos clave del proyecto

```bash
# Backend local
php artisan serve                      # levantar servidor en :8000
php artisan migrate:fresh --seed       # resetear BD completa
php artisan optimize:clear             # limpiar caché
php artisan route:list --path=api      # ver todos los endpoints

# Git (Railway autodeploy al hacer push)
git add .
git commit -m "descripcion del cambio"
git push

# Frontend local
npm run dev                            # levantar en localhost:3000
```

---

## Notas importantes para quien retome

1. **El token de Sanctum no expira automáticamente.** Si hay problemas de autenticación, hacer login de nuevo y copiar el token fresco.

2. **Railway tiene $3.28 de crédito restante** (al momento de este documento). Si el servidor no responde, verificar en Railway que el servicio dice "Online".

3. **El seeder NO corre en cada deploy** — solo las migraciones. Los datos de prueba ya están en la BD de Railway.

4. **Para cambios en el backend:** editar código → `git add . && git commit -m "..." && git push` → Railway redespliega en ~2 min.

5. **CORS:** si se publica el frontend en Vercel u otro dominio, agregar la URL en `config/cors.php` y hacer push.

6. **El estudiante no tiene acceso a los endpoints de cursos** — solo a `GET /api/mi-progreso`. Si el front necesita mostrarle info del curso, debe construirla desde la respuesta de ese endpoint (que incluye el curso con `with('curso.regional')`).

7. **Postman:** siempre incluir `Accept: application/json` en Headers o Laravel devuelve HTML en vez de JSON.

---

## Regionales en producción (seeder)

| ID | Nombre | Departamento |
|----|--------|-------------|
| 1 | Alto Occidente | Caldas |
| 2 | Alto Oriente | Caldas |
| 3 | Occidente Próspero | Caldas |
| 4 | Bajo Occidente | Caldas |

---

## Documentos generados en el proyecto

| Documento | Contenido |
|-----------|-----------|
| `sena-smartcourse-documentacion.docx` | Documentación técnica completa con prompts para IA |
| `ERD-SENA-SmartCourse.pdf` | Diagrama entidad-relación (A2 landscape) |
| `pruebas_seguridad_sena.docx` | Informe de pruebas de seguridad Bloque 1 con capturas |
| `guia_pruebas_postman_sena.docx` | Manual paso a paso de las 15 pruebas originales |
| `guia_pruebas_postman_v2.docx` | Manual actualizado con Bloque 6 — Descarga de PDFs (25 pruebas) |
| `API_SENA_SmartCourse.docx` | Documentación completa de los 31 endpoints con código Nuxt 4 |
| `sena_smartcourse_presentacion.html` | Presentación interactiva de 13 diapositivas del proyecto |
