import { Head, useForm, usePage, Link } from '@inertiajs/react';
import AuthenticatedLayout from '../../Layouts/AuthenticatedLayout';
import { useState } from 'react';

export default function Show() {
    const { auth, user_data, flash } = usePage().props;
    const user = auth.user;
    
    // Formulario de perfil
    const profileForm = useForm({
        nombres: user_data.nombres || '',
        apellidos: user_data.apellidos || '',
        telefono: user_data.telefono || '',
    });

    // Formulario de contraseña
    const passwordForm = useForm({
        current_password: '',
        new_password: '',
        new_password_confirmation: '',
    });

    const updateProfile = (e) => {
        e.preventDefault();
        profileForm.post(route('profile.update'), {
            preserveScroll: true,
            onSuccess: () => profileForm.reset('password'),
        });
    };

    const updatePassword = (e) => {
        e.preventDefault();
        passwordForm.post(route('profile.password'), {
            preserveScroll: true,
            onSuccess: () => passwordForm.reset(),
        });
    };

    const isBaseAdmin = ['gestor', 'super_admin', 'administrador', 'admin'].includes(user.rol);
    const isGestorActive = user.active_role === 'gestor' || user.active_role === 'super_admin';
    const isTecnicoActive = user.active_role === 'tecnico';

    return (
        <AuthenticatedLayout 
            title="Consola de Identidad"
            header={
                <div className="flex flex-col">
                    <h1 className="text-xs font-black text-ep-primary uppercase tracking-widest italic leading-none mb-1">
                        Consola de <span className="text-white">Identidad</span>
                    </h1>
                    <h2 className="text-sm font-bold text-indigo-100 uppercase tracking-tight leading-none italic">
                        Parametrización de Usuario
                    </h2>
                </div>
            }
        >
            <Head title="Mi Perfil" />

            <div className="px-2 sm:px-6 lg:px-8 py-8 text-white selection:bg-[#F69239]/30">
                <div className="max-w-[95rem] mx-auto">
                    <div className="grid grid-cols-1 lg:grid-cols-12 gap-10 animate-fade-in-down">
                        
                        {/* ── COLUMNA IZQUIERDA: Tactical ID Card ── */}
                        <div className="lg:col-span-4 space-y-8">
                            <div className="bg-[#16162a]/40 backdrop-blur-md rounded-3xl sm:rounded-[3rem] border border-white/5 p-6 sm:p-8 text-center shadow-2xl relative overflow-hidden group">
                                <div className="absolute inset-0 bg-gradient-to-b from-[#F69239]/5 to-transparent pointer-events-none"></div>

                                <div className="relative">
                                    <div className="w-32 h-32 bg-[#F69239]/10 rounded-[2.5rem] border border-[#F69239]/20 mx-auto flex items-center justify-center text-[#F69239] text-4xl font-black shadow-inner mb-6 rotate-3 group-hover:rotate-0 transition-all duration-700">
                                        {user_data.nombres?.charAt(0) || 'U'}
                                    </div>
                                    <h3 className="text-2xl font-black text-white italic uppercase tracking-tight">
                                        {user_data.nombres} {user_data.apellidos}
                                    </h3>
                                    <p className="text-[10px] font-black text-[#F69239] uppercase tracking-[0.3em] italic mt-2">
                                        {user.rol === 'gestor' ? 'Gestor de Seguridad' : (user.rol === 'tecnico' ? 'Especialista en Campo' : 'System Admin')}
                                    </p>
                                </div>

                                <div className="mt-10 pt-8 border-t border-white/5 text-left space-y-6">
                                    <div className="group/item">
                                        <span className="block text-[9px] font-black text-white uppercase tracking-widest italic mb-1 opacity-40">Identificador Digital (Email)</span>
                                        <span className="text-sm text-white font-bold italic tracking-wide group-hover/item:text-[#F69239] transition-colors">{user_data.email}</span>
                                    </div>
                                    {user_data.matricula && (
                                        <div className="group/item">
                                            <span className="block text-[9px] font-black text-white uppercase tracking-widest italic mb-1 opacity-40">Credencial Técnica</span>
                                            <div className="inline-flex items-center px-3 py-1 bg-[#F69239]/10 border border-[#F69239]/20 rounded-lg text-xs font-black text-[#F69239] italic">
                                                {user_data.matricula}
                                            </div>
                                        </div>
                                    )}
                                </div>
                            </div>

                            {/* Role Switcher */}
                            {isBaseAdmin && (
                                <div className="bg-[#16162a]/40 backdrop-blur-md rounded-3xl sm:rounded-[3rem] border border-white/5 p-6 sm:p-8 shadow-2xl relative overflow-hidden group/sw">
                                    <h3 className="text-[10px] font-black text-white uppercase tracking-[0.3em] mb-8 italic flex items-center gap-3">
                                        <svg className="w-4 h-4 text-[#F69239]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                        </svg>
                                        Módulos de Interfaz
                                    </h3>

                                    <div className="space-y-4">
                                        <Link href={route('switch-role', 'gestor')}
                                            className={`flex items-center gap-4 sm:gap-5 p-4 sm:p-5 rounded-2xl border transition-all duration-500 group ${isGestorActive ? 'bg-[#F69239]/20 border-[#F69239] shadow-[0_0_20px_rgba(246,146,57,0.1)]' : 'bg-white/5 border-white/5 hover:bg-white/10 hover:border-white/20'}`}>
                                            <div className={`w-10 h-10 sm:w-12 sm:h-12 rounded-xl flex items-center justify-center transition-all ${isGestorActive ? 'bg-[#F69239] text-white shadow-xl rotate-0' : 'bg-white/5 text-white/30 group-hover:text-white -rotate-6'}`}>
                                                <svg className="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                                </svg>
                                            </div>
                                            <div className="text-left flex-1">
                                                <span className={`block text-xs sm:text-sm font-black italic uppercase ${isGestorActive ? 'text-white' : 'text-white/40'}`}>Módulo Gestión</span>
                                                <span className={`text-[8px] sm:text-[9px] font-bold uppercase tracking-widest ${isGestorActive ? 'text-[#F69239]' : 'text-white/20'}`}>Consola Administrativa</span>
                                            </div>
                                            {isGestorActive && <div className="w-2.5 h-2.5 bg-[#F69239] rounded-full animate-pulse shadow-[0_0_10px_#f69239]"></div>}
                                        </Link>

                                        <Link href={route('switch-role', 'tecnico')}
                                            className={`flex items-center gap-4 sm:gap-5 p-4 sm:p-5 rounded-2xl border transition-all duration-500 group ${isTecnicoActive ? 'bg-[#F69239]/20 border-[#F69239] shadow-[0_0_20px_rgba(246,146,57,0.1)]' : 'bg-white/5 border-white/5 hover:bg-white/10 hover:border-white/20'}`}>
                                            <div className={`w-10 h-10 sm:w-12 sm:h-12 rounded-xl flex items-center justify-center transition-all ${isTecnicoActive ? 'bg-[#F69239] text-white shadow-xl rotate-0' : 'bg-white/5 text-white/30 group-hover:text-white -rotate-6'}`}>
                                                <svg className="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 011-1h1a2 2 0 100-4H7a1 1 0 01-1-1V7a1 1 0 011-1h3a1 1 0 001-1V4z" />
                                                </svg>
                                            </div>
                                            <div className="text-left flex-1">
                                                <span className={`block text-xs sm:text-sm font-black italic uppercase ${isTecnicoActive ? 'text-white' : 'text-white/40'}`}>Módulo Operativo</span>
                                                <span className={`text-[8px] sm:text-[9px] font-bold uppercase tracking-widest ${isTecnicoActive ? 'text-[#F69239]' : 'text-white/20'}`}>Interfaz de Campo</span>
                                            </div>
                                            {isTecnicoActive && <div className="w-2.5 h-2.5 bg-[#F69239] rounded-full animate-pulse shadow-[0_0_10px_#f69239]"></div>}
                                        </Link>
                                    </div>

                                    <div className="mt-6 p-4 rounded-xl bg-amber-500/10 border border-amber-500/20">
                                        <p className="text-[9px] text-amber-500/80 leading-relaxed italic uppercase font-black tracking-widest text-center">
                                            <span className="text-amber-500 block mb-1">⚠️ AVISO DE PROTOCOLO</span>
                                            El cambio de modo es instantáneo y adapta toda la interfaz a sus privilegios correspondientes.
                                        </p>
                                    </div>
                                </div>
                            )}
                        </div>

                        {/* ── COLUMNA DERECHA: Forms ── */}
                        <div className="lg:col-span-8 space-y-10">
                            
                            {/* Basic Info Form */}
                            <div className="bg-[#16162a]/40 backdrop-blur-md rounded-3xl sm:rounded-[3rem] border border-white/5 overflow-hidden shadow-2xl relative">
                                <div className="px-6 sm:px-10 py-6 bg-white/5 border-b border-white/5 flex items-center justify-between">
                                    <h3 className="text-[11px] sm:text-xs font-black text-white uppercase tracking-[0.3em] italic flex items-center gap-3">
                                        <svg className="w-5 h-5 text-[#F69239]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        Parametrización
                                    </h3>
                                    <div className="h-2 w-2 rounded-full bg-emerald-500 shadow-[0_0_8px_#10b981] animate-pulse"></div>
                                </div>
                                <div className="p-6 sm:p-10">
                                    <form onSubmit={updateProfile} className="space-y-8">
                                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-6 sm:gap-8">
                                            <div className="relative group">
                                                <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-[#F69239] uppercase tracking-[0.3em] italic z-10">Nombres</span>
                                                <input id="field_6" name="field_6" 
                                                    type="text" 
                                                    value={profileForm.data.nombres}
                                                    onChange={e => profileForm.setData('nombres', e.target.value)}
                                                    className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-[#F69239]/50 transition-all uppercase placeholder-white/5" 
                                                />
                                                {profileForm.errors.nombres && <span className="text-red-500 text-[9px] font-black uppercase italic tracking-widest mt-2 block ml-2">{profileForm.errors.nombres}</span>}
                                            </div>
                                            <div className="relative group">
                                                <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-[#F69239] uppercase tracking-[0.3em] italic z-10">Apellidos</span>
                                                <input id="field_5" name="field_5" 
                                                    type="text" 
                                                    value={profileForm.data.apellidos}
                                                    onChange={e => profileForm.setData('apellidos', e.target.value)}
                                                    className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-[#F69239]/50 transition-all uppercase placeholder-white/5" 
                                                />
                                                {profileForm.errors.apellidos && <span className="text-red-500 text-[9px] font-black uppercase italic tracking-widest mt-2 block ml-2">{profileForm.errors.apellidos}</span>}
                                            </div>
                                        </div>
                                        <div className="relative group">
                                            <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-[#F69239] uppercase tracking-[0.3em] italic z-10">Línea de Contacto (Teléfono)</span>
                                            <input id="field_4" name="field_4" 
                                                type="text" 
                                                value={profileForm.data.telefono}
                                                onChange={e => profileForm.setData('telefono', e.target.value)}
                                                className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-[#F69239]/50 transition-all placeholder-white/5" 
                                                placeholder="Ej: +54 9 123 4567" 
                                            />
                                            {profileForm.errors.telefono && <span className="text-red-500 text-[9px] font-black uppercase italic tracking-widest mt-2 block ml-2">{profileForm.errors.telefono}</span>}
                                        </div>
                                        <div className="pt-4">
                                            <button 
                                                type="submit" 
                                                disabled={profileForm.processing}
                                                className="w-full sm:w-auto h-14 px-12 bg-[#F69239] text-white text-[10px] font-black uppercase tracking-[0.2em] italic rounded-2xl hover:brightness-110 active:scale-95 transition-all shadow-xl shadow-[#F69239]/20 relative group overflow-hidden"
                                            >
                                                <div className="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                                                <span className="relative">{profileForm.processing ? 'Sincronizando...' : 'Sincronizar Datos de Perfil'}</span>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            {/* Password Change Form */}
                            <div className="bg-[#16162a]/40 backdrop-blur-md rounded-3xl sm:rounded-[3rem] border border-white/5 overflow-hidden shadow-2xl relative">
                                <div className="px-6 sm:px-10 py-6 bg-white/5 border-b border-white/5 flex items-center justify-between">
                                    <h3 className="text-[11px] sm:text-xs font-black text-white uppercase tracking-[0.3em] italic flex items-center gap-3">
                                        <svg className="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                        Protocolo de Seguridad
                                    </h3>
                                    <div className="p-2 bg-red-500/10 rounded-xl text-red-500 shadow-inner">
                                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={3} d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                    </div>
                                </div>
                                <div className="p-6 sm:p-10">
                                    <form onSubmit={updatePassword} className="space-y-8">
                                        <div className="relative group">
                                            <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-red-500 uppercase tracking-[0.3em] italic z-10">Contraseña Actual</span>
                                            <input id="field_3" name="field_3" 
                                                type="password" 
                                                value={passwordForm.data.current_password}
                                                onChange={e => passwordForm.setData('current_password', e.target.value)}
                                                className="w-full bg-black/40 border border-white/5 rounded-2xl px-6 py-4 text-sm text-white focus:ring-2 focus:ring-red-500/40 transition-all" 
                                            />
                                            {passwordForm.errors.current_password && <span className="text-red-500 text-[9px] font-black uppercase italic tracking-widest mt-2 block ml-2">{passwordForm.errors.current_password}</span>}
                                        </div>
                                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-6 sm:gap-8">
                                            <div className="relative group">
                                                <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-white/40 uppercase tracking-[0.3em] italic z-10">Nueva Clave</span>
                                                <input id="field_2" name="field_2" 
                                                    type="password" 
                                                    value={passwordForm.data.new_password}
                                                    onChange={e => passwordForm.setData('new_password', e.target.value)}
                                                    className="w-full bg-black/40 border border-white/5 rounded-2xl px-6 py-4 text-sm text-white focus:ring-2 focus:ring-[#F69239]/50 transition-all" 
                                                />
                                                {passwordForm.errors.new_password && <span className="text-red-500 text-[9px] font-black uppercase italic tracking-widest mt-2 block ml-2">{passwordForm.errors.new_password}</span>}
                                            </div>
                                            <div className="relative group">
                                                <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-white/40 uppercase tracking-[0.3em] italic z-10">Confirmación</span>
                                                <input id="field_1" name="field_1" 
                                                    type="password" 
                                                    value={passwordForm.data.new_password_confirmation}
                                                    onChange={e => passwordForm.setData('new_password_confirmation', e.target.value)}
                                                    className="w-full bg-black/40 border border-white/5 rounded-2xl px-6 py-4 text-sm text-white focus:ring-2 focus:ring-[#F69239]/50 transition-all" 
                                                />
                                            </div>
                                        </div>
                                        <div className="pt-4">
                                            <button 
                                                type="submit" 
                                                disabled={passwordForm.processing}
                                                className="w-full sm:w-auto h-14 px-12 bg-white text-black text-[10px] font-black uppercase tracking-[0.2em] italic rounded-2xl hover:bg-indigo-100 active:scale-95 transition-all shadow-xl shadow-white/5"
                                            >
                                                {passwordForm.processing ? 'Actualizando...' : 'Actualizar Protocolo de Seguridad'}
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <style>{`
                .animate-fade-in-down {
                    animation: fade-in-down 0.6s ease-out forwards;
                }
                @keyframes fade-in-down {
                    from { opacity: 0; transform: translateY(-20px); }
                    to { opacity: 1; transform: translateY(0); }
                }
            `}</style>
        </AuthenticatedLayout>
    );
}
