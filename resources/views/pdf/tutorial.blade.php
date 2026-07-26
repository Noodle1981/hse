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
            line-height: 1.5;
        }

        .markdown-body h1 {
            font-size: 16pt;
            color: {{ $cp }};
            border-bottom: 2px solid {{ $cp }};
            padding-bottom: 6px;
            margin-top: 15px;
            margin-bottom: 12px;
            text-transform: uppercase;
        }

        .markdown-body h2 {
            font-size: 13pt;
            color: #0f172a;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 4px;
            margin-top: 18px;
            margin-bottom: 10px;
        }

        .markdown-body h3 {
            font-size: 11pt;
            color: {{ $cp }};
            margin-top: 14px;
            margin-bottom: 6px;
        }

        .markdown-body p {
            margin-bottom: 10px;
        }

        .markdown-body ul, .markdown-body ol {
            margin-top: 5px;
            margin-bottom: 12px;
            padding-left: 20px;
        }

        .markdown-body li {
            margin-bottom: 4px;
        }

        .markdown-body blockquote {
            background-color: #f8fafc;
            border-left: 4px solid {{ $cp }};
            margin: 12px 0;
            padding: 8px 12px;
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
            margin: 18px 0;
        }
    </style>

    <div class="markdown-body">
        {!! $content !!}
    </div>
</x-pdf.layout>
