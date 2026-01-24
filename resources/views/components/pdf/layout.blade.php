<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $title ?? 'Documento' }}</title>
    <style>
        /** 
            Global Styles for DomPDF 
            Define corporate colors and typography here.
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

        /* Colors - Matching Platform (Tailwind Blue-800 based) */
        .text-primary { color: #1e40af; } /* Blue-800 */
        .bg-primary { background-color: #1e40af; color: white; }
        .text-secondary { color: #6b7280; } /* Gray-500 */
        .bg-gray { background-color: #f9fafb; } /* Gray-50 */
        
        /* Header & Footer */
        header {
            position: fixed;
            top: -80px;
            left: 0px;
            right: 0px;
            height: 80px;
            border-bottom: 2px solid #1e40af;
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
        th { background-color: #f1f5f9; font-weight: bold; color: #1e3a8a; font-size: 9pt; }
        
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
        .badge-danger { background-color: #dc2626; }

    </style>
</head>
<body>

    <!-- Header Global -->
    <header>
        <table class="header-table">
            <tr>
                <td style="width: 30%;">
                    {{-- Dynamic Logo --}}
                    <img src="{{ public_path('images/logo.png') }}" class="logo" alt="XAMASAFE" style="max-height: 50px; max-width: 200px;">
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
                            <span class="badge {{ match($status) { 'completed' => 'badge-success', 'pending' => 'badge-warning', default => 'badge' } }}">
                                {{ ucfirst($status) }}
                            </span>
                        </div>
                    @endif
                </td>
            </tr>
        </table>
    </header>

    <!-- Footer Global -->
    <footer>
        <table class="no-border">
            <tr>
                <td style="width: 33%; text-align: left;">
                    GENERADO AUTOMÁTICAMENTE POR XAMASAFE
                </td>
                <td style="width: 33%; text-align: center;">
                    Página <span class="page-number"></span>
                </td>
                <td style="width: 33%; text-align: right;">
                    {{ $footerText ?? 'Confidencial' }}
                </td>
            </tr>
        </table>
    </footer>

    <!-- Main Content -->
    <main>
        {{ $slot }}
    </main>
    
    <!-- Script for Page Numbers (DomPDF specific PHP inline used sometimes, but CSS counters act weird. text/php script is robust) -->
    <script type="text/php">
        if (isset($pdf)) {
            $x = 280;
            $y = 820;
            $text = "Página {PAGE_NUM} de {PAGE_COUNT}";
            $font = null;
            $size = 8;
            $color = array(0.4, 0.45, 0.55); // #64748b
            $word_space = 0.0;  //  default
            $char_space = 0.0;  //  default
            $angle = 0.0;   //  default
            $pdf->page_text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);
        }
    </script>

</body>
</html>