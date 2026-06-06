import { router, Head, Link, useForm, usePage } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { useState, useEffect } from 'react';
import axios from 'axios';

export default function Index({ technicians, companies, filters }) {
    const { auth, flash } = usePage().props;
    
    // Search synchronization
    const [search, setSearch] = useState(filters.search || '');
    
    // Modal State
    const [showAssignModal, setShowAssignModal] = useState(false);
    const [selectedTechnician, setSelectedTechnician] = useState(null);
    const [sedes, setSedes] = useState([]);
    const [currentAssignments, setCurrentAssignments] = useState([]);
    const [loadingAssignments, setLoadingAssignments] = useState(false);
    
    // Assignment Form
    const [selectedCompanyId, setSelectedCompanyId] = useState('');
    const [selectedSedeId, setSelectedSedeId] = useState('');

    // Sincronizar búsqueda con el servidor
    const updateFilters = () => {
        router.get(route('gestor.technicians.index'), { search }, {
            preserveState: true,
            replace: true,
        });
    };

    // Debounce para búsqueda
    useEffect(() => {
        const timeout = setTimeout(() => {
            if (search !== (filters.search || '')) updateFilters();
        }, 500);
        return () => clearTimeout(timeout);
    }, [search]);

    // Cargar sedes cuando cambia la empresa
    useEffect(() => {
        if (selectedCompanyId) {
            axios.get(`/gestor/api/companies/${selectedCompanyId}/sites`)
                .then(res => setSedes(res.data))
                .catch(err => console.error(err));
        } else {
            setSedes([]);
        }
    }, [selectedCompanyId]);

    const openAssignModal = (tech) => {
        setSelectedTechnician(tech);
        setSelectedCompanyId('');
        setSelectedSedeId('');
        setSedes([]);
        setLoadingAssignments(true);
        
        axios.get(`/gestor/api/technicians/${tech.id}/assignments`)
            .then(res => {
                setCurrentAssignments(res.data);
                setLoadingAssignments(false);
            })
            .catch(err => {
                console.error(err);
                setLoadingAssignments(false);
            });

        setShowAssignModal(true);
    };

    const handleAssign = () => {
        router.post(route('gestor.technicians.assign', selectedTechnician.id), {
            sede_id: selectedSedeId,
        }, {
            preserveScroll: true,
            onSuccess: () => {
                // Refresh assignments from server
                axios.get(`/gestor/api/technicians/${selectedTechnician.id}/assignments`)
                    .then(res => setCurrentAssignments(res.data));
                setSelectedCompanyId('');
                setSelectedSedeId('');
            }
        });
    };

    const handleRemove = (sedeId) => {
        router.post(route('gestor.technicians.remove', selectedTechnician.id), {
            sede_id: sedeId,
        }, {
            preserveScroll: true,
            onSuccess: () => {
                // Refresh assignments from server
                axios.get(`/gestor/api/technicians/${selectedTechnician.id}/assignments`)
                    .then(res => setCurrentAssignments(res.data));
            }
        });
    };

    return (
        <AuthenticatedLayout title="Escuadrón">
            <Head title="Gestión de Técnicos" />

            <div className="px-4 sm:px-6 lg:px-8 py-8 text-white selection:bg-[#F69239]/30">
                <div className="max-w-[95rem] mx-auto space-y-8 animate-fade-in">
                    
                    {/* BUSCADOR TÁCTICO */}
                    <div className="bg-[#16162a]/40 backdrop-blur-md rounded-[2.5rem] border border-white/5 p-6 shadow-2xl relative overflow-hidden animate-fade-in-down">
                        <div className="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-indigo-500/30 to-transparent"></div>
                        
                        <div className="relative z-10">
                            <div className="relative group">
                                <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-indigo-400 uppercase tracking-[0.3em] italic z-10">Frecuencia de Escaneo</span>
                                <input id="search" name="search" 
                                    type="text" 
                                    value={search}
                                    onChange={e => setSearch(e.target.value)}
                                    className="w-full bg-black/40 border border-white/10 rounded-2xl px-12 py-5 text-sm font-bold text-white focus:ring-2 focus:ring-indigo-500/50 transition-all placeholder-white/5 italic uppercase"
                                    placeholder="BUSCAR TÉCNICO, DOCUMENTO O CANAL DE ENLACE..."
                                />
                                <div className="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                                    <svg className="h-5 w-5 text-indigo-500/50 group-focus-within:text-indigo-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="3">
                                        <path strokeLinecap="round" strokeLinejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* MATRIZ DE DESPLIEGUE */}
                    <div className="bg-[#16162a]/60 backdrop-blur-md rounded-[3rem] border border-white/5 shadow-2xl relative overflow-hidden animate-fade-in-up">
                        <div className="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-[#F69239]/20 to-transparent"></div>
                        
                        <div className="overflow-x-auto">
                            <table className="w-full border-collapse min-w-[1000px]">
                                <thead>
                                    <tr className="bg-black/20 text-left">
                                        <th className="px-6 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none">Unidad Táctica / Perfil</th>
                                        <th className="px-6 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none">Canal de Enlace</th>
                                        <th className="px-6 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none">Identificación de Campo</th>
                                        <th className="px-6 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none text-center">Status</th>
                                        <th className="px-6 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none text-right">Comandos</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-white/5">
                                    {technicians.data.map(tech => (
                                        <tr key={tech.id} className="group/row hover:bg-white/5 transition-all duration-500 relative">
                                            <td className="px-6 py-6">
                                                <div className="flex items-center gap-4">
                                                    <div className="w-12 h-12 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400 text-xs font-black group-hover:scale-110 transition-transform shadow-[0_0_15px_rgba(99,102,241,0.1)]">
                                                        {tech.nombres?.charAt(0)}{tech.apellidos?.charAt(0)}
                                                    </div>
                                                    <div>
                                                        <div className="text-sm font-black text-white italic uppercase tracking-tight leading-none group-hover/row:text-indigo-400 transition-colors">
                                                            {tech.nombres} {tech.apellidos}
                                                        </div>
                                                        <div className="text-[9px] font-black text-white/20 uppercase tracking-widest mt-1.5 italic">
                                                            {tech.titulo || 'OPERADOR TÉCNICO'}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td className="px-6 py-6">
                                                <div className="text-[11px] font-bold text-white/40 italic mb-1 group-hover/row:text-white/60 transition-colors uppercase leading-none">{tech.email}</div>
                                                <div className="text-[10px] font-black text-indigo-400/40 italic leading-none">{tech.telefono}</div>
                                            </td>
                                            <td className="px-6 py-6">
                                                <div className="text-[11px] font-black text-white/80 italic uppercase tracking-widest mb-1">{tech.matricula || 'SIN MATRÍCULA'}</div>
                                                <div className="text-[9px] font-bold text-white/20 italic tracking-widest uppercase">DOC: {tech.n_doc || '---'}</div>
                                            </td>
                                            <td className="px-6 py-6 text-center">
                                                <StatusBadge active={tech.estado === 'activo'} text={tech.estado.toUpperCase()} />
                                            </td>
                                            <td className="px-6 py-6 text-right">
                                                <button onClick={() => openAssignModal(tech)} 
                                                        className="h-10 px-5 bg-indigo-500 text-white rounded-xl text-[10px] font-black uppercase tracking-widest italic flex items-center gap-2 ml-auto shadow-lg shadow-indigo-500/20 hover:brightness-110 active:scale-95 transition-all group/btn relative overflow-hidden">
                                                    <div className="absolute inset-0 bg-white/20 -translate-x-12 group-hover/btn:translate-x-0 transition-transform"></div>
                                                    <svg className="w-3.5 h-3.5 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="3">
                                                        <path d="M3 6a3 3 0 013-3h10a1 1 0 01.8 1.6L14.25 8l2.55 3.4A1 1 0 0116 13H6a1 1 0 00-1 1v3a1 1 0 11-2 0V6z"/>
                                                    </svg>
                                                    <span className="relative z-10">Desplegar</span>
                                                </button>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>

                        {/* Pagination */}
                        <div className="px-10 py-6 border-t border-white/5 bg-black/20 flex items-center justify-between">
                            <div className="text-[10px] font-bold text-white/20 uppercase italic">
                                Unidades Operativas: {technicians.total}
                            </div>
                            <div className="flex gap-2">
                                {technicians.links.map((link, i) => (
                                    <Link 
                                        key={i}
                                        href={link.url || '#'}
                                        dangerouslySetInnerHTML={{ __html: link.label }}
                                        className={`px-4 py-2 rounded-xl text-[10px] font-black transition-all border ${
                                            link.active 
                                                ? 'bg-indigo-500/10 border-indigo-500/30 text-indigo-400' 
                                                : 'bg-white/5 border-white/5 text-white/20 hover:text-white'
                                        } ${!link.url && 'opacity-30 cursor-not-allowed'}`}
                                    />
                                ))}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {/* MODAL DE ASIGNACIÓN */}
            {showAssignModal && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 animate-fade-in">
                    <div className="absolute inset-0 bg-black/85 backdrop-blur-md" onClick={() => setShowAssignModal(false)}></div>
                    
                    <div className="bg-[#16162a] border border-white/10 rounded-[3rem] max-w-5xl w-full relative z-10 shadow-2xl overflow-hidden">
                        <div className="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-transparent via-indigo-500 to-transparent"></div>
                        
                        <div className="p-10">
                            <div className="flex items-center justify-between mb-10 pb-6 border-b border-white/5">
                                <div>
                                    <h3 className="text-2xl font-black text-white italic tracking-tight uppercase">Terminal de Asignación</h3>
                                    <p className="text-[10px] font-black text-indigo-400 uppercase tracking-widest italic mt-1 leading-none">Unidad: <span className="text-white">{selectedTechnician?.nombres} {selectedTechnician?.apellidos}</span></p>
                                </div>
                                <button onClick={() => setShowAssignModal(false)} className="w-10 h-10 bg-white/5 border border-white/10 rounded-xl flex items-center justify-center text-white/40 hover:text-white transition-all">
                                    <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>

                            <div className="grid grid-cols-1 md:grid-cols-2 gap-10">
                                
                                {/* Formulario de Asignación */}
                                <div className="bg-black/30 p-8 rounded-[2.5rem] border border-white/5 relative group">
                                    <div className="absolute -top-3 left-8 bg-indigo-500 px-3 py-1 rounded-full text-[8px] font-black text-white uppercase tracking-widest italic shadow-lg">Nueva Orden</div>
                                    
                                    <div className="space-y-8 mt-4">
                                        <div>
                                            <label className="text-[9px] font-black text-white/40 uppercase tracking-[0.3em] ml-2 mb-2 block italic">Sector Corporativo</label>
                                            <select id="selectedCompanyId" name="selectedCompanyId" 
                                                value={selectedCompanyId} 
                                                onChange={e => setSelectedCompanyId(e.target.value)}
                                                className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-indigo-500/50 transition-all [color-scheme:dark] italic uppercase appearance-none cursor-pointer"
                                            >
                                                <option value="">SELECCIONE EMPRESA...</option>
                                                {companies.map(company => <option key={company.id} value={company.id}>{company.nombre}</option>)}
                                            </select>
                                        </div>

                                        <div>
                                            <label className="text-[9px] font-black text-white/40 uppercase tracking-[0.3em] ml-2 mb-2 block italic">Punto de Despliegue (Sede)</label>
                                            <select id="selectedSedeId" name="selectedSedeId" 
                                                value={selectedSedeId} 
                                                onChange={e => setSelectedSedeId(e.target.value)}
                                                disabled={sedes.length === 0}
                                                className={`w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-indigo-500/50 transition-all [color-scheme:dark] italic uppercase appearance-none cursor-pointer ${sedes.length === 0 ? 'opacity-20' : ''}`}
                                            >
                                                <option value="">SELECCIONE SEDE...</option>
                                                {sedes.map(sede => <option key={sede.id} value={sede.id}>{sede.nombre_sede}</option>)}
                                            </select>
                                        </div>

                                        <button 
                                            onClick={handleAssign}
                                            disabled={!selectedSedeId} 
                                            className="w-full py-5 bg-emerald-600 text-white rounded-2xl text-[11px] font-black uppercase tracking-[0.2em] italic transition-all shadow-lg hover:brightness-110 disabled:opacity-20"
                                        >
                                            Confirmar Despliegue
                                        </button>
                                    </div>
                                </div>

                                {/* Lista de Operaciones */}
                                <div className="flex flex-col h-full">
                                    <h4 className="text-[10px] font-black text-white/20 uppercase tracking-[0.4em] italic mb-6 ml-2">Asignaciones de Campo</h4>
                                    
                                    <div className="bg-black/20 border border-white/5 rounded-[2.5rem] overflow-y-auto max-h-[350px] p-2 space-y-2">
                                        {loadingAssignments ? (
                                            <div className="p-10 text-center animate-pulse text-[9px] font-black text-white/20 uppercase tracking-widest italic">Sincronizando...</div>
                                        ) : (
                                            <>
                                                {currentAssignments.length > 0 ? (
                                                    currentAssignments.map(assignment => (
                                                        <div key={assignment.id} className="group/item flex items-center justify-between p-5 bg-white/5 border border-white/5 rounded-2xl hover:bg-white/10 transition-all duration-300">
                                                            <div>
                                                                <div className="text-[11px] font-black text-white italic uppercase tracking-tight group-hover/item:text-[#F69239] transition-colors leading-none mb-1">{assignment.empresa?.nombre}</div>
                                                                <div className="text-[9px] font-bold text-white/30 italic uppercase leading-none">{assignment.nombre_sede}</div>
                                                            </div>
                                                            <button onClick={() => handleRemove(assignment.id)} className="h-10 w-10 bg-red-600/10 border border-red-500/20 text-red-500 hover:bg-red-600 hover:text-white rounded-xl flex items-center justify-center transition-all opacity-0 group-hover/item:opacity-100 duration-500">
                                                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" strokeWidth="2"/></svg>
                                                            </button>
                                                        </div>
                                                    ))
                                                ) : (
                                                    <div className="p-10 text-center">
                                                        <p className="text-[9px] font-black text-white/5 uppercase tracking-[0.4em] italic leading-relaxed">Sin operaciones activas</p>
                                                    </div>
                                                )}
                                            </>
                                        )}
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div className="bg-black/40 px-10 py-6 border-t border-white/5 flex justify-end">
                            <button onClick={() => setShowAssignModal(false)} className="px-8 py-3 bg-white/5 text-white/40 hover:text-white rounded-xl text-[9px] font-black uppercase tracking-widest italic transition-all">
                                Cerrar Terminal
                            </button>
                        </div>
                    </div>
                </div>
            )}

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

function StatusBadge({ active, text }) {
    return (
        <span className={`inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[8px] font-black uppercase tracking-widest italic border ${active ? 'text-emerald-400 border-emerald-500/30 bg-emerald-500/10' : 'text-white/20 border-white/5 bg-white/5'}`}>
            <span className={`w-1 h-1 rounded-full ${active ? 'bg-emerald-500 animate-pulse' : 'bg-white/20'}`}></span>
            {text}
        </span>
    );
}
