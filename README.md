# Demostración PHP Slim Framework con CI/CD

Una aplicación de demostración PHP mínima y lista para producción que muestra las mejores prácticas de CI/CD con Slim Framework 4, Docker, y un pipeline completo de integración, construcción, despliegue y promoción a producción.

## ✨ Características

- **PHP 8.2** con tipado estricto y análisis estático
- **Slim Framework 4** para enrutamiento ligero
- **Docker** con imágenes productivas (Nginx + PHP-FPM + Supervisord)
- **PHPUnit** para pruebas unitarias y de integración
- **PHPStan** (nivel 8) para análisis estático
- **PHP-CS-Fixer** para cumplimiento de estilo de código PSR-12
- **Pipeline CI/CD completo** con GitHub Actions:
  - ✅ Integración Continua (CI)
  - 🐳 Build y publicación de imágenes Docker
  - 🚀 Despliegue automático a DEV
  - 🏷️ Sistema de tagging y promoción a PROD
  - 🔍 Verificación de despliegues con health checks

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
│   ├── workflows/
│   │   ├── ci.yml              # Integración continua (tests, análisis)
│   │   ├── build.yml           # Build y publicación de imágenes Docker
│   │   ├── deploy-dev.yml      # Despliegue a desarrollo
│   │   ├── deploy-prod.yml     # Despliegue a producción
│   │   └── promote-to-prod.yml # Promoción de imagen DEV → PROD
├── docker/
│   ├── nginx/
│   │   └── nginx.conf          # Configuración de Nginx
│   ├── php/
│   │   ├── Dockerfile          # Dockerfile para desarrollo
│   │   └── Dockerfile.prod     # Dockerfile productivo (multi-stage)
│   └── supervisord.conf        # Configuración de Supervisord
├── public/
│   ├── index.php               # Punto de entrada de la aplicación
│   └── version.html            # Información de versión estática
├── src/
│   ├── Application.php         # Inicialización de la aplicación
│   └── Handler/
│       └── HealthHandler.php   # Handler del endpoint /health
├── tests/
│   ├── ApplicationTest.php     # Tests de aplicación completa
│   └── Handler/
│       ├── HealthHandlerTest.php           # Tests unitarios
│       └── HealthHandlerIntegrationTest.php # Tests de integración
├── .gitignore
├── .php-cs-fixer.dist.php      # Configuración de estilo de código
├── composer.json               # Dependencias y scripts
├── docker-compose.yml          # Compose para desarrollo
├── docker-compose.prod.yml     # Compose para testing de imagen productiva
├── phpstan.neon                # Configuración de análisis estático
└── phpunit.xml                 # Configuración de pruebas
```

## Endpoints Disponibles

### `GET /`
Mensaje de bienvenida con información de la API:
```json
{
  "mensaje": "Bienvenido a la API de Demostración PHP Slim",
  "version": "1.0.0",
  "fecha_hora": "2026-01-30T10:00:00+00:00"
}
```

### `GET /health`
Endpoint de verificación de salud con información de build y deployment:
```json
{
  "estado": "saludable",
  "fecha_hora": "2026-01-30T10:00:00+00:00",
  "tiempo_activo": "42 segundos",
  "version_php": "8.2.30",
  "build": {
    "commit": "abc1234",
    "branch": "master",
    "fecha": "2026-01-30T09:00:00+00:00",
    "tag": "abc1234"
  },
  "deployment": {
    "tag": "v1.0.0",
    "environment": "prod"
  }
}
```

**Nota:** El campo `build` contiene información estática de cómo se construyó la imagen Docker, mientras que `deployment` contiene información dinámica de cómo se desplegó (pasada mediante variables de entorno).

## Comandos de Desarrollo

```bash
# Ejecutar pruebas
composer test

# Ejecutar pruebas con cobertura
composer test:coverage

# Análisis estático
composer phpstan

# V🔄 Pipeline de CI/CD

El proyecto implementa un pipeline completo de CI/CD con GitHub Actions:

### 1. **Integración Continua** ([ci.yml](.github/workflows/ci.yml))
Se ejecuta en cada push a ramas de desarrollo y en pull requests:

- ✅ **Validación de Código**
  - Validación de `composer.json`
  - PHP-CS-Fixer (estilo de código PSR-12)
  
- 🔍 **Análisis Estático**
  - PHPStan nivel 8 (máximo nivel de análisis)
  
- 🧪 **Tests**
  - Tests unitarios
  - Tests de integración
  - Tests de aplicación completa (E2E)
  
- 📊 **Cobertura de Código**
  - Generación de reportes de cobertura
  - Comentarios automáticos en PRs
  - Upload a Codecov
  
- 🐳 **Tests Docker**
  - Build de imagen Docker
  - Health checks de contenedor

### 2. **Build y Publicación** ([build.yml](.github/workflows/build.yml))
Se ejecuta en cada push a `master`:

- 🏗️ **Construcción de Imagen Docker**
  - Build de imagen productiva (Nginx + PHP-FPM + Supervisord)
  - Genera múltiples tags:
    - `latest` - última versión
    - `<commit-short>` - identificador único del commit (ej: `abc1234`)
    - `<timestamp>` - marca temporal del build
  
- 🧪 **Testing de Imagen Productiva**
  - Inicia contenedor con docker-compose
  - Verifica endpoints funcionando
  - Valida respuestas HTTP
  
- 📤 **Publicación a Docker Hub**
  - Push de imagen con todos los tags
  - Verificación de disponibilidad
  
- 🚀 **Despliegue Automático a DEV**
  - Llama al workflow de deploy-dev
  - Despliega automáticamente la nueva imagen

### 3. **Despliegue a DEV** ([deploy-dev.yml](.github/workflows/deploy-dev.yml))
Despliega a ambiente de desarrollo:

- 🔍 **Verificación de Imagen**
  - Confirma que la imagen existe en Docker Hub
  
- 📡 **Activación de Webhook**
  - Envía comando de despliegue al servidor DEV
  - Pasa el tag de imagen a desplegar
  
- ⏳ **Espera y Verificación**
  - Espera a que el servidor complete el despliegue
  - Verifica health check del servicio
  - Valida que el tag desplegado coincida con el esperado
  
- 📝 **Resumen de Despliegue**
  - Genera resumen en GitHub con enlaces útiles

### 4. **Promoción a Producción** ([promote-to-prod.yml](.github/workflows/promote-to-prod.yml))
Se ejecuta cuando se crea un tag de versión (ej: `v1.0.0`):

- 🔍 **Obtención de Commit**
  - Identifica el commit asociado al tag
  - Obtiene el short commit ID
  
- ✅ **Verificación de Imagen DEV**
  - Confirma que existe la imagen con el commit correspondiente
  - Asegura que fue testeada en DEV
  
- 🏷️ **Promoción de Imagen**
  - Descarga imagen de DEV (ej: `php-demo:abc1234`)
  - Re-etiqueta con versión semántica (ej: `php-demo:v1.0.0`)
  - Publica nueva etiqueta a Docker Hub
  
- 🚀 **Despliegue a PROD**
  - Llama automáticamente al workflow de deploy-prod

### 5. **Despliegue a PROD** ([deploy-prod.yml](.github/workflows/deploy-prod.yml))
Despliega a ambiente de producción:

- Similar al despliegue a DEV pero con:
  - Tiempos de espera más largos
  - Más intentos de verificación
  - Variables de entorno específicas de PROD
  - Validación del tag semántico

## 🏷️ Sistema de Tagging y Promoción

### Flujo de Trabajo:

```
┌─────────────┐
│ Commit a    │
│ master      │
└──────┬──────┘
       │
       ▼
┌─────────────────────────────────┐
│ Build & Test                    │
│ Genera imágenes:                │
│ - latest                        │
│ - abc1234 (commit short)        │
│ - 20260130-100000 (timestamp)   │
└──────┬──────────────────────────┘
       │
       ▼
┌─────────────────┐
│ Deploy a DEV    │
│ Tag: abc1234    │
└──────┬──────────┘
       │
       │ (Testing manual en DEV)
       │
       ▼
┌─────────────────┐
│ Crear tag Git   │
│ git tag v1.0.0  │
└──────┬──────────┘
       │
       ▼
┌─────────────────────────────┐
│ Promote to Prod             │
│ abc1234 → v1.0.0            │
│ (Re-etiqueta misma imagen)  │
└──────┬──────────────────────┘
       │
       ▼
┌─────────────────┐
│ Deploy a PROD   │
│ Tag: v1.0.0     │
└─────────────────┘
```

### Comandos para Promover a Producción:

```bash
# 1. Verificar que DEV funciona correctamente
curl https://dev.ejemplo.com/health

# 2. Obtener el commit short ID desplegado en DEV
COMMIT_SHORT=$(curl -s https://dev.ejemplo.com/health | jq -r '.build.tag')

# 3. Crear y publicar tag de versión
git tag v1.0.0
git push origin v1.0.0

# 4. GitHub Actions automáticamente:
#    - Encuentra la imagen con ese commit
#    - La re-etiqueta como v1.0.0
#    - La despliega a producción
```

## 🐳 Variables de Entorno para Deployment

Para que el endpoint `/health` reporte correctamente la información de despliegue, el contenedor debe recibir estas variables de entorno:

```bash
# Variables de BUILD (estáticas, baked en la imagen)
GIT_COMMIT=abc1234
GIT_BRANCH=master
BUILD_DATE=2026-01-30T09:00:00+00:00
IMAGE_TAG=abc1234

# Variables de DEPLOYMENT (dinámicas, pasadas en runtime)
DEPLOY_TAG=v1.0.0        # Tag con el que se desplegó
DEPLOY_ENV=prod          # Ambiente (dev, staging, prod)
```

### Ejemplo con Docker Compose:

```yaml
services:
  app:
    image: usuario/php-demo:v1.0.0
    environment:
      - DEPLOY_TAG=v1.0.0
      - DEPLOY_ENV=prod
```

### Ejemplo con Docker Run:

```bash
docker run -d \
  -e DEPLOY_TAG=v1.0.0 \
  -e DEPLOY_ENV=prod \
  -p 8080:80 \
  usuario/php-demo:v1.0.0
```
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

### Código y Arquitectura
- ✅ Aplicación estricta de tipado (PHP 8.2)
- ✅ Inyección de dependencias con PHP-DI
- ✅ Middleware apropiado para manejo de errores
- ✅ Separación de concerns (handlers, routing, middleware)

### Docker e Infraestructura
- ✅ Imagen productiva autocontenida (Nginx + PHP-FPM)
- ✅ Compilaciones multi-etapa para optimización de tamaño
- ✅ Supervisord para gestión de múltiples procesos
- ✅ Health checks configurados
- ✅ Logs centralizados

### CI/CD y Deployment
- ✅ Pipeline completo de CI/CD automatizado
- ✅ Tests en múltiples niveles (unitarios, integración, E2E)
- ✅ Análisis estático y de calidad de código
- ✅ Sistema de tagging y promoción de imágenes
- ✅ Verificación automática de despliegues
- ✅ Separación entre build info y deployment info

### Calidad y Mantenibilidad
- ✅ Cobertura de código con reportes automáticos
- ✅ Estándares de código (PSR-12)
- ✅ Documentación completa
- ✅ Optimización del autoloader de Composer

## Agregar Nuevas Funcionalidades

1. Crear handler en `src/Handler/`
2. Registrar ruta en `src/Application.php`
3. Agregar pruebas en `tests/Handler/`
4. Ejecutar `composer ci` para verificar

## 🎯 Demostración de Fallos de Tests (Para PRs)

Este proyecto incluye código comentado que simula diferentes tipos de fallos para demostrar cómo los checks de GitHub bloquean un PR.

### Simular Fallo de PHPStan (Análisis Estático)

**Archivo:** [src/Handler/HealthHandler.php](src/Handler/HealthHandler.php#L37-L39)

1. En el método `getUptime()`, descomentar la línea:
   ```php
   return $uptime;
   ```
2. Comentar la línea correcta:
   ```php
   // return sprintf('%d segundos', $uptime);
   ```

**Resultado:** PHPStan detectará error de tipo (retorna `int` en vez de `string`)

### Simular Fallo de PHP-CS-Fixer (Estilo de Código)

**Archivo:** [src/Application.php](src/Application.php#L29-L30)

1. Descomentar la línea mal formateada:
   ```php
   $this->app->get('/bad-format', function($request,$response){return $response;});
   ```

**Resultado:** PHP-CS-Fixer detectará formato incorrecto (falta espacios, llaves incorrectas)

### Simular Fallo de PHPUnit (Test Unitario)

**Archivo:** [src/Handler/HealthHandler.php](src/Handler/HealthHandler.php#L73-L78)

1. Descomentar el método `getHealthStatus()` completo
2. En el método `__invoke()`, cambiar:
   ```php
   'estado' => 'saludable',
   ```
   Por:
   ```php
   'estado' => $this->getHealthStatus(),
   ```

**Resultado:** El test `testElEndpointHealthDevuelveEstadoDeSalud` fallará porque espera `'saludable'` pero recibe `'enfermo'`

### Flujo de Demostración

1. Crear una rama: `git checkout -b demo/test-failures`
2. Activar uno de los fallos (descomentar código)
3. Commit y push: `git commit -am "Demo: Simular fallo de [tipo]" && git push`
4. Crear PR en GitHub
5. **Observar:** GitHub Actions mostrará check fallido ❌
6. **Mostrar:** El botón "Merge" estará deshabilitado hasta que se corrija
7. Revertir el cambio, commit y push
8. **Observar:** Check pasa ✅ y se puede hacer merge

### Verificar Localmente

Antes de hacer el PR, puedes verificar los fallos localmente:

```bash
# Ejecutar todos los checks
composer ci

# O checks individuales
composer phpstan    # Análisis estático
composer cs:check   # Estilo de código
composer test       # Tests unitarios
```

## Licencia

MIT

## Contribuir

1. Hacer fork del repositorio
2. Crear una rama de funcionalidad
3. Realizar tus cambios
4. Asegurar que `composer ci` pase
5. Enviar un pull request
