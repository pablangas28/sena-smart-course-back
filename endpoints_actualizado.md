# Endpoints de la API - SENA SmartCourse

Este documento sirve como referencia rápida para el equipo de Frontend. Toda la documentación interactiva (con esquemas exactos de Request/Response) está disponible en el Swagger en `/docs/api`.

> **Base URL (Local):** `http://localhost:8000/api`
> **Base URL (Prod):** `https://sena-smart-course-back-production.up.railway.app/api`

---

## 🔒 Autenticación y Perfil
| Método | Endpoint | Descripción | Body (JSON) | Requiere Auth |
|---|---|---|---|---|
| `POST` | `/login` | Iniciar sesión | `email`, `password` | No |
| `POST` | `/logout` | Cerrar sesión | - | Sí |
| `GET` | `/me` | Obtener datos del usuario logueado | - | Sí |
| `POST` | `/cambiar-password` | Cambiar contraseña | `current_password`, `password`, `password_confirmation` | Sí |
| `PATCH` | `/usuarios/{id}` | Actualizar perfil de usuario | Campos a cambiar | Sí |

---

## 🌍 Públicas (Inscripción de Estudiantes)
| Método | Endpoint | Descripción | Body (JSON) | Requiere Auth |
|---|---|---|---|---|
| `GET` | `/test` | Health check de la API | - | No |
| `GET` | `/inscripcion/{token}` | Ver la información del curso al que se va a inscribir | - | No |
| `POST` | `/inscripcion/{token}` | Registrar al aprendiz (crea usuario y registro en el curso) | `nombre`, `apellidos`, `email`, `password`, `fecha_nacimiento` (YYYY-MM-DD), `genero`, `celular`, `documento`, `cel_contacto_emergencia`, `pantallazo_sofia` (File) | No |

---

## 📚 Cursos y Clases (Lectura: Todos los roles)
_Nota: Solo se muestran los cursos en los que el usuario tiene participación._
| Método | Endpoint | Descripción | Requiere Auth |
|---|---|---|---|
| `GET` | `/cursos` | Listar cursos | Sí |
| `GET` | `/cursos/{id}` | Detalle de un curso | Sí |
| `GET` | `/cursos/{id}/clases` | Listar clases de un curso | Sí |
| `GET` | `/cursos/{id}/clases/{clase_id}` | Detalle de una clase | Sí |

### Gestión de Cursos (Escritura: Solo Instructor/Aliado)
- `POST` `/cursos`
- `PATCH` `/cursos/{id}`
- `DELETE` `/cursos/{id}`
- `POST` `/cursos/{id}/clases`
- `PATCH` `/cursos/{curso_id}/clases/{clase_id}`
- `DELETE` `/cursos/{curso_id}/clases/{clase_id}`

---

## 📊 Asistencias y Calificaciones
_Nota: Cualquier usuario puede ver esto SIEMPRE Y CUANDO pertenezca al curso._
| Método | Endpoint | Descripción | Requiere Auth |
|---|---|---|---|
| `GET` | `/clases/{clase_id}/asistencia` | Ver lista de asistencia de la clase | Sí |
| `GET` | `/cursos/{curso_id}/estudiantes/{user_id}/asistencia` | Ver resumen de asistencia de un estudiante | Sí |
| `GET` | `/clases/{clase_id}/calificaciones` | Ver todas las notas de una clase | Sí |
| `GET` | `/cursos/{curso_id}/estudiantes/{user_id}/calificaciones`| Ver promedio final y notas de un estudiante | Sí |

### Subir Asistencias/Notas (Escritura: Solo Instructor/Aliado)
| Método | Endpoint | Descripción | Body (JSON) |
|---|---|---|---|
| `POST` | `/clases/{clase_id}/asistencia` | Registrar asistencia masiva | `{"asistencias": [ {"estudiante_id": 1, "asistio": true, "observacion": "..."} ]}` |
| `POST` | `/clases/{clase_id}/calificaciones` | Registrar notas masivas | `{"calificaciones": [ {"estudiante_id": 1, "nota": 4.5, "observacion": "..."} ]}` |

---

## 🧑‍🎓 Estudiantes y Mi Progreso
| Método | Endpoint | Descripción | Roles Permitidos |
|---|---|---|---|
| `GET` | `/mi-progreso` | Ve los cursos en los que el estudiante logueado está inscrito | **Solo Estudiante** |
| `GET` | `/cursos/{id}/estudiantes` | Listar estudiantes inscritos en el curso | Coordinador, Instructor, Aliado |
| `GET` | `/estudiantes/{registro_id}` | Ver detalle completo del registro de un estudiante | Coordinador, Instructor, Aliado |
| `PATCH` | `/estudiantes/{registro_id}/estado` | Cambiar estado (activo, desertado, graduado) | Instructor, Aliado |

---

## 🔗 Formularios de Inscripción (Instructor/Aliado)
| Método | Endpoint | Descripción |
|---|---|---|
| `GET` | `/cursos/{id}/formularios` | Listar formularios de un curso |
| `POST` | `/cursos/{id}/formularios` | Generar nuevo link/formulario (`fecha_cierre` opcional) |
| `PATCH` | `/formularios/{formulario_id}/toggle-activo` | Apagar/Prender un formulario de inscripción |

---

## 🏢 Regionales y Reportes (Admin / Coordinador)
- **Regionales:**
  - `GET` `/regionales` y `GET` `/regionales/{id}` (Lectura: Coord, Instructor, Aliado)
  - `POST`, `PATCH`, `DELETE` `/regionales` (Solo Coordinador)
- **Gestión de Usuarios (Admin):**
  - `GET`, `POST` `/usuarios`
  - `PATCH` `/usuarios/{id}/toggle-activo`
- **Reportes (Descarga PDF):**
  - `GET` `/reportes/cursos`
  - `GET` `/reportes/cursos/{id}/pdf`
  - `GET` `/reportes/cursos/{id}/asistencia-pdf`
  - `GET` `/reportes/cursos/{id}/calificaciones-pdf`
