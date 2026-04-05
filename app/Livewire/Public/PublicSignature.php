<?php

namespace App\Livewire\Public;

use App\Models\VisitCertificate;
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
        
        // Priorizar VisitCertificate
        $this->certificate = VisitCertificate::where('token_responsable', $token)->first();

        if (!$this->certificate) {
            $this->error_message = 'El enlace no es válido o ha expirado.';
        } else {
            if (!empty($this->certificate->responsible_signature)) {
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

        $this->certificate->update([
            'responsible_signature' => $this->signature,
            'date_firma_responsable' => now(),
            'status' => 'signed',
        ]);

        session()->flash('success', 'La constancia ha sido firmada exitosamente. Gracias.');
    }

    public function render()
    {
        return view('livewire.public.public-signature');
    }
}
