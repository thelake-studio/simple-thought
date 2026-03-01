# 🧠 Simple Thought

> **Diario Emocional y Gestión de Bienestar Personal.**
> *Trabajo de Fin de Grado (TFG) - Desarrollo de Aplicaciones Web.*

![Estado](https://img.shields.io/badge/Estado-Release_Candidate-green)
![Versión](https://img.shields.io/badge/Versión-v0.6.0-blue)
![Stack](https://img.shields.io/badge/Symfony-6.4%2B-black)

## 📖 Descripción
**Simple Thought** es una aplicación web diseñada para ayudar a los usuarios a registrar, entender y gestionar su bienestar integral. El sistema combina un diario emocional profundo con un gestor de objetivos cuantificables y un potente panel de analíticas para detectar patrones de conducta y bienestar.

---

## 🚀 Funcionalidades (v0.6.0)

### 1. El Diario (Core Module)
* **Registro Contextual**: Vinculación de emociones, actividades y etiquetas.
* **Timeline Cronológico**: Vista histórica de reflexiones.
* **Snapshot Emocional**: Integridad de datos históricos (valor del ánimo capturado en el momento de la entrada).

### 2. Gestión de Objetivos
* **Objetivos de Racha (Streaks)**: Contador visual de días consecutivos para hábitos.
* **Objetivos Acumulativos (Sum)**: Metas cuantificables con barras de progreso dinámicas.

### 3. Dashboard de Analíticas Base
Visualización de datos mediante **Chart.js** y **Stimulus**, procesados a través de una capa de servicio especializada (`StatsService`):
* **Frecuencia de Actividades**: Gráfico circular (Doughnut) que identifica las actividades más recurrentes.
* **Matriz de Impacto Emocional**: Gráfico de barras que correlaciona actividades con la media de bienestar percibido.

### 4. Gestión de Usuarios y Seguridad
* **Autenticación Fluida**: Sistema de registro e inicio de sesión con UI simétrica y diseño responsivo.
* **Perfil de Usuario (CRUD)**: Panel privado para visualizar y editar datos personales.
* **Privacidad Total (Zona de Peligro)**: Sistema de borrado de cuenta definitivo con validación CSRF y eliminación en cascada de todos los datos asociados.

### 5. Estadísticas Avanzadas e Interactividad
* **Filtros Dinámicos**: Selector global de fechas para recalcular todas las métricas del dashboard simultáneamente.
* **Gráfica Maestra**: Evolución del estado de ánimo adaptativa según el rango temporal seleccionado.
* **Correlación Objetivos vs. Ánimo**: Gráfica comparativa que analiza el impacto de cumplir metas en tu bienestar general.
* **Radar de Contexto**: Identificación automática de etiquetas "Potenciadoras" (días con notas altas) y "Frenos" (días con notas bajas).
* **El Año en Píxeles**: Mapa de calor anual (Heatmap) generado de forma nativa para visualizar tendencias emocionales a largo plazo.

### 6. Rediseño UI/UX y Clean Code (NUEVO v0.6.0)
* **UI Kit y Estandarización**: Interfaz completamente rediseñada bajo una guía de estilos unificada (Bootstrap 5), con diseño premium, tarjetas flotantes y paleta coherente.
* **Mobile-First y Navegabilidad**: Navegación lateral fija (sticky) en PC y menú offcanvas responsivo en móviles. Cero "callejones sin salida" garantizados en todo el flujo de la aplicación.
* **Accesibilidad (a11y) y Semántica**: Uso estricto de etiquetas HTML5 (`<article>`, `<header>`, `<section>`) y atributos `aria-label` para compatibilidad total con lectores de pantalla.
* **Arquitectura DRY en Twig**: Vistas altamente modularizadas, eliminación de CSS en línea e integración de macros/parciales.
* **Calidad de Código y Robustez**: Documentación PHPDoc exhaustiva, refactorización de controladores y gestión personalizada de errores.

---

## 🛠️ Stack Tecnológico
* **Backend**: PHP 8.2 + Symfony 6.4 (Service Layer Architecture)
* **Base de Datos**: MySQL 8.0
* **Frontend**: Twig + Bootstrap 5 + Stimulus + Chart.js (vía AssetMapper) + FontAwesome
* **Control de Versiones**: Git + GitFlow

---

## 🗺️ Hoja de Ruta (Roadmap)

- [x] **Fase 1: El Diario (v0.1.0)** - *Completado*
- [x] **Fase 2: Gestión de Objetivos (v0.2.0)** - *Completado*
- [x] **Fase 3: Estadísticas y Analíticas (v0.3.0)** - *Completado*
- [x] **Fase 4: Gestión de Usuarios Pro (v0.4.0)** - *Completado*
- [x] **Fase 5: Estadísticas Avanzadas e Interactividad (v0.5.0)** - *Completado*
- [x] **Fase 6: Optimización y Calidad de Código (v0.6.0)** - *Completado*
- [x] **Fase 7: UI/UX y Pulido Final (v0.6.0)** - *Completado*
- [ ] **Fase 8: Despliegue en Producción y Lanzamiento (v1.0.0)** - *Próximamente*

---

## 👤 Autor
Desarrollado por **José Luis Lázaro**.
*Curso 2025/2026*
