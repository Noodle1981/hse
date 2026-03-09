# 🎨 Guía de Diseño Visual - EP-Back V2.0.0.0

> **Estado:** Sistema de Diseño Completo ✅  
> **Última actualización:** 30 Diciembre 2024

---

## 🎯 Filosofía de Diseño

El diseño visual de EP-Back se basa en:

- **Simplicidad:** Interfaces limpias y minimalistas
- **Funcionalidad:** Cada elemento tiene un propósito
- **Consistencia:** Patrones repetibles en todo el sistema
- **Accesibilidad:** Contraste y legibilidad adecuados
- **Profesionalismo:** Estética corporativa y seria

---

## 🎨 Paleta de Colores

### Colores Primarios

```css
/* Azul Principal */
--primary-50: #eff6ff;
--primary-100: #dbeafe;
--primary-200: #bfdbfe;
--primary-300: #93c5fd;
--primary-400: #60a5fa;
--primary-500: #3b82f6;  /* Principal */
--primary-600: #2563eb;
--primary-700: #1d4ed8;
--primary-800: #1e40af;
--primary-900: #1e3a8a;
```

### Colores de Estado

```css
/* Éxito */
--success-400: #4ade80;
--success-500: #22c55e;
--success-600: #16a34a;

/* Advertencia */
--warning-400: #facc15;
--warning-500: #eab308;
--warning-600: #ca8a04;

/* Error */
--error-400: #f87171;
--error-500: #ef4444;
--error-600: #dc2626;

/* Info */
--info-400: #38bdf8;
--info-500: #0ea5e9;
--info-600: #0284c7;
```

### Colores Semánticos

```css
/* Criticidad */
--criticidad-bajo: #22c55e;      /* Verde */
--criticidad-medio: #eab308;     /* Amarillo */
--criticidad-alto: #f97316;      /* Naranja */
--criticidad-critico: #ef4444;   /* Rojo */

/* Estados de Hallazgos */
--estado-pendiente: #eab308;     /* Amarillo */
--estado-proceso: #3b82f6;       /* Azul */
--estado-resuelto: #22c55e;      /* Verde */
--estado-cerrado: #6b7280;       /* Gris */
```

### Colores Neutros (Grises)

```css
--gray-50: #f9fafb;
--gray-100: #f3f4f6;
--gray-200: #e5e7eb;
--gray-300: #d1d5db;
--gray-400: #9ca3af;
--gray-500: #6b7280;
--gray-600: #4b5563;
--gray-700: #374151;
--gray-800: #1f2937;
--gray-900: #111827;
```

---

## 📐 Tipografía

### Fuentes

```css
/* Fuente Principal */
font-family: 'Inter', ui-sans-serif, system-ui, -apple-system, sans-serif;

/* Fuente Monoespaciada */
font-family: ui-monospace, SFMono-Regular, 'SF Mono', Menlo, Consolas, monospace;
```

### Escalas de Tamaño

```css
/* Textos */
.text-xs { font-size: 0.75rem; }    /* 12px */
.text-sm { font-size: 0.875rem; }   /* 14px */
.text-base { font-size: 1rem; }     /* 16px */
.text-lg { font-size: 1.125rem; }   /* 18px */
.text-xl { font-size: 1.25rem; }    /* 20px */
.text-2xl { font-size: 1.5rem; }    /* 24px */
.text-3xl { font-size: 1.875rem; }  /* 30px */
.text-4xl { font-size: 2.25rem; }   /* 36px */
```

### Pesos de Fuente

```css
.font-normal { font-weight: 400; }     /* Normal */
.font-medium { font-weight: 500; }     /* Medio */
.font-semibold { font-weight: 600; }   /* Semi-negrita */
.font-bold { font-weight: 700; }       /* Negrita */
```

---

## 🎭 Componentes UI

### Botones

```html
<!-- Botón Primario -->
<button class="px-4 py-2 bg-primary-600 text-white rounded-md 
               hover:bg-primary-700 focus:ring-2 focus:ring-primary-500">
    Guardar
</button>

<!-- Botón Secundario -->
<button class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md 
               hover:bg-gray-300 focus:ring-2 focus:ring-gray-400">
    Cancelar
</button>

<!-- Botón Peligro -->
<button class="px-4 py-2 bg-red-600 text-white rounded-md 
               hover:bg-red-700 focus:ring-2 focus:ring-red-500">
    Eliminar
</button>

<!-- Botón Deshabilitado -->
<button class="px-4 py-2 bg-gray-300 text-gray-500 rounded-md cursor-not-allowed" 
        disabled>
    Guardar
</button>
```

### Badges (Insignias)

```html
<!-- Badge de Estado -->
<span class="px-2 py-1 text-xs font-semibold rounded-full
             bg-green-100 text-green-800">
    Activo
</span>

<!-- Badge de Criticidad -->
<span class="px-2 py-1 text-xs font-semibold rounded-full
             bg-red-100 text-red-800">
    Crítico
</span>

<!-- Badge Numérico -->
<span class="px-2 py-1 text-xs font-semibold rounded-full
             bg-blue-100 text-blue-800">
    12
</span>
```

### Cards (Tarjetas)

```html
<!-- Card Básica -->
<div class="bg-white rounded-lg shadow-md p-6 border border-gray-200">
    <h3 class="text-lg font-semibold mb-2">Título</h3>
    <p class="text-gray-600">Contenido de la tarjeta</p>
</div>

<!-- Card con Header -->
<div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold">Título</h3>
    </div>
    <div class="p-6">
        <p class="text-gray-600">Contenido</p>
    </div>
</div>
```

### Inputs (Campos de Entrada)

```html
<!-- Input de Texto -->
<input type="text" 
       class="w-full px-3 py-2 border border-gray-300 rounded-md 
              focus:ring-2 focus:ring-blue-500 focus:border-transparent"
       placeholder="Ingrese el valor">

<!-- Input con Error -->
<input type="text" 
       class="w-full px-3 py-2 border-2 border-red-500 rounded-md 
              focus:ring-2 focus:ring-red-500"
       placeholder="Campo con error">
<p class="mt-1 text-sm text-red-600">Este campo es obligatorio</p>

<!-- Select -->
<select class="w-full px-3 py-2 border border-gray-300 rounded-md 
               focus:ring-2 focus:ring-blue-500">
    <option>Seleccione una opción</option>
    <option>Opción 1</option>
    <option>Opción 2</option>
</select>
```

### Tablas

```html
<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium 
                           text-gray-500 uppercase tracking-wider">
                    Nombre
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium 
                           text-gray-500 uppercase tracking-wider">
                    Estado
                </th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    Juan Pérez
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 py-1 text-xs rounded-full 
                                 bg-green-100 text-green-800">
                        Activo
                    </span>
                </td>
            </tr>
        </tbody>
    </table>
</div>
```

### Modales

```html
<!-- Modal Overlay -->
<div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <!-- Modal Content -->
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold">Título del Modal</h3>
        </div>
        <!-- Body -->
        <div class="px-6 py-4">
            <p class="text-gray-600">Contenido del modal</p>
        </div>
        <!-- Footer -->
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 
                    flex justify-end space-x-2">
            <button class="px-4 py-2 bg-gray-200 rounded-md">Cancelar</button>
            <button class="px-4 py-2 bg-blue-600 text-white rounded-md">Confirmar</button>
        </div>
    </div>
</div>
```

---

## 🎨 Iconografía

### Sistema de Iconos

**Librería:** Heroicons (Tailwind UI)

```html
<!-- Icon Solid -->
<svg class="w-5 h-5 text-gray-500" fill="currentColor">
    <!-- SVG path -->
</svg>

<!-- Icon Outline -->
<svg class="w-5 h-5 text-gray-500" stroke="currentColor" fill="none">
    <!-- SVG path -->
</svg>
```

### Iconos Comunes

```
✓ Checkmark - Completado
✗ X - Cancelar/Cerrar
✏️ Pencil - Editar
🗑️ Trash - Eliminar
👁️ Eye - Ver
📄 Document - Documento
📊 Chart - Gráfico
⚙️ Gear - Configuración
👤 User - Usuario
🏢 Building - Empresa
📍 Pin - Ubicación
📋 Clipboard - Checklist
🎯 Target - Objetivo
⚠️ Warning - Advertencia
```

---

## 📱 Responsive Design

### Breakpoints

```css
/* Mobile First Approach */
/* Default: Mobile (< 640px) */

/* Small devices (sm: ≥640px) */
@media (min-width: 640px) { }

/* Medium devices (md: ≥768px) */
@media (min-width: 768px) { }

/* Large devices (lg: ≥1024px) */
@media (min-width: 1024px) { }

/* Extra large devices (xl: ≥1280px) */
@media (min-width: 1280px) { }

/* 2XL devices (2xl: ≥1536px) */
@media (min-width: 1536px) { }
```

### Clases Responsive

```html
<!-- Texto responsive -->
<h1 class="text-2xl md:text-3xl lg:text-4xl">
    Título Responsive
</h1>

<!-- Grid responsive -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    <!-- Items -->
</div>

<!-- Ocultar en móvil -->
<div class="hidden md:block">
    Solo visible en desktop
</div>

<!-- Ocultar en desktop -->
<div class="block md:hidden">
    Solo visible en móvil
</div>
```

---

## 🎭 Animaciones y Transiciones

### Transiciones CSS

```css
/* Transición suave */
.transition-all { transition: all 150ms ease-in-out; }

/* Transición de colores */
.transition-colors { transition: color, background-color 150ms ease-in-out; }

/* Transición de opacidad */
.transition-opacity { transition: opacity 300ms ease-in-out; }
```

### Animaciones con Alpine.js

```html
<!-- Fade In -->
<div x-show="open" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100">
    Contenido
</div>

<!-- Slide Down -->
<div x-show="open"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform -translate-y-2"
     x-transition:enter-end="opacity-100 transform translate-y-0">
    Contenido
</div>
```

---

## 🎯 Estados Visuales

### Loading States

```html
<!-- Spinner -->
<div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>

<!-- Skeleton Loading -->
<div class="animate-pulse">
    <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
    <div class="h-4 bg-gray-200 rounded w-1/2"></div>
</div>

<!-- Button Loading -->
<button class="px-4 py-2 bg-blue-600 text-white rounded-md flex items-center" 
        disabled>
    <svg class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
        <!-- Spinner SVG -->
    </svg>
    Procesando...
</button>
```

### Estados de Hover

```html
<!-- Card con hover -->
<div class="bg-white rounded-lg shadow-md p-6 
            hover:shadow-lg transition-shadow cursor-pointer">
    Contenido
</div>

<!-- Botón con hover -->
<button class="px-4 py-2 bg-blue-600 text-white rounded-md 
               hover:bg-blue-700 active:bg-blue-800 
               transform hover:scale-105 transition-all">
    Clickeame
</button>
```

### Estados de Enfoque

```html
<!-- Input con focus -->
<input type="text" 
       class="border border-gray-300 rounded-md
              focus:ring-2 focus:ring-blue-500 focus:border-transparent
              outline-none">
```

---

## 📐 Espaciado y Layout

### Sistema de Espaciado

```css
/* Padding y Margin (basado en 0.25rem = 4px) */
.p-0 { padding: 0; }
.p-1 { padding: 0.25rem; }   /* 4px */
.p-2 { padding: 0.5rem; }    /* 8px */
.p-3 { padding: 0.75rem; }   /* 12px */
.p-4 { padding: 1rem; }      /* 16px */
.p-6 { padding: 1.5rem; }    /* 24px */
.p-8 { padding: 2rem; }      /* 32px */
```

### Grid System

```html
<!-- Grid de 2 columnas -->
<div class="grid grid-cols-2 gap-4">
    <div>Columna 1</div>
    <div>Columna 2</div>
</div>

<!-- Grid responsive -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
    <!-- 1 col mobile, 2 tablet, 4 desktop -->
</div>
```

---

## 🎨 Temas Oscuros (Futuro)

### Preparación para Dark Mode

```css
/* Variables CSS para temas */
:root {
    --bg-primary: #ffffff;
    --text-primary: #111827;
}

@media (prefers-color-scheme: dark) {
    :root {
        --bg-primary: #1f2937;
        --text-primary: #f9fafb;
    }
}
```

---

## ✅ Checklist de Diseño

### Al crear nuevos componentes:

- [ ] ¿Usa la paleta de colores definida?
- [ ] ¿Es responsive?
- [ ] ¿Tiene estados (hover, focus, disabled)?
- [ ] ¿Tiene feedback visual?
- [ ] ¿Es accesible (contraste, aria-labels)?
- [ ] ¿Sigue los patrones existentes?
- [ ] ¿Tiene animaciones suaves?
- [ ] ¿Funciona en móvil?

---

## 📚 Recursos

### Documentación
- [Tailwind CSS](https://tailwindcss.com/docs)
- [Heroicons](https://heroicons.com/)
- [Alpine.js](https://alpinejs.dev/)

### Herramientas
- [Tailwind UI](https://tailwindui.com/) - Componentes premium
- [Headless UI](https://headlessui.com/) - Componentes accesibles
- [Color Palette Generator](https://tailwindcss.com/docs/customizing-colors)

---

*EP-Back V2.0.0.0 | Sistema de Diseño Visual Consistente | Diciembre 2024*
