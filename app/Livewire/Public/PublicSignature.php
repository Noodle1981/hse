<?php

namespace App\Livewire\Public;


use App\Models\GeneradorConstancia;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.guest')]
class PublicSignature extends Component
{
    public $token;
    public $certificate;
    public $code = '';
    public $signature = '';
    public $verified = false;
    public $error_message = '';

    public function mount($token)
    {
        $this->token = $token;
        $this->certificate = GeneradorConstancia::where('token_responsable', $token)->first();

        if (!$this->certificate) {
            $this->certificate = \App\Models\VisitCertificate::where('token_responsable', $token)->first();
        }

        if (!$this->certificate) {
            $this->error_message = 'El enlace no es válido o ha expirado.';
        } else {
            // Check for signature. Handle different column names if necessary 
            // (Both use firma_responsable? No, VisitCertificate uses responsible_signature in original code but updated migration uses... 
            // Wait, migration added signature_skip_reason etc but not responsible_signature because it already existed?
            // Let's check VisitInProgress: $data['responsible_signature'] = ...; 
            // And migration `2026_01_22_205642...` added responsible_signature.
            // GeneradorConstancia uses `firma_responsable`.
            // I need to normalize this or handle both.
            
            $hasSignature = false;
            if ($this->certificate instanceof \App\Models\VisitCertificate) {
                $hasSignature = !empty($this->certificate->responsible_signature);
            } else {
                $hasSignature = !empty($this->certificate->firma_responsable);
            }

            if ($hasSignature) {
                $this->error_message = 'Esta constancia ya ha sido firmada.';
            }
        }
    }

    public function verifyCode()
    {
        if ($this->code === $this->certificate->code) {
            $this->verified = true;
            $this->error_message = '';
        } else {
            $this->addError('code', 'El código de seguridad es incorrecto.');
        }
    }

    public function saveSignature()
    {
        $this->validate([
            'signature' => 'required',
        ], [
            'signature.required' => 'Por favor firme antes de confirmar.'
        ]);

        if ($this->certificate instanceof \App\Models\VisitCertificate) {
            $this->certificate->update([
                'responsible_signature' => $this->signature,
                'date_firma_responsable' => now(), // Migration added this to VisitCertificate too
                'status' => 'signed', // Or completed? Let's use signed
            ]);
        } else {
            $this->certificate->update([
                'firma_responsable' => $this->signature,
                'date_firma_responsable' => now(),
            ]);
        }

        session()->flash('success', 'La constancia ha sido firmada exitosamente. Gracias.');
    }

    public function render()
    {
        return view('livewire.public.public-signature');
    }
}
