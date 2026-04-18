<x-pdf.layout :title="'Reporte de Incidente / Accidente'" :code="'INC-'.$incident->id" :status="$incident->gravedad">
    
    {{-- Datos de Contexto --}}
    <x-pdf.card title="Contexto del Evento">
        <table class="no-border">
            <tr>
                <td style="width: 50%;">
                    <strong>Sede:</strong> {{ $incident->sede->nombre_sede ?? $incident->sede->nombre ?? 'N/A' }}<br>
                    <strong>Empresa:</strong> {{ $incident->sede->empresa->nombre ?? 'N/A' }}
                </td>
                <td style="width: 50%; text-align: right;">
                    <strong>Reportado por:</strong> {{ $incident->user->nombres ?? '' }} {{ $incident->user->apellidos ?? '' }}<br>
                    <strong>ID Global:</strong> #{{ $incident->id }}
                </td>
            </tr>
        </table>
    </x-pdf.card>

    <div class="mb-2"></div>

    {{-- Componente Modular de Detalle --}}
    <x-pdf.incidente-detail :incident="$incident" />

</x-pdf.layout>
