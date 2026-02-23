# 🧠 Simple Thought

> **Diario Emocional y Gestión de Bienestar Personal.**
> *Trabajo de Fin de Grado (TFG) - Desarrollo de Aplicaciones Web.*

![Estado](https://img.shields.io/badge/Estado-En_Desarrollo-green)
![Versión](https://img.shields.io/badge/Versión-v0.4.0-blue)
![Stack](https://img.shields.io/badge/Symfony-6.4%2B-black)

## 📖 Descripción
**Simple Thought** es una aplicación web diseñada para ayudar a los usuarios a registrar, entender y gestionar su bienestar integral. El sistema combina un diario emocional profundo con un gestor de objetivos cuantificables y un potente panel de analíticas para detectar patrones de conducta y bienestar.

---

## 🚀 Funcionalidades (v0.4.0)

Esta versión introduce el módulo de **Gestión de Usuarios y Seguridad**, convirtiendo la aplicación en un sistema multiusuario real y robusto:

### 1. El Diario (Core Module)
* **Registro Contextual**: Vinculación de emociones, actividades y etiquetas.
* **Timeline Cronológico**: Vista histórica de reflexiones.
* **Snapshot Emocional**: Integridad de datos históricos (valor del ánimo capturado en el momento de la entrada).

### 2. Gestión de Objetivos (v0.2.0)
* **Objetivos de Racha (Streaks)**: Contador visual de días consecutivos para hábitos.
* **Objetivos Acumulativos (Sum)**: Metas cuantificables con barras de progreso dinámicas.

### 3. Dashboard de Analíticas (v0.3.0)
Visualización de datos mediante **Chart.js** y **Stimulus**, procesados a través de una capa de servicio especializada (`StatsService`):
* **Evolución del Ánimo**: Gráficas de líneas para el seguimiento semanal y mensual.
* **Frecuencia de Actividades**: Gráfico circular (Doughnut) que identifica las actividades más recurrentes.
* **Matriz de Impacto Emocional**: Gráfico de barras que correlaciona actividades con la media de bienestar percibido.

### 4. Gestión de Usuarios y Seguridad (NUEVO v0.4.0)
* **Autenticación Fluida**: Sistema de registro e inicio de sesión con UI simétrica y diseño responsivo.
* **Perfil de Usuario (CRUD)**: Panel privado para visualizar y editar datos personales.
* **Privacidad Total (Zona de Peligro)**: Sistema de borrado de cuenta definitivo con validación CSRF y eliminación en cascada de todos los datos asociados.
* **Arquitectura Segura**: Rutas protegidas mediante firewalls de Symfony y atributos `#[IsGranted]`.

---

## 🛠️ Stack Tecnológico
* **Backend**: PHP 8.2 + Symfony 6.4 (Service Layer Architecture)
* **Base de Datos**: MySQL 8.0
* **Frontend**: Twig + Bootstrap 5 + Stimulus + Chart.js (vía AssetMapper)
* **Control de Versiones**: Git + GitFlow

---

## 🗺️ Hoja de Ruta (Roadmap)

- [x] **Fase 1: El Diario (v0.1.0)** - *Completado*
- [x] **Fase 2: Gestión de Objetivos (v0.2.0)** - *Completado*
- [x] **Fase 3: Estadísticas y Analíticas (v0.3.0)** - *Completado*
- [x] **Fase 4: Gestión de Usuarios Pro (v0.4.0)** - *Completado*
- [ ] **Fase 5: Estadísticas Avanzadas e Interactividad** - *Próximamente*
    - Filtros por fecha dinámicos.
    - Exportación de reportes.
- [ ] **Fase 6: Optimización y Calidad de Código**
- [ ] **Fase 7: UI/UX y Pulido Final**

---

## 👤 Autor
Desarrollado por **José Luis Lázaro**.
*Curso 2025/2026*
