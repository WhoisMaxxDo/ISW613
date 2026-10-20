# Workshop 3 — Gestión de cursos

Aplicación PHP y MySQL basada en una copia de Workshop 2. Conserva registro,
inicio de sesión y edición del perfil e incorpora el CRUD de cursos.

## Ejecutar con XAMPP

1. Inicia Apache y MySQL.
2. Importa `database.sql` desde phpMyAdmin. Crea la base `workshop3` sin modificar `workshop2`.
3. Revisa las credenciales de `conexion.php` (por defecto: `127.0.0.1`, `root`, sin contraseña).
4. Abre <http://localhost/Workshop%203/>.
5. Registra una cuenta o ingresa con usuario **isw613** y contraseña **isw613** (solo para demostración).

Requiere PHP con PDO MySQL y mbstring. Las contraseñas nuevas se guardan con
`password_hash`; la cuenta de ejemplo heredada se convierte a hash al iniciar sesión.

## Funcionalidad

- Menú con Mi perfil, Cursos y Cerrar sesión.
- Listado de cursos con nombre, código, ID de carrera, créditos y descripción.
- Crear y editar con validación en servidor, consultas preparadas y código único.
- Eliminar exclusivamente mediante POST, confirmación y token CSRF.
- Creación, edición y eliminación redirigen al listado de cursos.
- Acceso a cursos restringido a usuarios autenticados.
- Carreras relacionadas por clave foránea; incluye tres carreras y un curso de ejemplo.
- Créditos enteros de 1 a 30; descripción de hasta 5000 caracteres.

## Comprobación manual

1. Sin iniciar sesión, abre `cursos.php`: debe redirigir al inicio.
2. Inicia sesión y verifica los tres enlaces del menú.
3. Crea un curso y comprueba su aparición en la tabla.
4. Edita todos sus atributos y verifica los cambios.
5. Intenta repetir un código: debe mostrar error sin perder los datos ingresados.
6. Elimina el curso y verifica que desaparezca.
7. Abre `eliminar-curso.php` directamente: debe responder 405 y no eliminar nada.
8. Cierra sesión y comprueba que no puedes volver a los cursos sin autenticarte.

Repositorio: <https://github.com/WhoisMaxxDo/ISW613>
