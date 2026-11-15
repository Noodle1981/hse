import { router, Head, Link, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { useState, useEffect } from 'react';
import LocationFormModal from './Partials/LocationFormModal';
import LocationUsersModal from './Partials/LocationUsersModal';

export default function Locations({ empresa, locations, provincias, availableUsers, filters }) {
    const [search, setSearch] = useState(filters.search || '');
    const [showDeleted, setShowDeleted] = useState(filters.showDeleted === 'true' || filters.showDeleted === true);
    const [showForm, setShowForm] = useState(false);
    const [editingId, setEditingId] = useState(null);
    const [showUserModal, setShowUserModal] = useState(false);
    const [selectedLocation, setSelectedLocation] = useState(null);

    const form = useForm({
        id: null, nombre_sede: '', direccion: '', localidad: '', provincia_id: '', telefono: '', correo: '',
        tipo_sede: '', rubro: '', responsable_1_nombre: '', responsable_1_cargo: '', responsable_1_email: '',
        responsable_2_nombre: '', responsable_2_cargo: '', responsable_2_email: '',
        responsable_3_nombre: '', responsable_3_cargo: '', responsable_3_email: '',
        dias_operativos: [], hora_apertura: '', hora_cierre: '', es_roster_24_7: false,
        radio_tolerancia_metros: 500, latitud: '', longitud: '',
    });

    const updateFilters = () => {
        router.get(route('gestor.companies.locations', empresa.id), { search, showDeleted }, { preserveState: true, replace: true });
    };

    useEffect(() => {
        const timeout = setTimeout(() => { if (search !== (filters.search || '')) updateFilters(); }, 500);
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
        form.post(route('gestor.companies.locations.save', empresa.id), { onSuccess: () => setShowForm(false) });
    };

    const toggleUser = (userId) => {
        router.post(route('gestor.companies.locations.toggle-user', selectedLocation.id), { user_id: userId }, { preserveScroll: true });
    };

    const handleDelete = (id) => {
        if (confirm('¿Confirmar baja de esta sede?')) router.delete(route('gestor.companies.locations.delete', id));
    };

    const handleRestore = (id) => router.post(route('gestor.companies.locations.restore', id));

    const parseTime = (timeStr) => {
        if (!timeStr) return { hour: '', minute: '' };
        const parts = timeStr.split(':');
        return { hour: parts[0] || '', minute: parts[1] || '' };
    };

    const handleTimeChange = (field, type, value) => {
        const { hour, minute } = parseTime(form.data[field] || '');
        let newHour = type === 'hour' ? value : hour;
        let newMinute = type === 'minute' ? value : minute;
        if (!newHour && !newMinute) form.setData(field, '');
        else form.setData(field, `${newHour || '08'}:${newMinute || '00'}`);
    };

    const hoursList = Array.from({ length: 24 }, (_, i) => String(i).padStart(2, '0'));
    const minutesList = Array.from({ length: 60 }, (_, i) => String(i).padStart(2, '0'));

    return (
        <AuthenticatedLayout title="Unidades de Campo">
            <Head title={`Sedes - ${empresa.nombre}`} />
            <div className="px-4 sm:px-6 lg:px-8 py-8 text-white selection:bg-emerald-500/30">
                <div className="max-w-[95rem] mx-auto space-y-8 animate-fade-in">
                    <div className="bg-[#16162a]/40 backdrop-blur-md rounded-[2.5rem] border border-white/5 p-6 shadow-2xl">
                        <div className="flex flex-col lg:flex-row gap-6 justify-between items-center">
                            <div className="flex items-center gap-4">
                                <Link href={route('gestor.companies.index')} className="w-10 h-10 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center text-white/40 hover:text-white transition-all">
                                    <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="3" d="M15 19l-7-7 7-7" /></svg>
                                </Link>
                                <div>
                                    <h2 className="text-xl font-black text-white italic uppercase tracking-tight leading-none">{empresa.nombre}</h2>
                                    <p className="text-[9px] font-black text-emerald-400 uppercase tracking-widest italic mt-1.5">Gestión de Sedes y Unidades</p>
                                </div>
                            </div>

                            <div className="w-full lg:w-1/3 relative">
                                <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-emerald-400 uppercase tracking-[0.3em] italic z-10">Escaneo de Sedes</span>
                                <input id="search" name="search" type="text" value={search} onChange={e => setSearch(e.target.value)} placeholder="NOMBRE O LOCALIDAD..." className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-emerald-500/50 transition-all placeholder-white/5 italic uppercase" />
                            </div>

                            <div className="flex items-center gap-6">
                                <label className="inline-flex items-center cursor-pointer group">
                                    <input id="showDeleted" name="showDeleted" type="checkbox" checked={showDeleted} onChange={e => setShowDeleted(e.target.checked)} className="sr-only peer" />
                                    <div className="relative w-11 h-6 bg-white/5 border border-white/10 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white/20 after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-500/50"></div>
                                    <span className="ms-3 text-[10px] font-black text-white/40 uppercase tracking-widest italic group-hover:text-red-400 transition-colors">Bajas Históricas</span>
                                </label>
                                <button onClick={openCreateModal} className="flex h-12 px-8 bg-emerald-600 text-white hover:brightness-110 active:scale-95 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] italic transition-all items-center gap-3 shadow-[0_0_20px_rgba(16,185,129,0.3)]">
                                    + Nueva Sede
                                </button>
                            </div>
                        </div>
                    </div>

                    <div className="bg-[#16162a]/60 backdrop-blur-md rounded-[3rem] border border-white/5 shadow-2xl overflow-hidden">
                        <div className="overflow-x-auto">
                            <table className="w-full border-collapse min-w-[1000px]">
                                <thead>
                                    <tr className="bg-black/20 text-left">
                                        <th className="px-6 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic">Identificador Sede</th>
                                        <th className="px-6 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic">Ubicación / Matriz</th>
                                        <th className="px-6 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic text-center">Técnicos Asignados</th>
                                        <th className="px-6 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic text-right">Comandos</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-white/5">
                                    {locations.map(sede => (
                                        <tr key={sede.id} className={`group/row hover:bg-white/5 transition-all ${sede.deleted_at ? 'bg-red-500/5' : ''}`}>
                                            <td className="px-6 py-6">
                                                <div className="flex items-center gap-4">
                                                    <div className="w-10 h-10 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 text-xs font-black">{sede.id}</div>
                                                    <div>
                                                        <div className={`text-sm font-black text-white italic uppercase ${sede.deleted_at ? 'line-through opacity-40' : ''}`}>{sede.nombre_sede}</div>
                                                        <div className="text-[9px] font-bold text-white/20 uppercase tracking-widest mt-1.5 italic">{sede.tipo_sede || 'SEDE OPERATIVA'}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td className="px-6 py-6">
                                                <div className="text-[11px] font-bold text-white/40 italic mb-1 uppercase">{sede.direccion}</div>
                                                <div className="text-[9px] font-black text-emerald-400/40 italic uppercase mb-1.5">{sede.localidad}, {sede.provincia?.nombre}</div>
                                                {sede.latitud && sede.longitud ? (
                                                    <div className="text-[8px] font-black text-emerald-500/60 uppercase tracking-widest italic">GPS: {sede.latitud}, {sede.longitud} ({sede.radio_tolerancia_metros}m)</div>
                                                ) : (
                                                    <div className="text-[8px] font-black text-red-500/40 uppercase tracking-widest italic">SIN GPS CONFIGURADO</div>
                                                )}
                                            </td>
                                            <td className="px-6 py-6 text-center">
                                                <button onClick={() => { setSelectedLocation(sede); setShowUserModal(true); }} className={`inline-flex items-center gap-2 px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest italic border transition-all ${sede.users.length > 0 ? 'text-blue-400 border-blue-500/20 bg-blue-500/10' : 'text-white/10 border-white/5 bg-white/5'}`}>
                                                    {sede.users.length} Operadores
                                                </button>
                                            </td>
                                            <td className="px-6 py-6 text-right">
                                                <div className="flex justify-end gap-3">
                                                    {sede.deleted_at ? (
                                                        <button onClick={() => handleRestore(sede.id)} className="h-10 px-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 hover:bg-emerald-500 hover:text-white rounded-xl text-[9px] font-black uppercase tracking-widest italic transition-all">Restaurar</button>
                                                    ) : (
                                                        <>
                                                            <button onClick={() => openEditModal(sede)} className="h-10 w-10 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 hover:bg-emerald-500 hover:text-white rounded-xl flex items-center justify-center transition-all">✎</button>
                                                            <button onClick={() => handleDelete(sede.id)} className="h-10 w-10 bg-red-600/10 border border-red-500/20 text-red-500 hover:bg-red-600 hover:text-white rounded-xl flex items-center justify-center transition-all">🗑</button>
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

            <LocationFormModal
                show={showForm}
                editingId={editingId}
                form={form}
                provincias={provincias}
                hoursList={hoursList}
                minutesList={minutesList}
                parseTime={parseTime}
                handleTimeChange={handleTimeChange}
                onClose={() => setShowForm(false)}
                onSubmit={submit}
            />

            <LocationUsersModal
                show={showUserModal}
                selectedLocation={selectedLocation}
                availableUsers={availableUsers}
                onToggleUser={toggleUser}
                onClose={() => setShowUserModal(false)}
            />
        </AuthenticatedLayout>
    );
}
