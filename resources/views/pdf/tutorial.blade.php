@php
    $cp = $branding['primary'] ?? '#1e40af';
    $cs = $branding['secondary'] ?? '#6b7280';
    $ca = $branding['accent'] ?? '#16a34a';
@endphp

<x-pdf.layout 
    :title="$title" 
    :subtitle="$subtitle" 
    :code="$code" 
    :branding="$branding"
>
    <style>
        .markdown-body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 9.5pt;
            color: #1e293b;
            line-height: 1.6;
        }

        .markdown-body h1 {
            font-size: 15pt;
            color: {{ $cp }};
            padding-bottom: 8px;
            margin-top: 10px;
            margin-bottom: 14px;
            text-transform: uppercase;
        }

        .markdown-body h2 {
            font-size: 12pt;
            color: #0f172a;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 5px;
            margin-top: 22px;
            margin-bottom: 12px;
            font-weight: bold;
        }

        .markdown-body h3 {
            font-size: 10.5pt;
            color: #1e293b;
            margin-top: 16px;
            margin-bottom: 8px;
            font-weight: bold;
        }

        .markdown-body p {
            margin-top: 4px;
            margin-bottom: 10px;
        }

        .markdown-body ul, .markdown-body ol {
            margin-top: 6px;
            margin-bottom: 12px;
            padding-left: 18px;
        }

        .markdown-body li {
            margin-bottom: 5px;
        }

        .markdown-body blockquote {
            background-color: #f8fafc;
            border-left: 4px solid {{ $cp }};
            margin: 14px 0;
            padding: 8px 14px;
            color: #334155;
            font-style: italic;
            font-size: 9pt;
        }

        .markdown-body code {
            background-color: #f1f5f9;
            color: #0f172a;
            padding: 2px 5px;
            border-radius: 4px;
            font-family: monospace;
            font-size: 8.5pt;
            border: 1px solid #e2e8f0;
        }

        .markdown-body pre {
            background-color: #0f172a;
            color: #f8fafc;
            padding: 10px 12px;
            border-radius: 6px;
            overflow-x: auto;
            margin: 12px 0;
        }

        .markdown-body pre code {
            background-color: transparent;
            color: #38bdf8;
            border: none;
            padding: 0;
            font-size: 8pt;
        }

        .markdown-body table {
            width: 100%;
            border-collapse: collapse;
            margin: 14px 0;
        }

        .markdown-body th {
            background-color: {{ $cp }};
            color: white;
            padding: 6px 8px;
            font-size: 8.5pt;
            text-transform: uppercase;
            border: 1px solid {{ $cp }};
        }

        .markdown-body td {
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
            font-size: 8.5pt;
        }

        .markdown-body tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .markdown-body hr {
            border: none;
            border-top: 1px solid #e2e8f0;
            margin: 20px 0;
        }

        .markdown-body strong {
            color: #0f172a;
            font-weight: bold;
        }

        /* Reglas de salto de página para evitar que el texto o las listas se encimen en los márgenes */
        .markdown-body h1,
        .markdown-body h2,
        .markdown-body h3 {
            page-break-after: avoid;
        }

        .markdown-body li,
        .markdown-body blockquote,
        .markdown-body pre,
        .markdown-body tr {
            page-break-inside: avoid;
        }
    </style>

    <div class="markdown-body">
        @include($bladePartial)
    </div>
</x-pdf.layout>
