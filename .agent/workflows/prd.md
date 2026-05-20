---
description: Documento de Requisitos de Producto (PRD) para XamaSafe
---

# 📋 Documento de Requisitos de Producto (PRD) — XamaSafe

## 1. Control del Documento
* **Nombre del Producto:** XamaSafe
* **Versión del PRD:** v2.1 (Stack React + Inertia.js)
* **Estado:** Activo / En Desarrollo
* **Última Actualización:** 20 Mayo 2026
* **Tecnología Core:** Laravel 12 + Inertia.js (React 19) + Tailwind CSS v4 + SQLite/MySQL + Ziggy JS

---

## 2. Visión General del Producto
XamaSafe es un sistema SaaS integral de Higiene y Seguridad (HyS), Normas de Calidad (ISO, BPA) y Auditorías Ambientales. Está diseñado para simplificar la creación, gestión y ejecución de formularios dinámicos y checklists en terreno por parte de técnicos de campo, consolidando toda la información en una consola de mando táctica para administradores y consultores.

### 🌟 El Factor Diferencial: Tactical Premium Dark (HUD)
XamaSafe destaca por una interfaz inmersiva y de alto impacto visual. Emplea una estética **HUD Futurista / Dashboard Táctico** con tonos oscuros profundos (`#11111D`), bordes con efectos de cristalizado (*glassmorphism*), y colores de acento neón brillantes (Naranja, Verde Neón y Violeta) que transmiten precisión y tecnología.

---

## 3. Personas y Roles de Usuario

| Rol de Usuario | Descripción | Objetivos Clave |
|----------------|-------------|-----------------|
| **Gestor (Administrador / Consultor)** | Administra el sistema desde su laptop/escritorio. | Configurar checklists, supervisar el rendimiento de los técnicos en tiempo real, gestionar clientes/sedes, emitir reportes anuales de contratos y descargar PDFs consolidados. |
| **Técnico (Inspector de Terreno)** | Opera en campo utilizando dispositivos móviles o tablets. | Registrar visitas, certificar presencia mediante coordenadas GPS, completar checklists interactivos con firmas digitales y capturar hallazgos con registro fotográfico. |
| **Cliente Corporativo (Lector)** | Visualiza e inspecciona el estado de sus sedes. | Consultar las constancias de visitas, ver estadísticas de cumplimiento y descargar certificados oficiales de seguridad y capacitaciones. |

---

## 4. Módulos y Requisitos Funcionales

### 4.1. Core Engine: Checklists Dinámicos
* **Generación de Plantillas:** Permite a los Gestores definir plantillas de checklists con categorías y tipos de preguntas variados (texto, numérico, opción múltiple, booleano, adjunto de fotos).
* **Ejecución y Registro:** Los técnicos inician inspecciones basadas en estas plantillas. Cada respuesta queda vinculada de manera íntegra a una inspección específica (`answers` vinculadas a `inspections` y `questions`).

### 4.2. Módulo de Campo: Visitas con Validación GPS (Proof of Presence)
* **Geolocalización Inmediata:** Captura obligatoria de latitud/longitud del dispositivo móvil del técnico mediante la API nativa de geolocalización al realizar:
  1. **Check-in (Entrada):** Apertura de la visita.
  2. **Check-out (Salida):** Finalización de la visita.
* **Cálculo de Distancia (Fórmula de Haversine):** Compara el pin del técnico con las coordenadas registradas de la Sede en la base de datos.
  * Si la distancia cae dentro del `radio_tolerancia_metros` (ej. 500m), la visita es marcada como **Verificada por GPS** (sello verde brillante).
  * Si está fuera de rango, exige al técnico una justificación textual que queda registrada en la auditoría.
* **Auditoría de Tiempos:** Mide de forma exacta el tiempo en tránsito vs. el tiempo efectivo de trabajo físico dentro de la sede del cliente.

### 4.3. Módulo de Hallazgos e Incidentes
* **Reporte Rápido:** Durante una inspección, el técnico puede levantar un "Hallazgo" adjuntando una descripción del incidente, criticidad (Baja, Media, Alta, Crítica) y evidencia fotográfica.
* **Plan de Acción:** Los gestores y clientes pueden visualizar los hallazgos abiertos, definir planes de acción correctiva y marcar la resolución del problema con una foto de "después".

### 4.4. Módulo de Formación (Capacitaciones)
* **Registro Técnico:** Planificación y ejecución de capacitaciones grupales o individuales en campo.
* **Firma Digital Integradora:** Registro directo en pantalla de los asistentes y capacitadores utilizando un panel digital (`signature_pad` integrado en React).
* **Certificación:** Generación de un PDF de capacitación con validez legal conteniendo las firmas digitalizadas.

### 4.5. Módulo de Contratos y PPD (Programa de Prevención de Daños)
* **Control de Ciclos:** Seguimiento de contratos anuales activos por empresa.
* **Alertas de Vencimiento:** Avisos visuales y de control de visitas requeridas en base al acuerdo de servicio (ej. frecuencia mensual, trimestral).

---

## 5. Requisitos de Experiencia de Usuario y Diseño (Aesthetics)
Para garantizar la consistencia en el stack **React + Inertia + Tailwind CSS v4**, se deben respetar las siguientes directrices estéticas:

1. **Inmersión Oscura:** El lienzo principal debe usar un fondo `#11111D` sólido con tarjetas y contenedores en `#1E1E2D` (con gradientes internos sutiles).
2. **Micro-interacciones en Botones (Tactical Pill):** Los botones deben contar con efectos hover dinámicos, shimmer degradado y escala reducida en clic (`active:scale-95`).
3. **Firma Digital Clara:** Las firmas digitalizadas por el técnico deben renderizarse con contraste invertido (`brightness-0 invert opacity-90`) en la interfaz oscura para simular un HUD técnico sin perder legibilidad al imprimir reportes.
4. **Cargas Transparentes (Loading States):** Spinner técnicos con animación de "escaneo de datos" HUD infinita al procesar documentos o cargar recursos en segundo plano.

---

## 6. Requisitos Técnicos y Arquitectura

* **Monolito Híbrido:** Desarrollo ágil con Laravel en el backend e Inertia.js como puente para servir una SPA rápida basada en React, sin la sobrecarga de una API REST separada.
* **Seguridad / Multi-Tenancy:** Aislamiento estricto de los datos. Un usuario corporativo solo puede visualizar información relacionada a sus propias Empresas y Sedes. Las políticas de Laravel (`Policies`) auditan cada petición de Inertia.
* **Motor de PDF:** Laravel-DomPDF. Los archivos PDF generados se adaptan para impresión física, cambiando automáticamente la paleta oscura HUD a una paleta clara optimizada para tinta, manteniendo el branding y la visualización de las firmas.

---

*Documento creado: 20 Mayo 2026*  
*Propiedad del Proyecto XamaSafe*  
*Bajo especificaciones del Stack Inertia-React en la rama `feature-inertia-react`*  
