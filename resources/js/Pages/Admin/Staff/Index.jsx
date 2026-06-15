import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, useForm, router } from '@inertiajs/react';

export default function Index({ auth, staff, filters = {} }) {
    const [showModal, setShowModal] = useState(false);
    const [editingUser, setEditingUser] = useState(null);
    const [searchVal, setSearchVal] = useState(filters.search || '');

    const { data, setData, post, processing, errors, reset } = useForm({
        id: '',
        nombres: '',
        apellidos: '',
        email: '',
        password: '',
        rol: 'vendedor',
        estado: 'activo',
    });

    const openCreateModal = () => {
        reset();
        setEditingUser(null);
        setData({
            id: '',
            nombres: '',
            apellidos: '',
            email: '',
            password: '',
            rol: 'vendedor',
            estado: 'activo',
        });
        setShowModal(true);
    };

    const openEditModal = (user) => {
        setEditingUser(user);
        setData({
            id: user.id,
            nombres: user.nombres,
            apellidos: user.apellidos,
            email: user.email,
            password: '',
            rol: user.rol,
            estado: user.estado,
        });
        setShowModal(true);
    };

    const submit = (e) => {
        e.preventDefault();
        post(route('admin.staff.save'), {
            onSuccess: () => {
                setShowModal(false);
                reset();
            }
        });
    };

    const deleteUser = (userId) => {
        if (confirm('¿Está seguro de que desea dar de baja a este miembro del staff global?')) {
            router.delete(route('admin.staff.destroy', userId));
        }
    };

    const handleSearch = (e) => {
        e.preventDefault();
        router.get(route('admin.staff.index'), { search: searchVal }, { preserveState: true });
    };

    return (
        <AuthenticatedLayout
            user={auth.user}
            title="Súper Administración"
            header={
                <div className="flex flex-col">
                    <h1 className="text-xs font-black text-ep-primary uppercase tracking-[0.4em] italic leading-none mb-2">
                        Global Security
                    </h1>
                    <h2 className="text-xl font-black text-white uppercase italic tracking-tighter leading-none">
                        Personal <span className="text-indigo-400">Global</span>
                    </h2>
                </div>
            }
        >
            <Head title="Personal Global - Súper Administración" />

            <div className="py-12 bg-[#0f0f1a] min-h-screen text-white selection:bg-ep-primary/30 animate-fade-in">
                <div className="max-w-[98rem] mx-auto sm:px-6 lg:px-8 space-y-8">
                    
                    <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 border-b border-white/5 pb-6">
                        <div>
                            <p className="text-[10px] font-bold text-white/30 uppercase tracking-widest italic">
                                Súper Administradores y Vendedores de Soporte Técnico.
                            </p>
                        </div>
                        
                        <button
                            onClick={openCreateModal}
                            className="group relative px-6 py-3 bg-ep-primary text-white text-[10px] font-black uppercase italic tracking-widest rounded-xl hover:brightness-110 active:scale-95 transition-all shadow-lg shadow-ep-primary/20 overflow-hidden inline-flex items-center gap-2"
                        >
                            <div className="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2.5">
                                <path strokeLinecap="round" strokeLinejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z" />
                            </svg>
                            Registrar Miembro
                        </button>
                    </div>

                    {/* Search Form */}
                    <form onSubmit={handleSearch} className="flex gap-3 max-w-md bg-[#16162a]/40 backdrop-blur-md p-1.5 rounded-xl border border-white/5 shadow-2xl">
                        <input 
                            type="text" 
                            placeholder="Buscar por nombre, apellido o email..."
                            value={searchVal}
                            onChange={e => setSearchVal(e.target.value)}
                            className="flex-1 bg-transparent border-0 ring-0 text-xs text-white placeholder-white/20 focus:ring-0 focus:border-0 font-bold"
                        />
                        <button 
                            type="submit"
                            className="px-4 py-2 bg-white/5 hover:bg-white/10 text-white text-[9px] font-black uppercase italic tracking-widest rounded-lg transition-all border border-white/5"
                        >
                            Buscar
                        </button>
                    </form>

                    {/* Staff List Grid */}
                    <div className="bg-[#16162a]/40 backdrop-blur-xl border border-white/5 rounded-[2.5rem] overflow-hidden shadow-2xl">
                        <div className="overflow-x-auto">
                            <table className="w-full text-left border-collapse">
                                <thead>
                                    <tr className="border-b border-white/5 bg-black/20 text-[9px] font-black uppercase tracking-[0.25em] text-indigo-300 italic">
                                        <th className="py-5 px-8">Nombre Completo</th>
                                        <th className="py-5 px-8">Email</th>
                                        <th className="py-5 px-8 text-center">Rol de Sistema</th>
                                        <th className="py-5 px-8 text-center">Estado</th>
                                        <th className="py-5 px-8 text-right">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-white/5">
                                    {staff.data.length === 0 ? (
                                        <tr>
                                            <td colSpan="5" className="py-12 text-center text-xs font-bold text-white/20 uppercase tracking-widest italic">
                                                No se encontraron miembros de staff global
                                            </td>
                                        </tr>
                                    ) : (
                                        staff.data.map((user) => {
                                            const isSuperAdmin = user.rol === 'super_admin';
                                            const isActive = user.estado === 'activo';
                                            const isSelf = user.id === auth.user.id;

                                            return (
                                                <tr key={user.id} className="hover:bg-white/[0.02] transition-colors group">
                                                    <td className="py-5 px-8">
                                                        <div className="flex items-center gap-3">
                                                            <div className="w-9 h-9 bg-indigo-500/10 border border-indigo-500/20 rounded-xl flex items-center justify-center text-indigo-300 text-xs font-black">
                                                                {user.nombres.charAt(0)}{user.apellidos.charAt(0)}
                                                            </div>
                                                            <div className="flex flex-col">
                                                                <span className="text-xs font-black uppercase italic text-white tracking-wide">
                                                                    {user.apellidos}, {user.nombres}
                                                                </span>
                                                                {isSelf && (
                                                                    <span className="text-[7px] font-black uppercase tracking-widest text-indigo-400 mt-0.5">
                                                                        Tú (Sesión Activa)
                                                                    </span>
                                                                )}
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td className="py-5 px-8 font-mono text-xs text-white/60">
                                                        {user.email}
                                                    </td>
                                                    <td className="py-5 px-8 text-center">
                                                        <span className={`inline-block text-[8px] font-black uppercase tracking-[0.2em] italic px-3 py-1 rounded-full ${
                                                            isSuperAdmin 
                                                                ? 'bg-red-500/10 border border-red-500/20 text-red-400' 
                                                                : 'bg-indigo-500/10 border border-indigo-500/20 text-indigo-300'
                                                        }`}>
                                                            {isSuperAdmin ? 'Súper Administrador' : 'Vendedor'}
                                                        </span>
                                                    </td>
                                                    <td className="py-5 px-8 text-center">
                                                        <span className={`inline-block text-[8px] font-black uppercase tracking-[0.2em] italic px-3 py-1 rounded-full ${
                                                            isActive 
                                                                ? 'bg-emerald-500/10 border border-emerald-500/20 text-emerald-400' 
                                                                : 'bg-white/5 border border-white/5 text-white/30'
                                                        }`}>
                                                            {isActive ? 'Activo' : 'Inactivo'}
                                                        </span>
                                                    </td>
                                                    <td className="py-5 px-8 text-right">
                                                        <div className="flex items-center justify-end gap-2.5">
                                                            <button 
                                                                onClick={() => openEditModal(user)}
                                                                className="px-3.5 py-2 bg-white/5 hover:bg-white/10 text-white text-[9px] font-black uppercase tracking-widest italic rounded-lg border border-white/10 transition-all inline-flex items-center gap-1.5"
                                                            >
                                                                <svg className="w-3.5 h-3.5 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2.5">
                                                                    <path strokeLinecap="round" strokeLinejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                                                </svg>
                                                                Editar
                                                            </button>
                                                            {!isSelf && (
                                                                <button 
                                                                    onClick={() => deleteUser(user.id)}
                                                                    className="h-8 w-8 flex items-center justify-center bg-red-500/10 hover:bg-red-500 text-red-500 hover:text-white rounded-lg border border-red-500/20 hover:border-red-500 transition-all"
                                                                    title="Dar de baja"
                                                                >
                                                                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2.5">
                                                                        <path strokeLinecap="round" strokeLinejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                                    </svg>
                                                                </button>
                                                            )}
                                                        </div>
                                                    </td>
                                                </tr>
                                            );
                                        })
                                    )}
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {/* Pagination */}
                    {staff.links && staff.links.length > 3 && (
                        <div className="flex justify-center gap-2 mt-8">
                            {staff.links.map((link, i) => (
                                <Link
                                    key={i}
                                    href={link.url || '#'}
                                    dangerouslySetInnerHTML={{ __html: link.label }}
                                    className={`px-4 py-2 text-[10px] font-black uppercase italic tracking-widest rounded-xl transition-all ${
                                        link.active
                                            ? 'bg-ep-primary text-white shadow-lg'
                                            : 'bg-white/5 text-white/40 hover:bg-white/10 hover:text-white border border-white/5'
                                    }`}
                                />
                            ))}
                        </div>
                    )}
                </div>
            </div>

            {/* Creation/Edit Modal */}
            {showModal && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-md animate-fade-in">
                    <div className="w-full max-w-md bg-[#16162a] border border-white/10 rounded-[2.5rem] p-8 sm:p-10 shadow-2xl relative animate-scale-up">
                        {/* Close button */}
                        <button 
                            onClick={() => setShowModal(false)}
                            className="absolute top-6 right-6 text-white/40 hover:text-white transition-colors text-lg"
                        >
                            ✕
                        </button>

                        <div className="mb-6">
                            <h3 className="text-xs font-black text-ep-primary uppercase tracking-[0.4em] italic mb-1">
                                {editingUser ? 'Actualizar Accesos' : 'Registrar Nuevo'}
                            </h3>
                            <h4 className="text-lg font-black text-white uppercase italic tracking-tighter leading-none">
                                {editingUser ? 'Editar' : 'Crear'} <span className="text-indigo-400">Staff Global</span>
                            </h4>
                        </div>

                        <form onSubmit={submit} className="space-y-5">
                            <div>
                                <label className="block text-[9px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic mb-2">Nombres *</label>
                                <input 
                                    type="text" 
                                    value={data.nombres} 
                                    onChange={e => setData('nombres', e.target.value)}
                                    className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-xs text-white focus:border-ep-primary focus:ring-0 transition-all font-black uppercase italic"
                                    required
                                />
                                {errors.nombres && <p className="text-xs text-red-400 mt-1">{errors.nombres}</p>}
                            </div>

                            <div>
                                <label className="block text-[9px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic mb-2">Apellidos *</label>
                                <input 
                                    type="text" 
                                    value={data.apellidos} 
                                    onChange={e => setData('apellidos', e.target.value)}
                                    className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-xs text-white focus:border-ep-primary focus:ring-0 transition-all font-black uppercase italic"
                                    required
                                />
                                {errors.apellidos && <p className="text-xs text-red-400 mt-1">{errors.apellidos}</p>}
                            </div>

                            <div>
                                <label className="block text-[9px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic mb-2">Email *</label>
                                <input 
                                    type="email" 
                                    value={data.email} 
                                    onChange={e => setData('email', e.target.value)}
                                    className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-xs text-white focus:border-ep-primary focus:ring-0 transition-all font-bold"
                                    required
                                />
                                {errors.email && <p className="text-xs text-red-400 mt-1">{errors.email}</p>}
                            </div>

                            <div>
                                <label className="block text-[9px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic mb-2">
                                    Contraseña {editingUser ? '(Opcional para blanqueo)' : '*'}
                                </label>
                                <input 
                                    type="password" 
                                    value={data.password} 
                                    onChange={e => setData('password', e.target.value)}
                                    placeholder={editingUser ? 'Dejar en blanco para mantener' : 'Mínimo 8 caracteres'}
                                    className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-xs text-white focus:border-ep-primary focus:ring-0 transition-all font-bold"
                                    required={!editingUser}
                                />
                                {errors.password && <p className="text-xs text-red-400 mt-1">{errors.password}</p>}
                            </div>

                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <label className="block text-[9px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic mb-2">Rol *</label>
                                    <select 
                                        value={data.rol}
                                        onChange={e => setData('rol', e.target.value)}
                                        className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-xs text-white focus:border-ep-primary focus:ring-0 transition-all font-black uppercase italic"
                                    >
                                        <option value="vendedor" className="bg-[#16162a]">Vendedor</option>
                                        <option value="super_admin" className="bg-[#16162a]">Súper Admin</option>
                                    </select>
                                    {errors.rol && <p className="text-xs text-red-400 mt-1">{errors.rol}</p>}
                                </div>

                                <div>
                                    <label className="block text-[9px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic mb-2">Estado *</label>
                                    <select 
                                        value={data.estado}
                                        onChange={e => setData('estado', e.target.value)}
                                        className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-xs text-white focus:border-ep-primary focus:ring-0 transition-all font-black uppercase italic"
                                    >
                                        <option value="activo" className="bg-[#16162a]">Activo</option>
                                        <option value="inactivo" className="bg-[#16162a]">Inactivo</option>
                                    </select>
                                    {errors.estado && <p className="text-xs text-red-400 mt-1">{errors.estado}</p>}
                                </div>
                            </div>

                            <div className="flex justify-end gap-3 pt-4 border-t border-white/5">
                                <button 
                                    type="button"
                                    onClick={() => setShowModal(false)}
                                    className="px-5 py-3.5 bg-white/5 hover:bg-white/10 text-white text-[10px] font-black uppercase italic tracking-widest rounded-xl transition-all border border-white/5"
                                >
                                    Cancelar
                                </button>
                                <button 
                                    type="submit" 
                                    disabled={processing}
                                    className="px-6 py-3.5 bg-ep-primary text-white text-[10px] font-black uppercase italic tracking-widest rounded-xl hover:brightness-110 active:scale-95 transition-all shadow-lg shadow-ep-primary/20"
                                >
                                    {processing ? 'Guardando...' : editingUser ? 'Actualizar' : 'Crear'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </AuthenticatedLayout>
    );
}
