<?php

namespace App\Http\Controllers;

use App\Models\Consultora;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Inertia\Inertia;

class HelpCenterController extends Controller
{
    /**
     * Catálogo de tutoriales disponibles y sus metadatos.
     */
    protected function getCatalog(): array
    {
        return [
            'vendedores_manual' => [
                'key' => 'vendedores_manual',
                'title' => 'Manual de Vendedores y Súper Administración',
                'subtitle' => 'Onboarding, licencias, branding y reseteo de claves',
                'code' => 'MAN-VEND-01',
                'pilar' => 'admin', // admin, gestor, tecnico
                'color' => 'amber',
                'icon' => 'briefcase',
                'roles' => ['super_admin', 'vendedor'],
                'file' => base_path('doc/tutoriales/vendedores_manual.md'),
            ],
            'gestores_manual' => [
                'key' => 'gestores_manual',
                'title' => 'Manual de Operación para Gestores',
                'subtitle' => 'Gestión de personal, firmas, matrices y plan anual',
                'code' => 'MAN-GEST-01',
                'pilar' => 'gestor',
                'color' => 'orange',
                'icon' => 'shield',
                'roles' => ['super_admin', 'vendedor', 'gestor', 'Administrador', 'Socio'],
                'file' => base_path('doc/tutoriales/gestores_manual.md'),
            ],
            'tecnicos_manual' => [
                'key' => 'tecnicos_manual',
                'title' => 'Manual de Inspección y Campo para Técnicos',
                'subtitle' => 'Misiones, visitas GPS, wizard de checklists y capacitaciones',
                'code' => 'MAN-TEC-01',
                'pilar' => 'tecnico',
                'color' => 'indigo',
                'icon' => 'clipboard',
                'roles' => ['super_admin', 'vendedor', 'gestor', 'Administrador', 'tecnico', 'Técnico'],
                'file' => base_path('doc/tutoriales/tecnicos_manual.md'),
            ],
            'procedimientos_gestores' => [
                'key' => 'procedimientos_gestores',
                'title' => 'Procedimientos Operativos de Gestión',
                'subtitle' => 'Auditoría de informes maestros, SRE y hallazgos',
                'code' => 'PROC-GEST-01',
                'pilar' => 'gestor',
                'color' => 'red',
                'icon' => 'document-text',
                'roles' => ['super_admin', 'vendedor', 'gestor', 'Administrador', 'Socio'],
                'file' => base_path('doc/tutoriales/procedimientos_gestores.md'),
            ],
            'procedimientos_tecnicos' => [
                'key' => 'procedimientos_tecnicos',
                'title' => 'Procedimientos de Trabajo en Terreno',
                'subtitle' => 'Sincronización de tiempo, roster 24/7 y contingencias',
                'code' => 'PROC-TEC-01',
                'pilar' => 'tecnico',
                'color' => 'emerald',
                'icon' => 'lightning-bolt',
                'roles' => ['super_admin', 'vendedor', 'gestor', 'Administrador', 'tecnico', 'Técnico'],
                'file' => base_path('doc/tutoriales/procedimientos_tecnicos.md'),
            ],
        ];
    }

    /**
     * Filtrar catálogo según el rol del usuario autenticado.
     */
    protected function getAuthorizedCatalog(User $user): array
    {
        $all = $this->getCatalog();
        $filtered = [];

        foreach ($all as $key => $item) {
            if ($user->isSuperAdmin() || $user->isVendedor()) {
                $filtered[$key] = $item;

                continue;
            }

            foreach ($item['roles'] as $role) {
                if ($user->hasRole($role)) {
                    $filtered[$key] = $item;
                    break;
                }
            }
        }

        return $filtered;
    }

    /**
     * Centro de Ayuda e Index de Tutoriales
     */
    public function index(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        $catalog = $this->getAuthorizedCatalog($user);

        $selectedKey = $request->input('tutorial');
        if (! $selectedKey || ! isset($catalog[$selectedKey])) {
            $keys = array_keys($catalog);
            $selectedKey = reset($keys);
        }

        $selectedTutorial = null;
        $contentHtml = '';

        if ($selectedKey && isset($catalog[$selectedKey])) {
            $selectedTutorial = $catalog[$selectedKey];
            $filePath = $selectedTutorial['file'];

            if (File::exists($filePath)) {
                $markdown = File::get($filePath);
                $contentHtml = Str::markdown($markdown);
            } else {
                $contentHtml = '<div class="p-6 text-amber-400 font-bold italic">El contenido de este manual estará disponible en el próximo Sprint.</div>';
            }
        }

        // Formatear catálogo para Inertia descartando rutas locales de disco
        $formattedCatalog = array_map(function ($item) {
            unset($item['file']);
            $item['available'] = File::exists(base_path('doc/tutoriales/'.$item['key'].'.md'));

            return $item;
        }, array_values($catalog));

        return Inertia::render('HelpCenter/Index', [
            'catalog' => $formattedCatalog,
            'selectedKey' => $selectedKey,
            'selectedTutorial' => $selectedTutorial ? [
                'key' => $selectedTutorial['key'],
                'title' => $selectedTutorial['title'],
                'subtitle' => $selectedTutorial['subtitle'],
                'code' => $selectedTutorial['code'],
                'color' => $selectedTutorial['color'],
                'pilar' => $selectedTutorial['pilar'],
            ] : null,
            'contentHtml' => $contentHtml,
        ]);
    }

    /**
     * Descargar o visualizar el PDF del tutorial solicitado.
     */
    public function downloadPdf(string $key)
    {
        /** @var User $user */
        $user = Auth::user();
        $catalog = $this->getAuthorizedCatalog($user);

        abort_unless(isset($catalog[$key]), 404, 'Tutorial no encontrado o no autorizado.');

        $tutorial = $catalog[$key];
        $filePath = $tutorial['file'];

        abort_unless(File::exists($filePath), 404, 'El archivo del manual aún no ha sido redactado.');

        $markdown = File::get($filePath);
        $contentHtml = Str::markdown($markdown);

        // Branding dinámico según la consultora del usuario o marca XamaSafe por defecto
        $branding = [
            'primary' => '#F69239', // Naranja XamaSafe HUD
            'secondary' => '#6366f1',
            'accent' => '#10b981',
            'name' => 'XAMASAFE',
            'logo' => public_path('images/logo.png'),
        ];

        if ($user->consultora) {
            $consultora = $user->consultora;
            $branding['primary'] = $consultora->color_primary ?? '#F69239';
            $branding['secondary'] = $consultora->color_secondary ?? '#6366f1';
            $branding['accent'] = $consultora->color_accent ?? '#10b981';
            $branding['name'] = strtoupper($consultora->nombre);

            if ($consultora->logo_path) {
                $branding['logo'] = storage_path('app/public/'.$consultora->logo_path);
            }
        }

        $pdf = Pdf::loadView('pdf.tutorial', [
            'title' => $tutorial['title'],
            'subtitle' => $tutorial['subtitle'],
            'code' => $tutorial['code'],
            'content' => $contentHtml,
            'branding' => $branding,
        ]);

        $pdf->setPaper('a4', 'portrait');

        $filename = Str::slug($tutorial['title']).'.pdf';

        return $pdf->download($filename);
    }
}
