import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, useForm, router } from '@inertiajs/react';

export default function Users({ auth, consultant, users }) {
    const [showModal, setShowModal] = useState(false);
    const [editingUser, setEditingUser] = useState(null);

    const { data, setData, post, processing, errors, reset } = useForm({
        id: '',
        nombres: '',
        apellidos: '',
        email: '',
        password: '',
        rol: 'gestor',
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
            rol: 'gestor',
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
        post(route('admin.consultants.users.save', consultant.id), {
            onSuccess: () => {
                setShowModal(false);
                reset();
            }
        });
    };

    const deleteUser = (userId) => {
        if (confirm('¿Está seguro de que desea dar de baja a este usuario?')) {
            router.delete(route('admin.consultants.users.destroy', [consultant.id, userId]));
        }
    };

    return (
        <AuthenticatedLayout
            user={auth.user}
            title="Súper Administración"
            header={
                <div className="flex flex-col">
                    <h1 className="text-xs font-black text-ep-primary uppercase tracking-[0.4em] italic leading-none mb-2">
                        Staff Management
                    </h1>
                    <h2 className="text-xl font-black text-white uppercase italic tracking-tighter leading-none">
                        Gestión de <span className="text-indigo-400">Personal</span>
                    </h2>
                </div>
            }
        >
            <Head title={`Staff - ${consultant.nombre}`} />

            <div className="py-12 bg-[#0f0f1a] min-h-screen text-white selection:bg-ep-primary/30 animate-fade-in">
                <div className="max-w-[98rem] mx-auto sm:px-6 lg:px-8 space-y-8">
                    
                    {/* Return & Info */}
                    <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <Link 
                            href={route('admin.consultants.index')} 
                            className="inline-flex items-center gap-2 text-xs font-black text-white/40 hover:text-white uppercase italic tracking-widest transition-colors"
                        >
                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2.5"><path strokeLinecap="round" strokeLinejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" /></svg>
                            Volver a Consultoras
                        </Link>
                        
                        <button
                            onClick={openCreateModal}
                            className="group relative px-6 py-3 bg-ep-primary text-white text-[10px] font-black uppercase italic tracking-widest rounded-xl hover:brightness-110 active:scale-95 transition-all shadow-lg shadow-ep-primary/20 overflow-hidden inline-flex items-center gap-2"
                        >
                            <div className="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2.5"><path strokeLinecap="round" strokeLinejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z" /></svg>
                            Agregar Operador
                        </button>
                    </div>

                    {/* Info Card */}
                    <div className="bg-[#16162a]/40 backdrop-blur-xl rounded-[2.5rem] border border-white/5 p-8 shadow-2xl relative overflow-hidden">
                        <div className="absolute -right-24 -top-24 w-96 h-96 bg-indigo-500/5 rounded-full blur-[100px]"></div>
                        <div className="relative z-10">
                            <span className="text-[8px] font-black text-ep-primary uppercase tracking-[0.3em] mb-2 block italic">Estructura Corporativa</span>
                            <h2 className="text-2xl sm:text-3xl font-black italic uppercase text-white leading-none tracking-tight">
                                {consultant.nombre}
                            </h2>
                            <p className="text-xs text-white/40 font-bold uppercase tracking-widest mt-2">CUIT: {consultant.cuit}</p>
                        </div>
                    </div>

                    {/* Users Table */}
                    <div className="bg-[#16162a]/40 backdrop-blur-xl border border-white/5 rounded-[2.5rem] shadow-2xl overflow-hidden">
                        <div className="overflow-x-auto">
                            <table className="w-full text-left border-collapse">
                                <thead>
                                    <tr className="bg-black/20 border-b border-white/5">
                                        <th className="px-8 py-5 text-[10px] font-black text-ep-primary uppercase tracking-widest italic">Nombre Completo</th>
                                        <th className="px-8 py-5 text-[10px] font-black text-ep-primary uppercase tracking-widest italic">Email</th>
                                        <th className="px-8 py-5 text-[10px] font-black text-ep-primary uppercase tracking-widest italic">Rol</th>
                                        <th className="px-8 py-5 text-[10px] font-black text-ep-primary uppercase tracking-widest italic">Estado</th>
                                        <th className="px-8 py-5 text-[10px] font-black text-ep-primary uppercase tracking-widest italic text-right">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-white/5">
                                    {users.length === 0 ? (
                                        <tr>
                                            <td colSpan="5" className="px-8 py-10 text-center text-xs font-black text-white/20 uppercase tracking-widest italic">
                                                Cero operadores registrados en esta consultora
                                            </td>
                                        </tr>
                                    ) : (
                                        users.map((u) => (
                                            <tr key={u.id} className="hover:bg-white/[0.02] transition-colors">
                                                <td className="px-8 py-5">
                                                    <div className="flex items-center gap-3">
                                                        <div className="w-8 h-8 rounded-full bg-white/5 border border-white/10 flex items-center justify-center text-xs font-black text-white italic">
                                                            {u.nombres.charAt(0)}{u.apellidos.charAt(0)}
                                                        </div>
                                                        <div>
                                                            <div className="text-sm font-black text-white uppercase italic leading-none mb-1">{u.nombres} {u.apellidos}</div>
                                                            <div className="text-[8px] font-bold text-white/20 tracking-wider uppercase">Operador HSE</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td className="px-8 py-5 text-xs font-mono text-white/70">{u.email}</td>
                                                <td className="px-8 py-5">
                                                    <span className={`px-3 py-1 rounded-full text-[8px] font-black uppercase tracking-widest italic ${
                                                        u.rol === 'super_admin' ? 'bg-red-500/20 text-red-400 border border-red-500/30' :
                                                        u.rol === 'gestor' ? 'bg-purple-500/20 text-purple-400 border border-purple-500/30' :
                                                        'bg-indigo-500/20 text-indigo-400 border border-indigo-500/30'
                                                    }`}>
                                                        {u.rol}
                                                    </span>
                                                </td>
                                                <td className="px-8 py-5">
                                                    <span className={`inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[8px] font-black uppercase tracking-widest italic ${
                                                        u.estado === 'activo' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 shadow-[0_0_15px_rgba(16,185,129,0.1)]' : 'bg-white/5 text-white/30 border border-white/10'
                                                    }`}>
                                                        <span className={`w-1.5 h-1.5 rounded-full ${u.estado === 'activo' ? 'bg-emerald-400 animate-pulse' : 'bg-white/20'}`} />
                                                        {u.estado}
                                                    </span>
                                                </td>
                                                <td className="px-8 py-5 text-right relative z-20">
                                                    <div className="flex justify-end items-center gap-2">
                                                        <button
                                                            onClick={() => openEditModal(u)}
                                                            className="px-4 py-2 bg-white/5 hover:bg-white/10 text-white text-[9px] font-black uppercase tracking-widest italic rounded-lg border border-white/10 transition-all"
                                                        >
                                                            Configurar
                                                        </button>
                                                        {u.id !== auth.user.id && u.rol !== 'tecnico' && (
                                                            <button
                                                                onClick={() => deleteUser(u.id)}
                                                                className="px-4 py-2 bg-red-500/10 hover:bg-red-500 text-red-400 hover:text-white text-[9px] font-black uppercase tracking-widest italic rounded-lg border border-red-500/20 hover:border-red-500 transition-all"
                                                            >
                                                                Dar de Baja
                                                            </button>
                                                        )}
                                                    </div>
                                                </td>
                                            </tr>
                                        ))
                                    )}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {/* MODAL EDITAR / CREAR */}
            {showModal && (
                <div className="fixed inset-0 z-50 flex items-center justify-center px-4 overflow-y-auto">
                    <div className="fixed inset-0 bg-black/60 backdrop-blur-sm" onClick={() => setShowModal(false)} />
                    
                    <div className="relative bg-[#16162a] border border-white/10 rounded-[2.5rem] p-8 sm:p-10 max-w-lg w-full shadow-2xl animate-fade-in-up space-y-6">
                        <div className="border-b border-white/5 pb-4">
                            <h3 className="text-lg font-black text-white italic uppercase tracking-tighter">
                                {editingUser ? 'Configurar Operador' : 'Registrar Nuevo Operador'}
                            </h3>
                            <p className="text-[9px] font-bold text-white/20 uppercase tracking-widest mt-1">
                                Complete los parámetros de acceso para la consultora.
                            </p>
                        </div>

                        <form onSubmit={submit} className="space-y-4">
                            <div>
                                <label htmlFor="nombres" className="block text-[9px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic mb-1.5">Nombres *</label>
                                <input 
                                    type="text" 
                                    id="nombres"
                                    value={data.nombres} 
                                    onChange={e => setData('nombres', e.target.value)}
                                    className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:border-ep-primary focus:ring-0 transition-all font-black uppercase italic"
                                    required
                                />
                                {errors.nombres && <p className="text-xs text-red-400 mt-1">{errors.nombres}</p>}
                            </div>

                            <div>
                                <label htmlFor="apellidos" className="block text-[9px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic mb-1.5">Apellidos *</label>
                                <input 
                                    type="text" 
                                    id="apellidos"
                                    value={data.apellidos} 
                                    onChange={e => setData('apellidos', e.target.value)}
                                    className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:border-ep-primary focus:ring-0 transition-all font-black uppercase italic"
                                    required
                                />
                                {errors.apellidos && <p className="text-xs text-red-400 mt-1">{errors.apellidos}</p>}
                            </div>

                            <div>
                                <label htmlFor="email" className="block text-[9px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic mb-1.5">Email de Acceso *</label>
                                <input 
                                    type="email" 
                                    id="email"
                                    value={data.email} 
                                    onChange={e => setData('email', e.target.value)}
                                    className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:border-ep-primary focus:ring-0 transition-all font-bold"
                                    required
                                />
                                {errors.email && <p className="text-xs text-red-400 mt-1">{errors.email}</p>}
                            </div>

                            <div>
                                <label htmlFor="password" className="block text-[9px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic mb-1.5">
                                    Contraseña {editingUser ? '(Opcional)' : '*'}
                                </label>
                                <input 
                                    type="password" 
                                    id="password"
                                    value={data.password} 
                                    onChange={e => setData('password', e.target.value)}
                                    className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:border-ep-primary focus:ring-0 transition-all font-bold"
                                    required={!editingUser}
                                />
                                {errors.password && <p className="text-xs text-red-400 mt-1">{errors.password}</p>}
                            </div>

                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <label htmlFor="rol" className="block text-[9px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic mb-1.5">Rol de Operador</label>
                                    <select 
                                        id="rol"
                                        value={data.rol}
                                        onChange={e => setData('rol', e.target.value)}
                                        className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:border-ep-primary focus:ring-0 transition-all font-black uppercase italic"
                                    >
                                        <option value="gestor" className="bg-[#16162a]">Gestor / Admin</option>
                                        <option value="tecnico" className="bg-[#16162a]">Técnico Operario</option>
                                    </select>
                                    {errors.rol && <p className="text-xs text-red-400 mt-1">{errors.rol}</p>}
                                </div>
                                <div>
                                    <label htmlFor="estado" className="block text-[9px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic mb-1.5">Estado Acceso</label>
                                    <select 
                                        id="estado"
                                        value={data.estado}
                                        onChange={e => setData('estado', e.target.value)}
                                        className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:border-ep-primary focus:ring-0 transition-all font-black uppercase italic"
                                    >
                                        <option value="activo" className="bg-[#16162a]">Activo</option>
                                        <option value="inactivo" className="bg-[#16162a]">Suspendido</option>
                                    </select>
                                    {errors.estado && <p className="text-xs text-red-400 mt-1">{errors.estado}</p>}
                                </div>
                            </div>

                            <div className="flex justify-end gap-3 pt-6 border-t border-white/5">
                                <button 
                                    type="button" 
                                    onClick={() => setShowModal(false)}
                                    className="px-6 py-3 bg-white/5 border border-white/10 hover:bg-white/10 text-white text-[10px] font-black uppercase italic tracking-widest rounded-xl transition-all"
                                >
                                    Cancelar
                                </button>
                                <button 
                                    type="submit" 
                                    disabled={processing}
                                    className="group relative px-6 py-3 bg-ep-primary text-white text-[10px] font-black uppercase italic tracking-widest rounded-xl hover:brightness-110 active:scale-95 transition-all shadow-lg overflow-hidden inline-flex items-center gap-1.5"
                                >
                                    <div className="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2.5"><path strokeLinecap="round" strokeLinejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                    {processing ? 'Guardando...' : 'Guardar'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </AuthenticatedLayout>
    );
}
