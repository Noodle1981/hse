import { Link } from '@inertiajs/react';

export default function AssignmentTaskTable({
    activeTab,
    displayData,
    theme,
    selectedMonth,
    safeRoute,
    safeDate,
    handleExecute,
}) {
    return (
        <div className={`bg-[#1a1a2e]/40 backdrop-blur-3xl rounded-[3rem] border shadow-3xl overflow-hidden transition-all duration-700 ${theme.border}`}>
            <div className="overflow-x-auto">
                <table className="w-full text-left">
                    <thead>
                        <tr className="bg-black/20 text-[9px] font-black text-white/20 uppercase italic tracking-[0.3em] border-b border-white/5">
                            <th className="px-6 py-4">{theme.header}</th>
                            <th className="px-6 py-4">Ubicación</th>
                            <th className="px-6 py-4">Vencimiento</th>
                            <th className="px-6 py-4">{theme.column4Label}</th>
                            <th className="px-8 py-4 text-right">Acción</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y divide-white/[0.03]">
                        {displayData.length > 0 ? displayData.map((item, idx) => (
                            <tr key={`${activeTab}-${item?.id || idx}`} className="group hover:bg-white/[0.02] transition-colors">
                                <td className="px-6 py-4">
                                    <div className="flex items-center gap-4">
                                        <div className={`w-10 h-10 rounded-xl bg-black/40 flex items-center justify-center border border-white/5 transition-all duration-300 group-hover:shadow-[0_0_10px_currentColor] ${theme.primary} group-hover:border-current`}>
                                            {activeTab === 'inspections' ? (
                                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" strokeWidth="2.5"/></svg>
                                            ) : activeTab === 'trainings' ? (
                                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 14l9-5-9-5-9 5 9 5z" strokeWidth="2.5"/></svg>
                                            ) : (
                                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" strokeWidth="2.5"/></svg>
                                            )}
                                        </div>
                                        <div>
                                            <div className="flex items-center gap-2 mb-0.5">
                                                <div className="text-sm font-black text-white italic uppercase tracking-tighter leading-none group-hover:text-white transition-colors">{item?.title || 'Sin Título'}</div>
                                                <span className={`text-[7px] font-black px-1 py-0.5 rounded border italic transition-colors duration-500 ${item?.source === 'contract' ? 'text-indigo-400 bg-indigo-500/10 border-indigo-500/20' : 'text-emerald-400 bg-emerald-500/10 border-emerald-500/20'}`}>
                                                    {item?.source === 'contract' ? 'PPD' : 'SOL'}
                                                </span>
                                            </div>
                                            <div className="text-[8px] font-bold text-white/20 uppercase tracking-[0.2em] leading-none italic">
                                                ID: {item?.registry_code || 'N/A'}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td className="px-6 py-4">
                                    <div className="space-y-1">
                                        <div className="text-[10px] font-black text-white/80 uppercase italic tracking-wider leading-none">{item?.sede || 'N/A'}</div>
                                        <div className="text-[8px] font-bold text-white/20 uppercase italic tracking-widest leading-none">{item?.empresa || 'N/A'}</div>
                                    </div>
                                </td>

                                <td className="px-6 py-4">
                                    <div className="flex flex-col">
                                        <span className="text-[11px] font-black text-white italic tracking-tight">{safeDate(item?.due_date)}</span>
                                        <span className="text-[7px] font-bold text-white/10 uppercase tracking-widest mt-0.5 italic">Vencimiento</span>
                                    </div>
                                </td>

                                <td className="px-6 py-4">
                                    <TaskColumnStatus activeTab={activeTab} item={item} />
                                </td>

                                <td className="px-8 py-4 text-right">
                                    <div className="flex items-center justify-end gap-3 flex-wrap">
                                        {item?.checklist_id && (
                                            <Link
                                                href={safeRoute(
                                                    activeTab === 'inspections' ? 'tecnico.inspections.index' : 'tecnico.trainings.index',
                                                    { checklist_id: item.checklist_id }
                                                )}
                                                className="px-4 py-2 border border-white/10 hover:border-white/20 hover:bg-white/5 text-white/70 hover:text-white rounded-xl text-[9px] font-black uppercase italic tracking-[0.2em] transition-all"
                                            >
                                                Historial
                                            </Link>
                                        )}

                                        {activeTab === 'reports' ? (
                                            <ReportRowAction
                                                item={item}
                                                selectedMonth={selectedMonth}
                                                theme={theme}
                                                safeRoute={safeRoute}
                                            />
                                        ) : (
                                            <div className="flex items-center gap-2">
                                                {(item.status === 'completed' || item.status === 'finalizado') && (
                                                    <span className="text-[8px] font-black text-emerald-500 bg-emerald-500/10 px-3 py-1.5 rounded-xl border border-emerald-500/20 uppercase italic tracking-widest leading-none mr-1">
                                                        OK
                                                    </span>
                                                )}
                                                <button
                                                    onClick={() => handleExecute(item)}
                                                    className={`px-5 py-2.5 text-white rounded-xl text-[9px] font-black uppercase italic tracking-[0.2em] shadow-xl hover:scale-105 transition-all ${
                                                        item.status === 'in_progress' ? 'bg-amber-600 shadow-amber-600/20' : theme.bg
                                                    } ${theme.shadow || ''} outline-none`}
                                                >
                                                    {item.status === 'in_progress' ? 'Reanudar' : 'Ejecutar'}
                                                </button>
                                            </div>
                                        )}
                                    </div>
                                </td>
                            </tr>
                        )) : (
                            <tr>
                                <td colSpan="5" className="px-10 py-20 text-center">
                                    <div className="flex flex-col items-center opacity-20">
                                        <svg className="w-12 h-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" strokeWidth="1.5"/></svg>
                                        <span className="text-xs font-black uppercase italic tracking-widest">No se detectaron misiones</span>
                                    </div>
                                </td>
                            </tr>
                        )}
                    </tbody>
                </table>
            </div>
        </div>
    );
}

function TaskColumnStatus({ activeTab, item }) {
    if (activeTab === 'inspections') {
        return (
            <div className="flex flex-col gap-1.5">
                <div className="flex items-center gap-2">
                    <span className="text-[11px] font-black text-white italic tracking-tight uppercase leading-none">
                        {item?.checklist_count || 0} {item?.checklist_count === 1 ? 'registro' : 'registros'}
                    </span>
                    {item?.completed_count > 0 && (
                        <>
                            <span className="text-[9px] font-bold text-white/20 uppercase tracking-widest leading-none">|</span>
                            <span className={`text-[10px] font-black uppercase italic tracking-tight leading-none flex items-center gap-1 ${item?.passed_count === item?.completed_count ? 'text-emerald-400' : 'text-red-500'}`}>
                                Exitosos: {item?.passed_count}/{item?.completed_count}
                                {item?.passed_count !== item?.completed_count && <span>⚠️</span>}
                            </span>
                        </>
                    )}
                </div>
                {item?.compliance_pct !== null && (
                    <div className="flex items-center gap-2">
                        <span className="text-[8px] font-bold text-white/30 uppercase tracking-[0.2em] leading-none italic">Cumplimiento:</span>
                        <span className={`text-[10px] font-black leading-none ${item?.compliance_pct === 100 ? 'text-emerald-400 glow-text-emerald' : 'text-red-500 glow-text-red'}`}>
                            {item?.compliance_pct}%
                        </span>
                    </div>
                )}
            </div>
        );
    }

    if (activeTab === 'reports') {
        const badgeMap = {
            aprobado: { label: 'APROBADO', cls: 'text-emerald-400 bg-emerald-500/10 border-emerald-500/20' },
            pendiente: { label: 'PENDIENTE', cls: 'text-amber-400 bg-amber-500/10 border-amber-500/20' },
            corregir: { label: 'REVISIÓN', cls: 'text-red-400 bg-red-500/10 border-red-500/30 animate-pulse' },
            reanudar: { label: 'BORRADOR', cls: 'text-indigo-300 bg-indigo-500/10 border-indigo-500/20' },
            ejecutar: { label: 'PLANIF.', cls: 'text-white/20 bg-white/5 border-white/5' },
        };
        const badge = badgeMap[item?.accion] ?? badgeMap.ejecutar;
        return <span className={`px-3 py-1 rounded-lg text-[8px] font-black uppercase italic tracking-widest border transition-all duration-300 ${badge.cls}`}>{badge.label}</span>;
    }

    const statusCls = item?.status === 'completed' || item?.status === 'finalizado'
        ? 'text-emerald-500 bg-emerald-500/5 border-emerald-500/20'
        : item?.status === 'in_progress'
            ? 'text-ep-primary bg-ep-primary/5 border-ep-primary/20 animate-pulse'
            : 'text-white/20 bg-white/5 border-white/5';
    const statusLabel = item?.status === 'in_progress' ? 'CURSO' : (item?.status === 'completed' || item?.status === 'finalizado') ? 'FINAL' : 'SOL';

    return <span className={`px-3 py-1 rounded-lg text-[8px] font-black uppercase italic tracking-widest border transition-all duration-300 ${statusCls}`}>{statusLabel}</span>;
}

function ReportRowAction({ item, selectedMonth, theme, safeRoute }) {
    const accion = item?.accion;
    const reportId = item?.master_report_id;
    const periodo = `${new Date().getFullYear()}-${String(selectedMonth).padStart(2, '0')}`;

    if (accion === 'aprobado') {
        return (
            <Link href={`/master-reports/${reportId}`} className="px-4 py-2 text-[9px] font-black uppercase italic tracking-[0.2em] text-emerald-400 border border-emerald-500/20 rounded-xl hover:bg-emerald-500/10 transition-all duration-300 flex items-center gap-1.5">
                <svg className="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" strokeWidth="3"/></svg>
                VER INFORME
            </Link>
        );
    }

    if (accion === 'pendiente') {
        return (
            <span className="px-4 py-2 text-[9px] font-black uppercase italic tracking-[0.2em] text-amber-400 bg-amber-500/10 border border-amber-500/20 rounded-xl flex items-center gap-1.5">
                <svg className="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" strokeWidth="2"/></svg>
                PENDIENTE GESTOR
            </span>
        );
    }

    if (accion === 'corregir') {
        return (
            <Link href={`/master-reports/${reportId}/edit`} className="px-4 py-2 text-[9px] font-black uppercase italic tracking-[0.2em] text-red-400 bg-red-500/20 border border-red-500/50 rounded-xl shadow-[0_0_15px_rgba(239,68,68,0.3)] animate-pulse flex items-center gap-1.5">
                <svg className="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" strokeWidth="2"/></svg>
                ⚠ CORREGIR
            </Link>
        );
    }

    if (accion === 'reanudar') {
        return (
            <Link href={`/master-reports/${reportId}/edit`} className="px-4 py-2 text-[9px] font-black uppercase italic tracking-[0.2em] text-indigo-300 bg-indigo-500/20 border border-indigo-500/30 rounded-xl hover:bg-indigo-500/30 transition-all flex items-center gap-1.5">
                <svg className="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" strokeWidth="2"/></svg>
                REANUDAR
            </Link>
        );
    }

    return (
        <Link href={safeRoute('master-reports.create', { sede_id: item.sede_id, periodo })} className={`px-5 py-2.5 text-white rounded-xl text-[9px] font-black uppercase italic tracking-[0.2em] shadow-xl hover:scale-105 transition-all ${theme.bg} ${theme.shadow || ''} outline-none flex items-center gap-1.5`}>
            <svg className="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" strokeWidth="3"/></svg>
            EJECUTAR
        </Link>
    );
}
