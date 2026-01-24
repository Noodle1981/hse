@props(['title' => null, 'icon' => null])

<div class="card mb-4" style="border: 1px solid #e2e8f0; border-radius: 6px; overflow: hidden; page-break-inside: avoid; background-color: #ffffff;">
    @if($title)
        <div class="card-header" style="padding: 10px 15px; border-bottom: 2px solid #3b82f6; background-color: #f8fafc;">
            <h4 style="margin: 0; font-size: 11pt; text-transform: uppercase; color: #1e40af; font-family: sans-serif;">
                {{ $title }}
            </h4>
        </div>
    @endif
    
    {{-- Añadimos un padding superior más generoso aquí --}}
    <div class="card-body" style="padding: 20px 15px 15px 15px;">
        {{ $slot }}
    </div>
</div>