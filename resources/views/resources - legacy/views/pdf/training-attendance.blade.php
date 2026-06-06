<x-pdf.layout :title="'Registro de Capacitación'" :code="'CAP-'.$training->id" :status="$training->status">
    
    {{-- Datos Generales --}}
    <x-pdf.card title="Información General">
        <table class="no-border">
            <tr>
                <td style="width: 50%;">
                    <strong>Sede:</strong> {{ $training->sede->nombre_sede ?? 'N/A' }}<br>
                    <strong>Empresa:</strong> {{ $training->empresa->nombre ?? 'N/A' }}
                </td>
                <td style="width: 50%; text-align: right;">
                    <strong>Responsable:</strong> {{ $training->responsibleUser->nombres ?? 'Sistema' }}
                </td>
            </tr>
        </table>
    </x-pdf.card>

    <div class="mb-2"></div>

    {{-- Componente Modular --}}
    <x-pdf.training-record :training="$training" />

</x-pdf.layout>
