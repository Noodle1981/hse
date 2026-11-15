import { Head, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '../../Layouts/AuthenticatedLayout';
import { useState, useMemo, useEffect } from 'react';
import { route } from 'ziggy-js';
import AssignmentTaskTable from './Partials/AssignmentTaskTable';
import QuickExecutionModal from './Partials/QuickExecutionModal';

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
}) {
    const [activeTab, setActiveTab] = useState(filters?.tab || 'inspections');
    const [search, setSearch] = useState(filters?.search || '');
    const [selectedMonth, setSelectedMonth] = useState(filters?.month || new Date().getMonth() + 1);
    const [empresaId, setEmpresaId] = useState(filters?.empresa_id || '');
    const [sedeId, setSedeId] = useState(filters?.sede_id || '');
    const [selectedStatus, setSelectedStatus] = useState(filters?.status || 'all');
    const [showFilters, setShowFilters] = useState(false);

    const [modalOpen, setModalOpen] = useState(false);
    const [selectedSedeId, setSelectedSedeId] = useState('');
    const [trainingForm, setTrainingForm] = useState({ title: '', duration: '15', description: '', modality: 'presencial' });
    const [selectedChecklistIdForConfig, setSelectedChecklistIdForConfig] = useState(null);
    const [modalSubTab, setModalSubTab] = useState('planned');
    const [modalSearchQuery, setModalSearchQuery] = useState('');
    const [modalSelectedCategory, setModalSelectedCategory] = useState('');
    const [modalSelectedSubcategory, setModalSelectedSubcategory] = useState('');

    useEffect(() => {
        if (modalOpen) {
            setModalSubTab('planned');
            setModalSearchQuery('');
            setModalSelectedCategory('');
            setModalSelectedSubcategory('');
            setSelectedChecklistIdForConfig(null);
        }
    }, [modalOpen]);

    const safeRoute = (name, params = null) => {
        try {
            return route(name, params);
        } catch (e) {
            return '#';
        }
    };

    useEffect(() => {
        if (filters?.tab && filters?.tab !== activeTab) setActiveTab(filters.tab);
        if (filters?.search !== undefined) setSearch(filters.search || '');
        if (filters?.month !== undefined) setSelectedMonth(filters.month);
        if (filters?.empresa_id !== undefined) setEmpresaId(filters.empresa_id || '');
        if (filters?.sede_id !== undefined) setSedeId(filters.sede_id || '');
        if (filters?.status !== undefined) setSelectedStatus(filters.status || 'all');
    }, [filters]);

    useEffect(() => {
        setSelectedSedeId(activeVisit ? activeVisit.sede_id.toString() : '');
    }, [activeVisit, modalOpen]);

    const months = [
        { id: 1, name: 'Enero' }, { id: 2, name: 'Febrero' }, { id: 3, name: 'Marzo' },
        { id: 4, name: 'Abril' }, { id: 5, name: 'Mayo' }, { id: 6, name: 'Junio' },
        { id: 7, name: 'Julio' }, { id: 8, name: 'Agosto' }, { id: 9, name: 'Septiembre' },
        { id: 10, name: 'Octubre' }, { id: 11, name: 'Noviembre' }, { id: 12, name: 'Diciembre' },
    ];

    const updateFilters = (newFilters) => {
        router.get(safeRoute('tecnico.checklists.tasks.index'), {
            tab: activeTab, search, month: selectedMonth, empresa_id: empresaId, sede_id: sedeId, status: selectedStatus, ...newFilters,
        }, { preserveState: true, preserveScroll: true });
    };

    const getGpsCoordinates = () => new Promise((resolve) => {
        if (typeof window === 'undefined' || !navigator.geolocation) return resolve(null);
        navigator.geolocation.getCurrentPosition(
            (pos) => resolve({ checkin_lat: pos.coords.latitude, checkin_lng: pos.coords.longitude }),
            () => resolve(null),
            { enableHighAccuracy: true, timeout: 5000, maximumAge: 0 }
        );
    });

    const handleExecute = async (item) => {
        const coords = await getGpsCoordinates();
        router.post(safeRoute('tecnico.checklists.execute'), {
            type: item.type, id: item.id, source: item.source, sede_id: item.sede_id, ...(coords || {}),
        }, { onSuccess: () => setModalOpen(false) });
    };

    const handleExecuteAdhoc = async (type, templateId, extraData = {}) => {
        if (!selectedSedeId) return;
        const coords = await getGpsCoordinates();
        router.post(safeRoute('tecnico.checklists.execute'), {
            type, id: templateId, source: 'adhoc', sede_id: selectedSedeId, ...extraData, ...(coords || {}),
        }, {
            onSuccess: () => {
                setModalOpen(false);
                setTrainingForm({ title: '', duration: '15', description: '', modality: 'presencial' });
                setSelectedChecklistIdForConfig(null);
            },
        });
    };

    const theme = useMemo(() => ({
        inspections: { primary: 'text-ep-primary', bg: 'bg-ep-primary', border: 'border-ep-primary/20', shadow: 'shadow-ep-primary/20', header: 'Inspección', tabTitle: 'Inspecciones', buttonLabel: 'Inspeccionar', glow: 'shadow-[0_0_25px_rgba(246,146,57,0.35)]', column4Label: 'Checklists' },
        trainings: { primary: 'text-indigo-400', bg: 'bg-indigo-600', border: 'border-indigo-500/20', shadow: 'shadow-indigo-600/20', header: 'Programa / Charla', tabTitle: 'Capacitaciones', buttonLabel: 'Iniciar Capacitación', glow: 'shadow-[0_0_25px_rgba(99,102,241,0.35)]', column4Label: 'Estado' },
        reports: { primary: 'text-emerald-400', bg: 'bg-emerald-600', border: 'border-emerald-500/20', shadow: 'shadow-emerald-600/20', header: 'Título del Reporte', tabTitle: 'Informes', buttonLabel: 'Ejecutar Informe', glow: 'shadow-[0_0_25px_rgba(16,185,129,0.35)]', column4Label: 'Estado' },
    }[activeTab] || {}), [activeTab]);

    const inspectionsArray = useMemo(() => (Array.isArray(inspections) ? inspections : Object.values(inspections || {})), [inspections]);
    const trainingsArray = useMemo(() => (Array.isArray(trainings) ? trainings : Object.values(trainings || {})), [trainings]);
    const reportsArray = useMemo(() => (Array.isArray(reports) ? reports : Object.values(reports || {})), [reports]);

    const displayData = activeTab === 'inspections' ? inspectionsArray : activeTab === 'trainings' ? trainingsArray : reportsArray;

    const safeDate = (dateStr) => {
        if (!dateStr) return 'N/A';
        const d = new Date(dateStr);
        return isNaN(d.getTime()) ? 'N/A' : d.toLocaleDateString();
    };

    const tabKpis = useMemo(() => {
        const countDone = (arr) => arr.filter(i => ['completed', 'finalizado', 'cerrado'].includes(i.status)).length;
        if (activeTab === 'inspections') {
            const pct = inspectionsArray.length ? Math.round((countDone(inspectionsArray) / inspectionsArray.length) * 100) : 0;
            return { title1: 'Plan de Inspecciones', val1: `${countDone(inspectionsArray)} / ${inspectionsArray.length}`, title2: 'Hallazgos Activos', val2: kpis.critical_findings || 0, title3: 'Eficacia Mensual', pct };
        }
        if (activeTab === 'trainings') {
            const pct = trainingsArray.length ? Math.round((countDone(trainingsArray) / trainingsArray.length) * 100) : 0;
            return { title1: 'Sesiones Completadas', val1: `${countDone(trainingsArray)} / ${trainingsArray.length}`, title2: 'Charlas del Mes', val2: trainingsArray.length, title3: 'Progreso de Plan', pct };
        }
        const drafts = reportsArray.filter(r => ['in_progress', 'borrador'].includes(r.status)).length;
        const pct = reportsArray.length ? Math.round((countDone(reportsArray) / reportsArray.length) * 100) : 0;
        return { title1: 'Informes Finalizados', val1: `${countDone(reportsArray)} / ${reportsArray.length}`, title2: 'Informes en Borrador', val2: drafts, title3: 'Cumplimiento', pct };
    }, [activeTab, inspectionsArray, trainingsArray, reportsArray, kpis]);

    const modalPlannedTasks = useMemo(() => {
        if (!selectedSedeId) return [];
        return displayData.filter(i => i.sede_id?.toString() === selectedSedeId.toString());
    }, [selectedSedeId, displayData]);

    const activeSedeName = useMemo(() => sedes.find(s => s.id.toString() === selectedSedeId.toString())?.nombre_sede || '', [selectedSedeId, sedes]);

    const modalCategories = useMemo(() => Array.from(new Set(checklistTemplates.map(t => t.category_relation?.name || t.categoryRelation?.name).filter(Boolean))).sort(), [checklistTemplates]);
    const modalSubcategories = useMemo(() => {
        if (!modalSelectedCategory) return [];
        return Array.from(new Set(checklistTemplates.filter(t => (t.category_relation?.name || t.categoryRelation?.name) === modalSelectedCategory).map(t => t.subcategory_relation?.name || t.subcategoryRelation?.name).filter(Boolean))).sort();
    }, [checklistTemplates, modalSelectedCategory]);

    const modalFilteredTemplates = useMemo(() => checklistTemplates.filter(t => {
        const cat = t.category_relation?.name || t.categoryRelation?.name;
        const sub = t.subcategory_relation?.name || t.subcategoryRelation?.name;
        if (modalSearchQuery) {
            const q = modalSearchQuery.toLowerCase();
            if (!t.title?.toLowerCase().includes(q) && !t.registry_code?.toLowerCase().includes(q)) return false;
        }
        if (modalSelectedCategory && cat !== modalSelectedCategory) return false;
        if (modalSelectedSubcategory && sub !== modalSelectedSubcategory) return false;
        return true;
    }), [checklistTemplates, modalSearchQuery, modalSelectedCategory, modalSelectedSubcategory]);

    return (
        <AuthenticatedLayout user={auth?.user} header={<h2 className="text-sm font-bold text-indigo-100 uppercase tracking-tight italic">Misiones de Campo y Cumplimiento</h2>}>
            <Head title="Consola de Operaciones" />

            <div className="bg-[#11111d] min-h-screen text-white px-4 py-6 lg:px-8 lg:py-8">
                <div className="max-w-[100rem] mx-auto space-y-8">
                    <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                        <div className="lg:col-span-6">
                            <div className={`rounded-[2rem] border p-5 flex items-center justify-between gap-6 ${activeVisit ? 'bg-ep-primary/5 border-ep-primary/20' : 'bg-[#1a1a2e]/60 border-white/5'}`}>
                                <div className="min-w-0">
                                    <div className={`text-[8px] font-black uppercase tracking-[0.4em] mb-1 italic ${activeVisit ? 'text-ep-primary' : 'text-white/20'}`}>
                                        Estado: {activeVisit ? 'Sesión en Progreso' : 'Planificación'}
                                    </div>
                                    <h2 className="text-lg sm:text-xl font-black italic uppercase tracking-tighter truncate">
                                        {activeVisit?.sede?.nombre_sede || 'Sin Visita Activa'}
                                    </h2>
                                </div>
                                {activeVisit ? (
                                    <Link href={safeRoute('tecnico.visits.in-progress', activeVisit?.id)} className="px-5 py-3 bg-ep-primary text-white rounded-xl text-[9px] font-black uppercase italic tracking-[0.3em] whitespace-nowrap">Reanudar Visita</Link>
                                ) : (
                                    <Link href={safeRoute('gestor.operations.visits.create')} className="px-5 py-3 bg-[#1e1e38] text-white/80 rounded-xl text-[9px] font-black uppercase italic tracking-[0.3em] border border-white/5 whitespace-nowrap">Registrar Entrada</Link>
                                )}
                            </div>
                        </div>

                        <div className="lg:col-span-6 grid grid-cols-3 gap-4">
                            <div className="bg-[#1a1a2e]/60 rounded-3xl border border-white/5 p-4 flex flex-col justify-between min-h-[5.5rem]">
                                <div className="text-[7px] font-black text-white/30 uppercase tracking-[0.2em] italic">{tabKpis.title1}</div>
                                <div className="text-lg font-black italic uppercase">{tabKpis.val1}</div>
                            </div>
                            <div className="bg-[#1a1a2e]/60 rounded-3xl border border-white/5 p-4 flex flex-col justify-between min-h-[5.5rem]">
                                <div className="text-[7px] font-black text-white/30 uppercase tracking-[0.2em] italic">{tabKpis.title2}</div>
                                <div className="text-lg font-black italic uppercase">{tabKpis.val2}</div>
                            </div>
                            <div className="bg-[#1a1a2e]/60 rounded-3xl border border-white/5 p-4 flex flex-col items-center justify-center gap-2 min-h-[5.5rem]">
                                <div className="text-lg font-black italic">{tabKpis.pct || 0}%</div>
                                <span className="text-[7px] font-black text-white/30 uppercase tracking-[0.2em] italic">{tabKpis.title3}</span>
                            </div>
                        </div>
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <SectionCard active={activeTab === 'inspections'} onClick={() => { setActiveTab('inspections'); updateFilters({ tab: 'inspections' }); }} label="MISIONES" title="INSPECCIONES" count={inspectionsArray.length} color="ep-primary" />
                        <SectionCard active={activeTab === 'trainings'} onClick={() => { setActiveTab('trainings'); updateFilters({ tab: 'trainings' }); }} label="EDUCACIÓN" title="CAPACITACIONES" count={trainingsArray.length} color="indigo-600" />
                        <SectionCard active={activeTab === 'reports'} onClick={() => { setActiveTab('reports'); updateFilters({ tab: 'reports' }); }} label="MÉTRICAS" title="INFORMES" count={reportsArray.length} color="emerald-600" />
                    </div>

                    <div className="space-y-4">
                        <button onClick={() => setShowFilters(!showFilters)} className="text-[10px] font-black text-ep-primary uppercase italic tracking-widest hover:text-white">
                            {showFilters ? 'OCULTAR FILTROS' : 'OPCIONES DE FILTRADO'}
                        </button>
                        {showFilters && (
                            <div className="bg-[#1a1a2e]/60 rounded-[1.5rem] border border-white/5 p-6 grid grid-cols-1 md:grid-cols-5 gap-4">
                                <input type="text" placeholder="Checklist..." value={search} onChange={e => setSearch(e.target.value)} onKeyPress={e => e.key === 'Enter' && updateFilters({ search })} className="bg-black/40 border border-white/5 rounded-xl px-4 py-3 text-[10px] font-bold text-white uppercase" />
                                <select value={selectedMonth} onChange={e => { setSelectedMonth(e.target.value); updateFilters({ month: e.target.value }); }} className="bg-black/40 border border-white/5 rounded-xl px-4 py-3 text-[10px] font-bold text-white uppercase [color-scheme:dark]">
                                    {months.map(m => <option key={m.id} value={m.id}>{m.name.toUpperCase()}</option>)}
                                </select>
                                <select value={empresaId} onChange={e => { setEmpresaId(e.target.value); setSedeId(''); updateFilters({ empresa_id: e.target.value, sede_id: '' }); }} className="bg-black/40 border border-white/5 rounded-xl px-4 py-3 text-[10px] font-bold text-white uppercase [color-scheme:dark]">
                                    <option value="">TODAS</option>
                                    {empresas.map(e => <option key={e.id} value={e.id}>{e.nombre.toUpperCase()}</option>)}
                                </select>
                                <select value={sedeId} onChange={e => { setSedeId(e.target.value); updateFilters({ sede_id: e.target.value }); }} disabled={!empresaId} className="bg-black/40 border border-white/5 rounded-xl px-4 py-3 text-[10px] font-bold text-white uppercase [color-scheme:dark] disabled:opacity-20">
                                    <option value="">TODAS</option>
                                    {sedes.filter(s => s.empresa_id == empresaId).map(s => <option key={s.id} value={s.id}>{s.nombre_sede.toUpperCase()}</option>)}
                                </select>
                                <select value={selectedStatus} onChange={e => { setSelectedStatus(e.target.value); updateFilters({ status: e.target.value }); }} className="bg-black/40 border border-white/5 rounded-xl px-4 py-3 text-[10px] font-bold text-white uppercase [color-scheme:dark]">
                                    <option value="all">TODOS</option>
                                    <option value="pending">SOLICITADA</option>
                                    <option value="in_progress">EN CURSO</option>
                                    <option value="completed">FINALIZADA</option>
                                </select>
                            </div>
                        )}
                    </div>

                    <div className="flex justify-center md:justify-start">
                        <button onClick={() => setModalOpen(true)} className={`w-full md:w-auto h-16 px-10 rounded-2xl flex items-center justify-center gap-3 text-xs font-black uppercase italic tracking-[0.2em] text-white border border-white/10 ${theme.bg} ${theme.glow}`}>
                            + {theme.buttonLabel}
                        </button>
                    </div>

                    <AssignmentTaskTable activeTab={activeTab} displayData={displayData} theme={theme} selectedMonth={selectedMonth} safeRoute={safeRoute} safeDate={safeDate} handleExecute={handleExecute} />
                </div>
            </div>

            <QuickExecutionModal
                modalOpen={modalOpen} setModalOpen={setModalOpen} activeTab={activeTab} activeVisit={activeVisit}
                sedes={sedes} selectedSedeId={selectedSedeId} setSelectedSedeId={setSelectedSedeId}
                modalSubTab={modalSubTab} setModalSubTab={setModalSubTab} theme={theme} activeSedeName={activeSedeName}
                modalPlannedTasks={modalPlannedTasks} selectedMonth={selectedMonth} safeRoute={safeRoute}
                handleExecute={handleExecute} handleExecuteAdhoc={handleExecuteAdhoc}
                modalSearchQuery={modalSearchQuery} setModalSearchQuery={setModalSearchQuery}
                modalSelectedCategory={modalSelectedCategory} setModalSelectedCategory={setModalSelectedCategory}
                modalSelectedSubcategory={modalSelectedSubcategory} setModalSelectedSubcategory={setModalSelectedSubcategory}
                modalCategories={modalCategories} modalSubcategories={modalSubcategories} modalFilteredTemplates={modalFilteredTemplates}
                selectedChecklistIdForConfig={selectedChecklistIdForConfig} setSelectedChecklistIdForConfig={setSelectedChecklistIdForConfig}
                trainingForm={trainingForm} setTrainingForm={setTrainingForm}
            />
        </AuthenticatedLayout>
    );
}

function SectionCard({ active, onClick, label, title, count, color }) {
    const colorClasses = {
        'ep-primary': active ? 'bg-ep-primary text-white -translate-y-1 border-orange-400/50' : 'bg-[#1a1a2e] border-white/5',
        'indigo-600': active ? 'bg-indigo-600 text-white -translate-y-1' : 'bg-[#1a1a2e] border-white/5',
        'emerald-600': active ? 'bg-emerald-600 text-white -translate-y-1' : 'bg-[#1a1a2e] border-white/5',
    };

    return (
        <button onClick={onClick} className={`relative p-5 rounded-[2rem] border transition-all duration-500 text-left shadow-2xl ${colorClasses[color] || ''} ${!active ? 'opacity-40 hover:opacity-100' : ''}`}>
            <div className={`text-[8px] font-black uppercase italic tracking-[0.3em] mb-1.5 ${active ? 'text-white/60' : 'text-white/20'}`}>{label}</div>
            <h3 className="text-xs font-black uppercase italic tracking-tighter leading-none mb-4 text-white">{title}</h3>
            <div className="flex items-end justify-between">
                <div className="text-3xl font-black italic tracking-tighter leading-none">{count || 0}</div>
                {active && <div className="w-1.5 h-1.5 rounded-full bg-white animate-pulse" />}
            </div>
        </button>
    );
}
