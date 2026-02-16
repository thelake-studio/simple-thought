# 🧠 Simple Thought

> **Diario Emocional y Gestión de Bienestar Personal.**
> *Trabajo de Fin de Grado (TFG) - Desarrollo de Aplicaciones Web.*

![Estado](https://img.shields.io/badge/Estado-En_Desarrollo-green)
![Versión](https://img.shields.io/badge/Versión-v0.2.0_Beta-blue)
![Stack](https://img.shields.io/badge/Symfony-6.4%2B-black)

## 📖 Descripción
**Simple Thought** es una aplicación web diseñada para ayudar a los usuarios a registrar, entender y gestionar su bienestar integral. El sistema combina un diario emocional profundo con un gestor de objetivos cuantificables, permitiendo al usuario no solo reflexionar sobre cómo se siente, sino también actuar sobre lo que quiere conseguir.

---

## 🚀 Funcionalidades (v0.2.0)

Esta versión **Beta** introduce el módulo completo de Gestión de Objetivos, complementando al núcleo del Diario:

### 1. El Diario (Core Module)
* **Registro Contextual**: Vinculación de emociones, actividades y etiquetas en cada entrada.
* **Timeline Cronológico**: Vista histórica de pensamientos y estados de ánimo.
* **Snapshot Emocional**: Integridad de datos históricos.
* **Gestión de Catálogos**: Personalización total de emociones (colores/descripción), actividades y etiquetas.

### 2. Gestión de Objetivos (NUEVO v0.2.0)
El sistema permite definir metas y realizar un seguimiento del progreso mediante dos lógicas de negocio diferenciadas:
* **Objetivos de Racha (Streaks)**: Para la formación de hábitos diarios (ej: "Meditar", "No fumar").
    * Contador visual de días consecutivos (Fuego 🔥).
    * Detección automática de ruptura de rachas.
* **Objetivos Acumulativos (Sum)**: Para metas cuantificables (ej: "Leer 30 min", "Caminar 10k pasos").
    * Barras de progreso dinámicas.
    * Periodos configurables: Diario, Semanal o Mensual.
* **Dashboard Inteligente**:
    * **Acciones Rápidas**: Botones de "Check" o "Sumar" directamente desde el listado.
    * **Historial Detallado**: Tabla completa de registros con opciones de edición y borrado manual para corrección de datos.

---

## 🛠️ Stack Tecnológico
* **Backend**: PHP 8.2 + Symfony 6.4
* **Base de Datos**: MySQL 8.0 (Dockerizada)
* **Frontend**: Twig + Bootstrap 5 + FontAwesome
* **Control de Versiones**: Git + GitFlow

---

## 🗺️ Hoja de Ruta (Roadmap)

- [x] **Fase 1: El Diario (v0.1.0)** - *Completado*
- [x] **Fase 2: Gestión de Objetivos (v0.2.0)** - *Completado*
    - Definición de metas (Rachas y Sumatorios).
    - Lógica de cálculo de progreso (Service Layer).
    - Dashboard interactivo y gestión de historial.
- [ ] **Fase 3: Estadísticas y Dashboard Avanzado** - *En Desarrollo*
    - Gráficas de estado de ánimo (Chart.js).
    - Correlación entre actividades y emociones.
    - Visualización de consistencia en objetivos a largo plazo.

---

## 👤 Autor
Desarrollado por **José Luis Lázaro**.
*Curso 2025/2026*
