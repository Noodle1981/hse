---
description: Guía de Estética Visual Premium para Botones y Modales
---

# Estética Visual Premium XamaSafe

Para mantener la coherencia visual en todo el proyecto, utiliza las siguientes estructuras para botones y acciones de confirmación.

## 1. Estructura de Botón Estándar (Premium)

Todos los botones de acción deben seguir esta arquitectura de clases de Tailwind:

```html
<button class="px-3 py-1 flex items-center gap-1.5 bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 rounded-lg text-[10px] font-black transition-all hover:scale-105 shadow-sm uppercase tracking-widest">
    <!-- Heroicon (Outline) -->
    <svg class="w-3.5 h-3.5 text-ep-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M..." />
    </svg>
    Texto del Botón
</button>
```

### Variaciones de Color:
- **Principal (Naranja)**: `bg-ep-primary text-white border-none hover:brightness-110 shadow-ep-primary/20`
- **Peligro (Rojo)**: `bg-red-600 text-white border-none hover:bg-red-700 shadow-red-200`
- **Éxito (Verde)**: `bg-green-600 text-white border-none hover:bg-green-700 shadow-green-200`
- **Info (Indigo)**: `bg-indigo-50 text-indigo-700 border-indigo-100 hover:bg-indigo-100`

---

## 2. Implementación de Confirmación (Modal Premium)

Para acciones sensibles (eliminar, desactivar, etc.), no uses `wire:confirm`. Usa el componente `<x-modal-confirm />` disparando un evento de AlpineJS:

### El disparador (Trigger):
```html
<button @click="$dispatch('open-confirm-modal', { 
    id: {{ $item->id }}, 
    title: '¿Título de la Acción?', 
    message: 'Descripción clara de lo que va a suceder.',
    action: 'nombreMetodoLivewire',
    confirmLabel: 'Texto de Confirmar',
    confirmColor: 'bg-red-600 shadow-red-200' 
})" ...>
    <!-- Contenido del botón -->
</button>
```

### Requisito:
Asegúrate de incluir el componente al final de tu archivo Blade de Livewire:
```html
    <x-modal-confirm />
</div>
```

---

## 3. Estructura de Header Premium (Oscuro)

Para estandarizar la barra superior en todas las vistas, usa la sección `@section('header_custom')`:

```html
@section('header_custom')
    <header class="h-16 relative overflow-hidden bg-ep-dark flex items-center z-20 shadow-lg">
        <div class="relative w-full px-4 sm:px-6 lg:px-8 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <!-- Botón Menú Móvil -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden p-2 text-indigo-200 hover:text-white transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>

                <!-- Título -->
                <h1 class="text-lg md:text-xl font-medium text-white tracking-tight italic">
                    Título de la <span class="text-ep-primary">Sección</span>
                </h1>
            </div>

            <div class="flex items-center space-x-3">
                <!-- Botones de Acción (Estilo Pill) -->
                <a href="#" class="hidden xs:flex bg-white/10 text-white hover:bg-white/20 px-3 py-1 rounded-full text-[10px] font-black border border-white/10 transition-all items-center gap-1">
                    <svg ...>...</svg>
                    Añadir Nuevo
                </a>

                <!-- Perfil / Rol -->
                <a href="{{ route('profile') }}" class="flex items-center space-x-2 bg-white/5 backdrop-blur-sm p-1 pr-3 rounded-full border border-white/5 hover:bg-white/10 transition-all">
                    <div class="w-7 h-7 bg-ep-primary rounded-full flex items-center justify-center text-white text-[10px] font-black shadow-lg">
                        {{ substr(auth()->user()->nombres ?? 'U', 0, 1) }}
                    </div>
                    <div class="h-4 w-px bg-white/10 mx-1"></div>
                    <span class="text-[10px] font-bold text-indigo-100">{{ auth()->user()->nombres }}</span>
                </a>
            </div>
        </div>
    </header>
@endsection
```

---

## 4. Reglas de Oro Visuales
- **Sin Emojis**: Usa exclusivamente Heroicons (Outline) con `stroke-width="2"`.
- **Tipografía**: Usa siempre `font-black`, `uppercase` y `tracking-widest` para botones de acción.
- **Tamaño**: El estándar para acciones en tablas es `text-[10px]`.
- **Fondo**: Para componentes oscuros (headers, banners, modales), usa el color `#1E1E2D` (`bg-ep-dark`).

