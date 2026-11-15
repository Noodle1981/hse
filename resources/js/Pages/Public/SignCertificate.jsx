import React, { useEffect, useRef, useState } from 'react';
import { Head, useForm, usePage } from '@inertiajs/react';
import SignaturePad from 'signature_pad';

export default function SignCertificate({ visit, token, isUsed, isExpired }) {
    const { flash } = usePage().props;
    const canvasRef = useRef(null);
    const padRef = useRef(null);
    const [hasDrawn, setHasDrawn] = useState(false);

    const { data, setData, post, processing, errors } = useForm({
        responsible_name: visit?.responsible_name || '',
        signature: '',
    });

    useEffect(() => {
        if (!canvasRef.current || isUsed || isExpired) return;
        const canvas = canvasRef.current;
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        canvas.getContext('2d').scale(ratio, ratio);

        const pad = new SignaturePad(canvas, { penColor: '#0f172a' });
        pad.addEventListener('endStroke', () => {
            setHasDrawn(!pad.isEmpty());
            setData('signature', pad.toDataURL('image/png'));
        });
        padRef.current = pad;

        return () => pad.off();
    }, [isUsed, isExpired]);

    const clearSignature = () => {
        if (padRef.current) {
            padRef.current.clear();
            setHasDrawn(false);
            setData('signature', '');
        }
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        if (padRef.current && !padRef.current.isEmpty()) {
            setData('signature', padRef.current.toDataURL('image/png'));
        }
        post(`/constancia/firmar/${token}`);
    };

    return (
        <div className="min-h-screen bg-slate-950 text-slate-100 flex items-center justify-center p-4">
            <Head title="Firma Pública de Constancia" />
            <div className="w-full max-w-xl bg-slate-900 border border-slate-800 rounded-2xl p-6 shadow-2xl space-y-5">
                <div className="border-b border-slate-800 pb-4">
                    <span className="text-xs font-bold uppercase tracking-wider text-orange-400">
                        Constancia Digital de Visita HSE
                    </span>
                    <h1 className="text-xl font-bold text-white mt-1">
                        {visit?.sede?.empresa?.nombre} — {visit?.sede?.nombre_sede}
                    </h1>
                    <p className="text-sm text-slate-400 mt-1">
                        Código: <span className="font-mono text-slate-200">{visit?.code}</span> · Técnico:{' '}
                        {visit?.user?.nombres} {visit?.user?.apellidos}
                    </p>
                </div>

                {flash?.message && (
                    <div className="p-3 rounded-xl bg-emerald-500/15 border border-emerald-500/40 text-emerald-300 text-sm">
                        {flash.message}
                    </div>
                )}

                {flash?.error && (
                    <div className="p-3 rounded-xl bg-red-500/15 border border-red-500/40 text-red-300 text-sm">
                        {flash.error}
                    </div>
                )}

                {isUsed ? (
                    <div className="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-sm">
                        Esta constancia ya fue firmada por <strong>{visit?.responsible_name}</strong>.
                    </div>
                ) : isExpired ? (
                    <div className="p-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-300 text-sm">
                        El enlace de firma ha expirado. Solicite al técnico un nuevo enlace.
                    </div>
                ) : (
                    <form onSubmit={handleSubmit} className="space-y-4">
                        <div>
                            <label className="block text-sm font-medium text-slate-300 mb-1">
                                Nombre y Apellido del Responsable
                            </label>
                            <input
                                type="text"
                                value={data.responsible_name}
                                onChange={(e) => setData('responsible_name', e.target.value)}
                                className="w-full rounded-xl bg-slate-950 border border-slate-700 px-3 py-2 text-sm text-white"
                                required
                            />
                            {errors.responsible_name && (
                                <p className="text-xs text-red-400 mt-1">{errors.responsible_name}</p>
                            )}
                        </div>

                        <div>
                            <div className="flex items-center justify-between mb-1">
                                <label className="text-sm font-medium text-slate-300">Firma Manuscrita</label>
                                <button
                                    type="button"
                                    onClick={clearSignature}
                                    className="text-xs text-orange-400 hover:text-orange-300"
                                >
                                    Limpiar trazo
                                </button>
                            </div>
                            <canvas
                                ref={canvasRef}
                                className="w-full h-44 bg-white rounded-xl border border-slate-700 touch-none"
                            />
                            {errors.signature && <p className="text-xs text-red-400 mt-1">{errors.signature}</p>}
                        </div>

                        <button
                            type="submit"
                            disabled={processing || !hasDrawn}
                            className="w-full py-2.5 rounded-xl bg-orange-600 hover:bg-orange-500 disabled:opacity-50 font-semibold text-sm text-white transition"
                        >
                            {processing ? 'Registrando firma...' : 'Confirmar y Firmar Constancia'}
                        </button>
                    </form>
                )}
            </div>
        </div>
    );
}
