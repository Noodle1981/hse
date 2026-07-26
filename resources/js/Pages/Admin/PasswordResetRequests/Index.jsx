import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, usePage, router } from '@inertiajs/react';

export default function Index({ auth, requests }) {
    const { flash } = usePage().props;
    const [copied, setCopied] = useState(false);
    const [dismissedPassword, setDismissedPassword] = useState(false);

    const generatedPassword = !dismissedPassword && flash?.generated_password ? flash.generated_password : null;

    const copyToClipboard = (text) => {
        navigator.clipboard.writeText(text);
        setCopied(true);
        setTimeout(() => setCopied(false), 3000);
    };

    const handleResolve = (id) => {
        if (confirm('¿Estás seguro de resolver esta solicitud? Se generará una nueva contraseña temporal.')) {
            setDismissedPassword(false);
            router.post(route('admin.password-reset-requests.resolve', id));
        }
    };

    const handleReject = (id) => {
        if (confirm('¿Estás seguro de descartar esta solicitud de reseteo?')) {
            router.delete(route('admin.password-reset-requests.destroy', id));
        }
    };

    return (
        <AuthenticatedLayout
            user={auth.user}
            title="Solicitudes de Reseteo"
            header={
                <div className="flex flex-col">
                    <h1 className="text-xs font-black text-ep-primary uppercase tracking-[0.4em] italic leading-none mb-2">
                        Seguridad & Accesos
                    </h1>
                    <h2 className="text-xl font-black text-white uppercase italic tracking-tighter leading-none">
                        Solicitudes de <span className="text-amber-400">Reseteo de Contraseña</span>
                    </h2>
                </div>
            }
        >
            <Head title="Solicitudes de Reseteo" />

            <div className="py-12 bg-[#0f0f1a] min-h-screen text-white selection:bg-ep-primary/30">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

                    {/* Banner explicativo */}
                    <div className="bg-[#16162a]/40 backdrop-blur-xl border border-white/5 rounded-2xl p-6 flex items-start gap-4 shadow-xl">
                        <div className="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center text-amber-400 flex-shrink-0">
                            <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2">
                                <path strokeLinecap="round" strokeLinejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" />
                            </svg>
                        </div>
                        <div>
                            <h3 className="text-sm font-black text-white uppercase tracking-wider italic">
                                Solicitudes In-App de Blanqueo de Clave
                            </h3>
                            <p className="text-xs text-gray-400 mt-1 leading-relaxed">
                                Aquí se listan las solicitudes enviadas por usuarios que han olvidado su contraseña. Al presionar <strong>&quot;Generar Clave Temporal&quot;</strong>, se creará una clave segura de un solo uso que deberás comunicarle al usuario.
                            </p>
                        </div>
                    </div>

                    {/* Tabla de solicitudes */}
                    <div className="bg-[#16162a]/40 backdrop-blur-xl border border-white/5 rounded-[2.5rem] p-8 shadow-2xl overflow-hidden">
                        <div className="overflow-x-auto">
                            <table className="w-full text-left border-collapse">
                                <thead>
                                    <tr className="border-b border-white/10 text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic">
                                        <th className="py-4 px-4">Solicitante</th>
                                        <th className="py-4 px-4">Consultora</th>
                                        <th className="py-4 px-4">Fecha</th>
                                        <th className="py-4 px-4">Estado</th>
                                        <th className="py-4 px-4 text-right">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-white/5 text-xs">
                                    {requests.data.length === 0 ? (
                                        <tr>
                                            <td colSpan="5" className="py-8 text-center text-gray-500 font-medium">
                                                No tienes solicitudes de reseteo pendientes en este momento.
                                            </td>
                                        </tr>
                                    ) : (
                                        requests.data.map((r) => (
                                            <tr key={r.id} className="hover:bg-white/[0.02] transition-colors">
                                                <td className="py-4 px-4">
                                                    <div className="flex items-center gap-3">
                                                        <div className="w-9 h-9 rounded-lg bg-white/5 border border-white/10 flex items-center justify-center font-black text-white/70">
                                                            {r.user?.nombres?.charAt(0)}{r.user?.apellidos?.charAt(0)}
                                                        </div>
                                                        <div>
                                                            <div className="flex items-center gap-2">
                                                                <span className="font-black text-white uppercase italic tracking-wider">
                                                                    {r.user?.full_name}
                                                                </span>
                                                                {r.user?.is_primary && (
                                                                    <span className="text-[8px] font-black uppercase tracking-widest px-2 py-0.5 rounded bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                                                        Gestor Principal
                                                                    </span>
                                                                )}
                                                            </div>
                                                            <span className="text-[11px] text-gray-400 font-mono block mt-0.5">
                                                                {r.user?.email}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </td>

                                                <td className="py-4 px-4">
                                                    <span className="text-white/80 font-bold uppercase italic text-[11px]">
                                                        {r.user?.consultora?.nombre || 'Global / Súper Admin'}
                                                    </span>
                                                </td>

                                                <td className="py-4 px-4">
                                                    <div>
                                                        <span className="text-white/80 font-medium block">
                                                            {r.created_at_human}
                                                        </span>
                                                        <span className="text-[10px] text-gray-500 font-mono">
                                                            {r.created_at_formatted}
                                                        </span>
                                                    </div>
                                                </td>

                                                <td className="py-4 px-4">
                                                    <span className={`inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[9px] font-black uppercase tracking-wider border ${
                                                        r.status === 'pending'
                                                            ? 'bg-amber-500/10 text-amber-400 border-amber-500/30 animate-pulse'
                                                            : r.status === 'resolved'
                                                            ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30'
                                                            : 'bg-red-500/10 text-red-400 border-red-500/30'
                                                    }`}>
                                                        <span className={`w-1.5 h-1.5 rounded-full ${
                                                            r.status === 'pending' ? 'bg-amber-400' : r.status === 'resolved' ? 'bg-emerald-400' : 'bg-red-400'
                                                        }`} />
                                                        {r.status === 'pending' ? 'Pendiente' : r.status === 'resolved' ? 'Resuelto' : 'Descartado'}
                                                    </span>
                                                </td>

                                                <td className="py-4 px-4 text-right">
                                                    {r.status === 'pending' ? (
                                                        <div className="flex items-center justify-end gap-2">
                                                            <button
                                                                type="button"
                                                                onClick={() => handleResolve(r.id)}
                                                                className="px-3.5 py-2 bg-gradient-to-r from-ep-primary to-orange-600 hover:from-orange-600 hover:to-ep-primary text-white text-[10px] font-black uppercase italic tracking-widest rounded-xl transition-all shadow-md flex items-center gap-1"
                                                            >
                                                                Generar Clave Temporal
                                                            </button>
                                                            <button
                                                                type="button"
                                                                onClick={() => handleReject(r.id)}
                                                                className="px-3 py-2 bg-white/5 border border-white/10 hover:bg-white/10 text-white/50 hover:text-white text-[10px] font-black uppercase italic tracking-widest rounded-xl transition-all"
                                                            >
                                                                Descartar
                                                            </button>
                                                        </div>
                                                    ) : (
                                                        <span className="text-[10px] text-gray-500 italic">
                                                            {r.resolved_by ? `Por: ${r.resolved_by.name}` : 'Procesado'}
                                                        </span>
                                                    )}
                                                </td>
                                            </tr>
                                        ))
                                    )}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {/* MODAL CONTRASEÑA GENERADA */}
            {generatedPassword && (
                <div className="fixed inset-0 z-50 bg-black/80 backdrop-blur-md flex items-center justify-center p-4 animate-fade-in">
                    <div className="bg-[#16162a] border border-amber-500/40 rounded-3xl p-8 max-w-md w-full shadow-2xl space-y-6">
                        <div className="flex items-center gap-3 text-amber-400 border-b border-white/10 pb-4">
                            <div className="w-10 h-10 rounded-xl bg-amber-500/20 border border-amber-500/30 flex items-center justify-center">
                                <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" />
                                </svg>
                            </div>
                            <div>
                                <h3 className="text-base font-black uppercase italic tracking-tight text-white">
                                    Contraseña Temporal Generada
                                </h3>
                                <p className="text-[10px] font-bold text-amber-400 uppercase tracking-widest italic">
                                    Un solo uso · Cambio obligatorio
                                </p>
                            </div>
                        </div>

                        <p className="text-xs text-gray-300 leading-relaxed">
                            Se ha generado la siguiente clave temporal. Proporciónasela al usuario para que pueda ingresar y establecer su contraseña definitiva:
                        </p>

                        <div className="bg-black/60 border border-amber-500/30 rounded-2xl p-4 flex items-center justify-between gap-3">
                            <span className="font-mono text-lg font-black text-amber-400 tracking-wider select-all">
                                {generatedPassword}
                            </span>
                            <button
                                type="button"
                                onClick={() => copyToClipboard(generatedPassword)}
                                className="px-4 py-2 bg-amber-500 text-black hover:bg-amber-400 text-[10px] font-black uppercase italic tracking-widest rounded-xl transition-all flex items-center gap-1.5 shadow-md"
                            >
                                {copied ? '¡Copiada!' : 'Copiar'}
                            </button>
                        </div>

                        <div className="bg-red-500/10 border border-red-500/20 rounded-xl p-3 text-[10px] font-bold text-red-400 leading-normal flex items-start gap-2">
                            <span className="text-sm">⚠️</span>
                            <span>
                                <strong>IMPORTANTE:</strong> Esta contraseña NO se volverá a mostrar en pantalla por motivos de seguridad. Guárdala antes de cerrar.
                            </span>
                        </div>

                        <div className="pt-2">
                            <button
                                type="button"
                                onClick={() => setDismissedPassword(true)}
                                className="w-full py-3 bg-white/10 hover:bg-white/20 text-white text-[11px] font-black uppercase italic tracking-widest rounded-xl transition-all"
                            >
                                Entendido, ya la copié
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </AuthenticatedLayout>
    );
}
