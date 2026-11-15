<?php

namespace App\Http\Controllers;

use App\Services\GestorToolsService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PublicSignatureController extends Controller
{
    public function __construct(private readonly GestorToolsService $toolsService) {}

    public function show(string $token)
    {
        $visit = $this->toolsService->findVisitByToken($token);
        $isExpired = $visit->token_expires_at && $visit->token_expires_at->isPast();

        return Inertia::render('Public/SignCertificate', [
            'visit' => $visit,
            'token' => $token,
            'isUsed' => (bool) $visit->token_used,
            'isExpired' => (bool) $isExpired,
        ]);
    }

    public function store(Request $request, string $token)
    {
        $visit = $this->toolsService->findVisitByToken($token);

        if ($visit->token_used) {
            return back()->with('error', 'Este enlace de firma ya fue utilizado.');
        }

        if ($visit->token_expires_at && $visit->token_expires_at->isPast()) {
            return back()->with('error', 'El enlace de firma ha expirado.');
        }

        $validated = $request->validate([
            'responsible_name' => 'required|string|max:255',
            'signature' => 'required|string',
        ]);

        $this->toolsService->signPublicCertificate($visit, $validated['responsible_name'], $validated['signature']);

        return back()->with('message', 'Constancia de visita firmada exitosamente.');
    }
}
