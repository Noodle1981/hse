<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\SerializesModels;
use App\Models\GeneradorConstancia;
use App\Models\Sede;
use PDF;
use Illuminate\Support\Facades\Storage;

class PruebasAttach extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('carlos@grupoxamanen.com.ar', 'Carlos Taglio'),
            subject: 'Pruebas',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'welcome',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $constancia = GeneradorConstancia::where("id",3)->with('sede')->first();
        $sede = Sede::where("id",$constancia['sede_id'])->with('empresa')->first();
        $constancia['nombre_empresa']=$sede['empresa']->nombre;
        $constancia['razon_social']=$sede['empresa']->razon_social;
        $constancia['cuit']=$sede['empresa']->cuit;
        $pdf = PDF::loadView('constanciaPDF', ['data' => $constancia]);
        Storage::disk('local')->put('report.pdf', $pdf->stream());
        return [

            Attachment::fromData(fn () => $pdf->render(), 'Report.pdf')

                ->withMime('application/pdf'),

        ];
    }
}
