# Bienes Raices

Portal full stack para listar, promocionar y administrar propiedades inmobiliarias. El frontend publica un sitio responsivo con secciones de blog, listado y detalle de inmuebles, mientras que el backend en PHP expone un panel para agentes con autenticacion, altas/bajas/cambios y manejo de imagenes. El proyecto incluye una infraestructura dockerizada y un pipeline de assets con Gulp (Sass, JavaScript y optimizacion de imagenes).

## Tabla de contenido
- [Descripcion general](#descripcion-general)
- [Caracteristicas clave](#caracteristicas-clave)
- [Stack tecnologico](#stack-tecnologico)
- [Estructura del proyecto](#estructura-del-proyecto)
- [Requisitos previos](#requisitos-previos)
- [Puesta en marcha rapida con Docker](#puesta-en-marcha-rapida-con-docker)
- [Configuracion manual sin Docker](#configuracion-manual-sin-docker)
- [Compilacion de assets](#compilacion-de-assets)
- [Base de datos y autenticacion](#base-de-datos-y-autenticacion)
- [Despliegue](#despliegue)
- [Comandos utiles](#comandos-utiles)
- [Resolucion de problemas](#resolucion-de-problemas)

## Descripcion general
El proyecto sigue una arquitectura tradicional LAMP:

1. Las paginas publicas (`index.php`, `anuncios.php`, `anuncio.php`, `blog.php`, `nosotros.php`, `contacto.php`) se renderizan del lado del servidor y consumen datos directamente desde MySQL.
2. El panel `/admin` exige inicio de sesion y permite crear, editar y eliminar registros de propiedades, asociandolos con vendedores y manipulando imagenes almacenadas en `imagenes/`.
3. El frontend se escribe en Sass y JavaScript vanilla (`src/`), se transpila mediante Gulp y se publica en `build/`. Incluye modo oscuro opcional y navegacion responsive.
4. Un `docker-compose.yml` levanta PHP 8.2 con Apache, MySQL 8 y phpMyAdmin para desarrollo local o despliegues reproducibles.

## Caracteristicas clave
- Sitio publico con hero inicial, listado filtrable por limite, detalle de propiedad y secciones blog/testimoniales.
- Plantillas reutilizables (`includes/templates`) para header/footer y componentes (anuncios).
- Panel administrativo protegido por sesion (login + logout) con CRUD de propiedades y asignacion a vendedores.
- Subida de imagenes con validaciones de tamaño, generacion de nombre unico y persistencia en disco.
- Pipeline de estilos Sass -> CSS minificado, bundle JS + Modernizr y optimizacion de imagenes (JPEG, WebP, AVIF) mediante Sharp.
- Script Docker listo para usar con MySQL inicializado (propiedades, vendedores, usuarios) y phpMyAdmin.
- Variables de entorno para configurar la conexion a base de datos en cualquier entorno (Docker o manual).

## Stack tecnologico
| Capa | Tecnologias |
| --- | --- |
| Backend | PHP 8.2-apache, extensiones mysqli y mod_rewrite |
| Base de datos | MySQL 8.0 |
| Infraestructura | Docker Compose (app, mysql, phpmyadmin) |
| Frontend | HTML + Sass + JS vanilla, Gulp 5, Sharp para imagenes |
| Autenticacion | Sesiones PHP + password hashing (Bcrypt) |

## Estructura del proyecto
```
.
├── admin/                 # Panel de administracion (dashboard y formularios CRUD)
├── docker/                # Dockerfile PHP y script de inicializacion de MySQL
├── includes/
│   ├── config/database.php    # Conexion centralizada usando variables de entorno
│   ├── funciones.php          # Helpers varios (templates, auth)
│   └── templates/             # Header, footer y componente de anuncios
├── imagenes/              # Fotos cargadas desde el dashboard
├── src/                   # Fuentes Sass, JS y assets sin optimizar
├── build/                 # Salida compilada por Gulp (css/js/img)
├── docker-compose.yml     # Orquestacion completa del entorno
├── gulpfile.js            # Definicion de tareas css/js/imagenes + watcher
├── package.json           # Dependencias front y script npm
└── *.php                  # Paginas publicas (index, login, blog, contacto, etc.)
```

## Requisitos previos
### Opcion 1: Docker
- Docker Engine 24+
- Docker Compose Plugin 2.20+

### Opcion 2: Entorno nativo
- PHP 8.2 con mysqli y mod_rewrite habilitado (Apache o servidor embebido)
- MySQL 8.0
- Node.js 18+ (recomendado) y npm
- Herramientas de build basicas (make/gcc) para sharp en sistemas Linux

## Puesta en marcha rapida con Docker
1. Clona este repositorio y posicionate en la raiz.
2. Construye e inicia los servicios:
   ```bash
   chmod +x dev.sh
   # Escoje el comando
   ./dev.sh up        # construir y levantar
   ./dev.sh logs      # ver logs del servicio PHP
   ./dev.sh test      # prueba que /var/www/html/imagenes sea escribible
   ./dev.sh down      # apagar
   ./dev.sh status    # estado de contenedores
   ./dev.sh sh        # shell dentro del contenedor "app"
   ./dev.sh fix-perms # repara permisos de ./imagenes

   ```
3. Accede a:
   - Aplicacion: http://localhost:8080
   - phpMyAdmin: http://localhost:8081 (host `mysql`, user `bienes_user`, pass `bienes_pass`)
4. Cuando termines, detiene y elimina los contenedores:
   ```bash
   docker compose down
   ```

Los contenedores definen las variables necesarias (`DB_HOST`, `DB_DATABASE`, etc.) y montan el proyecto completo dentro de `/var/www/html`, por lo que los cambios locales se reflejan al instante.

## Configuracion manual sin Docker
1. Instala PHP, MySQL y Node segun tu sistema.
2. Crea la base de datos ejecutando `docker/mysql/init.sql` (puedes importarla desde tu cliente o `mysql -u root -p < docker/mysql/init.sql`).
3. Configura las variables de entorno antes de levantar el servidor web:
   ```bash
   export DB_HOST=127.0.0.1
   export DB_PORT=3306
   export DB_DATABASE=bienes_raices
   export DB_USERNAME=bienes_user
   export DB_PASSWORD=bienes_pass
   ```
   En Apache puedes declararlas con `SetEnv` o ajustarlas directamente en `includes/config/database.php`.
4. Instala dependencias de frontend:
   ```bash
   npm install
   ```
5. Compila los assets (ver seccion siguiente) y sirve la aplicacion con tu stack preferido. Para pruebas rapidas:
   ```bash
   php -S localhost:8000 -t /ruta/al/proyecto
   ```

## Compilacion de assets
Todas las tareas viven en `gulpfile.js`.

- Desarrollo con watcher:
  ```bash
  npm run dev
  ```
  Ejecuta las tareas `js`, `css`, `imagenes` y deja watch activo sobre `src/scss`, `src/js` y `src/img`.

- Build puntual (sin watcher), ideal para CI/CD:
  ```bash
  npx gulp js
  npx gulp css
  npx gulp imagenes
  ```

Los resultados se escriben en `build/` y se referencian desde los templates (`/build/css/app.css`, `/build/js/bundle.min.js`).

## Base de datos y autenticacion
- Script de creacion inicial: `docker/mysql/init.sql`.
- Tablas principales:
  - `vendedores`: datos de asesores (nombre, apellido, telefono).
  - `propiedades`: informacion comercial + FK a `vendedores`.
  - `usuarios`: credenciales para el dashboard (email + password bcrypt).
- Crear un usuario administrador:
  ```bash
  php -r "echo password_hash('tu_password_segura', PASSWORD_BCRYPT), PHP_EOL;"
  ```
  Copia el hash e inserta:
  ```sql
  INSERT INTO usuarios (email, password) VALUES ('admin@demo.com', '$2y$10$...');
  ```
- La sesion se inicia en `login.php` y se destruye en `cerrar-sesion.php`. Sin usuario valido no se puede acceder a `/admin`.

## Despliegue
1. Define las variables `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` en el entorno del servidor (Apache/Nginx + PHP-FPM).
2. Ejecuta `npm install` seguido de `npx gulp js css imagenes` para dejar `build/` actualizado; sube `build/` y `imagenes/` junto con el resto del codigo.
3. Provisiona MySQL ejecutando el script `docker/mysql/init.sql` o tus propios migrations/seeds.
4. Configura un host virtual que apunte a la raiz del repositorio (el proyecto espera rutas absolutas tipo `/build/...`).
5. Asegura permisos de escritura para `imagenes/` (subida de fotos) y de lectura para `build/`.
6. (Opcional) expone `phpMyAdmin` solo en entornos de desarrollo; en produccion se recomienda retirarlo.

Para despliegues containerizados puedes reutilizar los servicios de `docker-compose.yml` en tu plataforma (ECS, Swarm, etc.) o construir imagenes separadas a partir de `docker/php/Dockerfile`.

## Comandos utiles
| Tarea | Comando |
| --- | --- |
| Levantar entorno docker | `docker compose up --build -d` |
| Ver logs del contenedor PHP | `docker compose logs -f app` |
| Detener entorno docker | `docker compose down` |
| Ejecutar watcher de assets | `npm run dev` |
| Servidor PHP embebido | `php -S localhost:8000 -t .` |

## Resolucion de problemas
- **No conecta a MySQL:** confirma que las variables `DB_*` apuntan al host correcto y que el puerto esta accesible; con Docker usa `mysql` como host.
- **Sharp falla al instalarse:** en Linux instala dependencias de imagenes (`sudo apt install build-essential libvips-dev`) antes de `npm install`.
- **Imagenes no aparecen:** verifica permisos de `imagenes/` y que los archivos existan con el nombre generado en la BD.
- **Sesion no persiste:** el proyecto usa `session_start()` en los templates; asegúrate de que PHP pueda escribir en `session.save_path`.
- **info.php expone configuracion:** elimínalo o protege la ruta en ambientes productivos.

Con esto el repositorio queda documentado con la informacion necesaria para desarrollar, ejecutar y desplegar el portal de bienes raices.
