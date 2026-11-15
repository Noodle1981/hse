import React from 'react';

const groupCompletions = (completions) => {
    if (!completions || completions.length === 0) return [];
    const groups = {};
    completions.forEach(cc => {
        const title = cc.assignment?.checklist?.title || 'Inspección';
        const key = cc.assignment?.dynamic_checklist_id || title;
        if (!groups[key]) {
            groups[key] = { title, count: 0, passed: 0, failed: 0, assets: [] };
        }
        groups[key].count += 1;
        if (cc.failure_analysis?.is_failed) groups[key].failed += 1;
        else groups[key].passed += 1;

        const asset = cc.assignment?.asset || cc.assignment?.inspection?.asset;
        if (asset && !groups[key].assets.some(a => a.id === asset.id)) {
            groups[key].assets.push({ id: asset.id, identifier: asset.identifier, name: asset.name, status: asset.status });
        }
    });
    return Object.values(groups);
};

export default function VisitsLogSection({ visits = [], value, onChange, isReadOnly }) {
    return (
        <div className="bg-ep-dark/40 backdrop-blur-md p-10 sm:p-14 rounded-[3.5rem] border border-white/5 shadow-2xl relative overflow-hidden group">
            <div className="flex items-center gap-8 mb-12">
                <div className="w-16 h-16 bg-white/5 rounded-2xl flex items-center justify-center text-white font-black italic text-3xl border border-white/10 shadow-inner group-hover:bg-ep-primary/20 transition-all">1</div>
                <div>
                    <h3 className="text-3xl font-black text-white uppercase italic tracking-tighter">Bitácora de Visitas Técnicas</h3>
                    <p className="text-[10px] font-black text-indigo-100/20 uppercase tracking-[0.3em] mt-1 italic">Certificados de asistencia registrados ({visits.length})</p>
                </div>
            </div>

            <div className="space-y-6 mb-12">
                {visits.map((visit, idx) => {
                    const grouped = groupCompletions(visit.checklist_completions);
                    return (
                        <div key={idx} className="p-8 bg-black/40 rounded-[2rem] border border-white/5 flex flex-col md:flex-row gap-10 hover:border-ep-primary/20 transition-all group/item">
                            <div className="flex-shrink-0 text-center md:text-left min-w-[150px]">
                                <div className="text-2xl font-black text-ep-primary italic leading-none">{new Date(visit.visit_date).toLocaleDateString()}</div>
                                <div className="text-[10px] font-black text-indigo-100/30 uppercase tracking-[0.2em] mt-3">{visit.user?.nombres}</div>
                                <span className="inline-block mt-4 px-3 py-1 bg-white/5 rounded-lg text-[8px] font-black text-white/20 uppercase tracking-widest">CERTIFICADO OK</span>
                            </div>
                            <div className="flex-grow">
                                <p className="text-sm italic text-indigo-100/50 leading-relaxed mb-6 border-l-2 border-white/5 pl-6">{visit.general_observations || 'Sin observaciones generales registradas.'}</p>

                                {grouped.length > 0 && (
                                    <div className="overflow-x-auto mb-6 rounded-2xl border border-white/5 bg-black/20">
                                        <table className="w-full text-left border-collapse">
                                            <thead>
                                                <tr className="border-b border-white/5 bg-white/5 text-[9px] font-black text-indigo-100/40 uppercase tracking-widest italic">
                                                    <th className="px-6 py-4">Protocolo de Control / Inspección</th>
                                                    <th className="px-4 py-4 text-center">Cant.</th>
                                                    <th className="px-4 py-4 text-center">Conformes</th>
                                                    <th className="px-4 py-4 text-center">Con Desvíos</th>
                                                    <th className="px-6 py-4">Equipos / Activos Evaluados</th>
                                                </tr>
                                            </thead>
                                            <tbody className="divide-y divide-white/5 text-[11px] font-semibold text-white/80">
                                                {grouped.map((group, gIdx) => (
                                                    <tr key={gIdx} className="hover:bg-white/[0.02] transition-colors">
                                                        <td className="px-6 py-4 font-black uppercase text-indigo-100 tracking-wide">{group.title}</td>
                                                        <td className="px-4 py-4 text-center font-black">{group.count}</td>
                                                        <td className="px-4 py-4 text-center text-emerald-400 font-bold">{group.passed}</td>
                                                        <td className="px-4 py-4 text-center text-red-400 font-bold">{group.failed}</td>
                                                        <td className="px-6 py-4">
                                                            {group.assets.length > 0 ? (
                                                                <div className="flex flex-wrap gap-1.5">
                                                                    {group.assets.map((asset, aIdx) => {
                                                                        const statusColors = {
                                                                            operativo: 'bg-emerald-500/10 border-emerald-500/20 text-emerald-400',
                                                                            alerta: 'bg-amber-500/10 border-amber-500/20 text-amber-400',
                                                                            fuera_de_servicio: 'bg-red-500/10 border-red-500/20 text-red-400',
                                                                        };
                                                                        return (
                                                                            <span
                                                                                key={aIdx}
                                                                                title={`${asset.name} (${asset.status ? asset.status.replace(/_/g, ' ').toUpperCase() : 'N/A'})`}
                                                                                className={`inline-flex items-center px-2 py-0.5 border rounded-lg text-[9px] font-black uppercase tracking-wider ${statusColors[asset.status] || 'bg-white/5 border-white/10 text-white/60'}`}
                                                                            >
                                                                                {asset.identifier}
                                                                            </span>
                                                                        );
                                                                    })}
                                                                </div>
                                                            ) : (
                                                                <span className="text-white/25 text-[9px] font-bold uppercase tracking-widest">Sin Activos</span>
                                                            )}
                                                        </td>
                                                    </tr>
                                                ))}
                                            </tbody>
                                        </table>
                                    </div>
                                )}

                                {visit.trainings && visit.trainings.length > 0 && (
                                    <div className="mt-6">
                                        <span className="text-[9px] font-black text-indigo-100/35 uppercase tracking-[0.2em] block mb-2 px-1">Acciones Formativas Dictadas</span>
                                        <div className="flex flex-wrap gap-2">
                                            {visit.trainings.map((t, i) => (
                                                <span key={i} className="px-4 py-1.5 bg-blue-500/10 border border-blue-500/20 text-blue-400 text-[10px] font-black uppercase italic rounded-xl">
                                                    {t.title}
                                                </span>
                                            ))}
                                        </div>
                                    </div>
                                )}
                            </div>
                        </div>
                    );
                })}
                {visits.length === 0 && (
                    <div className="py-16 text-center bg-black/20 rounded-[2.5rem] border border-dashed border-white/10">
                        <p className="text-[11px] font-black text-white/5 uppercase tracking-[0.4em] italic leading-none">Cero visitas detectadas en el periodo</p>
                    </div>
                )}
            </div>

            <div className="space-y-4">
                <label className="block text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.4em] italic mb-3 px-8">Observaciones Técnicas de Gestión de Campo</label>
                <textarea
                    id="field_4"
                    name="field_4"
                    value={value}
                    onChange={e => onChange(e.target.value)}
                    rows={5}
                    readOnly={isReadOnly}
                    className="w-full bg-black/60 border-white/10 rounded-[2.5rem] px-10 py-8 text-white text-sm italic font-medium focus:ring-2 focus:ring-ep-primary transition-all placeholder-white/5 outline-none border read-only:opacity-60 read-only:cursor-not-allowed"
                    placeholder="..."
                />
            </div>
        </div>
    );
}
