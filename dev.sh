#!/usr/bin/env bash
set -euo pipefail

# =========================
# Config
# =========================
APP_SERVICE="${APP_SERVICE:-app}"          # nombre del servicio PHP en docker-compose.yml
COMPOSE_FILE="${COMPOSE_FILE:-docker-compose.yml}"
IMAGES_DIR="${IMAGES_DIR:-./imagenes}"
CONTAINER_IMAGES_PATH="/var/www/html/imagenes"
WWW_UID="${WWW_UID:-33}"                   # www-data uid típico (Debian/Apache)
WWW_GID="${WWW_GID:-33}"

# Detecta si necesitamos sudo para docker
DOCKER="docker"
if ! $DOCKER ps >/dev/null 2>&1; then
  if command -v sudo >/dev/null 2>&1; then
    DOCKER="sudo docker"
  fi
fi

# Helpers de salida
log()   { printf "\033[1;32m[OK]\033[0m %s\n"  "$*"; }
warn()  { printf "\033[1;33m[WARN]\033[0m %s\n" "$*"; }
err()   { printf "\033[1;31m[ERR]\033[0m %s\n"  "$*" >&2; }

need() {
  if ! command -v "$1" >/dev/null 2>&1; then
    err "No se encontró '$1' en PATH. Instálalo y reintenta."
    exit 1
  fi
}

compose() {
  $DOCKER compose -f "$COMPOSE_FILE" "$@"
}

# =========================
# Comprobaciones
# =========================
check() {
  need docker
  # compose plugin integrado
  if ! docker compose version >/dev/null 2>&1; then
    err "Tu instalación de Docker no tiene el plugin 'compose'. Instálalo."
    exit 1
  fi
  if [[ ! -f "$COMPOSE_FILE" ]]; then
    err "No encuentro $COMPOSE_FILE en la raíz del proyecto."
    exit 1
  fi
  if grep -qE '^\s*version\s*:' "$COMPOSE_FILE"; then
    warn "Tu docker-compose.yml tiene 'version:' (obsoleta). Puedes quitar esa línea para evitar el warning."
  fi
  log "Dependencias OK."
}

# Crea ./imagenes y ajusta permisos en host
prepare_images_dir() {
  mkdir -p "$IMAGES_DIR"
  # Intentamos cambiar dueño a 33:33 (www-data). Si falla sin sudo, reintentamos con sudo si existe.
  if chown -R "$WWW_UID:$WWW_GID" "$IMAGES_DIR" 2>/dev/null; then
    :
  else
    if command -v sudo >/dev/null 2>&1; then
      sudo chown -R "$WWW_UID:$WWW_GID" "$IMAGES_DIR" || true
    fi
  fi
  chmod 775 "$IMAGES_DIR" 2>/dev/null || true
  log "Carpeta $IMAGES_DIR preparada."
}

# Ajusta permisos desde dentro del contenedor (por si el chown en host no aplica)
fix_perms_in_container() {
  compose exec -T "$APP_SERVICE" bash -lc "
    mkdir -p '$CONTAINER_IMAGES_PATH' &&
    chown -R $WWW_UID:$WWW_GID '$CONTAINER_IMAGES_PATH' &&
    chmod 775 '$CONTAINER_IMAGES_PATH'
  "
  log "Permisos ajustados dentro del contenedor."
}

# Test de escritura en la ruta del contenedor
test_writable() {
  compose exec -T "$APP_SERVICE" bash -lc "
    php -r 'var_dump(is_writable(\"$CONTAINER_IMAGES_PATH\"));'
  "
}

# =========================
# Comandos
# =========================
cmd_up() {
  check
  prepare_images_dir
  compose up --build -d
  # segundo intento de permisos ya con contenedor arriba
  fix_perms_in_container || true
  log "Servicios arriba. App: http://localhost:8080  phpMyAdmin: http://localhost:8081"
  log "Prueba de escritura en $CONTAINER_IMAGES_PATH:"
  test_writable || true
}

cmd_down() {
  check
  compose down
  log "Servicios detenidos."
}

cmd_logs() {
  check
  compose logs -f "$APP_SERVICE"
}

cmd_status() {
  check
  compose ps
}

cmd_shell() {
  check
  compose exec "$APP_SERVICE" bash
}

cmd_fix_perms() {
  check
  prepare_images_dir
  fix_perms_in_container
  test_writable || true
}

cmd_test() {
  check
  test_writable
}

usage() {
  cat <<EOF
Uso: $(basename "$0") <comando>

Comandos:
  up         Construye y levanta el stack; prepara permisos de ./imagenes
  down       Detiene y elimina contenedores
  logs       Muestra logs del servicio PHP (app)
  status     Muestra estado de los servicios
  sh         Abre una shell dentro del servicio 'app'
  fix-perms  Repara permisos de ./imagenes (host) y en el contenedor
  test       Verifica si $CONTAINER_IMAGES_PATH es escribible desde PHP

Variables opcionales:
  APP_SERVICE   (default: app)
  COMPOSE_FILE  (default: docker-compose.yml)
  IMAGES_DIR    (default: ./imagenes)
  WWW_UID       (default: 33)
  WWW_GID       (default: 33)

Ejemplos:
  $(basename "$0") up
  $(basename "$0") logs
  IMAGES_DIR=./uploads $(basename "$0") fix-perms
EOF
}

# Router
cmd="${1:-}"
case "$cmd" in
  up)        shift; cmd_up "$@";;
  down)      shift; cmd_down "$@";;
  logs)      shift; cmd_logs "$@";;
  status)    shift; cmd_status "$@";;
  sh|shell)  shift; cmd_shell "$@";;
  fix-perms) shift; cmd_fix_perms "$@";;
  test)      shift; cmd_test "$@";;
  ""|-h|--help|help) usage;;
  *) err "Comando desconocido: $cmd"; usage; exit 1;;
esac
