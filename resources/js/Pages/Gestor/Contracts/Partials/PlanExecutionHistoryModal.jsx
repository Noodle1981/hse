import React from 'react';

const parseSafeDate = (dateStr, includeTime = false) => {
    if (!dateStr) return 'N/A';
    const d = new Date(dateStr);
    if (isNaN(d.getTime())) return 'N/A';
    if (includeTime) {
        const hasTime = dateStr.includes('T') || dateStr.includes(' ');
        if (hasTime) {
            return d.toLocaleString('es-ES', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }
    }
    return d.toLocaleDateString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
};

export default function PlanExecutionHistoryModal({ selectedCellHistory, onClose }) {
    if (!selectedCellHistory) return null;

    return (
        <div className="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div
                className="fixed inset-0 bg-[#0a0a0f]/80 backdrop-blur-md animate-fade-in"
                onClick={onClose}
            ></div>

            <div className="relative w-full max-w-xl bg-[#141421]/90 backdrop-blur-2xl border border-emerald-500/20 rounded-[2.5rem] shadow-[0_25px_70px_rgba(0,0,0,0.8)] overflow-hidden animate-scale-up">
                <div className="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-transparent via-emerald-500 to-transparent opacity-50"></div>

                <div className="p-8">
                    <div className="flex justify-between items-start mb-6 border-b border-white/5 pb-4">
                        <div>
                            <span className="block text-[10px] font-black text-emerald-400 uppercase tracking-[0.2em] italic leading-none">Matriz de Cumplimiento</span>
                            <h3 className="text-xl font-black text-white uppercase italic tracking-tighter mt-1">
                                Historial de Ejecución - {selectedCellHistory.month}
                            </h3>
                            <p className="text-[10px] font-bold text-indigo-100/30 uppercase tracking-widest mt-1">
                                {selectedCellHistory.plan?.activity_name} • {selectedCellHistory.plan?.activity_code || 'SIN CÓDIGO'}
                            </p>
                        </div>
                        <button
                            onClick={onClose}
                            className="w-8 h-8 rounded-full bg-white/5 border border-white/10 flex items-center justify-center text-white/50 hover:text-white hover:bg-white/10 transition-all text-xs font-bold"
                        >
                            ✕
                        </button>
                    </div>

                    <div className="max-h-[350px] overflow-y-auto custom-scrollbar pr-2 space-y-4 mb-8">
                        {(!selectedCellHistory.data?.details?.history || selectedCellHistory.data.details.history.length === 0) ? (
                            <div className="py-8 text-center bg-white/5 rounded-2xl border border-white/5">
                                <p className="text-xs font-black text-indigo-100/25 uppercase italic tracking-widest">Sin registros históricos detallados</p>
                            </div>
                        ) : (
                            selectedCellHistory.data.details.history.map((item, idx) => {
                                const category = selectedCellHistory.plan?.category;

                                if (category === 'Inspección') {
                                    return (
                                        <div key={idx} className="p-4 bg-white/5 rounded-2xl border border-white/5 hover:border-white/10 transition-colors">
                                            <div className="flex flex-col gap-3">
                                                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-white/5 pb-2">
                                                    <span className="text-[10px] font-black text-indigo-300 uppercase tracking-widest flex items-center gap-1">
                                                        📅 {parseSafeDate(item.date, true)}
                                                    </span>
                                                    <div className="text-xs font-bold text-white flex items-center gap-1">
                                                        👷 {item.technician_name || 'Técnico no especificado'}
                                                    </div>
                                                </div>

                                                <div className="grid grid-cols-1 sm:grid-cols-3 gap-2 mt-1">
                                                    <div className="flex items-center justify-between px-3 py-2 rounded-xl text-indigo-400 bg-indigo-500/10 border border-indigo-500/20 text-[10px] font-bold">
                                                        <span className="opacity-80">Checklists Hechos:</span>
                                                        <span className="font-black text-xs">{item.total_checklists ?? 0}</span>
                                                    </div>
                                                    <div className="flex items-center justify-between px-3 py-2 rounded-xl text-emerald-400 bg-emerald-500/10 border border-emerald-500/30 text-[10px] font-bold shadow-[0_0_10px_rgba(16,185,129,0.05)]">
                                                        <span className="opacity-80">Exitosos:</span>
                                                        <span className="font-black text-xs">{item.passed ?? 0}</span>
                                                    </div>
                                                    {(item.failed ?? 0) > 0 ? (
                                                        <div className="flex items-center justify-between px-3 py-2 rounded-xl text-red-400 bg-red-500/10 border border-red-500/30 text-[10px] font-bold shadow-[0_0_10px_rgba(239,68,68,0.05)]">
                                                            <span className="opacity-80">Fallidos:</span>
                                                            <span className="font-black text-xs">{item.failed}</span>
                                                        </div>
                                                    ) : (
                                                        <div className="flex items-center justify-between px-3 py-2 rounded-xl text-white/20 bg-white/5 border border-white/5 text-[10px] font-bold">
                                                            <span className="opacity-60">Fallidos:</span>
                                                            <span className="font-black text-xs">{item.failed ?? 0}</span>
                                                        </div>
                                                    )}
                                                </div>
                                            </div>
                                        </div>
                                    );
                                }

                                if (category === 'Capacitación') {
                                    const attendeesCount = item.asistentes ?? item.attendees_count ?? 0;
                                    return (
                                        <div key={idx} className="p-4 bg-white/5 rounded-2xl border border-white/5 hover:border-white/10 transition-colors">
                                            <div className="flex flex-col gap-3">
                                                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-white/5 pb-2">
                                                    <span className="text-[10px] font-black text-indigo-300 uppercase tracking-widest flex items-center gap-1">
                                                        📅 {parseSafeDate(item.date, true)}
                                                    </span>
                                                    <div className="text-xs font-bold text-white flex items-center gap-1">
                                                        👷 {item.technician_name || 'Instructor no especificado'}
                                                    </div>
                                                </div>
                                                <div className="flex flex-wrap items-center gap-2 mt-1">
                                                    <span className="px-3 py-1.5 rounded-xl text-emerald-400 bg-emerald-500/10 border border-emerald-500/30 text-[10px] font-black uppercase tracking-wider italic flex items-center gap-1.5 shadow-[0_0_10px_rgba(16,185,129,0.05)]">
                                                        👥 Asistentes: {attendeesCount} capacitados
                                                    </span>
                                                    {(item.observaciones || item.observations) && (
                                                        <span className="px-3 py-1.5 rounded-xl text-indigo-300 bg-indigo-500/10 border border-indigo-500/20 text-[10px] font-semibold italic flex items-center gap-1">
                                                            💬 {`"${item.observaciones ?? item.observations}"`}
                                                        </span>
                                                    )}
                                                </div>
                                            </div>
                                        </div>
                                    );
                                }

                                const observations = item.observaciones ?? item.observations ?? 'Sin observaciones registradas';
                                return (
                                    <div key={idx} className="p-4 bg-white/5 rounded-2xl border border-white/5 hover:border-white/10 transition-colors">
                                        <div className="flex flex-col gap-3">
                                            <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-white/5 pb-2">
                                                <span className="text-[10px] font-black text-indigo-300 uppercase tracking-widest flex items-center gap-1">
                                                    📅 {parseSafeDate(item.date, true)}
                                                </span>
                                                <div className="text-xs font-bold text-white flex items-center gap-1">
                                                    👷 {item.technician_name || 'Autor no especificado'}
                                                </div>
                                            </div>
                                            <div className="mt-1 p-3 bg-white/5 rounded-xl border border-white/5">
                                                <span className="block text-[8px] font-black text-white/30 uppercase tracking-wider mb-1">Observaciones / Detalles:</span>
                                                <p className="text-[11px] font-semibold text-indigo-200 italic leading-relaxed">
                                                    {`"${observations}"`}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                );
                            })
                        )}
                    </div>

                    <div className="flex justify-end pt-4 border-t border-white/5">
                        <button
                            onClick={onClose}
                            className="w-full sm:w-auto h-12 px-8 bg-white/5 border border-white/10 rounded-2xl text-[10px] font-black text-white uppercase tracking-widest italic hover:bg-white/10 active:scale-95 transition-all"
                        >
                            Cerrar Panel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    );
}
