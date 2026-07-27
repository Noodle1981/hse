<?php

namespace App\Http\Controllers;

use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
                'description' => 'Guía completa para el alta y gestión de consultoras: creación de cuentas, configuración del plan contratado, identidad visual personalizada y manejo de solicitudes de recuperación de contraseña.',
                'code' => 'MAN-VEND-01',
                'pilar' => 'admin',
                'color' => 'amber',
                'icon' => 'briefcase',
                'roles' => ['super_admin', 'vendedor'],
                'blade' => 'pdf.tutorials.vendedores_manual',
            ],
            'gestores_manual' => [
                'key' => 'gestores_manual',
                'title' => 'Manual de Operación para Gestores',
                'subtitle' => 'Gestión de personal, firmas, matrices y plan anual',
                'description' => 'Todo lo necesario para administrar el equipo de trabajo: alta de técnicos y gestores de apoyo, aprobación de firmas digitales, asignación de sedes y configuración del Plan Anual de actividades.',
                'code' => 'MAN-GEST-01',
                'pilar' => 'gestor',
                'color' => 'orange',
                'icon' => 'shield',
                'roles' => ['super_admin', 'vendedor', 'gestor', 'Administrador', 'Socio'],
                'blade' => 'pdf.tutorials.gestores_manual',
            ],
            'tecnicos_manual' => [
                'key' => 'tecnicos_manual',
                'title' => 'Manual de Inspección y Campo para Técnicos',
                'subtitle' => 'Visitas GPS, checklists, evidencias y capacitaciones en vivo',
                'description' => 'Guía de operación de campo: cómo iniciar y cerrar visitas con validación GPS, completar checklists dinámicos, reportar hallazgos con evidencia fotográfica y gestionar capacitaciones con lista de asistentes.',
                'code' => 'MAN-TEC-01',
                'pilar' => 'tecnico',
                'color' => 'indigo',
                'icon' => 'clipboard',
                'roles' => ['super_admin', 'vendedor', 'gestor', 'Administrador', 'tecnico', 'Técnico'],
                'blade' => 'pdf.tutorials.tecnicos_manual',
            ],
            'procedimientos_gestores' => [
                'key' => 'procedimientos_gestores',
                'title' => 'Procedimientos Operativos de Gestión',
                'subtitle' => 'Informes Maestros, auditoría de inspecciones y cierre de hallazgos',
                'description' => 'Protocolos detallados para el ciclo de vida del Informe Maestro, el monitoreo del semáforo de inspecciones, la emisión de certificados PDF y los permisos de cierre de hallazgos en campo.',
                'code' => 'PROC-GEST-01',
                'pilar' => 'gestor',
                'color' => 'red',
                'icon' => 'document-text',
                'roles' => ['super_admin', 'vendedor', 'gestor', 'Administrador', 'Socio'],
                'blade' => 'pdf.tutorials.procedimientos_gestores',
            ],
            'procedimientos_tecnicos' => [
                'key' => 'procedimientos_tecnicos',
                'title' => 'Procedimientos de Trabajo en Terreno',
                'subtitle' => 'GPS, Roster 24/7, contingencias y redacción de informes',
                'description' => 'Protocolos de terreno: cómo funciona la protección inmutable de tiempos, operación en sedes de turno continuo, contingencias de señal GPS y buenas prácticas de captura fotográfica para hallazgos.',
                'code' => 'PROC-TEC-01',
                'pilar' => 'tecnico',
                'color' => 'emerald',
                'icon' => 'lightning-bolt',
                'roles' => ['super_admin', 'vendedor', 'gestor', 'Administrador', 'tecnico', 'Técnico'],
                'blade' => 'pdf.tutorials.procedimientos_tecnicos',
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

        // Formatear catálogo para Inertia: solo datos estáticos, sin leer archivos del disco.
        $formattedCatalog = array_map(function ($item) {
            $blade = $item['blade'] ?? '';
            $item['available'] = view()->exists($blade);
            unset($item['blade'], $item['roles']);

            return $item;
        }, array_values($catalog));


        return Inertia::render('HelpCenter/Index', [
            'catalog' => $formattedCatalog,
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
        $bladePartial = $tutorial['blade'];

        abort_unless(view()->exists($bladePartial), 404, 'El contenido de este manual aún no está disponible.');

        // Branding dinámico según la consultora del usuario o marca XamaSafe por defecto
        $branding = [
            'primary'   => '#F69239',
            'secondary' => '#6366f1',
            'accent'    => '#10b981',
            'name'      => 'XAMASAFE',
            'logo'      => public_path('images/logo.png'),
        ];

        if ($user->consultora) {
            $consultora = $user->consultora;
            $branding['primary']   = $consultora->color_primary ?? '#F69239';
            $branding['secondary'] = $consultora->color_secondary ?? '#6366f1';
            $branding['accent']    = $consultora->color_accent ?? '#10b981';
            $branding['name']      = strtoupper($consultora->nombre);

            if ($consultora->logo_path) {
                $branding['logo'] = storage_path('app/public/'.$consultora->logo_path);
            }
        }

        $pdf = Pdf::loadView('pdf.tutorial', [
            'title'        => $tutorial['title'],
            'subtitle'     => $tutorial['subtitle'],
            'code'         => $tutorial['code'],
            'bladePartial' => $bladePartial,
            'branding'     => $branding,
        ]);

        $pdf->setPaper('a4', 'portrait');

        $filename = Str::slug($tutorial['title']).'.pdf';

        return $pdf->download($filename);
    }
}
