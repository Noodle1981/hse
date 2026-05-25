import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '../../Layouts/AuthenticatedLayout';
import { useState, useMemo, useEffect } from 'react';
import { route } from 'ziggy-js';

export default function MyAssignments({ 
    auth = {}, 
    inspections = [], 
    trainings = [], 
    reports = [], 
    kpis = { percentage: 0, critical_findings: 0 }, 
    activeVisit = null, 
    filters = {}, 
    empresas = [], 
    sedes = [],
    checklistTemplates = [],
    trainingTemplates = []
}) {
    const [activeTab, setActiveTab] = useState(filters?.tab || 'inspections');
    const [search, setSearch] = useState(filters?.search || '');
    const [selectedMonth, setSelectedMonth] = useState(filters?.month || new Date().getMonth() + 1);
    const [empresaId, setEmpresaId] = useState(filters?.empresa_id || '');
    const [sedeId, setSedeId] = useState(filters?.sede_id || '');
    const [selectedStatus, setSelectedStatus] = useState(filters?.status || 'all');
    const [showFilters, setShowFilters] = useState(false);

    // Modal state
    const [modalOpen, setModalOpen] = useState(false);
    const [selectedSedeId, setSelectedSedeId] = useState('');
    const [trainingForm, setTrainingForm] = useState({ title: '', duration: '15', description: '' });
    const [selectedChecklistForTraining, setSelectedChecklistForTraining] = useState('');
    const [modalSubTab, setModalSubTab] = useState('planned');

    useEffect(() => {
        if (modalOpen) {
            setModalSubTab('planned');
        }
    }, [modalOpen]);

    const safeRoute = (name, params = null) => {
        try {
            return route(name, params);
        } catch (e) {
            console.warn(`[Ziggy] Component Route "${name}" not found.`);
            return '#';
        }
    };

    // Sync filters with props
    useEffect(() => {
        if (filters?.tab && filters?.tab !== activeTab) setActiveTab(filters.tab);
        if (filters?.search !== undefined) setSearch(filters.search || '');
        if (filters?.month !== undefined) setSelectedMonth(filters.month);
        if (filters?.empresa_id !== undefined) setEmpresaId(filters.empresa_id || '');
        if (filters?.sede_id !== undefined) setSedeId(filters.sede_id || '');
        if (filters?.status !== undefined) setSelectedStatus(filters.status || 'all');
    }, [filters]);

    // Lock Sede in Modal if there is an active visit
    useEffect(() => {
        if (activeVisit) {
            setSelectedSedeId(activeVisit.sede_id.toString());
        } else {
            setSelectedSedeId('');
        }
    }, [activeVisit, modalOpen]);

    const months = [
        { id: 1, name: 'Enero' }, { id: 2, name: 'Febrero' }, { id: 3, name: 'Marzo' },
        { id: 4, name: 'Abril' }, { id: 5, name: 'Mayo' }, { id: 6, name: 'Junio' },
        { id: 7, name: 'Julio' }, { id: 8, name: 'Agosto' }, { id: 9, name: 'Septiembre' },
        { id: 10, name: 'Octubre' }, { id: 11, name: 'Noviembre' }, { id: 12, name: 'Diciembre' }
    ];

    const updateFilters = (newFilters) => {
        router.get(safeRoute('tecnico.checklists.tasks.index'), {
            tab: activeTab,
            search,
            month: selectedMonth,
            empresa_id: empresaId,
            sede_id: sedeId,
            status: selectedStatus,
            ...newFilters
        }, { preserveState: true, preserveScroll: true });
    };

    // Quick execute planned task
    const handleExecute = (item) => {
        router.post(safeRoute('tecnico.checklists.execute'), {
            type: item.type,
            id: item.id,
            source: item.source,
            sede_id: item.sede_id
        }, {
            onSuccess: () => setModalOpen(false)
        });
    };

    // Quick execute adhoc/free task
    const handleExecuteAdhoc = (type, templateId, extraData = {}) => {
        if (!selectedSedeId) return;
        router.post(safeRoute('tecnico.checklists.execute'), {
            type,
            id: templateId,
            source: 'adhoc',
            sede_id: selectedSedeId,
            ...extraData
        }, {
            onSuccess: () => {
                setModalOpen(false);
                // Reset form
                setTrainingForm({ title: '', duration: '15', description: '' });
                setSelectedChecklistForTraining('');
            }
        });
    };

    // Themes
    const theme = useMemo(() => {
        const themes = {
            'inspections': {
                primary: 'text-ep-primary',
                bg: 'bg-ep-primary',
                border: 'border-ep-primary/20',
                shadow: 'shadow-ep-primary/20',
                header: 'Inspección',
                tabTitle: 'Inspecciones',
                buttonLabel: 'Inspeccionar',
                badgeBg: 'bg-ep-primary/10',
                glow: 'shadow-[0_0_25px_rgba(246,146,57,0.35)]',
                accentColor: 'orange',
                column4Label: 'Checklists'
            },
            'trainings': {
                primary: 'text-indigo-400',
                bg: 'bg-indigo-600',
                border: 'border-indigo-500/20',
                shadow: 'shadow-indigo-600/20',
                header: 'Programa / Charla',
                tabTitle: 'Capacitaciones',
                buttonLabel: 'Iniciar Capacitación',
                badgeBg: 'bg-indigo-500/10',
                glow: 'shadow-[0_0_25px_rgba(99,102,241,0.35)]',
                accentColor: 'indigo',
                column4Label: 'Estado'
            },
            'reports': {
                primary: 'text-emerald-400',
                bg: 'bg-emerald-600',
                border: 'border-emerald-500/20',
                shadow: 'shadow-emerald-600/20',
                header: 'Título del Reporte',
                tabTitle: 'Informes',
                buttonLabel: 'Ejecutar Informe',
                badgeBg: 'bg-emerald-500/10',
                glow: 'shadow-[0_0_25px_rgba(16,185,129,0.35)]',
                accentColor: 'emerald',
                column4Label: 'Estado'
            }
        };
        return themes[activeTab] || themes['inspections'];
    }, [activeTab]);

    const inspectionsArray = useMemo(() => {
        if (!inspections) return [];
        return Array.isArray(inspections) ? inspections : Object.values(inspections);
    }, [inspections]);

    const trainingsArray = useMemo(() => {
        if (!trainings) return [];
        return Array.isArray(trainings) ? trainings : Object.values(trainings);
    }, [trainings]);

    const reportsArray = useMemo(() => {
        if (!reports) return [];
        return Array.isArray(reports) ? reports : Object.values(reports);
    }, [reports]);

    const displayData = useMemo(() => {
        const rawData = activeTab === 'inspections' ? inspectionsArray : (activeTab === 'trainings' ? trainingsArray : reportsArray);
        return rawData;
    }, [activeTab, inspectionsArray, trainingsArray, reportsArray]);

    const safeDate = (dateStr) => {
        if (!dateStr) return 'N/A';
        try {
            const date = new Date(dateStr);
            return isNaN(date.getTime()) ? 'N/A' : date.toLocaleDateString();
        } catch (e) { return 'N/A'; }
    };

    // Dynamic Tab-Specific KPIs
    const tabKpis = useMemo(() => {
        if (activeTab === 'inspections') {
            const total = inspectionsArray.length;
            const completed = inspectionsArray.filter(i => i.status === 'completed' || i.status === 'finalizado').length;
            const pct = total > 0 ? Math.round((completed / total) * 100) : 0;
            return {
                title1: 'Plan de Inspecciones',
                val1: `${completed} / ${total}`,
                title2: 'Hallazgos Activos',
                val2: kpis.critical_findings || 0,
                title3: 'Eficacia Mensual',
                val3: `${pct}%`,
                pct: pct
            };
        } else if (activeTab === 'trainings') {
            const total = trainingsArray.length;
            const completed = trainingsArray.filter(t => t.status === 'completed' || t.status === 'finalizado').length;
            const pct = total > 0 ? Math.round((completed / total) * 100) : 0;
            return {
                title1: 'Sesiones Completadas',
                val1: `${completed} / ${total}`,
                title2: 'Charlas del Mes',
                val2: total,
                title3: 'Progreso de Plan',
                val3: `${pct}%`,
                pct: pct
            };
        } else {
            const total = reportsArray.length;
            const completed = reportsArray.filter(r => r.status === 'completed' || r.status === 'finalizado' || r.status === 'cerrado').length;
            const drafts = reportsArray.filter(r => r.status === 'in_progress' || r.status === 'borrador').length;
            const pct = total > 0 ? Math.round((completed / total) * 100) : 0;
            return {
                title1: 'Informes Finalizados',
                val1: `${completed} / ${total}`,
                title2: 'Informes en Borrador',
                val2: drafts,
                title3: 'Cumplimiento',
                val3: `${pct}%`,
                pct: pct
            };
        }
    }, [activeTab, inspectionsArray, trainingsArray, reportsArray, kpis]);

    // Modal Specific calculations
    const modalPlannedTasks = useMemo(() => {
        if (!selectedSedeId) return [];
        if (activeTab === 'inspections') {
            return inspectionsArray.filter(i => i.sede_id?.toString() === selectedSedeId.toString());
        } else if (activeTab === 'trainings') {
            return trainingsArray.filter(t => t.sede_id?.toString() === selectedSedeId.toString());
        } else {
            return reportsArray.filter(r => r.sede_id?.toString() === selectedSedeId.toString());
        }
    }, [selectedSedeId, activeTab, inspectionsArray, trainingsArray, reportsArray]);

    const activeSedeName = useMemo(() => {
        if (!selectedSedeId) return '';
        const found = sedes.find(s => s.id.toString() === selectedSedeId.toString());
        return found ? found.nombre_sede : '';
    }, [selectedSedeId, sedes]);

    return (
        <AuthenticatedLayout 
            user={auth?.user} 
            header={
                <div className="flex flex-col">
                    <h1 className="text-xs font-black text-ep-primary uppercase tracking-widest italic leading-none mb-1">
                        Consola de <span className="text-white">Operaciones</span>
                    </h1>
                    <h2 className="text-sm font-bold text-indigo-100 uppercase tracking-tight leading-none italic">
                        Misiones de Campo y Cumplimiento
                    </h2>
                </div>
            }
        >
            <Head title="Consola de Operaciones" />

            <div className="bg-[#11111d] min-h-screen text-white">
                <div className="px-4 py-6 lg:px-8 lg:py-8 selection:bg-ep-primary/30">
                    <div className="max-w-[100rem] mx-auto space-y-8">
                        
                        {/* HUD DE BITÁCORA / SECCIÓN SUPERIOR */}
                        <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                            {/* Panel Despliegue Visita Activa */}
                            <div className="lg:col-span-6">
                                <div className={`rounded-[2rem] border p-5 flex items-center justify-between gap-6 shadow-3xl overflow-hidden relative group transition-all duration-700
                                    ${activeVisit ? 'bg-ep-primary/5 border-ep-primary/20' : 'bg-[#1a1a2e]/60 border-white/5'}
                                `}>
                                    <div className="flex flex-row items-center gap-4 relative z-10 flex-nowrap min-w-0">
                                        <div className={`w-12 h-12 rounded-2xl flex items-center justify-center border flex-shrink-0 transition-all duration-700
                                             ${activeVisit ? 'bg-ep-primary/10 border-ep-primary/20 text-ep-primary shadow-[0_0_20px_rgba(246,146,57,0.2)] animate-pulse' : 'bg-white/5 border-white/5 text-white/20'}
                                        `}>
                                             <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z" strokeWidth="2.5"/></svg>
                                        </div>
                                        <div className="flex flex-col min-w-0">
                                            <div className={`text-[8px] font-black uppercase tracking-[0.4em] mb-1 italic leading-none transition-colors duration-700 ${activeVisit ? 'text-ep-primary' : 'text-white/20'} truncate`}>
                                                Estado: {activeVisit ? 'Sesión en Progreso' : 'Planificación'}
                                            </div>
                                            <h2 className={`text-lg sm:text-xl font-black italic uppercase tracking-tighter leading-none transition-colors duration-700 ${activeVisit ? 'text-white' : 'text-white/40'} truncate`}>
                                                {activeVisit?.sede?.nombre_sede || 'Sin Visita Activa'}
                                            </h2>
                                        </div>
                                    </div>
                                    <div className="relative z-10 flex-shrink-0">
                                        {activeVisit ? (
                                            <Link href={safeRoute('tecnico.visits.in-progress', activeVisit?.id)} className="px-5 py-3 bg-ep-primary text-white rounded-xl text-[9px] font-black uppercase italic tracking-[0.3em] shadow-xl hover:brightness-110 hover:scale-[1.03] transition-all whitespace-nowrap inline-block">
                                                Reanudar Visita
                                            </Link>
                                        ) : (
                                            <Link href={safeRoute('gestor.operations.visits.create')} className="px-5 py-3 bg-[#1e1e38] text-white/80 rounded-xl text-[9px] font-black uppercase italic tracking-[0.3em] border border-white/5 hover:border-ep-primary/30 hover:text-white transition-all whitespace-nowrap inline-block">
                                                Registrar Entrada
                                            </Link>
                                        )}
                                    </div>
                                </div>
                            </div>

                            {/* DYNAMIC KPIs (Tab-Specific Indicators) */}
                            <div className="lg:col-span-6 grid grid-cols-3 gap-4">
                                <div className="bg-[#1a1a2e]/60 rounded-3xl border border-white/5 p-4 flex flex-col justify-between shadow-2xl group hover:border-white/10 transition-all min-h-[5.5rem]">
                                    <div className="text-[7px] font-black text-white/30 uppercase tracking-[0.2em] italic mb-2 leading-none">{tabKpis.title1}</div>
                                    <div className="text-lg font-black italic tracking-tighter uppercase text-white leading-none">{tabKpis.val1}</div>
                                </div>

                                <div className="bg-[#1a1a2e]/60 rounded-3xl border border-white/5 p-4 flex flex-col justify-between shadow-2xl group hover:border-white/10 transition-all min-h-[5.5rem]">
                                    <div className="text-[7px] font-black text-white/30 uppercase tracking-[0.2em] italic mb-2 leading-none">{tabKpis.title2}</div>
                                    <div className="text-lg font-black italic tracking-tighter uppercase text-white leading-none">{tabKpis.val2}</div>
                                </div>

                                <div className="bg-[#1a1a2e]/60 rounded-3xl border border-white/5 p-4 flex flex-col items-center justify-center gap-2 shadow-2xl relative min-h-[5.5rem] overflow-hidden">
                                    <div className="absolute inset-0 bg-gradient-to-br from-white/[0.01] to-transparent pointer-events-none"></div>
                                    <div className="relative w-11 h-11 flex items-center justify-center">
                                        <svg className="w-full h-full transform -rotate-90">
                                            <circle cx="22" cy="22" r="18" stroke="currentColor" strokeWidth="3" fill="transparent" className="text-white/5" />
                                            <circle cx="22" cy="22" r="18" stroke="currentColor" strokeWidth="3" fill="transparent" 
                                                strokeDasharray={113}
                                                strokeDashoffset={113 - (113 * (tabKpis.pct || 0)) / 100}
                                                className={`${theme.primary} transition-all duration-1000 ease-out`} 
                                                strokeLinecap="round"
                                            />
                                        </svg>
                                        <div className="absolute inset-0 flex items-center justify-center text-[9px] font-black italic text-white">{tabKpis.pct || 0}%</div>
                                    </div>
                                    <span className="text-[7px] font-black text-white/30 uppercase tracking-[0.2em] italic leading-none">{tabKpis.title3}</span>
                                </div>
                            </div>
                        </div>

                        {/* SELECTORES DE SECCIÓN (PILLAR CARDS) */}
                        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <SectionCard 
                                active={activeTab === 'inspections'} 
                                onClick={() => { setActiveTab('inspections'); updateFilters({ tab: 'inspections' }); }}
                                label="MISIONES"
                                title="INSPECCIONES"
                                count={inspectionsArray.length}
                                color="ep-primary"
                                icon={<svg className="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" strokeWidth="1.5"/></svg>}
                            />
                            <SectionCard 
                                active={activeTab === 'trainings'} 
                                onClick={() => { setActiveTab('trainings'); updateFilters({ tab: 'trainings' }); }}
                                label="EDUCACIÓN"
                                title="CAPACITACIONES"
                                count={trainingsArray.length}
                                color="indigo-600"
                                icon={<svg className="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 14l9-5-9-5-9 5 9 5z" strokeWidth="1.5"/><path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" strokeWidth="1.5"/></svg>}
                            />
                            <SectionCard 
                                active={activeTab === 'reports'} 
                                onClick={() => { setActiveTab('reports'); updateFilters({ tab: 'reports' }); }}
                                label="MÉTRICAS"
                                title="INFORMES"
                                count={reportsArray.length}
                                color="emerald-600"
                                icon={<svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" strokeWidth="1.5"/></svg>}
                            />
                        </div>

                        {/* FILTROS COMANDO */}
                        <div className="space-y-4">
                            <div className="flex items-center justify-between">
                                <button 
                                    onClick={() => setShowFilters(!showFilters)}
                                    className="flex items-center gap-3 text-[10px] font-black text-ep-primary uppercase italic tracking-widest hover:text-white transition-colors"
                                >
                                    <svg className={`w-4 h-4 transition-transform duration-300 ${showFilters ? 'rotate-180' : ''}`} fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" strokeWidth="3"/></svg>
                                    {showFilters ? 'OCULTAR FILTROS' : 'OPCIONES DE FILTRADO'}
                                </button>
                                {!showFilters && (search || empresaId || selectedStatus !== 'all') && (
                                    <div className="text-[8px] font-bold text-white/20 uppercase tracking-widest italic animate-fade-in">
                                        Filtros Activos: <span className="text-ep-primary">{search || 'GENERAL'}</span> • {selectedStatus.toUpperCase()}
                                    </div>
                                )}
                            </div>

                            {showFilters && (
                                <div className="bg-[#1a1a2e]/60 rounded-[1.5rem] border border-white/5 p-6 flex flex-col xl:flex-row gap-4 items-end shadow-2xl animate-in slide-in-from-top-2 duration-300">
                                    <FilterItem label="Búsqueda" flex="flex-1">
                                        <input id="search" name="search" 
                                            type="text" 
                                            placeholder="Checklist..."
                                            value={search}
                                            onChange={e => setSearch(e.target.value)}
                                            onKeyPress={e => e.key === 'Enter' && updateFilters({ search })}
                                            className="w-full bg-black/40 border border-white/5 rounded-xl px-4 py-3 text-[10px] font-bold text-white placeholder-white/5 italic focus:ring-ep-primary/50 transition-all uppercase"
                                        />
                                    </FilterItem>

                                    <FilterItem label="Mes" width="w-full xl:w-36">
                                        <select id="selectedMonth" name="selectedMonth" 
                                            value={selectedMonth}
                                            onChange={e => { setSelectedMonth(e.target.value); updateFilters({ month: e.target.value }); }}
                                            className="w-full bg-black/40 border border-white/5 rounded-xl px-4 py-3 text-[10px] font-bold text-white italic focus:ring-ep-primary/50 transition-all uppercase [color-scheme:dark]"
                                        >
                                            {months.map(m => <option key={m.id} value={m.id}>{m.name.toUpperCase()}</option>)}
                                        </select>
                                    </FilterItem>

                                    <FilterItem label="Entidad" width="w-full xl:w-44">
                                        <select id="empresaId" name="empresaId" 
                                            value={empresaId}
                                            onChange={e => { setEmpresaId(e.target.value); setSedeId(''); updateFilters({ empresa_id: e.target.value, sede_id: '' }); }}
                                            className="w-full bg-black/40 border border-white/5 rounded-xl px-4 py-3 text-[10px] font-bold text-white italic focus:ring-ep-primary/50 transition-all uppercase [color-scheme:dark]"
                                        >
                                            <option value="">TODAS</option>
                                            {(Array.isArray(empresas) ? empresas : []).map(e => <option key={e.id} value={e.id}>{e.nombre.toUpperCase()}</option>)}
                                        </select>
                                    </FilterItem>

                                    <FilterItem label="Sede" width="w-full xl:w-44">
                                        <select id="sedeId" name="sedeId" 
                                            value={sedeId}
                                            onChange={e => { setSedeId(e.target.value); updateFilters({ sede_id: e.target.value }); }}
                                            disabled={!empresaId}
                                            className="w-full bg-black/40 border border-white/5 rounded-xl px-4 py-3 text-[10px] font-bold text-white italic focus:ring-ep-primary/50 transition-all uppercase [color-scheme:dark] disabled:opacity-20"
                                        >
                                            <option value="">TODAS</option>
                                            {(Array.isArray(sedes) ? sedes : []).filter(s => s.empresa_id == empresaId).map(s => <option key={s.id} value={s.id}>{s.nombre_sede.toUpperCase()}</option>)}
                                        </select>
                                    </FilterItem>

                                    <FilterItem label="Estado" width="w-full xl:w-36">
                                        <select id="selectedStatus" name="selectedStatus" 
                                            value={selectedStatus}
                                            onChange={e => { setSelectedStatus(e.target.value); updateFilters({ status: e.target.value }); }}
                                            className="w-full bg-black/40 border border-white/5 rounded-xl px-4 py-3 text-[10px] font-bold text-white italic focus:ring-ep-primary/50 transition-all uppercase [color-scheme:dark]"
                                        >
                                            <option value="all">TODOS</option>
                                            <option value="pending">SOLICITADA</option>
                                            <option value="in_progress">EN CURSO</option>
                                            <option value="completed">FINALIZADA</option>
                                        </select>
                                    </FilterItem>
                                </div>
                            )}
                        </div>

                        {/* BIG INTERACTIVE BUTTON BEFORE TABLE */}
                        <div className="flex justify-center md:justify-start pt-2">
                            <button
                                onClick={() => setModalOpen(true)}
                                className={`w-full md:w-auto h-16 px-10 rounded-2xl flex items-center justify-center gap-3 text-xs font-black uppercase italic tracking-[0.2em] transition-all text-white border border-white/10 hover:border-white/20
                                    ${theme.bg} ${theme.glow} hover:scale-[1.02] active:scale-95`}
                            >
                                <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="3">
                                    <path d="M12 4v16m8-8H4" strokeLinecap="round" strokeLinejoin="round"/>
                                </svg>
                                {theme.buttonLabel}
                            </button>
                        </div>

                        {/* MATRIZ DE TAREAS OPERATIVAS (TABLA) */}
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
                                                                <span className={`text-[7px] font-black px-1 py-0.5 rounded border italic transition-colors duration-500
                                                                    ${item?.source === 'contract' ? 'text-indigo-400 bg-indigo-500/10 border-indigo-500/20' : 'text-emerald-400 bg-emerald-500/10 border-emerald-500/20'}
                                                                `}>
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
                                                     {activeTab === 'inspections' ? (
                                                         <div className="flex flex-col gap-1.5">
                                                             <div className="flex items-center gap-2">
                                                                 <span className="text-[11px] font-black text-white italic tracking-tight uppercase leading-none">
                                                                     {item?.checklist_count || 0} {item?.checklist_count === 1 ? 'registro' : 'registros'}
                                                                 </span>
                                                                 {item?.completed_count > 0 && (
                                                                     <>
                                                                         <span className="text-[9px] font-bold text-white/20 uppercase tracking-widest leading-none">|</span>
                                                                         <span className={`text-[10px] font-black uppercase italic tracking-tight leading-none flex items-center gap-1
                                                                             ${item?.passed_count === item?.completed_count ? 'text-emerald-400' : 'text-red-500'}
                                                                         `}>
                                                                             Exitosos: {item?.passed_count}/{item?.completed_count}
                                                                             {item?.passed_count !== item?.completed_count && <span>⚠️</span>}
                                                                         </span>
                                                                     </>
                                                                 )}
                                                             </div>
                                                             {item?.compliance_pct !== null && (
                                                                 <div className="flex items-center gap-2">
                                                                     <span className="text-[8px] font-bold text-white/30 uppercase tracking-[0.2em] leading-none italic">
                                                                         Cumplimiento:
                                                                     </span>
                                                                     <span className={`text-[10px] font-black leading-none
                                                                         ${item?.compliance_pct === 100 ? 'text-emerald-400 glow-text-emerald' : 'text-red-500 glow-text-red'}
                                                                     `}>
                                                                         {item?.compliance_pct}%
                                                                     </span>
                                                                 </div>
                                                             )}
                                                         </div>
                                                     ) : (
                                                         <span className={`px-3 py-1 rounded-lg text-[8px] font-black uppercase italic tracking-widest border transition-all duration-300
                                                             ${item?.status === 'completed' || item?.status === 'finalizado' ? 'text-emerald-500 bg-emerald-500/5 border-emerald-500/20' : 
                                                               item?.status === 'in_progress' ? 'text-ep-primary bg-ep-primary/5 border-ep-primary/20 animate-pulse' : 'text-white/20 bg-white/5 border-white/5'}
                                                         `}>
                                                             {item?.status === 'in_progress' ? 'CURSO' : (item?.status === 'completed' || item?.status === 'finalizado') ? 'FINAL' : 'SOL'}
                                                         </span>
                                                     )}
                                                 </td>

                                                <td className="px-8 py-4 text-right">
                                                    <div className="flex items-center justify-end gap-3 flex-wrap">
                                                        {/* Secondary Action: Historial (only for checklists with checklist_id) */}
                                                        {item?.checklist_id && (
                                                            <Link
                                                                href={safeRoute(
                                                                    activeTab === 'inspections' 
                                                                        ? 'tecnico.inspections.index' 
                                                                        : 'tecnico.trainings.index', 
                                                                    { checklist_id: item.checklist_id }
                                                                )}
                                                                className="px-4 py-2 border border-white/10 hover:border-white/20 hover:bg-white/5 text-white/70 hover:text-white rounded-xl text-[9px] font-black uppercase italic tracking-[0.2em] transition-all"
                                                            >
                                                                Historial
                                                            </Link>
                                                        )}

                                                        {/* Primary Action: Execute / Resume / OK */}
                                                        {activeTab === 'reports' ? (
                                                            item.status !== 'completed' && item.status !== 'finalizado' ? (
                                                                <Link
                                                                    href={safeRoute('master-reports.create', { 
                                                                        sede_id: item.sede_id, 
                                                                        periodo: `${new Date().getFullYear()}-${String(selectedMonth).padStart(2, '0')}` 
                                                                    })}
                                                                    className={`px-5 py-2.5 text-white rounded-xl text-[9px] font-black uppercase italic tracking-[0.2em] shadow-xl hover:scale-105 transition-all ${theme.bg} ${theme.shadow || ''} outline-none`}
                                                                >
                                                                    {item?.status === 'borrador' ? 'Reanudar' : 'Ejecutar'}
                                                                </Link>
                                                            ) : (
                                                                <div className="flex items-center gap-1.5 text-emerald-500 bg-emerald-500/10 px-4 py-2 rounded-xl border border-emerald-500/20">
                                                                    <svg className="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" strokeWidth="4"/></svg>
                                                                    <span className="text-[8px] font-black uppercase italic tracking-widest leading-none">OK</span>
                                                                </div>
                                                            )
                                                        ) : (
                                                            <div className="flex items-center gap-2">
                                                                {/* Status indicator for completed items */}
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

                    </div>
                </div>
            </div>

            {/* TAB-SPECIFIC ACTION MODALS */}
            {modalOpen && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
                    {/* Backdrop */}
                    <div 
                        className="absolute inset-0 bg-[#07070c]/90 backdrop-blur-sm transition-all duration-300 animate-fade-in"
                        onClick={() => setModalOpen(false)}
                    ></div>

                    {/* Modal Card */}
                    <div className="relative w-full max-w-4xl bg-[#141421] border border-white/10 rounded-[2.5rem] shadow-[0_25px_60px_rgba(0,0,0,0.8)] overflow-hidden flex flex-col max-h-[85vh] animate-scale-up z-10 text-white">
                        {/* Header glowing line */}
                        <div className={`absolute top-0 left-0 right-0 h-1 bg-gradient-to-r ${
                            activeTab === 'inspections' ? 'from-orange-500 to-amber-500' :
                            activeTab === 'trainings' ? 'from-indigo-600 to-blue-500' :
                            'from-emerald-500 to-teal-500'
                        }`}></div>

                        {/* Header */}
                        <div className="p-8 border-b border-white/5 bg-black/20 flex items-center justify-between">
                            <div>
                                <h3 className="text-xl font-black uppercase italic tracking-tighter">
                                    {activeTab === 'inspections' ? 'Consola de Inspección' :
                                     activeTab === 'trainings' ? 'Consola de Capacitación' :
                                     'Consola de Informe'}
                                </h3>
                                <p className="text-[10px] text-white/40 uppercase tracking-widest mt-1">
                                    {activeVisit ? `Ejecutando en Sede: ${activeVisit.sede?.nombre_sede}` : 'Establecer sede e iniciar ejecución'}
                                </p>
                            </div>
                            <button 
                                onClick={() => setModalOpen(false)}
                                className="p-2 bg-white/5 hover:bg-white/10 rounded-full text-white/50 hover:text-white transition-colors"
                            >
                                <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round"/></svg>
                            </button>
                        </div>

                        {/* Body */}
                        <div className="flex-1 overflow-y-auto p-8 space-y-6">
                            {/* SEDE SELECTOR (If visit not active) */}
                            {!activeVisit && (
                                <div className="bg-white/[0.02] border border-white/5 rounded-2xl p-6 space-y-3">
                                    <label className="text-[10px] font-black text-white/50 uppercase tracking-widest italic block">Seleccionar Sede Destino</label>
                                    <select
                                        value={selectedSedeId}
                                        onChange={e => setSelectedSedeId(e.target.value)}
                                        className="w-full bg-[#11111d] border border-white/10 rounded-xl px-5 py-4 text-xs font-black text-white focus:ring-ep-primary/50 transition-all italic uppercase [color-scheme:dark]"
                                    >
                                        <option value="">SELECCIONA UBICACIÓN...</option>
                                        {sedes.map(s => (
                                            <option key={s.id} value={s.id}>{s.nombre_sede.toUpperCase()}</option>
                                        ))}
                                    </select>
                                </div>
                            )}

                            {/* MAIN MODAL OPTIONS (Only displayed when Sede is selected or activeVisit is locked) */}
                            {selectedSedeId ? (
                                <div className="space-y-6">
                                    {/* Selector de Plan o Libre */}
                                    <div className="flex bg-[#11111d] p-1 rounded-2xl border border-white/5 w-full">
                                        <button
                                            type="button"
                                            onClick={() => setModalSubTab('planned')}
                                            className={`flex-1 py-3 px-6 rounded-xl text-[10px] font-black uppercase tracking-[0.15em] italic transition-all flex items-center justify-center gap-2 ${
                                                modalSubTab === 'planned' 
                                                    ? (activeTab === 'inspections' 
                                                        ? 'bg-orange-500 text-white shadow-lg shadow-orange-500/20' 
                                                        : activeTab === 'trainings'
                                                        ? 'bg-[#4f46e5] text-white shadow-lg shadow-[#4f46e5]/20'
                                                        : 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/20')
                                                    : 'text-white/40 hover:text-white/80'
                                            }`}
                                        >
                                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 00-2 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round"/>
                                            </svg>
                                            Según el Plan
                                        </button>
                                        <button
                                            type="button"
                                            onClick={() => setModalSubTab('adhoc')}
                                            className={`flex-1 py-3 px-6 rounded-xl text-[10px] font-black uppercase tracking-[0.15em] italic transition-all flex items-center justify-center gap-2 ${
                                                modalSubTab === 'adhoc' 
                                                    ? (activeTab === 'inspections' 
                                                        ? 'bg-orange-500 text-white shadow-lg shadow-orange-500/20' 
                                                        : activeTab === 'trainings'
                                                        ? 'bg-[#4f46e5] text-white shadow-lg shadow-[#4f46e5]/20'
                                                        : 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/20')
                                                    : 'text-white/40 hover:text-white/80'
                                            }`}
                                        >
                                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 4v16m8-8H4" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round"/>
                                            </svg>
                                            Ejecución Libre / Ad-hoc
                                        </button>
                                    </div>

                                    {/* 1. Tareas Planificadas */}
                                    {modalSubTab === 'planned' && (
                                        <div className="space-y-4 animate-fade-in">
                                            <h4 className={`text-xs font-black uppercase tracking-widest italic ${theme.primary}`}>
                                                Tareas Planificadas para {activeSedeName.toUpperCase() || 'ESTA SEDE'}
                                            </h4>
                                            
                                            {modalPlannedTasks.length === 0 ? (
                                                <p className="text-white/40 text-xs italic bg-white/[0.02] px-6 py-4 rounded-xl border border-white/5">
                                                    No hay tareas planificadas pendientes de {theme.tabTitle.toLowerCase()} para esta sede en este mes.
                                                </p>
                                            ) : (
                                                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                    {modalPlannedTasks.map(item => (
                                                        <div 
                                                            key={`modal-planned-${item.id}`}
                                                            className="bg-[#1b1b2d] border border-white/5 rounded-[1.5rem] p-5 flex flex-col justify-between hover:border-white/10 transition-all group/card relative overflow-hidden"
                                                        >
                                                            {/* Glow top border */}
                                                            <div className={`absolute top-0 left-0 right-0 h-[3px] bg-gradient-to-r opacity-30 group-hover/card:opacity-100 transition-opacity
                                                                ${activeTab === 'inspections' ? 'from-orange-500 to-amber-500' :
                                                                  activeTab === 'trainings' ? 'from-indigo-600 to-blue-500' :
                                                                  'from-emerald-500 to-teal-500'}`}
                                                            ></div>
                                                            <div>
                                                                <div className="text-[8px] font-black text-white/30 uppercase tracking-widest italic mb-1">PROGRAMADO • ID: {item.registry_code}</div>
                                                                <h5 className="text-xs font-black text-white uppercase italic leading-tight">{item.title}</h5>
                                                            </div>
                                                            
                                                            {activeTab === 'reports' ? (
                                                                <Link
                                                                    href={safeRoute('master-reports.create', { 
                                                                        sede_id: selectedSedeId, 
                                                                        periodo: `${new Date().getFullYear()}-${String(selectedMonth).padStart(2, '0')}` 
                                                                    })}
                                                                    onClick={() => setModalOpen(false)}
                                                                    className={`mt-4 w-full h-11 rounded-xl text-[9px] font-black uppercase tracking-widest italic transition-all flex items-center justify-center gap-2 border border-white/10
                                                                        ${theme.bg} hover:brightness-110 shadow-lg`}
                                                                >
                                                                    Iniciar
                                                                </Link>
                                                            ) : (
                                                                <button
                                                                    onClick={() => handleExecute(item)}
                                                                    className={`mt-4 w-full h-11 rounded-xl text-[9px] font-black uppercase tracking-widest italic transition-all flex items-center justify-center gap-2 border border-white/10
                                                                        ${theme.bg} hover:brightness-110 shadow-lg`}
                                                                >
                                                                    {item.status === 'in_progress' ? 'Reanudar' : 'Iniciar'}
                                                                </button>
                                                            )}
                                                        </div>
                                                    ))}
                                                </div>
                                            )}
                                        </div>
                                    )}

                                    {/* 2. Tareas Libres / Ad-hoc */}
                                    {modalSubTab === 'adhoc' && (
                                        <div className="space-y-4 animate-fade-in">
                                            <h4 className="text-xs font-black text-indigo-100/50 uppercase tracking-widest italic">
                                                Ejecución Libre / Ad-hoc
                                            </h4>

                                            {/* INSPECTIONS: Select a checklist template to start ad-hoc */}
                                            {activeTab === 'inspections' && (
                                                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                    {checklistTemplates.map(template => (
                                                        <div 
                                                            key={`adhoc-ins-${template.id}`}
                                                            className="bg-black/20 border border-white/5 rounded-2xl p-5 flex items-center justify-between hover:bg-white/[0.01] hover:border-white/10 transition-all"
                                                        >
                                                            <div>
                                                                <div className="text-[7px] font-bold text-white/30 uppercase tracking-wider mb-0.5">CHECKLIST</div>
                                                                <div className="text-xs font-black text-white uppercase italic leading-none">{template.title}</div>
                                                            </div>
                                                            <button 
                                                                onClick={() => handleExecuteAdhoc('inspection', template.id)}
                                                                className="px-4 py-2 bg-ep-primary/10 hover:bg-ep-primary text-ep-primary hover:text-white border border-ep-primary/20 hover:border-transparent rounded-xl text-[8px] font-black uppercase italic tracking-widest transition-all"
                                                            >
                                                                Iniciar
                                                            </button>
                                                        </div>
                                                    ))}
                                                </div>
                                            )}

                                            {/* TRAININGS: Express Training Form OR training templates */}
                                            {activeTab === 'trainings' && (
                                                <div className="space-y-6">
                                                    {/* Charla Express */}
                                                    <div className="bg-indigo-500/5 border border-indigo-500/10 rounded-[2rem] p-6 space-y-4">
                                                        <div>
                                                            <h5 className="text-xs font-black text-indigo-400 uppercase tracking-widest italic">Charla de 5 Minutos / Express</h5>
                                                            <p className="text-[9px] text-white/40 uppercase tracking-wider mt-0.5">Registra un adiestramiento rápido in-situ</p>
                                                        </div>
                                                        
                                                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                            <div className="space-y-2">
                                                                <label className="text-[9px] font-black text-white/40 uppercase tracking-widest block ml-1">Tema / Título</label>
                                                                <input 
                                                                    type="text" 
                                                                    value={trainingForm.title} 
                                                                    onChange={e => setTrainingForm({...trainingForm, title: e.target.value})} 
                                                                    className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-xs text-white italic font-bold focus:ring-indigo-500 focus:border-indigo-500 transition-all" 
                                                                    placeholder="Ej: Uso Correcto de EPP" 
                                                                />
                                                            </div>
                                                            <div className="space-y-2">
                                                                <label className="text-[9px] font-black text-white/40 uppercase tracking-widest block ml-1">Duración (Minutos)</label>
                                                                <select 
                                                                    value={trainingForm.duration} 
                                                                    onChange={e => setTrainingForm({...trainingForm, duration: e.target.value})} 
                                                                    className="w-full bg-[#11111d] border border-white/10 rounded-xl px-4 py-3 text-xs text-white italic font-bold focus:ring-indigo-500 transition-all [color-scheme:dark]"
                                                                >
                                                                    <option value="5">5 MINUTOS</option>
                                                                    <option value="10">10 MINUTOS</option>
                                                                    <option value="15">15 MINUTOS</option>
                                                                    <option value="30">30 MINUTOS</option>
                                                                    <option value="45">45 MINUTOS</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        
                                                        <div className="space-y-2">
                                                            <label className="text-[9px] font-black text-white/40 uppercase tracking-widest block ml-1">Descripción / Puntos Clave</label>
                                                            <textarea 
                                                                value={trainingForm.description} 
                                                                onChange={e => setTrainingForm({...trainingForm, description: e.target.value})} 
                                                                className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-xs text-white italic font-bold focus:ring-indigo-500 focus:border-indigo-500 transition-all" 
                                                                rows="2" 
                                                                placeholder="Ingrese una breve descripción de los puntos tratados..."
                                                            ></textarea>
                                                        </div>

                                                        <button 
                                                            onClick={() => handleExecuteAdhoc('training', null, { title: trainingForm.title, duration: trainingForm.duration, description: trainingForm.description })}
                                                            disabled={!trainingForm.title}
                                                            className="w-full h-11 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-[9px] font-black uppercase tracking-widest italic shadow-lg shadow-indigo-600/20 transition-all disabled:opacity-30 disabled:pointer-events-none"
                                                        >
                                                            Iniciar Charla Express
                                                        </button>
                                                    </div>

                                                    {/* Training with Checklist */}
                                                    <div className="bg-white/[0.02] border border-white/5 rounded-[2rem] p-6 space-y-4">
                                                        <div>
                                                            <h5 className="text-xs font-black text-white uppercase tracking-widest italic">Capacitación con Checklist Estándar</h5>
                                                            <p className="text-[9px] text-white/40 uppercase tracking-wider mt-0.5">Ejecuta una capacitación evaluada con preguntas estándar</p>
                                                        </div>
                                                        
                                                        <div className="flex flex-col md:flex-row gap-4 items-end">
                                                            <div className="flex-1 space-y-2 w-full">
                                                                <label className="text-[9px] font-black text-white/40 uppercase tracking-widest block ml-1">Seleccionar Plantilla Evaluativa</label>
                                                                <select
                                                                    value={selectedChecklistForTraining}
                                                                    onChange={e => setSelectedChecklistForTraining(e.target.value)}
                                                                    className="w-full bg-[#11111d] border border-white/10 rounded-xl px-4 py-3 text-xs text-white italic font-bold focus:ring-indigo-500 transition-all [color-scheme:dark]"
                                                                >
                                                                    <option value="">SELECCIONA CHECKLIST EVALUATIVO...</option>
                                                                    {checklistTemplates.map(c => (
                                                                        <option key={c.id} value={c.id}>{c.title}</option>
                                                                    ))}
                                                                </select>
                                                            </div>
                                                            <button 
                                                                onClick={() => handleExecuteAdhoc('training', selectedChecklistForTraining, { title: checklistTemplates.find(c => c.id == selectedChecklistForTraining)?.title })}
                                                                disabled={!selectedChecklistForTraining}
                                                                className="h-12 px-6 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-[9px] font-black uppercase tracking-widest italic shadow-lg shadow-indigo-600/20 transition-all disabled:opacity-30 disabled:pointer-events-none w-full md:w-auto"
                                                            >
                                                                Iniciar Capacitación
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            )}

                                            {/* REPORTS: Master report libre */}
                                            {activeTab === 'reports' && (
                                                <div className="bg-emerald-500/5 border border-emerald-500/10 rounded-[2rem] p-8 flex flex-col sm:flex-row items-center justify-between gap-6 relative overflow-hidden">
                                                    <div>
                                                        <h5 className="text-xs font-black text-emerald-400 uppercase tracking-widest italic">Informe Técnico Ad-hoc / Libre</h5>
                                                        <p className="text-[9px] text-white/40 uppercase tracking-wider mt-0.5">Comienza a redactar un informe maestro mensual libre para esta sede</p>
                                                    </div>
                                                    
                                                    <Link
                                                        href={safeRoute('master-reports.create', { 
                                                            sede_id: selectedSedeId, 
                                                            periodo: `${new Date().getFullYear()}-${String(selectedMonth).padStart(2, '0')}` 
                                                        })}
                                                        onClick={() => setModalOpen(false)}
                                                        className="w-full sm:w-auto px-6 h-12 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-[9px] font-black uppercase tracking-widest italic shadow-lg flex items-center justify-center gap-2 hover:scale-[1.03] active:scale-95 transition-all"
                                                    >
                                                        Redactar Informe
                                                    </Link>
                                                </div>
                                            )}
                                        </div>
                                    )}
                                </div>
                            ) : (
                                <div className="py-20 text-center text-white/30 animate-pulse">
                                    <svg className="w-12 h-12 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" strokeWidth="1.5"/><path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" strokeWidth="1.5"/></svg>
                                    <p className="text-xs font-black uppercase italic tracking-widest">Por favor, selecciona una sede para continuar</p>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            )}

            <style dangerouslySetInnerHTML={{ __html: `
                .shadow-3xl { filter: drop-shadow(0 35px 60px rgba(0,0,0,0.6)); }
                select { background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%23ffffff20' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e"); background-position: right 1rem center; background-repeat: no-repeat; background-size: 1.5em 1.5em; padding-right: 2.5rem; -webkit-appearance: none; -moz-appearance: none; appearance: none; }
                .glow-text-emerald { text-shadow: 0 0 8px rgba(52,211,153,0.5); }
                .glow-text-red { text-shadow: 0 0 8px rgba(239,68,68,0.5); }
            `}} />
        </AuthenticatedLayout>
    );
}

function FilterItem({ label, flex, width, children }) {
    return (
        <div className={`${flex || ''} ${width || ''} space-y-2.5`}>
            <label className="text-[9px] font-black text-ep-primary uppercase italic tracking-[0.4em] ml-1 block opacity-60">{label}</label>
            {children}
        </div>
    );
}

function SectionCard({ active, onClick, label, title, count, color, icon }) {
    const colorClasses = {
        'ep-primary': active ? 'bg-ep-primary text-white shadow-ep-primary/40 -translate-y-1 border-orange-400/50' : 'bg-[#1a1a2e] border-white/5 hover:border-ep-primary/30',
        'indigo-600': active ? 'bg-indigo-600 text-white shadow-indigo-600/40 -translate-y-1' : 'bg-[#1a1a2e] border-white/5 hover:border-indigo-600/30',
        'emerald-600': active ? 'bg-emerald-600 text-white shadow-emerald-600/40 -translate-y-1' : 'bg-[#1a1a2e] border-white/5 hover:border-emerald-600/30',
    };

    return (
        <button 
            onClick={onClick}
            className={`relative p-5 rounded-[2rem] border transition-all duration-500 text-left group overflow-hidden shadow-2xl ${colorClasses[color] || ''} ${!active ? 'opacity-40 hover:opacity-100' : ''}`}
        >
            <div className={`absolute top-0 right-0 p-5 transition-all duration-700 ${active ? 'opacity-20 scale-110 rotate-12 text-white' : 'opacity-[0.05] text-white group-hover:opacity-10'}`}>
                {icon}
            </div>
            
            <div className="relative z-10">
                <div className={`text-[8px] font-black uppercase italic tracking-[0.3em] mb-1.5 ${active ? 'text-white/60' : 'text-white/20'}`}>{label}</div>
                <h3 className={`text-xs font-black uppercase italic tracking-tighter leading-none mb-4 ${active ? 'text-white' : 'text-white'}`}>{title}</h3>
                
                <div className="flex items-end justify-between">
                    <div className="text-3xl font-black italic tracking-tighter leading-none">{count || 0}</div>
                    {active && <div className="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></div>}
                </div>
            </div>
        </button>
    );
}
