# ✍️ ROADMAP: Mejoras de Firmas Digitales

> **Nivel de dependencia:** 🏗️ FUNDACIÓN (NIVEL 0)  
> **Prerequisitos:** Ninguno (ES prerequisito de otros)  
> **Prioridad:** 🔴 ALTA  
> **Duración:** 2-3 semanas  
> **Sprint sugerido:** Sprint 1 (en paralelo con Performance)

---

## 📌 Propósito

Resolver el cuello de botella de legibilidad de firmas digitales mejorando calidad y formato, con un roadmap hacia certificación digital legal.

## 🎯 Objetivos por Fase

### Corto Plazo (Sprint 1)
- Firmas legibles en PDFs
- Formato SVG escalable
- Implementación rápida (1-2 días)

### Mediano Plazo (3 meses)
- Firma digital certificada (PKCS#7)
- Validación legal

### Largo Plazo (6+ meses)
- Firma biométrica avanzada
- Análisis forense

---

## 🛠️ Implementación

### Fase 1: SVG Alta Resolución (Semana 1)

#### 1.1 Frontend: Actualizar Signature Pad
```bash
npm install signature_pad
```

**`resources/js/components/firma-pad.js`:**
```javascript
import SignaturePad from 'signature_pad';

export function initFirmaPad(canvasId) {
    const canvas = document.getElementById(canvasId);
    const signaturePad = new SignaturePad(canvas, {
        backgroundColor: 'rgb(255, 255, 255)',
        penColor: 'rgb(0, 0, 139)', // Azul oscuro para mejor contraste
        minWidth: 1.5,
        maxWidth: 3,
        velocityFilterWeight: 0.7,
    });
    
    // Aumentar resolución 3x
    function resizeCanvas() {
        const ratio = Math.max(window.devicePixelRatio || 1, 3);
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        canvas.getContext('2d').scale(ratio, ratio);
        signaturePad.clear();
    }
    
    window.addEventListener('resize', resizeCanvas);
    resizeCanvas();
    
    return {
        clear: () => signaturePad.clear(),
        isEmpty: () => signaturePad.isEmpty(),
        
        // Exportar como SVG
        toSVG: () => signaturePad.toDataURL('image/svg+xml'),
        
        // Fallback PNG alta resolución
        toPNG: () => signaturePad.toDataURL('image/png'),
    };
}
```

#### 1.2 Componente Livewire
```blade
<!-- resources/views/livewire/firma-captura.blade.php -->
<div x-data="firmaComponent()" class="space-y-4">
    <div class="border-2 border-gray-300 rounded-lg">
        <canvas 
            id="firma-canvas" 
            class="w-full h-48"
            style="touch-action: none;"
        ></canvas>
    </div>
    
    <div class="flex gap-2">
        <button @click="limpiar()" class="btn btn-secondary">
            Limpiar
        </button>
        <button @click="guardar()" class="btn btn-primary">
            Guardar Firma
        </button>
    </div>
</div>

<script>
function firmaComponent() {
    return {
        pad: null,
        
        init() {
            this.pad = initFirmaPad('firma-canvas');
        },
        
        limpiar() {
            this.pad.clear();
        },
        
        async guardar() {
            if (this.pad.isEmpty()) {
                alert('Por favor firme primero');
                return;
            }
            
            const svgData = this.pad.toSVG();
            
            @this.call('guardarFirma', svgData);
        }
    }
}
</script>
```

#### 1.3 Backend: Guardar SVG
```php
// app/Http/Livewire/FirmaCaptura.php
public function guardarFirma($svgData)
{
    // Decodificar base64
    $svg = base64_decode(explode(',', $svgData)[1]);
    
    // Generar nombre único
    $filename = 'firma_' . auth()->id() . '_' . time() . '.svg';
    
    // Guardar en storage
    Storage::disk('local')->put("firmas/{$filename}", $svg);
    
    // Actualizar inspección
    $this->inspection->update([
        'firma_path' => $filename,
        'firmado_at' => now(),
    ]);
    
    $this->emit('firmaSaved');
}
```

#### 1.4 Incrustar en PDF
```php
// app/Services/PdfService.php
use Barryvdh\DomPDF\Facade\Pdf;

public function generarCertificado($inspection)
{
    $firmaPath = storage_path("app/firmas/{$inspection->firma_path}");
    
    // Convertir SVG a imagen inline para DOMPDF
    $svgContent = file_get_contents($firmaPath);
    $firmaBase64 = 'data:image/svg+xml;base64,' . base64_encode($svgContent);
    
    $pdf = Pdf::loadView('pdfs.certificado', [
        'inspection' => $inspection,
        'firma' => $firmaBase64,
    ]);
    
    return $pdf->download("certificado_{$inspection->id}.pdf");
}
```

**Vista PDF:**
```blade
<!-- resources/views/pdfs/certificado.blade.php -->
<div style="margin-top: 50px;">
    <p>Firma del técnico:</p>
    <img src="{{ $firma }}" style="width: 200px; height: auto;" />
    <p style="border-top: 1px solid #000; width: 200px; padding-top: 5px;">
        {{ $inspection->tecnico->name }}
    </p>
</div>
```

---

### Fase 2: Firma Digital Certificada (Semana 2-3 del Sprint Futuro)

#### 2.1 Biblioteca TCPDF
```bash
composer require tecnickcom/tcpdf
```

#### 2.2 Implementar Firma Digital
```php
// app/Services/DigitalSignatureService.php
namespace App\Services;

use TCPDF;

class DigitalSignatureService
{
    public function signPDF($pdfPath, $certificatePath, $password)
    {
        $pdf = new TCPDF();
        
        // Configurar certificado
        $certificate = 'file://' . $certificatePath;
        
        // Información de firma
        $info = [
            'Name' => auth()->user()->name,
            'Location' => 'Argentina',
            'Reason' => 'Inspección Técnica',
            'ContactInfo' => auth()->user()->email,
        ];
        
        // Aplicar firma digital
        $pdf->setSignature($certificate, $certificate, $password, '', 2, $info);
        
        // Cargar PDF existente
        $pageCount = $pdf->setSourceFile($pdfPath);
        
        for ($i = 1; $i <= $pageCount; $i++) {
            $tplId = $pdf->importPage($i);
            $pdf->AddPage();
            $pdf->useTemplate($tplId);
        }
        
        // Guardar PDF firmado
        $signedPath = str_replace('.pdf', '_firmado.pdf', $pdfPath);
        $pdf->Output($signedPath, 'F');
        
        return $signedPath;
    }
}
```

#### 2.3 Endpoint para firmar
```php
// routes/web.php
Route::post('/inspections/{inspection}/sign', [InspectionController::class, 'signDigitally'])
    ->middleware('auth');

// app/Http/Controllers/InspectionController.php
public function signDigitally(Request $request, Inspection $inspection)
{
    $request->validate([
        'certificate' => 'required|file|mimes:p12,pfx',
        'password' => 'required|string',
    ]);
    
    $certificatePath = $request->file('certificate')->store('certificates');
    
    $signatureService = new DigitalSignatureService();
    $signedPdf = $signatureService->signPDF(
        storage_path("app/pdfs/certificado_{$inspection->id}.pdf"),
        storage_path("app/{$certificatePath}"),
        $request->password
    );
    
    $inspection->update([
        'pdf_firmado_path' => $signedPdf,
        'firmado_digitalmente' => true,
    ]);
    
    return redirect()->back()->with('success', 'Certificado firmado digitalmente');
}
```

---

### Fase 3: Validación y Verificación (Semana 3)

#### 3.1 Endpoint de Verificación
```php
// routes/api.php
Route::get('/verify-signature/{inspection}', [SignatureController::class, 'verify']);

// app/Http/Controllers/SignatureController.php
public function verify(Inspection $inspection)
{
    if (!$inspection->firmado_digitalmente) {
        return response()->json(['valid' => false, 'message' => 'No firmado digitalmente']);
    }
    
    // Verificar con openssl
    $pdfPath = storage_path("app/{$inspection->pdf_firmado_path}");
    
    exec("openssl pkcs7 -inform DER -in {$pdfPath} -print_certs -text", $output, $return);
    
    return response()->json([
        'valid' => $return === 0,
        'signed_by' => $inspection->tecnico->name,
        'signed_at' => $inspection->firmado_at,
    ]);
}
```

---

## ✅ Criterios de Completitud

### Fase 1 (Sprint 1) - CRÍTICO
- [x] Signature Pad con SVG implementado
- [x] Resolución 3x en canvas
- [x] Firmas legibles en PDFs
- [x] Storage de firmas SVG
- [x] Testing: Firma visible en zoom 200%

### Fase 2 (Futuro) - OPCIONAL
- [ ] TCPDF instalado
- [ ] Firma digital PKCS#7 funcionando
- [ ] Certificados .p12 soportados
- [ ] Validación legal confirmada

---

## 🚫 Bloquea a

Este roadmap es FUNDACIÓN. Si no se completa Fase 1:
- ⚠️ Certificados poco profesionales
- ⚠️ Posibles rechazos legales

---

## 📊 Métricasde Progreso

```
Fase 1 (SVG):                  [ ░░░░░░░░░░ ] 0%
Fase 2 (Certificada):          [ FUTURO     ]
Fase 3 (Verificación):         [ FUTURO     ]

TOTAL (Sprint 1):              [ ░░░░░░░░░░ ] 0%
```

---

## 📝 Testing

### Caso 1: Firma manuscrita
1. Abrir wizard de inspección
2. Firmar en canvas
3. Guardar
4. Generar PDF
5. Ampliar PDF al 200%
6. **Verificar:** Firma nítida y legible

### Caso 2: Firma digital (Futuro)
1. Cargar certificado .p12
2. Firmar PDF
3. Usar validador online
4. **Verificar:** Firma válida y certificado confiable
