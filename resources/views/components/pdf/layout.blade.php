<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $title ?? 'Documento' }}</title>
    <style>
        @php
            $cp  = $branding['primary']   ?? '#1e40af';
            $cs  = $branding['secondary'] ?? '#6b7280';
            $ca  = $branding['accent']    ?? '#16a34a';
        @endphp

        /** 
            Global Styles for DomPDF 
            Colors are injected dynamically from Consultora branding.
        */
        @page {
            margin: 100px 25px 60px 25px; /* Top margin space for Header */
        }
        
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10pt;
            color: #333;
            line-height: 1.4;
        }

        /* Colors — Dynamic from Consultora */
        .text-primary   { color: {{ $cp }}; }
        .bg-primary     { background-color: {{ $cp }}; color: white; }
        .text-secondary { color: {{ $cs }}; }
        .bg-gray        { background-color: #f9fafb; }
        
        /* Header & Footer */
        header {
            position: fixed;
            top: -80px;
            left: 0px;
            right: 0px;
            height: 80px;
            border-bottom: 3px solid {{ $cp }};
            padding-bottom: 10px;
        }

        footer {
            position: fixed;
            bottom: -40px;
            left: 0px;
            right: 0px;
            height: 40px;
            font-size: 8pt;
            color: #6b7280;
            text-align: center;
            border-top: 1px solid #e5e7eb;
            padding-top: 5px;
        }

        /* Tables */
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { padding: 6px; border: 1px solid #e2e8f0; text-align: left; }
        th { background-color: #f1f5f9; font-weight: bold; color: {{ $cs }}; font-size: 9pt; }
        
        .no-border td { border: none; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .text-xs { font-size: 8pt; }
        .text-sm { font-size: 9pt; }
        
        /* Utilities */
        .page-break { page-break-after: always; }
        .mb-2 { margin-bottom: 0.5rem; }
        .mt-4 { margin-top: 1rem; }
        
        /* Layout Grid for Header Info */
        .header-table td { border: none; padding: 2px; }
        .logo { max-height: 60px; max-width: 200px; }
        
        /* Status Badges */
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 8pt;
            font-weight: bold;
            color: white;
            background-color: #64748b;
        }
        .badge-success { background-color: #16a34a; }
        .badge-warning { background-color: #ca8a04; }
        .badge-danger  { background-color: #dc2626; }

        /* Accent color utility */
        .text-accent { color: {{ $ca }}; }
        .border-primary { border-color: {{ $cp }}; }

    </style>
</head>
<body>

    <!-- Header Global -->
    <header>
        <table class="header-table">
            <tr>
                <td style="width: 30%;">
                    {{-- Logo dinámico de la Consultora --}}
                    @php $logoPath = $branding['logo'] ?? public_path('images/logo.png'); @endphp
                    @if(file_exists($logoPath))
                        <img src="{{ $logoPath }}" class="logo" alt="{{ $branding['name'] ?? 'Logo' }}" style="max-height: 50px; max-width: 200px;">
                    @else
                        <span style="font-size: 14pt; font-weight: bold; color: {{ $cp }};">{{ $branding['name'] ?? 'XAMASAFE' }}</span>
                    @endif
                </td>
                <td style="width: 40%; text-align: center;">
                    <h3 style="margin: 5px 0 0 0; text-transform: uppercase; color: #111827;">{{ $title ?? 'Reporte' }}</h3>
                    <div class="text-secondary text-xs">{{ $subtitle ?? '' }}</div>
                </td>
                <td style="width: 30%; text-align: right; font-size: 8pt;">
                    <div><strong>Fecha:</strong> {{ now()->format('d/m/Y H:i') }}</div>
                    <div><strong>Código:</strong> {{ $code ?? 'N/A' }}</div>
                    @if(isset($status))
                        <div style="margin-top: 2px;">
                            <span class="badge {{ match($status) { 
                                'cerrado', 'verificado', 'resuelto', 'completed' => 'badge-success', 
                                'en_proceso', 'pending' => 'badge-warning', 
                                'pendiente' => 'badge-danger', 
                                default => 'badge' 
                            } }}">
                                {{ ucfirst(str_replace('_', ' ', $status)) }}
                            </span>
                        </div>
                    @endif
                </td>
            </tr>
        </table>
    </header>

    <!-- Footer Global -->
    <footer>
        <table class="no-border" style="width: 100%; border: none; border-collapse: collapse;">
            <tr>
                <td style="width: 35%; text-align: left; vertical-align: middle; border: none; padding: 0;">
                    @php $logoPath = $branding['logo'] ?? public_path('images/logo.png'); @endphp
                    @if(file_exists($logoPath))
                        <img src="{{ $logoPath }}" style="max-height: 18px; max-width: 100px; vertical-align: middle; margin-right: 5px;">
                    @endif
                    <span style="font-weight: bold; font-size: 8pt; vertical-align: middle; color: #475569;">{{ strtoupper($branding['name'] ?? 'XAMASAFE') }}</span>
                </td>
                <td style="width: 30%; text-align: center; vertical-align: middle; border: none; padding: 0; line-height: 1.1;">
                    <span style="font-weight: bold; color: {{ $cp }}; font-size: 8.5pt;">XamaSafe</span><br>
                    <span style="font-size: 7.5pt; color: #94a3b8; font-weight: 500;">www.xamasafe.com</span>
                </td>
                <td style="width: 35%; text-align: right; vertical-align: middle; border: none; padding: 0;">
                    &nbsp;
                </td>
            </tr>
        </table>
    </footer>

    <!-- Main Content -->
    <main>
        {{ $slot }}
    </main>
    
    <!-- Script for Page Numbers (DomPDF) -->
    <script type="text/php">
        if (isset($pdf)) {
            $x = 490;
            $y = 820;
            $text = "Página {PAGE_NUM} de {PAGE_COUNT}";
            $font = null;
            $size = 8;
            $color = array(0.4, 0.45, 0.55); // #64748b
            $word_space = 0.0;
            $char_space = 0.0;
            $angle = 0.0;
            $pdf->page_text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);
        }
    </script>

</body>
</html>