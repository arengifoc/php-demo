# Demostración PHP Slim Framework

Una aplicación de demostración PHP mínima y lista para producción que muestra las mejores prácticas de CI/CD con Slim Framework 4, Docker y herramientas modernas de PHP.

## Características

- **PHP 8.2** con tipado estricto
- **Slim Framework 4** para enrutamiento ligero
- **Docker Compose** configurado (nginx + php-fpm)
- **PHPUnit** para pruebas
- **PHPStan** (nivel 8) para análisis estático
- **PHP-CS-Fixer** para cumplimiento de estilo de código
- **GitHub Actions** pipeline de CI/CD

## Requisitos

- Docker & Docker Compose
- PHP 8.2+ (para desarrollo local)
- Composer 2.x

## Inicio Rápido

### Usando Docker (Recomendado)

```bash
# Construir e iniciar contenedores
docker-compose up -d

# Instalar dependencias dentro del contenedor (requerido en la primera ejecución)
docker-compose exec php composer install

# Acceder a la aplicación
curl http://localhost:8080
curl http://localhost:8080/health
```

**Nota:** Debe ejecutar `composer install` dentro del contenedor después del primer inicio, ya que el montaje del volumen sobrescribe el sistema de archivos del contenedor.

### Desarrollo Local

```bash
# Instalar dependencias
composer install

# Ejecutar servidor PHP integrado
php -S localhost:8080 -t public

# O usar el script de desarrollo
composer serve
```

## Estructura del Proyecto

```
.
├── .github/
│   └── workflows/
│       └── ci.yml          # Pipeline CI de GitHub Actions
├── docker/
│   ├── nginx/
│   │   └── nginx.conf      # Configuración de Nginx
│   └── php/
│       └── Dockerfile      # Dockerfile de PHP-FPM
├── public/
│   └── index.php           # Punto de entrada de la aplicación
├── src/
│   ├── Application.php     # Inicialización de la aplicación
│   └── Handler/
│       └── HealthHandler.php
├── tests/
│   └── Handler/
│       └── HealthHandlerTest.php
├── .gitignore
├── .php-cs-fixer.dist.php  # Configuración de estilo de código
├── composer.json
├── docker-compose.yml
├── phpstan.neon            # Configuración de análisis estático
└── phpunit.xml             # Configuración de pruebas
```

## Endpoints Disponibles

- `GET /` - Mensaje de bienvenida con información de la API
- `GET /health` - Endpoint de verificación de salud

## Comandos de Desarrollo

```bash
# Ejecutar pruebas
composer test

# Ejecutar pruebas con cobertura
composer test:coverage

# Análisis estático
composer phpstan

# Verificar estilo de código
composer cs:check

# Corregir estilo de código
composer cs:fix

# Ejecutar todas las verificaciones (simulación de CI)
composer ci
```

## Comandos de Docker

```bash
# Iniciar servicios
docker-compose up -d

# Ver logs
docker-compose logs -f

# Detener servicios
docker-compose down

# Reconstruir contenedores
docker-compose build --no-cache

# Ejecutar comandos en el contenedor PHP
docker-compose exec php composer install
docker-compose exec php composer test
```

## Pipeline de CI/CD

El flujo de trabajo de GitHub Actions se ejecuta en cada push y pull request:

1. **Verificaciones de Calidad de Código**
   - Validación de Composer
   - PHP-CS-Fixer (estilo de código)
   - PHPStan (análisis estático)

2. **Pruebas**
   - Suite de pruebas PHPUnit
   - Reporte de cobertura de código

3. **Compilación Docker**
   - Construir imágenes Docker
   - Iniciar contenedores
   - Verificación de salud

## Configuración

### PHP-CS-Fixer
Configuración en [.php-cs-fixer.dist.php](.php-cs-fixer.dist.php) - sigue PSR-12 con migraciones de PHP 8.2.

### PHPStan
Configuración en [phpstan.neon](phpstan.neon) - análisis de nivel 8 (el más estricto).

### PHPUnit
Configuración en [phpunit.xml](phpunit.xml) - incluye configuración de cobertura.

## Consideraciones de Producción

Esta demostración incluye prácticas listas para producción:

- Aplicación estricta de tipado
- Inyección de dependencias con PHP-DI
- Middleware apropiado para manejo de errores
- Compilaciones multi-etapa de Docker para optimización
- Optimización del autoloader de Composer
- Endpoints de verificación de salud para monitoreo
- Pipeline completo de CI/CD

## Agregar Nuevas Funcionalidades

1. Crear handler en `src/Handler/`
2. Registrar ruta en `src/Application.php`
3. Agregar pruebas en `tests/Handler/`
4. Ejecutar `composer ci` para verificar

## Licencia

MIT

## Contribuir

1. Hacer fork del repositorio
2. Crear una rama de funcionalidad
3. Realizar tus cambios
4. Asegurar que `composer ci` pase
5. Enviar un pull request
