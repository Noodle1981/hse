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
    sedes = [] 
}) {
    // Inicialización segura de estados
    const [activeTab, setActiveTab] = useState(filters?.tab || 'inspections');
    const [search, setSearch] = useState(filters?.search || '');
    const [selectedMonth, setSelectedMonth] = useState(filters?.month || new Date().getMonth() + 1);
    const [empresaId, setEmpresaId] = useState(filters?.empresa_id || '');
    const [sedeId, setSedeId] = useState(filters?.sede_id || '');
    const [selectedStatus, setSelectedStatus] = useState(filters?.status || 'all');
    const [showFilters, setShowFilters] = useState(false);

    // Helper para rutas seguras en el componente
    const safeRoute = (name, params = null) => {
        try {
            return route(name, params);
        } catch (e) {
            console.warn(`[Ziggy] Component Route "${name}" not found.`);
            return '#';
        }
    };

    // Sincronizar estado local con props de Inertia
    useEffect(() => {
        if (filters?.tab && filters?.tab !== activeTab) setActiveTab(filters.tab);
        if (filters?.search !== undefined) setSearch(filters.search || '');
        if (filters?.month !== undefined) setSelectedMonth(filters.month);
        if (filters?.empresa_id !== undefined) setEmpresaId(filters.empresa_id || '');
        if (filters?.sede_id !== undefined) setSedeId(filters.sede_id || '');
        if (filters?.status !== undefined) setSelectedStatus(filters.status || 'all');
    }, [filters]);

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

    // Dinámica de Colores por Categoría
    const theme = useMemo(() => {
        const themes = {
            'inspections': {
                primary: 'text-ep-primary',
                bg: 'bg-ep-primary',
                border: 'border-ep-primary/20',
                shadow: 'shadow-ep-primary/20',
                header: 'Tarea Técnica',
                tabTitle: 'Inspecciones'
            },
            'trainings': {
                primary: 'text-indigo-500',
                bg: 'bg-indigo-600',
                border: 'border-indigo-500/20',
                shadow: 'shadow-indigo-600/20',
                header: 'Programa / Temario',
                tabTitle: 'Capacitaciones'
            },
            'reports': {
                primary: 'text-emerald-500',
                bg: 'bg-emerald-600',
                border: 'border-emerald-500/20',
                shadow: 'shadow-emerald-600/20',
                header: 'Título del Reporte',
                tabTitle: 'Informes'
            }
        };
        return themes[activeTab] || themes['inspections'];
    }, [activeTab]);

    // Normalización forzada de datos
    const displayData = useMemo(() => {
        const rawData = activeTab === 'inspections' ? inspections : (activeTab === 'trainings' ? trainings : reports);
        if (!rawData) return [];
        return Array.isArray(rawData) ? rawData : Object.values(rawData);
    }, [activeTab, inspections, trainings, reports]);

    const safeDate = (dateStr) => {
        if (!dateStr) return 'N/A';
        try {
            const date = new Date(dateStr);
            return isNaN(date.getTime()) ? 'N/A' : date.toLocaleDateString();
        } catch (e) { return 'N/A'; }
    };

    return (
        <AuthenticatedLayout 
            user={auth?.user} 
            header={
                <div className="flex flex-col">
                    <h1 className="text-xs font-black text-ep-primary uppercase tracking-widest italic leading-none mb-1">
                        Consola de <span className="text-white">Operaciones</span>
                    </h1>
                    <h2 className="text-sm font-bold text-indigo-100 uppercase tracking-tight leading-none italic">
                        Gestión de Misiones y Tareas
                    </h2>
                </div>
            }
        >
            <Head title="Consola de Operaciones" />

            <div className="bg-[#11111d] min-h-screen">
                <div className="px-4 py-6 lg:px-8 lg:py-8 selection:bg-ep-primary/30">
                    <div className="max-w-[100rem] mx-auto space-y-8">
                        
                        {/* HUD SUPERIOR: KPI + DESPLIEGUE */}
                        <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 animate-in fade-in slide-in-from-top-4 duration-700">
                            {/* KPIs Izquierda */}
                            <div className="lg:col-span-3 space-y-3">
                                <div className="bg-[#1a1a2e] rounded-3xl border border-white/5 p-4 flex items-center gap-4 shadow-2xl group hover:border-ep-primary/30 transition-all">
                                    <div className="relative w-12 h-12 flex items-center justify-center">
                                        <svg className="w-full h-full transform -rotate-90">
                                            <circle cx="24" cy="24" r="20" stroke="currentColor" strokeWidth="3.5" fill="transparent" className="text-white/5" />
                                            <circle cx="24" cy="24" r="20" stroke="currentColor" strokeWidth="3.5" fill="transparent" 
                                                strokeDasharray={125.6}
                                                strokeDashoffset={125.6 - (125.6 * (kpis?.percentage || 0)) / 100}
                                                className={`${theme?.primary || 'text-ep-primary'} transition-all duration-1000 ease-out`} 
                                                strokeLinecap="round"
                                            />
                                        </svg>
                                        <div className="absolute inset-0 flex items-center justify-center text-[10px] font-black italic text-white/90">{kpis?.percentage || 0}%</div>
                                    </div>
                                    <div>
                                        <div className="text-[7px] font-black text-white/20 uppercase tracking-[0.3em] mb-0.5 italic leading-none">Cumplimiento</div>
                                        <div className="text-sm font-black text-white italic tracking-tighter uppercase">Eficiencia</div>
                                    </div>
                                </div>

                                <div className="bg-[#1a1a2e] rounded-3xl border border-white/5 p-4 flex items-center gap-4 shadow-2xl group border-l-4 border-l-red-500/50 hover:bg-red-500/[0.02] transition-all">
                                    <div className="w-10 h-10 bg-red-500/10 rounded-xl flex items-center justify-center text-red-500 border border-red-500/20 animate-pulse">
                                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" strokeWidth="2.5"/></svg>
                                    </div>
                                    <div>
                                        <div className="text-[7px] font-black text-red-500/40 uppercase tracking-[0.3em] mb-0.5 italic leading-none">Críticos</div>
                                        <div className="text-xl font-black italic tracking-tighter leading-none text-white">{kpis?.critical_findings || 0}</div>
                                    </div>
                                </div>
                            </div>

                             {/* Despliegue Derecha */}
                             <div className={`lg:col-span-9 rounded-[2rem] border p-5 flex flex-col md:flex-row items-center justify-between gap-6 shadow-3xl overflow-hidden relative group transition-all duration-700
                                 ${activeVisit ? 'bg-ep-primary/5 border-ep-primary/20' : 'bg-[#1a1a2e]/60 border-white/5'}
                             `}>
                                <div className="flex flex-row items-center gap-5 relative z-10 flex-nowrap">
                                    <div className={`w-12 h-12 sm:w-14 sm:h-14 rounded-2xl flex items-center justify-center border flex-shrink-0 transition-all duration-700
                                         ${activeVisit ? 'bg-ep-primary/10 border-ep-primary/20 text-ep-primary shadow-[0_0_20px_rgba(246,146,57,0.2)] animate-pulse' : 'bg-white/5 border-white/5 text-white/10'}
                                    `}>
                                         <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z" strokeWidth="2"/></svg>
                                    </div>
                                    <div className="flex flex-col min-w-0">
                                        <div className={`text-[8px] font-black uppercase tracking-[0.4em] mb-1 italic leading-none transition-colors duration-700 ${activeVisit ? 'text-ep-primary' : 'text-white/20'} truncate`}>
                                            Estado: {activeVisit ? 'Misión Conectada' : 'Planificación'}
                                        </div>
                                        <h2 className={`text-xl sm:text-2xl font-black italic uppercase tracking-tighter leading-none transition-colors duration-700 ${activeVisit ? 'text-white' : 'text-white/40'} truncate`}>
                                            {activeVisit?.sede?.nombre_sede || 'Consola Desconectada'}
                                        </h2>
                                    </div>
                                </div>
 
                                <div className="relative z-10 flex-shrink-0 w-full md:w-auto mt-2 md:mt-0">
                                    {activeVisit ? (
                                        <Link href={safeRoute('tecnico.visits.in-progress', activeVisit?.id)} className="w-full md:w-auto text-center px-6 py-3.5 bg-ep-primary text-white rounded-xl text-[9px] font-black uppercase italic tracking-[0.3em] shadow-2xl shadow-ep-primary/20 hover:brightness-110 hover:scale-[1.03] active:scale-95 transition-all whitespace-nowrap inline-block">
                                            Reanudar Misión
                                        </Link>
                                    ) : (
                                        <Link href={safeRoute('gestor.operations.visits.create')} className="w-full md:w-auto text-center px-6 py-3.5 bg-ep-primary text-white rounded-xl text-[9px] font-black uppercase italic tracking-[0.3em] shadow-2xl shadow-ep-primary/20 hover:brightness-110 hover:scale-[1.03] active:scale-95 transition-all outline-none whitespace-nowrap inline-block">
                                            Nueva Visita
                                        </Link>
                                    )}
                                </div>
                             </div>
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
                                <div className="bg-[#1a1a2e] rounded-[1.5rem] border border-white/5 p-6 flex flex-col xl:flex-row gap-4 items-end shadow-2xl animate-in slide-in-from-top-2 duration-300">
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

                        {/* SELECTORES DE SECCIÓN (TAB CARDS) */}
                        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <SectionCard 
                                active={activeTab === 'inspections'} 
                                onClick={() => { setActiveTab('inspections'); updateFilters({ tab: 'inspections' }); }}
                                label="MISIONES"
                                title="INSPECCIONES"
                                count={(Array.isArray(inspections) ? inspections : []).length}
                                color="ep-primary"
                                icon={<svg className="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" strokeWidth="1.5"/></svg>}
                            />
                            <SectionCard 
                                active={activeTab === 'trainings'} 
                                onClick={() => { setActiveTab('trainings'); updateFilters({ tab: 'trainings' }); }}
                                label="EDUCACIÓN"
                                title="CAPACITACIONES"
                                count={(Array.isArray(trainings) ? trainings : []).length}
                                color="indigo-600"
                                icon={<svg className="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 14l9-5-9-5-9 5 9 5z" strokeWidth="1.5"/><path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" strokeWidth="1.5"/></svg>}
                            />
                            <SectionCard 
                                active={activeTab === 'reports'} 
                                onClick={() => { setActiveTab('reports'); updateFilters({ tab: 'reports' }); }}
                                label="MÉTRICAS"
                                title="INFORMES"
                                count={(Array.isArray(reports) ? reports : []).length}
                                color="emerald-600"
                                icon={<svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" strokeWidth="1.5"/></svg>}
                            />
                        </div>

                        {/* MATRIZ DE TAREAS TÉCNICAS (TABLA) */}
                        <div className={`bg-[#1a1a2e]/40 backdrop-blur-3xl rounded-[3rem] border shadow-3xl overflow-hidden transition-all duration-700 ${theme?.border || 'border-white/5'}`}>
                            <div className="overflow-x-auto">
                                <table className="w-full text-left">
                                    <thead>
                                        <tr className="bg-black/20 text-[9px] font-black text-white/20 uppercase italic tracking-[0.3em] border-b border-white/5 transition-colors duration-500">
                                            <th className="px-6 py-4">{theme?.header || 'Tarea'}</th>
                                            <th className="px-6 py-4">Ubicación</th>
                                            <th className="px-6 py-4">Vencimiento</th>
                                            <th className="px-6 py-4">Estado</th>
                                            <th className="px-8 py-4 text-right">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-white/[0.03]">
                                        {displayData.length > 0 ? displayData.map((item, idx) => (
                                            <tr key={`${activeTab}-${item?.id || idx}`} className="group hover:bg-white/[0.02] transition-colors">
                                                <td className="px-6 py-4">
                                                    <div className="flex items-center gap-4">
                                                        <div className={`w-10 h-10 rounded-xl bg-black/40 flex items-center justify-center border border-white/5 transition-all duration-300 group-hover:shadow-[0_0_10px_currentColor] ${theme?.primary || 'text-ep-primary'} group-hover:border-current`}>
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
                                                   <span className={`px-3 py-1 rounded-lg text-[8px] font-black uppercase italic tracking-widest border transition-all duration-300
                                                        ${item?.status === 'completed' || item?.status === 'finalizado' ? 'text-emerald-500 bg-emerald-500/5 border-emerald-500/20' : 
                                                          item?.status === 'in_progress' ? 'text-ep-primary bg-ep-primary/5 border-ep-primary/20 animate-pulse' : 'text-white/20 bg-white/5 border-white/5'}
                                                   `}>
                                                        {item?.status === 'in_progress' ? 'CURSO' : (item?.status === 'completed' || item?.status === 'finalizado') ? 'FINAL' : 'SOL'}
                                                   </span>
                                                </td>

                                                <td className="px-8 py-4 text-right">
                                                    <div className="flex items-center justify-end gap-2">
                                                        {item.status !== 'completed' && item.status !== 'finalizado' ? (
                                                            activeTab === 'reports' ? (
                                                                <Link
                                                                    href={safeRoute('master-reports.create', { 
                                                                        sede_id: item.sede_id, 
                                                                        periodo: `${new Date().getFullYear()}-${String(selectedMonth).padStart(2, '0')}` 
                                                                    })}
                                                                    className={`px-6 py-2.5 text-white rounded-xl text-[9px] font-black uppercase italic tracking-[0.2em] shadow-xl hover:scale-105 transition-all ${theme?.bg || 'bg-ep-primary'} ${theme?.shadow || ''} outline-none`}
                                                                >
                                                                    {item?.status === 'borrador' ? 'Continuar Redacción' : 'Ejecutar'}
                                                                </Link>
                                                            ) : item?.checklist_id ? (
                                                                <button
                                                                    onClick={() => router.post(safeRoute('tecnico.checklists.execute'), {
                                                                        type: item.type,
                                                                        id: item.id,
                                                                        source: item.source,
                                                                        sede_id: item.sede_id
                                                                    })}
                                                                    className={`px-6 py-2.5 text-white rounded-xl text-[9px] font-black uppercase italic tracking-[0.2em] shadow-xl hover:scale-105 transition-all ${theme?.bg || 'bg-ep-primary'} ${theme?.shadow || ''} outline-none`}
                                                                >
                                                                    {item?.status === 'in_progress' ? 'Reanudar' : 'Ejecutar'}
                                                                </button>
                                                            ) : (
                                                                <button disabled className="px-6 py-2.5 bg-white/5 text-white/20 rounded-xl text-[9px] font-black uppercase italic tracking-[0.2em] cursor-not-allowed">
                                                                    Ver
                                                                </button>
                                                            )
                                                        ) : (
                                                            <div className="flex items-center gap-2 text-emerald-500 bg-emerald-500/10 px-4 py-2 rounded-xl border border-emerald-500/20">
                                                                <svg className="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" strokeWidth="4"/></svg>
                                                                <span className="text-[8px] font-black uppercase italic tracking-widest leading-none">OK</span>
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

            <style dangerouslySetInnerHTML={{ __html: `
                .shadow-3xl { filter: drop-shadow(0 35px 60px rgba(0,0,0,0.6)); }
                select { background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%23ffffff20' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e"); background-position: right 1rem center; background-repeat: no-repeat; background-size: 1.5em 1.5em; padding-right: 2.5rem; -webkit-appearance: none; -moz-appearance: none; appearance: none; }
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
