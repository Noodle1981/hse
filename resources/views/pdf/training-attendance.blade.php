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

    {{-- Firmas de Cierre al Final --}}
    @if(isset($training->visit) && $training->visit)
        <div style="margin-top: 30px; page-break-inside: avoid;">
            <table style="width: 100%; border-collapse: collapse; border: none; margin-top: 20px;">
                <tr>
                    <td style="width: 50%; text-align: center; border: none; padding: 0 45px; vertical-align: bottom;">
                        @if($training->visit->signature)
                            <img src="{{ $training->visit->signature }}" style="max-height: 55px; margin-bottom: 5px; display: block; margin-left: auto; margin-right: auto;">
                        @else
                            <div style="height: 55px;"></div>
                        @endif
                        <div style="border-top: 1px solid #94a3b8; padding-top: 6px;">
                            <span style="font-weight: bold; font-size: 10px; color: #1e293b; display: block; text-transform: uppercase;">{{ $training->visit->responsible_name ?? 'Responsable de Sede' }}</span>
                            <span style="font-size: 8px; color: #64748b; text-transform: uppercase; font-weight: bold;">Por la Empresa Cliente</span>
                        </div>
                    </td>
                    <td style="width: 50%; text-align: center; border: none; padding: 0 45px; vertical-align: bottom;">
                        @if($training->visit->user && $training->visit->user->signature)
                            @php 
                                $sigPath = public_path('storage/' . $training->visit->user->signature); 
                            @endphp
                            @if(str_starts_with($training->visit->user->signature, 'data:image'))
                                <img src="{{ $training->visit->user->signature }}" style="max-height: 55px; margin-bottom: 5px; display: block; margin-left: auto; margin-right: auto;">
                            @elseif(file_exists($sigPath))
                                <img src="{{ $sigPath }}" style="max-height: 55px; margin-bottom: 5px; display: block; margin-left: auto; margin-right: auto;">
                            @else
                                <div style="height: 55px; color: #cbd5e1; font-size: 8px; line-height: 55px; text-align: center;">Firma no encontrada</div>
                            @endif
                        @else
                            <div style="height: 55px;"></div>
                        @endif
                        <div style="border-top: 1px solid #94a3b8; padding-top: 6px;">
                            <span style="font-weight: bold; font-size: 10px; color: #1e293b; display: block; text-transform: uppercase;">{{ $training->visit->user->nombres ?? 'Asesor Técnico' }} {{ $training->visit->user->apellidos ?? '' }}</span>
                            <span style="font-size: 8px; color: #64748b; text-transform: uppercase; font-weight: bold;">Asesor Profesional EP HyS</span>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    @endif

</x-pdf.layout>
