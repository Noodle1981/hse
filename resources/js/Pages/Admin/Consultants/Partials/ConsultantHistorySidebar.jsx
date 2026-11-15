import React from 'react';

const FIELD_LABELS = {
    estado_suscripcion: 'Estado Suscripción',
    max_tecnicos: 'Límite Técnicos',
    max_gestores: 'Límite Gestores',
    max_empresas: 'Límite Empresas',
};

function formatAuditVal(val) {
    if (val === 'activa') return 'Activa';
    if (val === 'morosa') return 'Morosa';
    if (val === 'baja') return 'Baja';
    return val;
}

export default function ConsultantHistorySidebar({ activeTab, setActiveTab, auditLogs, suscripcionLogs }) {
    return (
        <div className="bg-[#16162a]/40 backdrop-blur-xl border border-white/5 rounded-[2.5rem] p-8 sm:p-10 shadow-2xl space-y-6">
            <div className="border-b border-white/5 pb-3">
                <h3 className="text-sm font-black text-ep-primary uppercase tracking-widest italic mb-4">
                    Historial & Trazabilidad
                </h3>
                <div className="flex gap-2 p-1 bg-black/25 rounded-xl border border-white/5">
                    <button
                        type="button"
                        onClick={() => setActiveTab('audit')}
                        className={`flex-1 py-2 text-[9px] font-black uppercase italic tracking-widest rounded-lg transition-all ${
                            activeTab === 'audit' ? 'bg-ep-primary text-white shadow-md' : 'text-white/40 hover:text-white/80'
                        }`}
                    >
                        Auditoría
                    </button>
                    <button
                        type="button"
                        onClick={() => setActiveTab('subscriptions')}
                        className={`flex-1 py-2 text-[9px] font-black uppercase italic tracking-widest rounded-lg transition-all ${
                            activeTab === 'subscriptions' ? 'bg-ep-primary text-white shadow-md' : 'text-white/40 hover:text-white/80'
                        }`}
                    >
                        Suscripciones
                    </button>
                </div>
            </div>

            {activeTab === 'audit' && (
                <div className="relative border-l border-white/10 pl-6 ml-2 space-y-8 max-h-[500px] overflow-y-auto pr-2 scrollbar-thin scrollbar-thumb-white/10 scrollbar-track-transparent">
                    {auditLogs.length === 0 ? (
                        <p className="text-[10px] font-bold text-white/30 uppercase tracking-widest italic py-4">Sin registros de auditoría.</p>
                    ) : (
                        auditLogs.map((log, index) => {
                            const isCreate = log.action === 'create';
                            return (
                                <div key={log.id || index} className="relative">
                                    <span className={`absolute -left-[31px] top-1.5 flex h-4.5 w-4.5 items-center justify-center rounded-full border ${
                                        isCreate ? 'bg-indigo-500/10 border-indigo-500 text-indigo-400' : 'bg-amber-500/10 border-amber-500 text-amber-400'
                                    }`}>
                                        <span className={`h-1.5 w-1.5 rounded-full ${isCreate ? 'bg-indigo-500' : 'bg-amber-500'}`} />
                                    </span>

                                    <div className="space-y-1.5">
                                        <div className="flex items-center justify-between gap-2">
                                            <span className={`text-[9px] font-black uppercase tracking-[0.2em] italic px-2 py-0.5 rounded ${
                                                isCreate ? 'bg-indigo-500/10 text-indigo-400 border border-indigo-500/20' : 'bg-amber-500/10 text-amber-400 border border-amber-500/20'
                                            }`}>
                                                {isCreate ? 'Alta de Consultora' : 'Modificación de Accesos'}
                                            </span>
                                            <span className="text-[8px] font-black text-white/30 uppercase tracking-wider">{log.created_at_formatted}</span>
                                        </div>

                                        <div className="text-[10px] font-bold text-white/60 italic">
                                            {log.user ? (
                                                <span>
                                                    Por: {log.user.name} <span className="text-[8px] uppercase tracking-wider text-indigo-300">({log.user.rol === 'super_admin' ? 'Súper Admin' : 'Vendedor'})</span>
                                                </span>
                                            ) : (
                                                <span>Por: Sistema</span>
                                            )}
                                        </div>

                                        <div className="bg-black/30 border border-white/5 rounded-xl p-3 space-y-2 mt-2">
                                            {log.payload && Object.keys(log.payload).length > 0 ? (
                                                Object.entries(log.payload).map(([field, diff]) => (
                                                    <div key={field} className="flex flex-col gap-0.5 text-[9px] border-b border-white/5 last:border-b-0 pb-1.5 last:pb-0">
                                                        <span className="text-white/40 font-bold uppercase tracking-wider text-[8px]">{FIELD_LABELS[field] || field}</span>
                                                        <div className="flex items-center font-mono">
                                                            {isCreate ? (
                                                                <span className="text-emerald-400 font-black">{formatAuditVal(diff.new)}</span>
                                                            ) : (
                                                                <div className="flex items-center gap-1.5">
                                                                    <span className="text-red-400/60 line-through">{formatAuditVal(diff.old)}</span>
                                                                    <span className="text-white/30">➔</span>
                                                                    <span className="text-emerald-400 font-black">{formatAuditVal(diff.new)}</span>
                                                                </div>
                                                            )}
                                                        </div>
                                                    </div>
                                                ))
                                            ) : (
                                                <span className="text-white/30 italic text-[9px]">Sin cambios en accesos.</span>
                                            )}
                                        </div>
                                    </div>
                                </div>
                            );
                        })
                    )}
                </div>
            )}

            {activeTab === 'subscriptions' && (
                <div className="relative border-l border-white/10 pl-6 ml-2 space-y-8 max-h-[500px] overflow-y-auto pr-2 scrollbar-thin scrollbar-thumb-white/10 scrollbar-track-transparent">
                    {suscripcionLogs.map((log, index) => {
                        const isActiva = log.estado_nuevo === 'activa';
                        const isMorosa = log.estado_nuevo === 'morosa';
                        return (
                            <div key={log.id || index} className="relative">
                                <span className={`absolute -left-[31px] top-1.5 flex h-4.5 w-4.5 items-center justify-center rounded-full border ${
                                    isActiva ? 'bg-emerald-500/10 border-emerald-500 text-emerald-400' :
                                    isMorosa ? 'bg-amber-500/10 border-amber-500 text-amber-400 animate-pulse' :
                                    'bg-red-500/10 border-red-500 text-red-400'
                                }`}>
                                    <span className={`h-1.5 w-1.5 rounded-full ${isActiva ? 'bg-emerald-500' : isMorosa ? 'bg-amber-500' : 'bg-red-500'}`} />
                                </span>

                                <div className="space-y-1.5">
                                    <div className="flex items-center justify-between gap-2">
                                        <span className={`text-[9px] font-black uppercase tracking-[0.2em] italic px-2 py-0.5 rounded ${
                                            isActiva ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' :
                                            isMorosa ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' :
                                            'bg-red-500/10 text-red-400 border border-red-500/20'
                                        }`}>
                                            {isActiva ? 'Suscripción Activa' : isMorosa ? 'Estado Moroso' : 'Baja del Servicio'}
                                        </span>
                                        {log.is_current && (
                                            <span className="bg-indigo-500/15 border border-indigo-500/30 text-indigo-300 text-[7px] font-black uppercase tracking-[0.2em] italic px-2 py-0.5 rounded-full">
                                                Actual
                                            </span>
                                        )}
                                    </div>

                                    <div className="text-[10px] font-bold text-white/50 italic leading-snug">
                                        {log.duration && (
                                            <span className="text-white font-black block not-italic">
                                                {log.is_current ? 'Activo hace: ' : 'Duración: '}
                                                <span className="text-indigo-300 uppercase tracking-wide text-[9px]">{log.duration}</span>
                                            </span>
                                        )}
                                        <div className="mt-1">Iniciado: {log.created_at_formatted}</div>
                                        {log.user && (
                                            <div className="text-white/30 text-[8px] uppercase tracking-wider mt-0.5">
                                                Por: {log.user.name} ({log.user.email})
                                            </div>
                                        )}
                                    </div>
                                </div>
                            </div>
                        );
                    })}
                </div>
            )}
        </div>
    );
}
