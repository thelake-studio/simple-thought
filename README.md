# 🧠 Simple Thought

> **Tu refugio digital. Diario Emocional y Gestión de Bienestar Personal.**
> *Trabajo de Fin de Grado (TFG) - Desarrollo de Aplicaciones Web.*

![Estado](https://img.shields.io/badge/Estado-Stable_Release-success)
![Versión](https://img.shields.io/badge/Versión-v1.0.2-blue)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?logo=php&logoColor=white)
![Symfony](https://img.shields.io/badge/Symfony-7.4-000000?logo=symfony&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-Ready-2496ED?logo=docker&logoColor=white)

## 📖 Filosofía del Proyecto
En la era de la "economía de la atención", la gran mayoría de las aplicaciones compiten por nuestro tiempo y comercian con nuestra información. **Simple Thought** nace como una alternativa radical: una herramienta íntima, libre de ruido visual y diseñada con la **privacidad por defecto**.

No es una red social. No hay administradores globales que puedan leer tus datos. Es un espacio seguro donde el usuario puede registrar su estado de ánimo, gestionar metas cuantificables y analizar su evolución personal a través de un panel analítico interactivo.

---

## 🚀 Características Principales (Core Features)

### 📓 El Diario (Integridad y Contexto)
* **Registro Contextual**: Vinculación de entradas con catálogos personalizables de emociones, actividades y etiquetas.
* **Preservación Histórica (Snapshot)**: El sistema congela el valor numérico del estado de ánimo en el momento de la escritura, garantizando que futuras ediciones del catálogo no corrompan las métricas del pasado.

### 🎯 Gamificación y Objetivos
* **Rachas (Streaks)**: Contadores visuales para fomentar la constancia en hábitos diarios.
* **Metas Acumulativas (Sum)**: Objetivos a largo plazo con barras de progreso dinámicas.

### 📊 Panel Analítico (Dashboard)
Desarrollado con **Chart.js** y orquestado por una arquitectura de Servicios Limpios (`StatsService`):
* **El Año en Píxeles**: Mapa de calor (Heatmap) que resume la evolución emocional de todo un año de un solo vistazo.
* **Matriz de Impacto**: Correlación automática entre las actividades realizadas y la nota media del estado de ánimo.
* **Correlación de Metas**: Gráficas que demuestran visualmente si el cumplimiento de objetivos mejora el bienestar general.

### 🛡️ Privacidad y Seguridad (Zero Trust)
* **Aislamiento de Datos**: Consultas blindadas a nivel de repositorio para evitar vulnerabilidades IDOR. Cada cuenta es un silo aislado.
* **Soberanía Estática**: Uso de **Symfony AssetMapper**. Todos los recursos (Bootstrap, FontAwesome, Chart.js) se sirven de forma local. No hay llamadas a CDNs externas que puedan rastrear la IP del usuario.

---

## 🛠️ Stack Tecnológico
* **Backend**: PHP 8.2 (Tipado estricto, Attributes) + Symfony 7.4.
* **Persistencia**: MySQL 8.0 + Doctrine ORM.
* **Frontend**: Twig + Bootstrap 5 (Mobile-First) + Stimulus + Chart.js.
* **Infraestructura**: Docker + Docker Compose + Entorno DevContainers.
* **Despliegue (CI/CD)**: Railway (PaaS) con Nixpacks.

---

## ⚙️ Despliegue Local (Guía Rápida)

Gracias a la virtualización con Docker, puedes levantar tu propia instancia privada de Simple Thought en 3 sencillos pasos, sin necesidad de instalar PHP ni bases de datos en tu máquina.

**1. Clonar el repositorio y levantar contenedores:**
```bash
git clone [https://github.com/thelake-studio/simple-thought.git](https://github.com/thelake-studio/simple-thought.git)
cd simple-thought
docker-compose up -d --build
```

**2. Instalar dependencias dentro del contenedor:**

```bash
docker exec -it simple_thought_app composer install
```

**3. Preparar la Base de Datos y cargar el entorno de demostración (60 días de datos):**

```bash
docker exec -it simple_thought_app php bin/console doctrine:migrations:migrate --no-interaction
docker exec -it simple_thought_app php bin/console doctrine:fixtures:load --no-interaction
```

📍 **Acceso:** Abre tu navegador en `http://localhost:8000`.

* *Usuario de prueba:* `alumno@daw.com` | *Contraseña:* `123456`
* *Gestor de Base de Datos (phpMyAdmin):* `http://localhost:8081`

---

## 🗺️ Roadmap (Estado del Proyecto)

* [x] **Fase 1-3:** Core del Diario, Objetivos y Estadísticas Base.
* [x] **Fase 4-5:** Perfiles, Seguridad Zero Trust e Interactividad Avanzada.
* [x] **Fase 6-7:** Refactorización (Clean Code), UX/UI y Accesibilidad (a11y).
* [x] **Fase 8:** Despliegue CI/CD en la Nube (Railway).
* [x] **Versión Final (v1.0.2)** - Presentación TFG.

---

## 📄 Licencia y Autoría

Desarrollado por **José Luis Lázaro Jiménez de Cisneros**.
Proyecto de Fin de Grado (Desarrollo de Aplicaciones Web) - Curso 2025/2026.

Este proyecto está bajo la Licencia MIT - mira el archivo [LICENSE](LICENSE) para más detalles.
