---
description: Guía de Estética Visual Premium DARK para XamaSafe
---

# Estética Visual Premium Dark XamaSafe (v2.0)

Esta guía define las leyes de diseño para la transición de XamaSafe a una interfaz de alta gama, técnica e inmersiva.

## 1. Fundamentos de Color
- **Fondo Raíz (Canvas)**: `#11111D` (Negro Profundo). Se aplica al `main` y `body`.
- **Contenedores Sólidos**: `#1E1E2D` (`bg-ep-dark`). Para tarjetas y paneles principales.
- **Módulos Glass (Cristal)**: `bg-white/5` con `backdrop-blur-xl` y `border-white/10`. Para filtros y elementos flotantes.
- **Acento Primario**: `ep-primary` (Naranja Técnico). Usado para acciones, títulos y resplandores.

---

## 2. Sistema de Iconografía "Technical Module"
Todos los iconos deben seguir este estándar para mantener la "pertenencia" de marca:
- **Set**: Heroicons v2 (Outline).
- **Grosor**: `stroke-width="2"` estrictamente.
- **Contenedor**: Cuadrado orgánico (`rounded-2xl`) con fondo `bg-white/5` y sombra interna (`shadow-inner`).
- **Comportamiento**: Rotación leve (`rotate-3`) y escalado al 110% en hover.

```html
<div class="h-12 w-12 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-ep-primary shadow-inner rotate-3 hover:rotate-0 transition-all duration-500">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
        <!-- Path de Heroicon -->
    </svg>
</div>
```

---

## 3. Tipografía Técnica
Para títulos y labels, busca siempre un aire de "Consola de Operaciones":
- **Títulos (H1, H2)**: `font-black text-white italic uppercase tracking-tight`.
- **Labels (Etiquetas)**: `text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic`.
- **Datos Relevantes (CID)**: `text-[11px] font-black text-indigo-100/50 uppercase tracking-widest italic`.

---

## 4. Botones de Acción "Tactical Pill"
Los botones deben sentirse como interruptores de una máquina de alta precisión:
- **Base**: `rounded-xl` o `rounded-2xl`.
- **Efecto Shimmer**: Usar degradados que se desplacen en hover.
- **Acción Principal**: 
```html
<button class="px-6 py-3 bg-ep-primary text-white text-[10px] font-black uppercase italic tracking-widest rounded-xl hover:brightness-110 active:scale-95 transition-all shadow-lg shadow-ep-primary/20 relative overflow-hidden group">
    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
    Texto de Acción
</button>
```

---

## 5. Tablas de Alta Fidelidad
- **Thead**: Fondo `black/20` con texto `ep-primary` itálico.
- **Tbody Rows**: Fondo base `bg-white/5` u opaco, con `hover:bg-white/[0.07]` y transición suave.
- **Separadores**: `border-white/5` (Casi invisibles pero presentes).

---

## 6. Canal de Éxito "Verde Neón"
Para elementos de éxito o completados en fondos oscuros:
- **Color**: `text-emerald-400` y `border-emerald-500/30`.
- **Efecto Glow**: Usar `shadow-[0_0_20px_rgba(16,185,129,0.2)]`.
- **Resplandor de Texto**: Añadir `filter: drop-shadow(0 0 8px rgba(16,185,129,0.4))` a los iconos.

---

## 7. Badges de Notificación (Contadores)
Los indicadores numéricos deben ser disruptivos y claros:
- **Forma**: `rounded-2xl` (no circulares perfectos, para seguir el aire técnico).
- **Tamaño**: Mínimo `h-10 w-10` con texto `text-xl font-black`.
- **Posicionamiento**: `absolute -top-3 -right-3` para que sobresalgan del contenedor.
- **IMPORTANTE**: El contenedor padre **NUNCA** debe tener `overflow-hidden` si lleva un badge.

---

## 8. Estados de Procesamiento (Loading)
Para evitar la incertidumbre durante procesos pesados (PDF, cálculos):
- **Layout**: Fondo `bg-[#11111D]` con un único módulo central glass.
- **Spinner Técnico**: No usar círculos simples. Usar un spinner con `border-t-ep-primary` y un punto central con `animate-ping`.
- **Barra de Progreso HUD**: Una barra de 2px de alto con animación de "escaneo" infinita.
- **Mensajería**: Usar verbos de acción técnica (e.g., "Sincronizando Nodos", "Renderizando Motor PDF").

```html
<!-- Estructura sugerida -->
<div class="loader-hud ease-linear rounded-full border-2 border-t-2 border-white/5 border-t-ep-primary h-12 w-12 transition-all"></div>
```

---

## Reglas de Oro
1. **Contraste Invertido en Firmas**: Imágenes de firmas oscuras deben tener filtro `brightness-0 invert opacity-90`.
2. **Resplandores (Glow)**: Usar `glow-text-red` o `glow-text-orange` (con text-shadow en CSS) para estados críticos.
3. **Inmersión Móvil**: Priorizar el uso de tarjetas (`cards`) modulares sobre tablas horizontales en pantallas pequeñas.
