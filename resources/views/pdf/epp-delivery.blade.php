<x-pdf.layout :title="'Constancia de Entrega de EPP'" :code="'EPP-'.$delivery->id" status="completed">
    
    {{-- Datos de Contexto --}}
    <x-pdf.card title="Datos de la Entrega">
        <table class="no-border">
            <tr>
                <td style="width: 50%;">
                    <strong>Sede:</strong> {{ $delivery->employee->sede->nombre_sede ?? 'N/A' }}<br>
                    <strong>Empresa:</strong> {{ $delivery->employee->empresa->nombre ?? 'N/A' }}
                </td>
                <td style="width: 50%; text-align: right;">
                    <strong>ID Entrega:</strong> #{{ $delivery->id }}
                </td>
            </tr>
        </table>
    </x-pdf.card>

    <div class="mb-2"></div>

    {{-- Componente Modular --}}
    <x-pdf.epp-delivery :delivery="$delivery" />

</x-pdf.layout>
