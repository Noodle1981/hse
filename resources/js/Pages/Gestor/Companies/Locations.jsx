import { router, Head, Link, useForm, usePage } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { useState, useEffect } from 'react';

export default function Locations({ empresa, locations, provincias, availableUsers, filters }) {
    const { auth, flash } = usePage().props;
    
    const [search, setSearch] = useState(filters.search || '');
    const [showDeleted, setShowDeleted] = useState(filters.showDeleted === 'true' || filters.showDeleted === true);
    
    const [showForm, setShowForm] = useState(false);
    const [editingId, setEditingId] = useState(null);
    const [showUserModal, setShowUserModal] = useState(false);
    const [selectedLocation, setSelectedLocation] = useState(null);

    const form = useForm({
        id: null,
        nombre_sede: '',
        direccion: '',
        localidad: '',
        provincia_id: '',
        telefono: '',
        correo: '',
        tipo_sede: '',
        rubro: '',
        responsable_1_nombre: '',
        responsable_1_cargo: '',
        responsable_1_email: '',
        responsable_2_nombre: '',
        responsable_2_cargo: '',
        responsable_2_email: '',
        responsable_3_nombre: '',
        responsable_3_cargo: '',
        responsable_3_email: '',
        dias_operativos: [],
        hora_apertura: '',
        hora_cierre: '',
        es_roster_24_7: false,
        radio_tolerancia_metros: 500,
        latitud: '',
        longitud: '',
    });

    const updateFilters = () => {
        router.get(route('gestor.companies.locations', empresa.id), { search, showDeleted }, {
            preserveState: true,
            replace: true,
        });
    };

    useEffect(() => {
        const timeout = setTimeout(() => {
            if (search !== (filters.search || '')) updateFilters();
        }, 500);
        return () => clearTimeout(timeout);
    }, [search]);

    useEffect(() => {
        if (showDeleted !== (filters.showDeleted === 'true')) updateFilters();
    }, [showDeleted]);

    const openCreateModal = () => {
        form.reset();
        form.clearErrors();
        setEditingId(null);
        setShowForm(true);
    };

    const openEditModal = (location) => {
        form.clearErrors();
        setEditingId(location.id);
        form.setData({
            id: location.id,
            nombre_sede: location.nombre_sede || '',
            direccion: location.direccion || '',
            localidad: location.localidad || '',
            provincia_id: location.provincia_id || '',
            telefono: location.telefono || '',
            correo: location.correo || '',
            tipo_sede: location.tipo_sede || '',
            rubro: location.rubro || '',
            responsable_1_nombre: location.responsable_1_nombre || '',
            responsable_1_cargo: location.responsable_1_cargo || '',
            responsable_1_email: location.responsable_1_email || '',
            responsable_2_nombre: location.responsable_2_nombre || '',
            responsable_2_cargo: location.responsable_2_cargo || '',
            responsable_2_email: location.responsable_2_email || '',
            responsable_3_nombre: location.responsable_3_nombre || '',
            responsable_3_cargo: location.responsable_3_cargo || '',
            responsable_3_email: location.responsable_3_email || '',
            dias_operativos: location.dias_operativos || [],
            hora_apertura: location.hora_apertura || '',
            hora_cierre: location.hora_cierre || '',
            es_roster_24_7: !!location.es_roster_24_7,
            radio_tolerancia_metros: location.radio_tolerancia_metros ?? 500,
            latitud: location.latitud || '',
            longitud: location.longitud || '',
        });
        setShowForm(true);
    };

    const submit = (e) => {
        e.preventDefault();
        form.post(route('gestor.companies.locations.save', empresa.id), {
            onSuccess: () => setShowForm(false),
        });
    };

    const toggleUser = (userId) => {
        router.post(route('gestor.companies.locations.toggle-user', selectedLocation.id), {
            user_id: userId
        }, {
            preserveScroll: true,
            onSuccess: () => {
                // Sincronizar localmente si es necesario o dejar que Inertia recargue props
            }
        });
    };

    const handleDelete = (id) => {
        if(confirm('¿Confirmar baja de esta sede?')) {
            router.delete(route('gestor.companies.locations.delete', id));
        }
    };

    const handleRestore = (id) => {
        router.post(route('gestor.companies.locations.restore', id));
    };

    return (
        <AuthenticatedLayout title="Unidades de Campo">
            <Head title={`Sedes - ${empresa.nombre}`} />

            <div className="px-4 sm:px-6 lg:px-8 py-8 text-white selection:bg-emerald-500/30">
                <div className="max-w-[95rem] mx-auto space-y-8 animate-fade-in">
                    
                    {/* HUB DE FILTRADO */}
                    <div className="bg-[#16162a]/40 backdrop-blur-md rounded-[2.5rem] border border-white/5 p-6 shadow-2xl relative overflow-hidden animate-fade-in-down">
                        <div className="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-emerald-500/30 to-transparent"></div>
                        
                        <div className="flex flex-col lg:flex-row gap-6 justify-between items-center relative z-10">
                            <div className="flex items-center gap-4">
                                <Link href={route('gestor.companies.index')} className="w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-white/40 hover:text-white transition-all">
                                    <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="3" d="M15 19l-7-7 7-7"/></svg>
                                </Link>
                                <div>
                                    <h2 className="text-xl font-black text-white italic uppercase tracking-tight leading-none bg-gradient-to-r from-white to-white/40 bg-clip-text text-transparent">{empresa.nombre}</h2>
                                    <p className="text-[9px] font-black text-emerald-400 uppercase tracking-widest italic mt-1.5">Gestión de Sedes y Unidades</p>
                                </div>
                            </div>

                            <div className="w-full lg:w-1/3 relative group">
                                <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-emerald-400 uppercase tracking-[0.3em] italic z-10">Escaneo de Sedes</span>
                                <input id="search" name="search" 
                                    type="text" 
                                    value={search}
                                    onChange={e => setSearch(e.target.value)}
                                    placeholder="NOMBRE O LOCALIDAD..."
                                    className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-emerald-500/50 transition-all placeholder-white/5 italic uppercase"
                                />
                            </div>

                            <div className="flex items-center gap-6">
                                <label className="inline-flex items-center cursor-pointer group">
                                    <input id="showDeleted" name="showDeleted" type="checkbox" checked={showDeleted} onChange={e => setShowDeleted(e.target.checked)} className="sr-only peer" />
                                    <div className="relative w-11 h-6 bg-white/5 border border-white/10 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white/20 after:border-white/10 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-500/50"></div>
                                    <span className="ms-3 text-[10px] font-black text-white/40 uppercase tracking-widest italic group-hover:text-red-400 transition-colors">Bajas Históricas</span>
                                </label>

                                <button onClick={openCreateModal} 
                                      className="flex h-12 px-8 bg-emerald-600 text-white hover:brightness-110 active:scale-95 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] italic transition-all items-center gap-3 shadow-[0_0_20px_rgba(16,185,129,0.3)]">
                                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="3">
                                        <path d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Nueva Sede
                                </button>
                            </div>
                        </div>
                    </div>

                    {/* Tabla de Sedes */}
                    <div className="bg-[#16162a]/60 backdrop-blur-md rounded-[3rem] border border-white/5 shadow-2xl relative overflow-hidden animate-fade-in-up">
                        <div className="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-emerald-500/20 to-transparent"></div>
                        
                        <div className="overflow-x-auto">
                            <table className="w-full border-collapse min-w-[1000px]">
                                <thead>
                                    <tr className="bg-black/20 text-left">
                                        <th className="px-6 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none">Identificador Sede</th>
                                        <th className="px-6 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none">Ubicación / Matriz</th>
                                        <th className="px-6 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none text-center">Técnicos Asignados</th>
                                        <th className="px-6 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none text-right">Comandos</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-white/5">
                                    {locations.map(sede => (
                                        <tr key={sede.id} className={`group/row hover:bg-white/5 transition-all duration-500 relative ${sede.deleted_at ? 'bg-red-500/5' : ''}`}>
                                            <td className="px-6 py-6">
                                                <div className="flex items-center gap-4">
                                                    <div className="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 text-xs font-black group-hover:scale-110 transition-transform">
                                                        {sede.id}
                                                    </div>
                                                    <div>
                                                        <div className={`text-sm font-black text-white italic uppercase tracking-tight leading-none group-hover/row:text-emerald-400 transition-colors ${sede.deleted_at ? 'line-through opacity-40' : ''}`}>
                                                            {sede.nombre_sede}
                                                        </div>
                                                        <div className="text-[9px] font-bold text-white/20 uppercase tracking-widest mt-1.5 italic">
                                                            {sede.tipo_sede || 'SEDE OPERATIVA'}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td className="px-6 py-6">
                                                <div className="text-[11px] font-bold text-white/40 italic mb-1 group-hover/row:text-white/60 transition-colors uppercase leading-none">{sede.direccion}</div>
                                                <div className="text-[9px] font-black text-emerald-400/40 italic leading-none uppercase mb-1.5">{sede.localidad}, {sede.provincia?.nombre}</div>
                                                {sede.latitud && sede.longitud ? (
                                                    <div className="text-[8px] font-black text-emerald-500/60 uppercase tracking-widest leading-none italic flex items-center gap-1.5">
                                                        <span className="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                                                        GPS: {sede.latitud}, {sede.longitud} ({sede.radio_tolerancia_metros}m)
                                                    </div>
                                                ) : (
                                                    <div className="text-[8px] font-black text-red-500/40 uppercase tracking-widest leading-none italic flex items-center gap-1.5">
                                                        <span className="w-1.5 h-1.5 bg-red-500/50 rounded-full"></span>
                                                        SIN GPS CONFIGURADO
                                                    </div>
                                                )}
                                            </td>
                                            <td className="px-6 py-6 text-center">
                                                <button onClick={() => { setSelectedLocation(sede); setShowUserModal(true); }} className={`inline-flex items-center gap-2 px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest italic border transition-all ${sede.users.length > 0 ? 'text-blue-400 border-blue-500/20 bg-blue-500/10' : 'text-white/10 border-white/5 bg-white/5 hover:border-blue-500/20 hover:text-blue-400'}`}>
                                                    <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" strokeWidth="2"/></svg>
                                                    {sede.users.length} Operadores
                                                </button>
                                            </td>
                                            <td className="px-6 py-6 text-right">
                                                <div className="flex justify-end gap-3 transition-all duration-500">
                                                    {sede.deleted_at ? (
                                                        <button onClick={() => handleRestore(sede.id)} className="h-10 px-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 hover:bg-emerald-500 hover:text-white rounded-xl flex items-center gap-2 text-[9px] font-black uppercase tracking-widest italic transition-all">Restaurar</button>
                                                    ) : (
                                                        <>
                                                            <button onClick={() => openEditModal(sede)} className="h-10 w-10 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 hover:bg-emerald-500 hover:text-white rounded-xl flex items-center justify-center transition-all shadow-lg">
                                                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" strokeWidth="2"/></svg>
                                                            </button>
                                                            <button onClick={() => handleDelete(sede.id)} className="h-10 w-10 bg-red-600/10 border border-red-500/20 text-red-500 hover:bg-red-600 hover:text-white rounded-xl flex items-center justify-center transition-all shadow-lg">
                                                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" strokeWidth="2"/></svg>
                                                            </button>
                                                        </>
                                                    )}
                                                </div>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {/* Sede Form Modal */}
            {showForm && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 animate-fade-in overflow-y-auto">
                    <div className="absolute inset-0 bg-black/85 backdrop-blur-md" onClick={() => setShowForm(false)}></div>
                    <div className="bg-[#16162a] border border-white/10 rounded-[3rem] p-8 max-w-5xl w-full relative z-10 shadow-2xl overflow-hidden">
                        <div className="absolute inset-x-0 top-0 h-1 bg-emerald-500/50"></div>
                        <h3 className="text-xl font-black text-white italic mb-8 uppercase tracking-widest flex items-center gap-3">
                            <span className="w-2 h-8 bg-emerald-500 rounded-full"></span>
                            {editingId ? 'Parametrización de Sede' : 'Registro de Punto Operativo'}
                        </h3>
                        
                        <form onSubmit={submit} className="relative z-10 space-y-8 max-h-[70vh] overflow-y-auto pr-4 custom-scrollbar">
                            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <div className="space-y-2">
                                    <label className="text-[9px] font-black text-white/40 uppercase tracking-[0.3em] ml-4">Nombre de Sede</label>
                                    <input id="nombre_sede" name="nombre_sede" type="text" value={form.data.nombre_sede} onChange={e => form.setData('nombre_sede', e.target.value)} required 
                                        className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-emerald-500 transition-all uppercase"/>
                                </div>
                                <div className="space-y-2">
                                    <label className="text-[9px] font-black text-white/40 uppercase tracking-[0.3em] ml-4">Dirección</label>
                                    <input id="direccion" name="direccion" type="text" value={form.data.direccion} onChange={e => form.setData('direccion', e.target.value)}
                                        className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-emerald-500 transition-all uppercase"/>
                                </div>
                                <div className="space-y-2">
                                    <label className="text-[9px] font-black text-white/40 uppercase tracking-[0.3em] ml-4">Localidad</label>
                                    <input id="localidad" name="localidad" type="text" value={form.data.localidad} onChange={e => form.setData('localidad', e.target.value)}
                                        className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-emerald-500 transition-all uppercase"/>
                                </div>
                                <div className="space-y-2">
                                    <label className="text-[9px] font-black text-white/40 uppercase tracking-[0.3em] ml-4">Provincia</label>
                                    <select id="provincia_id" name="provincia_id" value={form.data.provincia_id} onChange={e => form.setData('provincia_id', e.target.value)} className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-emerald-500 transition-all [color-scheme:dark] italic uppercase">
                                        <option value="">SELECCIONE PROVINCIA...</option>
                                        {provincias.map(p => <option key={p.id} value={p.id}>{p.nombre}</option>)}
                                    </select>
                                </div>
                                <div className="space-y-2">
                                    <label className="text-[9px] font-black text-white/40 uppercase tracking-[0.3em] ml-4">Teléfono Sede</label>
                                    <input id="telefono" name="telefono" type="text" value={form.data.telefono} onChange={e => form.setData('telefono', e.target.value)}
                                        className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-emerald-500 transition-all uppercase"/>
                                </div>
                                <div className="space-y-2">
                                    <label className="text-[9px] font-black text-white/40 uppercase tracking-[0.3em] ml-4">Email Sede</label>
                                    <input id="correo" name="correo" type="email" value={form.data.correo} onChange={e => form.setData('correo', e.target.value)}
                                        className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-emerald-500 transition-all uppercase"/>
                                </div>
                            </div>

                            {/* Auditoría de Jornada y Roster 24/7 */}
                            <div className="pt-6 border-t border-white/5">
                                <div className="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 mb-6">
                                    <div>
                                        <h4 className="text-[10px] font-black text-emerald-400 uppercase tracking-[0.4em] italic ml-4">Auditoría de Jornada y GPS</h4>
                                        <p className="text-[8px] font-bold text-white/20 uppercase tracking-[0.2em] ml-4 mt-1">Parametrización horaria y tolerancia de presencia</p>
                                    </div>

                                    {/* Toggle Switch Roster 24/7 (Tactical Pill) */}
                                    <div className="flex items-center gap-4 bg-black/40 px-6 py-3 rounded-2xl border border-white/5">
                                        <span className="text-[9px] font-black text-white/40 uppercase tracking-widest italic">Roster Minero 24/7</span>
                                        <button 
                                            type="button"
                                            onClick={() => {
                                                const newVal = !form.data.es_roster_24_7;
                                                form.setData(data => ({
                                                    ...data,
                                                    es_roster_24_7: newVal,
                                                    // Si se activa, vaciamos dias y horas
                                                    dias_operativos: newVal ? [] : data.dias_operativos,
                                                    hora_apertura: newVal ? '' : data.hora_apertura,
                                                    hora_cierre: newVal ? '' : data.hora_cierre
                                                }));
                                            }}
                                            className={`px-6 py-2.5 rounded-xl text-[9px] font-black uppercase italic tracking-widest transition-all duration-300 border ${
                                                form.data.es_roster_24_7 
                                                    ? 'bg-[#F69239]/20 border-[#F69239] text-[#F69239] shadow-lg shadow-[#F69239]/20 scale-105' 
                                                    : 'bg-white/5 border-white/10 text-white/40 hover:text-white hover:border-white/20'
                                            }`}
                                        >
                                            {form.data.es_roster_24_7 ? 'ACTIVO 24/7' : 'DESACTIVADO'}
                                        </button>
                                    </div>
                                </div>

                                <div className="bg-white/5 p-6 rounded-[2.5rem] border border-white/5 space-y-6">
                                    {form.data.es_roster_24_7 ? (
                                        /* Roster 24/7 Activo - Mostrar Badge Informativo HUD */
                                        <div className="flex flex-col items-center justify-center py-6 border border-[#F69239]/10 rounded-2xl bg-[#F69239]/5 shadow-inner">
                                            <span className="text-xl font-black text-[#F69239] tracking-[0.4em] uppercase italic animate-pulse">
                                                ★ OPERACIÓN CONTINUA 24/7 ★
                                            </span>
                                            <p className="text-[9px] font-bold text-white/30 uppercase tracking-widest italic mt-2">
                                                MODALIDAD ROSTER MINERO HABILITADA. AUDITORÍA DE JORNADA Y CONTROL DOMINICAL OMITIDOS.
                                            </p>
                                        </div>
                                    ) : (
                                        /* Jornada Ordinaria - Mostrar selectores de días y horas */
                                        <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                            {/* Selector de Días Operativos (Píldoras Tácticas) */}
                                            <div className="space-y-3">
                                                <label className="text-[9px] font-black text-white/40 uppercase tracking-[0.3em] ml-2">Días de Operación Ordinaria</label>
                                                <div className="flex flex-wrap gap-2">
                                                    {['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'].map(day => {
                                                        const isSelected = form.data.dias_operativos.includes(day);
                                                        return (
                                                            <button
                                                                key={day}
                                                                type="button"
                                                                onClick={() => {
                                                                    const currentDays = [...form.data.dias_operativos];
                                                                    if (isSelected) {
                                                                        form.setData('dias_operativos', currentDays.filter(d => d !== day));
                                                                    } else {
                                                                        form.setData('dias_operativos', [...currentDays, day]);
                                                                    }
                                                                }}
                                                                className={`h-11 px-4 rounded-xl text-[10px] font-black uppercase italic tracking-wider transition-all border duration-300 ${
                                                                    isSelected
                                                                        ? 'bg-[#F69239] border-[#F69239] text-white shadow-md shadow-[#F69239]/20'
                                                                        : 'bg-black/40 border-white/10 text-white/40 hover:text-white hover:border-white/20'
                                                                }`}
                                                            >
                                                                {day.substring(0, 3)}
                                                            </button>
                                                        );
                                                    })}
                                                </div>
                                            </div>

                                            {/* Horas de apertura y cierre */}
                                            <div className="grid grid-cols-2 gap-4">
                                                <div className="space-y-2">
                                                    <label className="text-[9px] font-black text-white/40 uppercase tracking-[0.3em] ml-2">Apertura</label>
                                                    <input 
                                                        type="time" 
                                                        value={form.data.hora_apertura || ''} 
                                                        onChange={e => form.setData('hora_apertura', e.target.value)} 
                                                        className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-3 text-sm font-bold text-white focus:ring-2 focus:ring-emerald-500 transition-all [color-scheme:dark] italic"
                                                    />
                                                </div>
                                                <div className="space-y-2">
                                                    <label className="text-[9px] font-black text-white/40 uppercase tracking-[0.3em] ml-2">Cierre</label>
                                                    <input 
                                                        type="time" 
                                                        value={form.data.hora_cierre || ''} 
                                                        onChange={e => form.setData('hora_cierre', e.target.value)} 
                                                        className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-3 text-sm font-bold text-white focus:ring-2 focus:ring-emerald-500 transition-all [color-scheme:dark] italic"
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                    )}

                                    {/* Tolerancia GPS - Siempre visible para el módulo Proof of Presence */}
                                    <div className="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4 border-t border-white/5">
                                        <div className="space-y-2">
                                            <label className="text-[9px] font-black text-white/40 uppercase tracking-[0.3em] ml-2">Latitud GPS</label>
                                            <input 
                                                type="number" 
                                                step="any"
                                                value={form.data.latitud} 
                                                onChange={e => form.setData('latitud', e.target.value)} 
                                                placeholder="Ej: -31.372506"
                                                className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-3 text-sm font-bold text-white focus:ring-2 focus:ring-emerald-500 transition-all [color-scheme:dark]"
                                            />
                                            {form.errors.latitud && <span className="text-red-500 text-[9px] font-black italic ml-2 mt-1 block">{form.errors.latitud}</span>}
                                        </div>
                                        <div className="space-y-2">
                                            <label className="text-[9px] font-black text-white/40 uppercase tracking-[0.3em] ml-2">Longitud GPS</label>
                                            <input 
                                                type="number" 
                                                step="any"
                                                value={form.data.longitud} 
                                                onChange={e => form.setData('longitud', e.target.value)} 
                                                placeholder="Ej: -64.182416"
                                                className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-3 text-sm font-bold text-white focus:ring-2 focus:ring-emerald-500 transition-all [color-scheme:dark]"
                                            />
                                            {form.errors.longitud && <span className="text-red-500 text-[9px] font-black italic ml-2 mt-1 block">{form.errors.longitud}</span>}
                                        </div>
                                        <div className="space-y-2">
                                            <label className="text-[9px] font-black text-white/40 uppercase tracking-[0.3em] ml-2">Radio Tolerancia (Metros)</label>
                                            <input 
                                                type="number" 
                                                min="0"
                                                value={form.data.radio_tolerancia_metros} 
                                                onChange={e => form.setData('radio_tolerancia_metros', parseInt(e.target.value) || 0)} 
                                                className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-3 text-sm font-bold text-white focus:ring-2 focus:ring-emerald-500 transition-all uppercase"
                                            />
                                            {form.errors.radio_tolerancia_metros && <span className="text-red-500 text-[9px] font-black italic ml-2 mt-1 block">{form.errors.radio_tolerancia_metros}</span>}
                                        </div>
                                    </div>
                                    <div className="text-[9px] font-bold text-white/20 uppercase tracking-widest italic pl-2 pt-2">
                                        💡 Módulo Proof of Presence: Coordenadas geográficas y rango de tolerancia en metros para verificar el inicio y fin de visitas de campo.
                                    </div>
                                </div>
                            </div>

                            <div className="pt-6 border-t border-white/5">
                                <h4 className="text-[10px] font-black text-emerald-400 uppercase tracking-[0.4em] mb-6 italic ml-4">Matriz de Responsables</h4>
                                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 bg-white/5 p-6 rounded-[2.5rem] border border-white/5">
                                    {/* Responsable 1 */}
                                    <div className="space-y-4">
                                        <label className="text-[8px] font-black text-white/20 uppercase tracking-[0.3em] ml-2 italic">Primer Responsable</label>
                                        <input id="responsable_1_nombre" name="responsable_1_nombre" type="text" value={form.data.responsable_1_nombre} onChange={e => form.setData('responsable_1_nombre', e.target.value)} placeholder="NOMBRE COMPLETO" className="w-full bg-black/40 border border-white/5 rounded-xl px-4 py-3 text-xs text-white"/>
                                        <input id="responsable_1_cargo" name="responsable_1_cargo" type="text" value={form.data.responsable_1_cargo} onChange={e => form.setData('responsable_1_cargo', e.target.value)} placeholder="CARGO / FUNCIÓN" className="w-full bg-black/40 border border-white/5 rounded-xl px-4 py-3 text-xs text-white"/>
                                    </div>
                                    {/* Responsable 2 */}
                                    <div className="space-y-4">
                                        <label className="text-[8px] font-black text-white/20 uppercase tracking-[0.3em] ml-2 italic">Segundo Responsable</label>
                                        <input id="responsable_2_nombre" name="responsable_2_nombre" type="text" value={form.data.responsable_2_nombre} onChange={e => form.setData('responsable_2_nombre', e.target.value)} placeholder="NOMBRE COMPLETO" className="w-full bg-black/40 border border-white/5 rounded-xl px-4 py-3 text-xs text-white"/>
                                        <input id="responsable_2_cargo" name="responsable_2_cargo" type="text" value={form.data.responsable_2_cargo} onChange={e => form.setData('responsable_2_cargo', e.target.value)} placeholder="CARGO / FUNCIÓN" className="w-full bg-black/40 border border-white/5 rounded-xl px-4 py-3 text-xs text-white"/>
                                    </div>
                                    {/* Responsable 3 */}
                                    <div className="space-y-4">
                                        <label className="text-[8px] font-black text-white/20 uppercase tracking-[0.3em] ml-2 italic">Tercer Responsable</label>
                                        <input id="responsable_3_nombre" name="responsable_3_nombre" type="text" value={form.data.responsable_3_nombre} onChange={e => form.setData('responsable_3_nombre', e.target.value)} placeholder="NOMBRE COMPLETO" className="w-full bg-black/40 border border-white/5 rounded-xl px-4 py-3 text-xs text-white"/>
                                        <input id="responsable_3_cargo" name="responsable_3_cargo" type="text" value={form.data.responsable_3_cargo} onChange={e => form.setData('responsable_3_cargo', e.target.value)} placeholder="CARGO / FUNCIÓN" className="w-full bg-black/40 border border-white/5 rounded-xl px-4 py-3 text-xs text-white"/>
                                    </div>
                                </div>
                            </div>

                            <div className="flex justify-end gap-4 pt-4">
                                <button type="button" onClick={() => setShowForm(false)} className="px-8 py-4 bg-white/5 text-white/40 hover:text-white rounded-2xl text-[10px] font-black uppercase tracking-widest italic transition-all">Abortar</button>
                                <button type="submit" disabled={form.processing} className="px-10 py-4 bg-emerald-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest italic transition-all shadow-lg hover:brightness-110">
                                    {form.processing ? 'Procesando...' : (editingId ? 'Confirmar Sede' : 'Registrar Sede')}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}

            {/* User Assignment Modal */}
            {showUserModal && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 animate-fade-in">
                    <div className="absolute inset-0 bg-black/85 backdrop-blur-md" onClick={() => setShowUserModal(false)}></div>
                    <div className="bg-[#16162a] border border-white/10 rounded-[3rem] p-10 max-w-2xl w-full relative z-10 shadow-2xl overflow-hidden">
                        <div className="absolute inset-x-0 top-0 h-1 bg-blue-500/50"></div>
                        <h3 className="text-xl font-black text-white italic mb-2 uppercase tracking-tight flex items-center gap-3">Asignación de Operadores</h3>
                        <p className="text-[10px] font-black text-blue-400 uppercase tracking-widest italic mb-10 border-b border-white/5 pb-4">Unidad: <span className="text-white">{selectedLocation?.nombre_sede}</span></p>
                        
                        <div className="grid grid-cols-1 gap-4 max-h-[50vh] overflow-y-auto pr-4 custom-scrollbar">
                            {availableUsers.map(user => {
                                const isAssigned = selectedLocation?.users.some(u => u.id === user.id);
                                return (
                                    <div key={user.id} className="flex items-center justify-between p-4 bg-white/5 border border-white/5 rounded-2xl hover:bg-white/10 transition-all group">
                                        <div className="flex items-center gap-4">
                                            <div className="w-10 h-10 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400 text-xs font-black group-hover:scale-110 transition-transform">{user.nombres.charAt(0)}{user.apellidos.charAt(0)}</div>
                                            <div>
                                                <div className="text-xs font-black text-white italic uppercase tracking-tight leading-none mb-1">{user.apellidos}, {user.nombres}</div>
                                                <div className="text-[9px] font-bold text-white/20 italic tracking-widest">MAT: {user.matricula || '---'}</div>
                                            </div>
                                        </div>
                                        <button onClick={() => toggleUser(user.id)} className={`h-10 px-6 rounded-xl text-[9px] font-black uppercase tracking-widest italic transition-all ${isAssigned ? 'bg-red-600/10 text-red-500 border border-red-500/20 hover:bg-red-600 hover:text-white' : 'bg-emerald-600/10 text-emerald-500 border border-emerald-500/20 hover:bg-emerald-600 hover:text-white'}`}>
                                            {isAssigned ? 'Desvincular' : 'Vincular'}
                                        </button>
                                    </div>
                                );
                            })}
                        </div>
                        
                        <div className="flex justify-end mt-10">
                            <button onClick={() => setShowUserModal(false)} className="px-10 py-4 bg-white/5 text-white/40 hover:text-white rounded-2xl text-[10px] font-black uppercase tracking-widest italic transition-all">Cerrar Terminal</button>
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
                .custom-scrollbar::-webkit-scrollbar { width: 4px; }
                .custom-scrollbar::-webkit-scrollbar-track { background: rgba(255,255,255,0.02); border-radius: 10px; }
                .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(246,146,57,0.1); border-radius: 10px; }
                .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(246,146,57,0.3); }
            `}</style>
        </AuthenticatedLayout>
    );
}
