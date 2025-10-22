# Bienes Raices (inicio)

Repositorio base para un sitio de bienes raices que usa un flujo de trabajo con Gulp para compilar estilos, compactar JavaScript y optimizar imagenes. Este README es provisional y debera ampliarse conforme avance el desarrollo.

## Requisitos previos
- Node.js 16 o superior
- npm (incluido con Node.js)

## Instalacion
```bash
npm install
```

## Desarrollo
```bash
npm run dev
```
El comando anterior ejecuta la tarea por defecto de Gulp, que:
- Compila los archivos SCSS de `src/scss` a CSS minificado en `build/css`
- Une y minifica los scripts de `src/js` en `build/js`
- Optimiza las imagenes de `src/img` (incluye version WebP)
- Mantiene un watcher activo para recompilar ante cualquier cambio

## Estructura principal
```
.
├── gulpfile.js        # Configuracion de tareas de Gulp
├── src
│   ├── img            # Recursos graficos de ejemplo
│   ├── js             # Archivos JavaScript (bundle principal y Modernizr)
│   └── scss           # Estilos base en Sass
└── build/             # Salida generada al ejecutar la tarea por defecto
```

## Pendiente
- Documentar la estructura HTML y componentes cuando esten disponibles
- Agregar instrucciones de despliegue o build para produccion
- Incluir capturas o recursos visuales del sitio terminado
