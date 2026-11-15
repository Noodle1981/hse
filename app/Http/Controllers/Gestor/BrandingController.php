<?php

namespace App\Http\Controllers\Gestor;

use App\Http\Controllers\Controller;
use App\Services\GestorToolsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class BrandingController extends Controller
{
    public function __construct(private readonly GestorToolsService $toolsService) {}

    public function edit()
    {
        return Inertia::render('Gestor/Branding/Edit', [
            'consultora' => $this->toolsService->getBrandingData(Auth::user()),
        ]);
    }

    public function update(Request $request)
    {
        $consultora = Auth::user()->consultora;
        if (! $consultora) {
            return back()->with('error', 'No hay una consultora asociada a tu cuenta.');
        }

        $validated = $request->validate([
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:1000',
            'color_primary' => 'required|string|size:7',
            'color_secondary' => 'required|string|size:7',
            'color_accent' => 'required|string|size:7',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
        ]);

        $this->toolsService->updateBranding($consultora, $request, $validated);

        return back()->with('message', 'Identidad visual actualizada correctamente.');
    }
}
