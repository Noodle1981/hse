import { router, Head, Link, usePage } from '@inertiajs/react';
import AuthenticatedLayout from '../../../Layouts/AuthenticatedLayout';
import { useState, useEffect } from 'react';

export default function Index({ inspections, filters, empresas = [], tecnicos = [] }) {
    const { auth } = usePage().props;
    
    const [empresa, setEmpresa] = useState(filters.empresa || '');
    const [sede, setSede] = useState(filters.sede || '');
    const [fechaInicio, setFechaInicio] = useState(filters.fecha_inicio || '');
    const [fechaFin, setFechaFin] = useState(filters.fecha_fin || '');
    const [tecnicoId, setTecnicoId] = useState(filters.tecnico_id || '');
    const [expandedGroups, setExpandedGroups] = useState({});

    const toggleGroup = (key) => {
        setExpandedGroups(prev => ({
            ...prev,
            [key]: !prev[key]
        }));
    };

    const updateFilters = () => {
        router.get(route('gestor.inspections.index'), { 
            empresa, 
            sede, 
            fecha_inicio: fechaInicio, 
            fecha_fin: fechaFin,
            tecnico_id: tecnicoId
        }, {
            preserveState: true,
            replace: true,
        });
    };

    useEffect(() => {
        const timeout = setTimeout(() => {
            if (
                empresa !== (filters.empresa || '') || 
                sede !== (filters.sede || '') || 
                fechaInicio !== (filters.fecha_inicio || '') ||
                fechaFin !== (filters.fecha_fin || '') ||
                tecnicoId !== (filters.tecnico_id || '')
            ) {
                updateFilters();
            }
        }, 500);
        return () => clearTimeout(timeout);
    }, [empresa, sede, fechaInicio, fechaFin, tecnicoId]);

    // Compute available sedes based on selected empresa
    const availableSedes = empresa 
        ? (empresas.find(e => e.id.toString() === empresa.toString())?.sedes || [])
        : empresas.flatMap(e => e.sedes || []);

    const handleEmpresaChange = (val) => {
        setEmpresa(val);
        if (val) {
            const selectedEmp = empresas.find(emp => emp.id.toString() === val.toString());
            const hasSede = selectedEmp?.sedes.some(s => s.id.toString() === sede.toString());
            if (!hasSede) {
                setSede('');
            }
        }
    };

    const handleSedeChange = (val) => {
        setSede(val);
        if (val && !empresa) {
            const foundEmp = empresas.find(emp => emp.sedes.some(s => s.id.toString() === val.toString()));
            if (foundEmp) {
                setEmpresa(foundEmp.id.toString());
            }
        }
    };

    return (
        <AuthenticatedLayout 
            title="Bóveda de Inspecciones"
            header={
                <div className="flex flex-col">
                    <h1 className="text-xs font-black text-ep-primary uppercase tracking-widest italic leading-none mb-1">
                        Bóveda de <span className="text-white">Inspecciones</span>
                    </h1>
                    <h2 className="text-sm font-bold text-indigo-100 uppercase tracking-tight leading-none italic">
                        Historial Consolidado de Misiones
                    </h2>
                </div>
            }
        >
            <Head title="Historial de Inspecciones" />

            <div className="px-4 sm:px-6 lg:px-8 py-8 text-white selection:bg-orange-500/30">
                <div className="max-w-[100rem] mx-auto space-y-8 animate-fade-in">
                    
                    {/* HUB DE BÚSQUEDA */}
                    <div className="bg-[#16162a]/40 backdrop-blur-md rounded-[2.5rem] border border-white/5 p-6 shadow-2xl relative overflow-hidden animate-fade-in-down">
                        <div className="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-orange-500/30 to-transparent"></div>
                        
                        <div className="flex flex-col gap-6 relative z-10 pt-4">
                            <span className="absolute -top-10 left-6 bg-[#11111d] px-3 py-1 rounded-t-lg text-[9px] font-black text-orange-400 uppercase tracking-[0.3em] italic z-10 border border-b-0 border-white/5">Filtro de Historial</span>
                            <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-6 w-full">
                                <div className="flex flex-col gap-2">
                                    <label htmlFor="empresa" className="text-[10px] font-black text-orange-400 uppercase tracking-widest italic pl-2 leading-none">
                                        Empresa
                                    </label>
                                    <select id="empresa" name="empresa" 
                                        value={empresa}
                                        onChange={e => handleEmpresaChange(e.target.value)}
                                        className="w-full bg-[#11111d] border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-orange-500/50 transition-all italic uppercase cursor-pointer"
                                    >
                                        <option value="" className="bg-[#11111d] text-white">TODAS LAS EMPRESAS</option>
                                        {empresas.map(emp => (
                                            <option key={emp.id} value={emp.id} className="bg-[#11111d] text-white">
                                                {emp.nombre.toUpperCase()}
                                            </option>
                                        ))}
                                    </select>
                                </div>

                                <div className="flex flex-col gap-2">
                                    <label htmlFor="sede" className="text-[10px] font-black text-orange-400 uppercase tracking-widest italic pl-2 leading-none">
                                        Sede / Ubicación
                                    </label>
                                    <select id="sede" name="sede" 
                                        value={sede}
                                        onChange={e => handleSedeChange(e.target.value)}
                                        className="w-full bg-[#11111d] border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-orange-500/50 transition-all italic uppercase cursor-pointer"
                                    >
                                        <option value="" className="bg-[#11111d] text-white">TODAS LAS SEDES</option>
                                        {availableSedes.map(s => (
                                            <option key={s.id} value={s.id} className="bg-[#11111d] text-white">
                                                {s.localidad 
                                                    ? `${s.nombre_sede.toUpperCase()} - ${s.localidad.toUpperCase()}`
                                                    : s.nombre_sede.toUpperCase()
                                                }
                                            </option>
                                        ))}
                                    </select>
                                </div>

                                <div className="flex flex-col gap-2">
                                    <label htmlFor="tecnico_id" className="text-[10px] font-black text-orange-400 uppercase tracking-widest italic pl-2 leading-none">
                                        Técnico
                                    </label>
                                    <select id="tecnico_id" name="tecnico_id" 
                                        value={tecnicoId}
                                        onChange={e => setTecnicoId(e.target.value)}
                                        className="w-full bg-[#11111d] border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-orange-500/50 transition-all italic uppercase cursor-pointer"
                                    >
                                        <option value="" className="bg-[#11111d] text-white">TODOS LOS TÉCNICOS</option>
                                        {tecnicos.map(t => (
                                            <option key={t.id} value={t.id} className="bg-[#11111d] text-white">
                                                {t.name.toUpperCase()}
                                            </option>
                                        ))}
                                    </select>
                                </div>

                                <div className="flex flex-col gap-2">
                                    <label htmlFor="fecha_inicio" className="text-[10px] font-black text-orange-400 uppercase tracking-widest italic pl-2 leading-none">
                                        Fecha Desde
                                    </label>
                                    <input id="fecha_inicio" name="fecha_inicio" 
                                        type="date" 
                                        value={fechaInicio}
                                        onChange={e => setFechaInicio(e.target.value)}
                                        className="w-full bg-[#11111d] border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-orange-500/50 transition-all italic uppercase [color-scheme:dark]"
                                    />
                                </div>

                                <div className="flex flex-col gap-2">
                                    <label htmlFor="fecha_fin" className="text-[10px] font-black text-orange-400 uppercase tracking-widest italic pl-2 leading-none">
                                        Fecha Hasta
                                    </label>
                                    <input id="fecha_fin" name="fecha_fin" 
                                        type="date" 
                                        value={fechaFin}
                                        onChange={e => setFechaFin(e.target.value)}
                                        className="w-full bg-[#11111d] border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-orange-500/50 transition-all italic uppercase [color-scheme:dark]"
                                    />
                                </div>
                            </div>

                            <div className="flex items-center justify-end">
                                <span className="text-[10px] font-black text-white/20 uppercase tracking-widest italic">Jornadas Operativas: {inspections.total}</span>
                            </div>
                        </div>
                    </div>

                    {/* Tabla de Historial agrupado */}
                    <div className="bg-[#16162a]/60 backdrop-blur-md rounded-[3rem] border border-white/5 shadow-2xl relative overflow-hidden animate-fade-in-up">
                        <div className="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-orange-500/20 to-transparent"></div>
                        
                        <div className="overflow-x-auto">
                            <table className="w-full border-collapse min-w-[1100px]">
                                <thead>
                                    <tr className="bg-black/20 text-left">
                                        <th className="px-10 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none" style={{ width: '13%' }}>Fecha</th>
                                        <th className="px-6 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none" style={{ width: '22%' }}>Sede / Empresa</th>
                                        <th className="px-6 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none" style={{ width: '20%' }}>Técnico Responsable</th>
                                        <th className="px-6 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none" style={{ width: '20%' }}>KPIs & Resumen</th>
                                        <th className="px-10 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none text-right" style={{ width: '25%' }}>Comandos</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-white/5">
                                    {inspections.data.map(grp => {
                                        const groupKey = `${grp.completed_date}_${grp.sede_id}_${grp.tecnico_id}`;
                                        const isExpanded = !!expandedGroups[groupKey];

                                        return (
                                            <tr key={groupKey} className="contents">
                                                <tr className="group hover:bg-white/5 transition-all duration-300">
                                                    <td className="px-10 py-6">
                                                        <div className="text-sm font-black text-white italic uppercase tracking-tighter">
                                                            {grp.completed_date_formatted}
                                                        </div>
                                                        <div className="text-[9px] font-black text-white/20 uppercase tracking-widest mt-1 italic">
                                                            Jornada Completa
                                                        </div>
                                                    </td>
                                                    <td className="px-6 py-6">
                                                        <div className="flex items-center gap-4">
                                                            <div className="w-10 h-10 rounded-xl bg-orange-500/10 border border-orange-500/20 flex items-center justify-center text-orange-500 shadow-md shadow-orange-500/5">
                                                                <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5" strokeWidth="2"/>
                                                                </svg>
                                                            </div>
                                                            <div>
                                                                <div className="text-sm font-black text-white italic uppercase group-hover:text-orange-400 transition-colors">
                                                                    {grp.sede_nombre}
                                                                </div>
                                                                <div className="text-[9px] font-black text-white/20 uppercase tracking-widest mt-1 italic">
                                                                    {grp.empresa_nombre}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td className="px-6 py-6">
                                                        <div className="flex items-center gap-3">
                                                            <div className="w-8 h-8 rounded-full bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-[10px] font-black text-blue-400 uppercase italic shadow-md">
                                                                {grp.tecnico_nombre?.substring(0, 2)}
                                                            </div>
                                                            <div className="text-[11px] font-black text-white/60 italic uppercase tracking-wider">
                                                                {grp.tecnico_nombre}
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td className="px-6 py-6">
                                                        <div className="flex flex-col gap-1">
                                                            <div className="text-xs font-black text-white uppercase italic tracking-wider">
                                                                {grp.total_count} {grp.total_count === 1 ? 'Checklist' : 'Checklists'}
                                                            </div>
                                                            <div className="flex items-center gap-2 mt-0.5">
                                                                <span className={`text-[10px] font-extrabold px-2 py-0.5 rounded border ${
                                                                    grp.failed_count > 0 
                                                                        ? 'bg-rose-500/10 border-rose-500/20 text-rose-400 [text-shadow:_0_0_8px_rgba(244,63,94,0.3)] animate-pulse'
                                                                        : 'bg-emerald-500/10 border-emerald-500/20 text-emerald-400 [text-shadow:_0_0_8px_rgba(16,185,129,0.3)]'
                                                                }`}>
                                                                    Exitosos: {grp.success_count}/{grp.total_count}
                                                                </span>
                                                                <span className={`text-[10px] font-black uppercase tracking-wider ${
                                                                    grp.compliance_percentage === 100 
                                                                        ? 'text-emerald-400 [text-shadow:_0_0_10px_rgba(16,185,129,0.4)]'
                                                                        : 'text-amber-400 [text-shadow:_0_0_10px_rgba(245,158,11,0.4)]'
                                                                }`}>
                                                                    {grp.compliance_percentage}% Cumplimiento
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td className="px-10 py-6 text-right">
                                                        <div className="flex items-center justify-end gap-3">
                                                            {grp.visit_certificate_id ? (
                                                                <a 
                                                                    href={route('gestor.visits.certificate.preview', { id: grp.visit_certificate_id })} 
                                                                    target="_blank" 
                                                                    className="h-10 px-4 bg-orange-500/10 border border-orange-500/20 text-orange-400 hover:bg-orange-500 hover:text-white rounded-xl flex items-center justify-center gap-2 transition-all shadow-lg font-black text-[10px] tracking-wider uppercase italic"
                                                                    title="Ver Constancia de Visita Principal (Firmada por Jefe)"
                                                                >
                                                                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" strokeWidth="2"/>
                                                                    </svg>
                                                                    Constancia Principal
                                                                </a>
                                                            ) : (
                                                                <button 
                                                                    disabled
                                                                    className="h-10 px-4 bg-white/5 border border-white/5 text-white/20 rounded-xl flex items-center justify-center gap-2 transition-all font-black text-[10px] tracking-wider uppercase italic cursor-not-allowed"
                                                                    title="No se ha cerrado ni firmado la visita para este día y sede"
                                                                >
                                                                    <svg className="w-4 h-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" strokeWidth="2"/>
                                                                    </svg>
                                                                    Sin Constancia
                                                                </button>
                                                            )}

                                                            <button 
                                                                onClick={() => toggleGroup(groupKey)}
                                                                className={`h-10 px-4 rounded-xl font-black text-[10px] tracking-wider uppercase italic flex items-center gap-2 border transition-all ${
                                                                    isExpanded 
                                                                        ? 'bg-orange-500/20 border-orange-500/30 text-orange-400' 
                                                                        : 'bg-white/5 border-white/10 text-white/80 hover:bg-white/10'
                                                                }`}
                                                            >
                                                                {isExpanded ? 'Ocultar' : `Ver (${grp.total_count})`}
                                                                <svg className={`w-3.5 h-3.5 transition-transform duration-300 ${isExpanded ? 'rotate-180' : ''}`} fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path d="M19 9l-7 7-7-7" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round"/>
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>

                                                {/* Detalle Desplegable con Sub-acordeón */}
                                                {isExpanded && (
                                                    <tr>
                                                        <td colSpan="5" className="bg-[#11111d]/50 px-10 py-6 border-t border-white/5 animate-fade-in">
                                                            <div className="space-y-4">
                                                                <div className="text-[10px] font-black text-orange-400 uppercase tracking-widest italic mb-3">
                                                                    Desglose de Checklists del Técnico ({grp.checklists.length})
                                                                </div>
                                                                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                                    {grp.checklists.map((child) => (
                                                                        <div key={child.id} className="bg-[#16162a]/80 border border-white/5 rounded-2xl p-4 flex items-center justify-between hover:border-orange-500/30 transition-all duration-300">
                                                                            <div className="space-y-1">
                                                                                <div className="text-xs font-black text-white italic uppercase">
                                                                                    {child.checklist_title}
                                                                                </div>
                                                                                <div className="text-[9px] font-bold text-white/40 uppercase tracking-wider">
                                                                                    Código: {child.registry_code || 'S/N'} • Hora: {child.completed_time} HRS
                                                                                </div>
                                                                                <div className="text-[10px] font-black text-white/60">
                                                                                    Quién Firmó: <span className="text-orange-300 italic">{child.supervisor_name}</span>
                                                                                </div>
                                                                            </div>
                                                                            <div className="flex items-center gap-3">
                                                                                {child.has_failed ? (
                                                                                    <span className="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest text-red-400 border border-red-500/20 bg-red-500/10 [text-shadow:_0_0_8px_rgba(239,68,68,0.3)] animate-pulse">
                                                                                        ⚠️ Falló
                                                                                    </span>
                                                                                ) : (
                                                                                    <span className="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest text-emerald-400 border border-emerald-500/20 bg-emerald-500/10 [text-shadow:_0_0_8px_rgba(16,185,129,0.3)]">
                                                                                        Pasó
                                                                                    </span>
                                                                                )}
                                                                                <a 
                                                                                    href={route('gestor.inspections.pdf.preview', { id: child.id })} 
                                                                                    target="_blank" 
                                                                                    className="h-8 w-8 bg-red-500/10 border border-red-500/20 text-red-500 hover:bg-red-500 hover:text-white rounded-xl flex items-center justify-center transition-all shadow-lg"
                                                                                    title="Descargar PDF Checklist"
                                                                                >
                                                                                    <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                        <path d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" strokeWidth="2"/>
                                                                                    </svg>
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                    ))}
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                )}
                                            </tr >
                                        );
                                    })}
                                </tbody>
                            </table>
                        </div>

                        {/* Pagination */}
                        <div className="px-10 py-6 border-t border-white/5 bg-black/20 flex items-center justify-between font-black italic text-[10px]">
                            <div className="text-white/20">Jornadas Operativas: {inspections.total}</div>
                            <div className="flex gap-2">
                                {inspections.links.map((link, i) => (
                                    <Link 
                                        key={i}
                                        href={link.url || '#'}
                                        dangerouslySetInnerHTML={{ __html: link.label }}
                                        className={`px-4 py-2 rounded-xl border ${link.active ? 'bg-orange-500/10 border-orange-500/30 text-orange-400' : 'bg-white/5 border-white/5 text-white/20 hover:text-white'} ${!link.url && 'opacity-20 cursor-not-allowed'}`}
                                    />
                                ))}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <style>{`
                .animate-fade-in { animation: fade-in 0.4s ease-out forwards; }
                .animate-fade-in-down { animation: fade-in-down 0.6s ease-out forwards; }
                .animate-fade-in-up { animation: fade-in-up 0.5s ease-out forwards; }
                @keyframes fade-in { from { opacity: 0; } to { opacity: 1; } }
                @keyframes fade-in-down { from { opacity: 0; transform: translateY(-30px); } to { opacity: 1; transform: translateY(0); } }
                @keyframes fade-in-up { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
            `}</style>
        </AuthenticatedLayout>
    );
}
