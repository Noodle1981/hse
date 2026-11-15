import React from 'react';
import { Link } from '@inertiajs/react';

export default function CompletedProtocolsCard({ completedTasks }) {
    return (
        <div className="bg-[#16162a]/40 backdrop-blur-md rounded-[2.5rem] border border-white/5 overflow-hidden shadow-2xl relative">
            <div className="px-8 py-6 bg-black/20 border-b border-white/5 flex justify-between items-center relative z-10">
                <div className="flex items-center gap-4">
                    <div className="h-10 w-10 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 flex items-center justify-center rotate-3 shadow-inner">
                        <svg className="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <h2 className="font-black text-white uppercase tracking-[0.2em] text-[11px] italic">Protocolos <span className="text-emerald-500">Hechos</span></h2>
                </div>
                <span className="text-[9px] font-black uppercase tracking-[0.2em] text-white/40 italic">
                    Historial: {completedTasks.length} {completedTasks.length === 1 ? 'tarea' : 'tareas'}
                </span>
            </div>

            <div className="p-8 space-y-5 relative z-10">
                {completedTasks.length === 0 ? (
                    <div className="text-center py-16 border-2 border-dashed border-white/5 rounded-[2rem] bg-black/10">
                        <p className="text-[10px] font-black text-white/20 uppercase tracking-[0.4em] italic leading-relaxed max-w-xs mx-auto">
                            Aún no se han registrado tareas en esta visita. Presiona &quot;Nueva Tarea&quot; arriba para iniciar.
                        </p>
                    </div>
                ) : (
                    <div className="space-y-4">
                        {completedTasks.map((task) => {
                            const timeStr = task.date ? task.date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: false }) : '';
                            return (
                                <div
                                    key={task.id}
                                    className={`relative border rounded-3xl p-6 flex flex-col sm:flex-row gap-4 justify-between sm:items-center group/done transition-all duration-300 ${
                                        task.type === 'inspection'
                                            ? (task.failures.failed > 0
                                                ? 'bg-rose-500/[0.02] border-rose-500/10 hover:border-rose-500/30'
                                                : 'bg-orange-500/[0.02] border-orange-500/10 hover:border-orange-500/30')
                                            : 'bg-indigo-500/[0.02] border-indigo-500/10 hover:border-indigo-500/30'
                                    }`}
                                >
                                    <div className="flex items-center gap-5">
                                        <div className={`h-12 w-12 rounded-2xl border flex items-center justify-center shrink-0 shadow-inner transition-transform duration-500 group-hover/done:scale-110 ${
                                            task.type === 'inspection'
                                                ? (task.failures.failed > 0
                                                    ? 'bg-rose-500/10 border-rose-500/20 text-rose-400'
                                                    : 'bg-orange-500/10 border-orange-500/20 text-orange-400')
                                                : 'bg-indigo-500/10 border-indigo-500/20 text-indigo-400'
                                        }`}>
                                            {task.type === 'inspection' ? (
                                                task.failures.failed > 0 ? (
                                                    <svg className="w-5.5 h-5.5 animate-pulse text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="3"><path strokeLinecap="round" strokeLinejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                                ) : (
                                                    <svg className="w-5.5 h-5.5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="3"><path strokeLinecap="round" strokeLinejoin="round" d="M5 13l4 4L19 7" /></svg>
                                                )
                                            ) : (
                                                <svg className="w-5.5 h-5.5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2"><path strokeLinecap="round" strokeLinejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                                            )}
                                        </div>

                                        <div className="space-y-1">
                                            <div className="flex flex-wrap items-center gap-2">
                                                <h4 className="text-sm font-black text-white uppercase italic tracking-tight leading-none">{task.title}</h4>
                                                <span className="px-2 py-0.5 rounded-lg bg-white/5 border border-white/10 text-[9px] font-black text-indigo-100/40 uppercase tracking-widest italic">{timeStr} HS</span>
                                            </div>

                                            <div className="flex flex-wrap items-center gap-2">
                                                <span className={`text-[9px] font-black uppercase tracking-widest italic ${
                                                    task.type === 'inspection'
                                                        ? (task.failures.failed > 0 ? 'text-rose-500/60' : 'text-orange-500/60')
                                                        : 'text-indigo-500/60'
                                                }`}>
                                                    {task.type === 'inspection' ? 'INSPECCIÓN COMPLETADA' : 'CAPACITACIÓN COMPLETADA'}
                                                </span>
                                                {task.type === 'inspection' && task.failures && (
                                                    <>
                                                        <span className="h-1 w-1 rounded-full bg-white/20"></span>
                                                        <span className={`px-2 py-0.5 rounded-md text-[8px] font-black uppercase tracking-widest italic ${
                                                            task.failures.failed > 0
                                                                ? 'bg-rose-500/10 border border-rose-500/20 text-rose-500 shadow-[0_0_15px_rgba(244,63,94,0.1)]'
                                                                : 'bg-orange-500/10 border border-orange-500/20 text-orange-400 shadow-[0_0_15px_rgba(249,115,22,0.1)]'
                                                        }`}>
                                                            {task.failures.failed > 0 ? `${task.failures.failed} Fallas` : 'Sin Fallas'}
                                                        </span>
                                                    </>
                                                )}

                                                {task.type === 'inspection' && task.detail?.assignment?.inspection?.asset && (
                                                    <>
                                                        <span className="h-1 w-1 rounded-full bg-white/20"></span>
                                                        <span className={`px-2 py-0.5 rounded-md text-[8px] font-black uppercase tracking-widest italic ${
                                                            task.detail.assignment.inspection.asset.status === 'operativo' ? 'bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 shadow-[0_0_15px_rgba(16,185,129,0.1)]' :
                                                            task.detail.assignment.inspection.asset.status === 'alerta' ? 'bg-amber-500/10 border border-amber-500/20 text-amber-400 shadow-[0_0_15px_rgba(245,158,11,0.1)]' :
                                                            'bg-red-500/10 border border-red-500/20 text-red-400 shadow-[0_0_15px_rgba(239,68,68,0.1)]'
                                                        }`}>
                                                            {task.detail.assignment.inspection.asset.status === 'operativo' ? 'OPERATIVO' :
                                                             task.detail.assignment.inspection.asset.status === 'alerta' ? 'EN ALERTA' : 'FUERA DE SERVICIO'}
                                                        </span>
                                                        <span className="h-1 w-1 rounded-full bg-white/20"></span>
                                                        <span className="px-2 py-0.5 rounded-md text-[8px] font-black uppercase tracking-widest italic bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 shadow-[0_0_15px_rgba(99,102,241,0.1)] flex items-center gap-1">
                                                            <span>📦</span> {task.detail.assignment.inspection.asset.identifier} ({task.detail.assignment.inspection.asset.name})
                                                        </span>
                                                    </>
                                                )}
                                            </div>

                                            <div className="text-[10px] font-bold text-white/40 uppercase tracking-widest italic flex items-center gap-1.5 pt-0.5">
                                                <svg className="w-3.5 h-3.5 opacity-40 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                                {task.type === 'inspection' ? (
                                                    <span>
                                                        Firmado por: <span className="text-white font-black">{task.detail.assignment?.inspection?.supervisor_name || 'Responsable de Sede'}</span>
                                                    </span>
                                                ) : (
                                                    <span>
                                                        Firmado por: <span className="text-white font-black">
                                                            {(() => {
                                                                const adhocAttendees = task.detail.attendees;
                                                                const plannedAttendances = task.detail.training?.attendances;
                                                                let namesList = [];
                                                                if (Array.isArray(adhocAttendees) && adhocAttendees.length > 0) {
                                                                    namesList = adhocAttendees.map(a => a.name);
                                                                } else if (Array.isArray(plannedAttendances) && plannedAttendances.length > 0) {
                                                                    namesList = plannedAttendances.map(a => a.employee_name);
                                                                }
                                                                if (namesList.length === 0) return 'Participantes';
                                                                if (namesList.length <= 2) return namesList.join(', ');
                                                                return `${namesList[0]}, ${namesList[1]} y ${namesList.length - 2} más`;
                                                            })()}
                                                        </span>
                                                    </span>
                                                )}
                                            </div>
                                        </div>
                                    </div>

                                    {task.type === 'inspection' && task.detail?.assignment?.inspection?.id && (
                                        <Link
                                            href={route('tecnico.inspections.show', task.detail.assignment.inspection.id)}
                                            className={`sm:self-center shrink-0 h-9 px-4 bg-white/5 hover:bg-white/10 text-white rounded-xl text-[9px] font-black uppercase tracking-widest italic border border-white/5 hover:border-white/10 transition-all flex items-center justify-center
                                                ${task.failures.failed > 0 ? 'hover:text-rose-400 hover:border-rose-500/20' : 'hover:text-orange-400 hover:border-orange-500/20'}`}
                                        >
                                            Ver Detalle
                                        </Link>
                                    )}
                                </div>
                            );
                        })}
                    </div>
                )}
            </div>
        </div>
    );
}
