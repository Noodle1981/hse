import { router, Head, Link, useForm, usePage } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { useState, useEffect } from 'react';
import PendingSignaturesList from './Partials/PendingSignaturesList';

export default function Index({ users, filters }) {
    const { auth, flash } = usePage().props;
    
    const [search, setSearch] = useState(filters.search || '');
    const [roleFilter, setRoleFilter] = useState(filters.roleFilter || 'all');
    const [showDeleted, setShowDeleted] = useState(filters.showDeleted === 'true' || filters.showDeleted === true);
    
    const [showForm, setShowForm] = useState(false);
    const [editingId, setEditingId] = useState(null);
    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [userToDelete, setUserToDelete] = useState(null);

    // Visor de Firma Aprobada
    const [showSignaturePreview, setShowSignaturePreview] = useState(false);
    const [previewUser, setPreviewUser] = useState(null);

    const openSignaturePreview = (user) => {
        setPreviewUser(user);
        setShowSignaturePreview(true);
    };

    // Formulario unificado para Crear/Editar
    const form = useForm({
        id: null,
        nombres: '',
        apellidos: '',
        email: '',
        password: '',
        rol: 'tecnico',
        matricula: '',
        telefono: '',
        n_doc: '',
        estado: 'activo',
        foto: '',
    });

    // Sincronizar búsqueda y filtros con el servidor
    const updateFilters = () => {
        router.get(route('gestor.users.index'), {
            search,
            roleFilter,
            showDeleted,
        }, {
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

    // Filtros inmediatos
    useEffect(() => {
        if (roleFilter !== (filters.roleFilter || 'all') || showDeleted !== (filters.showDeleted === 'true')) {
            updateFilters();
        }
    }, [roleFilter, showDeleted]);

    const openCreateModal = () => {
        form.reset();
        form.clearErrors();
        setEditingId(null);
        setShowForm(true);
    };

    const openEditModal = (user) => {
        form.clearErrors();
        setEditingId(user.id);
        form.setData({
            id: user.id,
            nombres: user.nombres,
            apellidos: user.apellidos,
            email: user.email,
            password: '',
            rol: user.rol,
            matricula: user.matricula || '',
            telefono: user.telefono || '',
            n_doc: user.n_doc || '',
            estado: user.estado,
            foto: user.foto || '',
        });
        setShowForm(true);
    };

    const submit = (e) => {
        e.preventDefault();
        form.post(route('gestor.users.save'), {
            onSuccess: () => {
                setShowForm(false);
                form.reset();
            },
        });
    };

    const confirmDelete = (user) => {
        setUserToDelete(user);
        setShowDeleteModal(true);
    };

    const handleDelete = () => {
        router.delete(route('gestor.users.delete', userToDelete.id), {
            onSuccess: () => setShowDeleteModal(false),
        });
    };

    const handleRestore = (id) => {
        router.post(route('gestor.users.restore', id));
    };

    return (
        <AuthenticatedLayout 
            title="Matriz Identidades"
            header={
                <div className="flex items-center gap-4">
                    <Link 
                        href={route('gestor.administration.hub')} 
                        className="flex items-center justify-center w-10 h-10 rounded-xl bg-white/5 border border-white/10 hover:bg-[#F69239]/10 hover:border-[#F69239]/30 text-white/60 hover:text-white transition-all group/back shadow-lg"
                        title="Volver al Panel de Administración"
                    >
                        <svg className="w-5 h-5 transform group-hover/back:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={3} d="M15 19l-7-7 7-7" />
                        </svg>
                    </Link>
                    <div className="flex flex-col">
                        <h1 className="text-xs font-black text-ep-primary uppercase tracking-widest italic leading-none mb-1">
                            Administración
                        </h1>
                        <h2 className="text-sm font-bold text-white uppercase tracking-tight leading-none italic">
                            Matriz de Usuarios
                        </h2>
                    </div>
                </div>
            }
        >
            <Head title="Gestión de Usuarios" />

            <div className="px-4 sm:px-6 lg:px-8 py-8 text-white selection:bg-[#F69239]/30">
                <div className="max-w-[95rem] mx-auto space-y-12 animate-fade-in">
                    
                    {/* AUDITORÍA DE FIRMAS PENDIENTES */}
                    <PendingSignaturesList />

                    {/* HUB DE FILTRADO Y ACCIÓN MAESTRA */}
                    <div className="bg-[#16162a]/40 backdrop-blur-md rounded-[2rem] border border-white/5 p-8 shadow-2xl relative overflow-hidden animate-fade-in-down">
                        <div className="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-[#F69239]/30 to-transparent"></div>
                        
                        <div className="flex flex-col lg:flex-row gap-6 justify-between items-center relative z-10">
                            <div className="w-full lg:w-1/3 relative group">
                                <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-[#F69239] uppercase tracking-[0.3em] italic z-10">Terminal de Búsqueda</span>
                                <input id="search" name="search" 
                                    type="text" 
                                    value={search}
                                    onChange={e => setSearch(e.target.value)}
                                    placeholder="NOMBRE, EMAIL O MATRÍCULA..."
                                    className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-[#F69239]/50 transition-all placeholder-white/5 italic uppercase"
                                />
                            </div>

                            <div className="flex flex-wrap items-center gap-6 w-full lg:w-auto justify-end">
                                <div className="flex items-center gap-4 bg-black/20 px-6 py-3 rounded-2xl border border-white/5">
                                    <label className="inline-flex items-center cursor-pointer group">
                                        <input id="showDeleted" name="showDeleted" type="checkbox" checked={showDeleted} onChange={e => setShowDeleted(e.target.checked)} className="sr-only peer" />
                                        <div className="relative w-11 h-6 bg-white/5 border border-white/10 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white/20 after:border-white/10 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-500/50"></div>
                                        <span className="ms-3 text-[10px] font-black text-white/40 uppercase tracking-widest italic group-hover:text-red-400 transition-colors">Bajas Sistema</span>
                                    </label>
                                </div>

                                <select id="roleFilter" name="roleFilter" 
                                    value={roleFilter} 
                                    onChange={e => setRoleFilter(e.target.value)}
                                    className="bg-black/40 border border-white/10 rounded-2xl px-6 py-3 text-[10px] font-black text-white focus:ring-2 focus:ring-indigo-500/50 transition-all [color-scheme:dark] italic uppercase appearance-none cursor-pointer"
                                >
                                    <option value="all">TODOS LOS ROLES</option>
                                    <option value="gestor">GESTORES</option>
                                    <option value="tecnico">TÉCNICOS</option>
                                </select>

                                <button onClick={openCreateModal} 
                                        className="flex h-12 px-8 bg-[#F69239] text-white hover:brightness-110 active:scale-95 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] italic transition-all items-center gap-3 shadow-[0_0_20px_rgba(246,146,57,0.3)] group/btn relative overflow-hidden">
                                    <div className="absolute inset-0 bg-white/10 translate-y-12 group-hover/btn:translate-y-0 transition-transform"></div>
                                    <svg className="w-4 h-4 group-hover/btn:rotate-90 transition-transform duration-500 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="3">
                                        <path d="M12 4v16m8-8H4"/>
                                    </svg>
                                    <span className="relative z-10">Nuevo Usuario</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    {/* Form Modal */}
                    {showForm && (
                        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 animate-fade-in overflow-y-auto">
                            <div className="absolute inset-0 bg-black/80 backdrop-blur-sm" onClick={() => setShowForm(false)}></div>
                            <div className="bg-[#16162a] border border-white/10 rounded-[3rem] p-8 max-w-4xl w-full relative z-10 shadow-2xl overflow-hidden">
                                <div className="absolute inset-0 bg-gradient-to-br from-[#F69239]/5 to-transparent"></div>
                                <h3 className="text-xl font-black text-white italic mb-8 uppercase tracking-widest relative z-10 flex items-center gap-3">
                                    <span className="w-2 h-8 bg-[#F69239] rounded-full"></span>
                                    {editingId ? 'Actualizar Perfil' : 'Registro de Identidad'}
                                </h3>
                                
                                <form onSubmit={submit} className="relative z-10 space-y-8">
                                    {form.data.foto && (
                                        <div className="flex items-center gap-3 bg-white/5 border border-white/5 p-3 rounded-2xl max-w-sm animate-fade-in">
                                            <div className="w-12 h-12 rounded-xl border border-[#F69239]/30 overflow-hidden shrink-0">
                                                <img src={`/storage/${form.data.foto}`} alt="Foto de Perfil" className="w-full h-full object-cover" />
                                            </div>
                                            <div>
                                                <span className="text-[8px] font-black text-[#F69239] uppercase tracking-widest block italic mb-0.5">Identidad Digital</span>
                                                <span className="text-xs font-bold text-white uppercase italic">{form.data.nombres} {form.data.apellidos}</span>
                                            </div>
                                        </div>
                                    )}
                                    
                                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                        <div className="space-y-2">
                                            <label className="text-[9px] font-black text-white/40 uppercase tracking-[0.3em] ml-4">Nombres</label>
                                            <input id="nombres" name="nombres" type="text" value={form.data.nombres} onChange={e => form.setData('nombres', e.target.value)} required 
                                                className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-[#F69239] transition-all"/>
                                            {form.errors.nombres && <span className="text-red-500 text-[10px] font-black italic ml-4">{form.errors.nombres}</span>}
                                        </div>
                                        <div className="space-y-2">
                                            <label className="text-[9px] font-black text-white/40 uppercase tracking-[0.3em] ml-4">Apellidos</label>
                                            <input id="apellidos" name="apellidos" type="text" value={form.data.apellidos} onChange={e => form.setData('apellidos', e.target.value)} required 
                                                className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-[#F69239] transition-all"/>
                                            {form.errors.apellidos && <span className="text-red-500 text-[10px] font-black italic ml-4">{form.errors.apellidos}</span>}
                                        </div>
                                        <div className="space-y-2">
                                            <label className="text-[9px] font-black text-white/40 uppercase tracking-[0.3em] ml-4">Email Corporativo</label>
                                            <input id="email" name="email" type="email" value={form.data.email} onChange={e => form.setData('email', e.target.value.toLowerCase())} required 
                                                className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-[#F69239] transition-all lowercase"/>
                                            {form.errors.email && <span className="text-red-500 text-[10px] font-black italic ml-4">{form.errors.email}</span>}
                                        </div>
                                        <div className="space-y-2">
                                            <label className="text-[9px] font-black text-white/40 uppercase tracking-[0.3em] ml-4">Contraseña</label>
                                            <input id="password" name="password" type="password" value={form.data.password} onChange={e => form.setData('password', e.target.value)} required={!editingId} 
                                                className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-[#F69239] transition-all"/>
                                            {form.errors.password && <span className="text-red-500 text-[10px] font-black italic ml-4">{form.errors.password}</span>}
                                        </div>
                                        <div className="space-y-2">
                                            <label className="text-[9px] font-black text-white/40 uppercase tracking-[0.3em] ml-4">Rol Sistema</label>
                                            <select id="rol" name="rol" value={form.data.rol} onChange={e => form.setData('rol', e.target.value)} className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-[#F69239] transition-all [color-scheme:dark] italic uppercase">
                                                <option value="tecnico">TÉCNICO OPERATIVO</option>
                                                <option value="gestor">GESTOR ADMINISTRATIVO</option>
                                            </select>
                                        </div>
                                        <div className="space-y-2">
                                            <label className="text-[9px] font-black text-white/40 uppercase tracking-[0.3em] ml-4">Matrícula ID</label>
                                            <input id="matricula" name="matricula" type="text" value={form.data.matricula} onChange={e => form.setData('matricula', e.target.value.toUpperCase())} required 
                                                className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-[#F69239] transition-all uppercase"/>
                                            {form.errors.matricula && <span className="text-red-500 text-[10px] font-black italic ml-4">{form.errors.matricula}</span>}
                                        </div>
                                        <div className="space-y-2">
                                            <label className="text-[9px] font-black text-white/40 uppercase tracking-[0.3em] ml-4">Teléfono Enlace</label>
                                            <input id="telefono" name="telefono" type="text" value={form.data.telefono} onChange={e => form.setData('telefono', e.target.value)} required 
                                                className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-[#F69239] transition-all uppercase"/>
                                            {form.errors.telefono && <span className="text-red-500 text-[10px] font-black italic ml-4">{form.errors.telefono}</span>}
                                        </div>
                                        <div className="space-y-2">
                                            <label className="text-[9px] font-black text-white/40 uppercase tracking-[0.3em] ml-4">Nº Documento (Opcional)</label>
                                            <input id="n_doc" name="n_doc" type="text" value={form.data.n_doc} onChange={e => form.setData('n_doc', e.target.value.toUpperCase())} 
                                                className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-[#F69239] transition-all uppercase"/>
                                            {form.errors.n_doc && <span className="text-red-500 text-[10px] font-black italic ml-4">{form.errors.n_doc}</span>}
                                        </div>
                                        <div className="space-y-2">
                                            <label className="text-[9px] font-black text-white/40 uppercase tracking-[0.3em] ml-4">Estado</label>
                                            <select id="estado" name="estado" value={form.data.estado} onChange={e => form.setData('estado', e.target.value)} className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-[#F69239] transition-all [color-scheme:dark] italic uppercase">
                                                <option value="activo">ACTIVO</option>
                                                <option value="inactivo">INACTIVO</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div className="flex justify-end gap-4 pt-4">
                                        <button type="button" onClick={() => setShowForm(false)} className="px-8 py-4 bg-white/5 text-white/40 hover:text-white rounded-2xl text-[10px] font-black uppercase tracking-widest italic transition-all">
                                            Abortar Operación
                                        </button>
                                        <button type="submit" disabled={form.processing} className="px-10 py-4 bg-[#F69239] text-white rounded-2xl text-[10px] font-black uppercase tracking-widest italic transition-all shadow-lg shadow-[#F69239]/20 hover:brightness-110">
                                            {form.processing ? 'Procesando...' : (editingId ? 'Confirmar Actualización' : 'Inyectar Usuario')}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    )}

                    {/* User Table */}
                    <div className="bg-[#16162a]/60 backdrop-blur-md rounded-[3rem] border border-white/5 shadow-2xl relative overflow-hidden animate-fade-in-up">
                        <div className="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-indigo-500/20 to-transparent"></div>
                        
                        <div className="overflow-x-auto">
                            <table className="w-full border-collapse min-w-[1000px]">
                                <thead>
                                    <tr className="bg-black/20 text-left">
                                        <th className="px-6 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none">Operativo / Perfil</th>
                                        <th className="px-6 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none">Canal de Enlace</th>
                                        <th className="px-6 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none text-center">Rol HUD</th>
                                        <th className="px-6 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none text-center">Matrícula</th>
                                        <th className="px-6 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none text-center">Status</th>
                                        <th className="px-6 py-6 text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic leading-none text-right">Comandos</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-white/5">
                                    {users.data.map(user => (
                                        <tr key={user.id} className={`group/row hover:bg-white/5 transition-all duration-500 relative ${user.deleted_at ? 'bg-red-500/5' : ''}`}>
                                            <td className="px-6 py-6">
                                                <div className="flex items-center gap-4">
                                                    <div className="w-10 h-10 rounded-xl bg-[#F69239]/10 flex items-center justify-center text-[#F69239] text-xs font-black border border-[#F69239]/20 group-hover/row:scale-110 transition-transform">
                                                        {user.nombres?.charAt(0)}{user.apellidos?.charAt(0)}
                                                    </div>
                                                    <div>
                                                        <div className={`text-sm font-black text-white italic uppercase tracking-tight leading-none group-hover/row:text-[#F69239] transition-colors ${user.deleted_at ? 'line-through opacity-40' : ''}`}>
                                                            {user.apellidos}, {user.nombres}
                                                        </div>
                                                        {user.deleted_at && <span className="text-[8px] font-black text-red-500 uppercase tracking-widest mt-1 inline-block">BAJA DETECTADA</span>}
                                                    </div>
                                                </div>
                                            </td>
                                            <td className="px-6 py-6">
                                                <div className="text-[11px] font-bold text-white/40 italic group-hover/row:text-white/60 transition-colors">{user.email}</div>
                                                {user.n_doc && <div className="text-[9px] font-bold text-[#F69239]/50 uppercase tracking-wider mt-1">DNI: {user.n_doc}</div>}
                                            </td>
                                            <td className="px-6 py-6 text-center">
                                                <RoleBadge role={user.rol} />
                                            </td>
                                            <td className="px-6 py-6 text-center text-[11px] font-black text-white/60 italic uppercase tracking-widest">
                                                {user.matricula || '---'}
                                            </td>
                                            <td className="px-6 py-6 text-center">
                                                <StatusBadge active={user.estado === 'activo' && !user.deleted_at} text={user.deleted_at ? 'INACTIVO' : user.estado.toUpperCase()} />
                                            </td>
                                            <td className="px-6 py-6 text-right">
                                                <div className="flex justify-end gap-3">
                                                    {user.deleted_at ? (
                                                        <button onClick={() => handleRestore(user.id)} className="h-10 px-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 hover:bg-emerald-500 hover:text-white rounded-xl flex items-center gap-2 text-[9px] font-black uppercase tracking-widest italic transition-all shadow-lg">
                                                            Restaurar
                                                        </button>
                                                    ) : (
                                                        <>
                                                            {user.firma && (
                                                                <button onClick={() => openSignaturePreview(user)} className="h-10 w-10 bg-amber-500/10 border border-amber-500/20 text-amber-400 hover:bg-amber-500 hover:text-white rounded-xl flex items-center justify-center transition-all shadow-lg" title="Ver Firma Aprobada">
                                                                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                                    </svg>
                                                                </button>
                                                            )}
                                                            <button onClick={() => openEditModal(user)} className="h-10 w-10 bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 hover:bg-indigo-500 hover:text-white rounded-xl flex items-center justify-center transition-all shadow-lg">
                                                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" strokeWidth="2"/></svg>
                                                            </button>
                                                            {user.id !== auth.user.id && (
                                                                <button onClick={() => confirmDelete(user)} className="h-10 w-10 bg-red-600/10 border border-red-500/20 text-red-500 hover:bg-red-600 hover:text-white rounded-xl flex items-center justify-center transition-all shadow-lg">
                                                                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" strokeWidth="2"/></svg>
                                                                </button>
                                                            )}
                                                        </>
                                                    )}
                                                </div>
                                            </td>
                                        </tr>
                                    ))}
                                    {users.data.length === 0 && (
                                        <tr>
                                            <td colSpan="6" className="px-8 py-32 text-center text-white/5 uppercase font-black text-sm tracking-[0.4em] italic leading-relaxed">
                                                Matriz de identidades vacía <br /> 
                                                <span className="text-[9px] tracking-widest text-white/3 italic">Inyecte un nuevo usuario para iniciar el registro</span>
                                            </td>
                                        </tr>
                                    )}
                                </tbody>
                            </table>
                        </div>

                        {/* Pagination */}
                        <div className="px-10 py-6 border-t border-white/5 bg-black/20 flex items-center justify-between">
                            <div className="text-[10px] font-bold text-white/20 uppercase italic">
                                Mostrando {users.from} a {users.to} de {users.total} identidades
                            </div>
                            <div className="flex gap-2">
                                {users.links.map((link, i) => (
                                    <Link 
                                        key={i}
                                        href={link.url || '#'}
                                        dangerouslySetInnerHTML={{ __html: link.label }}
                                        className={`px-4 py-2 rounded-xl text-[10px] font-black transition-all border ${
                                            link.active 
                                                ? 'bg-[#F69239]/10 border-[#F69239]/30 text-[#F69239]' 
                                                : 'bg-white/5 border-white/5 text-white/20 hover:text-white'
                                        } ${!link.url && 'opacity-30 cursor-not-allowed'}`}
                                    />
                                ))}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {/* Deletion Modal */}
            {showDeleteModal && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 animate-fade-in">
                    <div className="absolute inset-0 bg-black/80 backdrop-blur-sm" onClick={() => setShowDeleteModal(false)}></div>
                    <div className="bg-[#16162a] border border-white/10 rounded-[3rem] p-10 max-w-lg w-full relative z-10 shadow-2xl text-center overflow-hidden">
                        <div className="absolute inset-x-0 top-0 h-1 bg-red-500/50"></div>
                        <div className="w-20 h-20 bg-red-500/20 rounded-3xl flex items-center justify-center mx-auto mb-6 text-red-500 border border-red-500/20">
                            <svg className="w-10 h-10 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={3} d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <h3 className="text-xl font-black text-white uppercase italic tracking-widest mb-4">Revocar Acceso</h3>
                        <p className="text-sm text-white/40 mb-10 font-bold leading-relaxed italic uppercase">
                            ESTÁ A PUNTO DE DAR DE BAJA A <span className="text-red-400">{userToDelete?.nombres} {userToDelete?.apellidos}</span>.<br/><br/>
                            EL REGISTRO NO SE ELIMINARÁ PERMANENTEMENTE PARA PRESERVAR EL HISTORIAL FORENSE.
                        </p>
                        <div className="flex justify-center gap-4">
                            <button onClick={() => setShowDeleteModal(false)} className="px-8 py-4 bg-white/5 text-white/40 hover:text-white rounded-2xl text-[10px] font-black uppercase tracking-widest italic transition-all">
                                Cancelar
                             </button>
                            <button onClick={handleDelete} className="px-10 py-4 bg-red-600 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest italic transition-all shadow-lg animate-pulse">
                                Confirmar Baja
                            </button>
                        </div>
                    </div>
                </div>
            )}

            {/* Signature Preview Modal */}
            {showSignaturePreview && previewUser && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 animate-fade-in">
                    <div className="absolute inset-0 bg-black/80 backdrop-blur-sm" onClick={() => setShowSignaturePreview(false)}></div>
                    <div className="bg-[#16162a] border border-white/10 rounded-[3rem] p-10 max-w-lg w-full relative z-10 shadow-2xl text-center overflow-hidden">
                        <div className="absolute inset-x-0 top-0 h-1 bg-amber-500/50"></div>
                        <h3 className="text-xl font-black text-white uppercase italic tracking-widest mb-2">Visor de Firma</h3>
                        <p className="text-[10px] text-white/40 uppercase tracking-widest mb-6 font-black italic">
                            Identidad: {previewUser.nombres} {previewUser.apellidos}
                        </p>
                        
                        <div className="bg-white/5 border border-white/10 rounded-3xl p-6 mb-8 flex flex-col items-center justify-center relative">
                            {/* Grid pattern background */}
                            <div className="absolute inset-0 opacity-5 pointer-events-none bg-[linear-gradient(to_right,#808080_1px,transparent_1px),linear-gradient(to_bottom,#808080_1px,transparent_1px)] bg-[size:14px_24px]"></div>
                            
                            <div className="w-full h-32 bg-black/40 border border-white/5 rounded-2xl flex items-center justify-center p-4 relative z-10">
                                <img 
                                    src={`/storage/${previewUser.firma}`} 
                                    alt="Firma Aprobada" 
                                    className="max-h-full brightness-0 invert opacity-90" 
                                />
                            </div>
                        </div>

                        <div className="flex flex-col gap-4 items-center mb-8">
                            <span className="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest italic border text-emerald-400 border-emerald-500/30 bg-emerald-500/10 shadow-[0_0_15px_rgba(16,185,129,0.2)]">
                                <span className="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                Firma Oficial Aprobada
                            </span>
                            <span className="text-[9px] font-black text-white/30 uppercase tracking-widest italic">
                                Matrícula: {previewUser.matricula || 'N/A'}
                            </span>
                        </div>

                        <div className="flex justify-center">
                            <button onClick={() => setShowSignaturePreview(false)} className="px-10 py-4 bg-white/5 text-white/60 hover:text-white rounded-2xl text-[10px] font-black uppercase tracking-widest italic transition-all border border-white/5 hover:border-white/10">
                                Cerrar Visor
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

function RoleBadge({ role }) {
    const theme = role === 'gestor' 
        ? 'text-purple-400 border-purple-500/30 bg-purple-500/10 shadow-[0_0_10px_rgba(168,85,247,0.2)]'
        : 'text-indigo-400 border-indigo-500/30 bg-indigo-500/10 shadow-[0_0_10px_rgba(99,102,241,0.2)]';
    
    return (
        <span className={`inline-flex px-3 py-1 rounded-full text-[8px] font-black uppercase tracking-widest italic border ${theme}`}>
            {role.toUpperCase()}
        </span>
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
