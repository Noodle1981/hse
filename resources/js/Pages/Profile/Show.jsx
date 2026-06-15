import { Head, useForm, usePage, Link, router } from '@inertiajs/react';
import AuthenticatedLayout from '../../Layouts/AuthenticatedLayout';
import { useState, useRef, useEffect } from 'react';
import SignaturePad from 'signature_pad';

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

    // Formulario de firma
    const signatureForm = useForm({
        signature: '',
    });

    const canvasRef = useRef(null);
    const signaturePad = useRef(null);

    const isTecnico = user.rol === 'tecnico';

    useEffect(() => {
        if (isTecnico && user_data.firma_status !== 'pending' && canvasRef.current) {
            signaturePad.current = new SignaturePad(canvasRef.current, {
                backgroundColor: 'rgba(255, 255, 255, 0.05)',
                penColor: '#F69239', // Naranja Técnico
            });

            const resizeCanvas = () => {
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                if (canvasRef.current) {
                    canvasRef.current.width = canvasRef.current.offsetWidth * ratio;
                    canvasRef.current.height = canvasRef.current.offsetHeight * ratio;
                    canvasRef.current.getContext("2d").scale(ratio, ratio);
                    signaturePad.current.clear();
                }
            };

            window.addEventListener("resize", resizeCanvas);
            resizeCanvas();
            return () => window.removeEventListener("resize", resizeCanvas);
        }
    }, [isTecnico, user_data.firma_status]);

    const submitSignature = (e) => {
        e.preventDefault();
        if (!signaturePad.current || signaturePad.current.isEmpty()) {
            alert('Por favor, dibuje su firma en el panel antes de enviarla.');
            return;
        }

        signatureForm.transform((data) => ({
            ...data,
            signature: signaturePad.current.toDataURL()
        }));

        signatureForm.post(route('profile.signature'), {
            preserveScroll: true,
            onSuccess: () => {
                signatureForm.reset();
            }
        });
    };

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

    const handlePhotoChange = (e) => {
        const file = e.target.files[0];
        if (!file) return;

        if (file.size > 15 * 1024 * 1024) {
            alert('La imagen no debe superar los 15 MB.');
            return;
        }

        const formData = new FormData();
        formData.append('foto', file);

        router.post(route('profile.photo'), formData, {
            preserveScroll: true,
            forceFormData: true,
            onSuccess: () => {},
            onError: (err) => {
                alert(err.foto || 'Error al procesar y subir la foto.');
            }
        });
    };

    const isBaseAdmin = ['gestor', 'administrador', 'admin'].includes(user.rol) && !user.is_super_admin && user.rol !== 'super_admin';
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

            <div className="px-2 sm:px-4 lg:px-6 py-4 text-white selection:bg-[#F69239]/30">
                <div className="max-w-[95rem] mx-auto">
                    <div className="grid grid-cols-1 lg:grid-cols-12 gap-6 animate-fade-in-down">
                        
                        {/* ── COLUMNA IZQUIERDA / SUPERIOR: Tactical ID & Signature ── */}
                        <div className={`${isTecnico ? 'lg:col-span-12 grid grid-cols-1 lg:grid-cols-2 gap-6 mb-1' : 'lg:col-span-4 space-y-4'}`}>
                            
                            <div className="space-y-4">
                                {/* ── TARJETA TÁCTICA DE IDENTIDAD (CARLOS PEREZ) ── */}
                                <div className="bg-[#16162a]/40 backdrop-blur-md rounded-2xl border border-white/5 p-4 sm:p-5 text-center shadow-lg relative overflow-hidden group">
                                    <div className="absolute inset-0 bg-gradient-to-b from-[#F69239]/5 to-transparent pointer-events-none"></div>

                                    <div className="relative">
                                        <div className="relative w-24 h-24 mx-auto mb-3 group/avatar">
                                            <div className="w-24 h-24 bg-[#F69239]/10 rounded-[2rem] border border-[#F69239]/20 flex items-center justify-center text-[#F69239] text-3xl font-black shadow-inner overflow-hidden rotate-3 group-hover/avatar:rotate-0 transition-all duration-700">
                                                {user_data.foto ? (
                                                    <img src={`/storage/${user_data.foto}`} alt="Foto de Perfil" className="w-full h-full object-cover animate-fade-in" />
                                                ) : (
                                                    user_data.nombres?.charAt(0) || 'U'
                                                )}
                                            </div>
                                            {/* Upload Overlay */}
                                            <label htmlFor="foto-upload" className="absolute inset-0 rounded-[2rem] bg-black/75 opacity-0 group-hover/avatar:opacity-100 flex flex-col items-center justify-center cursor-pointer transition-opacity duration-300 z-10 border border-[#F69239]/50 shadow-inner">
                                                <svg className="w-5 h-5 text-[#F69239]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.5} d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.5} d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                <span className="text-[7px] font-black text-white uppercase tracking-widest mt-1">Cargar Foto</span>
                                                <input 
                                                    id="foto-upload" 
                                                    type="file" 
                                                    accept="image/*" 
                                                    className="sr-only" 
                                                    onChange={handlePhotoChange} 
                                                    />
                                            </label>
                                        </div>
                                        <h3 className="text-xl font-black text-white italic uppercase tracking-tight">
                                            {user_data.nombres} {user_data.apellidos}
                                        </h3>
                                        <p className="text-[9px] font-black text-[#F69239] uppercase tracking-[0.3em] italic mt-1">
                                            {(user.rol === 'super_admin' || user.is_super_admin) ? 'Súper Administrador' : (user.rol === 'tecnico' ? 'Especialista en Campo' : 'Gestor de Seguridad')}
                                        </p>
                                    </div>

                                    <div className="mt-6 pt-4 border-t border-white/5 text-left space-y-3">
                                        <div className="group/item">
                                            <span className="block text-[8px] font-black text-white uppercase tracking-widest italic mb-0.5 opacity-40">Identificador Digital (Email)</span>
                                            <span className="text-xs text-white font-bold italic tracking-wide group-hover/item:text-[#F69239] transition-colors">{user_data.email}</span>
                                        </div>
                                        {user_data.matricula && (
                                            <div className="group/item">
                                                <span className="block text-[8px] font-black text-white uppercase tracking-widest italic mb-0.5 opacity-40">Credencial Técnica</span>
                                                <div className="inline-flex items-center px-2.5 py-0.5 bg-[#F69239]/10 border border-[#F69239]/20 rounded-md text-[10px] font-black text-[#F69239] italic">
                                                    {user_data.matricula}
                                                </div>
                                            </div>
                                        )}
                                    </div>
                                </div>

                                {/* Role Switcher */}
                                {isBaseAdmin && (
                                    <div className="bg-[#16162a]/40 backdrop-blur-md rounded-2xl border border-white/5 p-4 sm:p-5 shadow-lg relative overflow-hidden group/sw">
                                        <h3 className="text-[9px] font-black text-white uppercase tracking-[0.3em] mb-4 italic flex items-center gap-3">
                                            <svg className="w-3.5 h-3.5 text-[#F69239]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                            </svg>
                                            Módulos de Interfaz
                                        </h3>

                                        <div className="space-y-2.5">
                                            <Link href={route('switch-role', 'gestor')}
                                                className={`flex items-center gap-3 p-3 rounded-xl border transition-all duration-500 group ${isGestorActive ? 'bg-[#F69239]/20 border-[#F69239] shadow-[0_0_15px_rgba(246,146,57,0.1)]' : 'bg-white/5 border-white/5 hover:bg-white/10 hover:border-white/20'}`}>
                                                <div className={`w-8 h-8 sm:w-10 sm:h-10 rounded-lg flex items-center justify-center transition-all ${isGestorActive ? 'bg-[#F69239] text-white shadow-md rotate-0' : 'bg-white/5 text-white/30 group-hover:text-white -rotate-6'}`}>
                                                    <svg className="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                                    </svg>
                                                </div>
                                                <div className="text-left flex-1">
                                                    <span className={`block text-[11px] sm:text-xs font-black italic uppercase ${isGestorActive ? 'text-white' : 'text-white/40'}`}>Módulo Gestión</span>
                                                    <span className={`text-[7px] sm:text-[8px] font-bold uppercase tracking-widest ${isGestorActive ? 'text-[#F69239]' : 'text-white/20'}`}>Consola Administrativa</span>
                                                </div>
                                                {isGestorActive && <div className="w-2 h-2 bg-[#F69239] rounded-full animate-pulse shadow-[0_0_8px_#f69239]"></div>}
                                            </Link>

                                            <Link href={route('switch-role', 'tecnico')}
                                                className={`flex items-center gap-3 p-3 rounded-xl border transition-all duration-500 group ${isTecnicoActive ? 'bg-[#F69239]/20 border-[#F69239] shadow-[0_0_15px_rgba(246,146,57,0.1)]' : 'bg-white/5 border-white/5 hover:bg-white/10 hover:border-white/20'}`}>
                                                <div className={`w-8 h-8 sm:w-10 sm:h-10 rounded-lg flex items-center justify-center transition-all ${isTecnicoActive ? 'bg-[#F69239] text-white shadow-md rotate-0' : 'bg-white/5 text-white/30 group-hover:text-white -rotate-6'}`}>
                                                    <svg className="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 011-1h1a2 2 0 100-4H7a1 1 0 01-1-1V7a1 1 0 011-1h3a1 1 0 001-1V4z" />
                                                    </svg>
                                                </div>
                                                <div className="text-left flex-1">
                                                    <span className={`block text-[11px] sm:text-xs font-black italic uppercase ${isTecnicoActive ? 'text-white' : 'text-white/40'}`}>Módulo Operativo</span>
                                                    <span className={`text-[7px] sm:text-[8px] font-bold uppercase tracking-widest ${isTecnicoActive ? 'text-[#F69239]' : 'text-white/20'}`}>Interfaz de Campo</span>
                                                </div>
                                                {isTecnicoActive && <div className="w-2 h-2 bg-[#F69239] rounded-full animate-pulse shadow-[0_0_8px_#f69239]"></div>}
                                            </Link>
                                        </div>

                                        <div className="mt-4 p-3 rounded-lg bg-amber-500/10 border border-amber-500/20">
                                            <p className="text-[8px] text-amber-500/80 leading-relaxed italic uppercase font-black tracking-widest text-center">
                                                <span className="text-amber-500 block mb-0.5">⚠️ AVISO DE PROTOCOLO</span>
                                                El cambio de modo es instantáneo y adapta toda la interfaz.
                                            </p>
                                        </div>
                                    </div>
                                )}
                            </div>

                            {/* ── TARJETA DE FIRMA DIGITAL (TECNICO ONLY) ── */}
                            {isTecnico && (
                                <div className="bg-[#16162a]/40 backdrop-blur-md rounded-2xl border border-white/5 p-4 sm:p-5 shadow-lg relative overflow-hidden group/sig h-full flex flex-col justify-between">
                                    <div className="absolute inset-0 bg-gradient-to-b from-[#F69239]/5 to-transparent pointer-events-none"></div>

                                    <h3 className="text-[9px] font-black text-white uppercase tracking-[0.3em] mb-4 italic flex items-center gap-3 relative z-10">
                                        <svg className="w-3.5 h-3.5 text-[#F69239]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                        Firma de Seguridad
                                    </h3>

                                    {/* CONDICIONAL: PENDIENTE DE APROBACIÓN */}
                                    {user_data.firma_status === 'pending' ? (
                                        <div className="space-y-4 relative z-10 text-center py-2 flex-1 flex flex-col justify-center">
                                            <div className="w-12 h-12 rounded-full bg-amber-500/10 border border-amber-500/20 mx-auto flex items-center justify-center text-amber-500 animate-pulse">
                                                <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.5} d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                </svg>
                                            </div>
                                            <span className="inline-flex px-3 py-1 bg-amber-500/10 border border-amber-500/25 rounded-lg text-[9px] font-black uppercase tracking-[0.2em] italic text-amber-500 animate-pulse">
                                                Firma temporal en revisión
                                            </span>
                                            <p className="text-[9px] text-white/40 italic leading-relaxed uppercase mb-2">
                                                Tu firma anterior sigue siendo válida para operar.
                                            </p>
                                            <button 
                                                disabled
                                                className="w-full h-9 bg-amber-500/10 border border-amber-500/30 text-amber-400 rounded-lg text-[8px] font-black uppercase tracking-widest italic cursor-not-allowed flex items-center justify-center gap-1.5 animate-pulse"
                                            >
                                                <span className="w-1 h-1 rounded-full bg-amber-500 animate-pulse"></span>
                                                Pendiente de Aprobación
                                            </button>
                                        </div>
                                    ) : (
                                        <form onSubmit={submitSignature} className="space-y-4 relative z-10 flex-1 flex flex-col justify-between">
                                            {/* CONDICIONAL: RECHAZADA */}
                                            {user_data.firma_status === 'rejected' && (
                                                <div className="p-3 bg-red-500/10 border border-red-500/20 rounded-xl flex items-center gap-2">
                                                    <svg className="w-4 h-4 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2.5">
                                                        <path strokeLinecap="round" strokeLinejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                    </svg>
                                                    <span className="text-[8px] font-black text-red-500 uppercase tracking-widest italic leading-none">
                                                        Firma rechazada. Suba una firma válida.
                                                    </span>
                                                </div>
                                            )}

                                            <div className="space-y-1.5 flex-1 flex flex-col">
                                                <div className="flex justify-between items-center px-1">
                                                    <span className="text-[7px] font-black text-white/40 uppercase tracking-[0.3em] italic">Dibuje su firma en el recuadro</span>
                                                    <button type="button" onClick={() => signaturePad.current?.clear()} className="text-[7px] font-black text-red-500 uppercase italic hover:underline">Limpiar</button>
                                                </div>
                                                <div className="bg-white/5 border border-white/10 rounded-xl overflow-hidden min-h-[8rem] flex-1 touch-none cursor-crosshair relative">
                                                    <canvas ref={canvasRef} className="w-full h-full min-h-[8rem]"></canvas>
                                                    <div className="absolute inset-0 flex items-center justify-center pointer-events-none opacity-[0.02]">
                                                        <span className="text-xs font-black italic uppercase text-white tracking-[0.3em]">XAMASAFE</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <button 
                                                type="submit" 
                                                disabled={signatureForm.processing}
                                                className="w-full h-9 bg-white/5 hover:bg-[#F69239]/20 hover:border-[#F69239]/40 border border-white/10 text-white rounded-lg text-[8px] font-black uppercase tracking-widest italic transition-all flex items-center justify-center gap-2 mt-2"
                                            >
                                                {signatureForm.processing ? 'Guardando...' : 'Enviar Firma a Revisión'}
                                            </button>
                                        </form>
                                    )}

                                    {/* FIRMA ACTIVA ACTUAL (GLOW VERDE SI APROBADA) */}
                                    {user_data.firma && (
                                        <div className="mt-4 pt-4 border-t border-white/5 space-y-2">
                                            <span className="text-[7px] font-black text-emerald-400 uppercase tracking-[0.3em] italic flex items-center gap-1.5">
                                                <span className="w-1 h-1 rounded-full bg-emerald-500 shadow-[0_0_8px_#10b981] animate-pulse"></span>
                                                Firma Certificada Activa
                                            </span>
                                            <div className="w-full h-20 bg-white/5 rounded-xl flex items-center justify-center p-2 border border-emerald-500/10 shadow-[0_0_15px_rgba(16,185,129,0.05)]">
                                                <img src={user_data.firma.startsWith('data:image') ? user_data.firma : `/storage/${user_data.firma}`} alt="Firma Activa" className="max-h-full brightness-0 invert opacity-90" />
                                            </div>
                                        </div>
                                    )}
                                </div>
                            )}

                        </div>

                        {/* ── COLUMNA DERECHA: Forms ── */}
                        <div className={isTecnico ? "lg:col-span-12 grid grid-cols-1 lg:grid-cols-2 gap-6" : "lg:col-span-8 space-y-6"}>
                            
                            {/* Basic Info Form */}
                            <div className="bg-[#16162a]/40 backdrop-blur-md rounded-2xl border border-white/5 overflow-hidden shadow-lg relative">
                                <div className="px-5 py-3 bg-white/5 border-b border-white/5 flex items-center justify-between">
                                    <h3 className="text-[9px] sm:text-[10px] font-black text-white uppercase tracking-[0.3em] italic flex items-center gap-3">
                                        <svg className="w-4 h-4 text-[#F69239]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        Parametrización
                                    </h3>
                                    <div className="h-1.5 w-1.5 rounded-full bg-emerald-500 shadow-[0_0_8px_#10b981] animate-pulse"></div>
                                </div>
                                <div className="p-4 sm:p-5">
                                    <form onSubmit={updateProfile} className="space-y-5">
                                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                            <div className="relative group">
                                                <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[7px] font-black text-[#F69239] uppercase tracking-[0.3em] italic z-10">Nombres</span>
                                                <input id="field_6" name="field_6" 
                                                    type="text" 
                                                    value={profileForm.data.nombres}
                                                    onChange={e => profileForm.setData('nombres', e.target.value)}
                                                    className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-xs font-bold text-white focus:ring-2 focus:ring-[#F69239]/50 transition-all uppercase placeholder-white/5" 
                                                />
                                                {profileForm.errors.nombres && <span className="text-red-500 text-[8px] font-black uppercase italic tracking-widest mt-1 block ml-2">{profileForm.errors.nombres}</span>}
                                            </div>
                                            <div className="relative group">
                                                <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[7px] font-black text-[#F69239] uppercase tracking-[0.3em] italic z-10">Apellidos</span>
                                                <input id="field_5" name="field_5" 
                                                    type="text" 
                                                    value={profileForm.data.apellidos}
                                                    onChange={e => profileForm.setData('apellidos', e.target.value)}
                                                    className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-xs font-bold text-white focus:ring-2 focus:ring-[#F69239]/50 transition-all uppercase placeholder-white/5" 
                                                />
                                                {profileForm.errors.apellidos && <span className="text-red-500 text-[8px] font-black uppercase italic tracking-widest mt-1 block ml-2">{profileForm.errors.apellidos}</span>}
                                            </div>
                                        </div>
                                        <div className="relative group">
                                            <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[7px] font-black text-[#F69239] uppercase tracking-[0.3em] italic z-10">Línea de Contacto (Teléfono)</span>
                                            <input id="field_4" name="field_4" 
                                                type="text" 
                                                value={profileForm.data.telefono}
                                                onChange={e => profileForm.setData('telefono', e.target.value)}
                                                className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-xs font-bold text-white focus:ring-2 focus:ring-[#F69239]/50 transition-all placeholder-white/5" 
                                                placeholder="Ej: +54 9 123 4567" 
                                            />
                                            {profileForm.errors.telefono && <span className="text-red-500 text-[8px] font-black uppercase italic tracking-widest mt-1 block ml-2">{profileForm.errors.telefono}</span>}
                                        </div>
                                        <div className="pt-2">
                                            <button 
                                                type="submit" 
                                                disabled={profileForm.processing}
                                                className="w-full sm:w-auto h-9 px-6 bg-[#F69239] text-white text-[8px] font-black uppercase tracking-[0.2em] italic rounded-lg hover:brightness-110 active:scale-95 transition-all shadow-md shadow-[#F69239]/20 relative group overflow-hidden"
                                            >
                                                <div className="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                                                <span className="relative">{profileForm.processing ? 'Sincronizando...' : 'Sincronizar Perfil'}</span>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            {/* Password Change Form */}
                            <div className="bg-[#16162a]/40 backdrop-blur-md rounded-2xl border border-white/5 overflow-hidden shadow-lg relative">
                                <div className="px-5 py-3 bg-white/5 border-b border-white/5 flex items-center justify-between">
                                    <h3 className="text-[9px] sm:text-[10px] font-black text-white uppercase tracking-[0.3em] italic flex items-center gap-3">
                                        <svg className="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                        Seguridad
                                    </h3>
                                    <div className="p-1 bg-red-500/10 rounded-lg text-red-500 shadow-inner">
                                        <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={3} d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                    </div>
                                </div>
                                <div className="p-4 sm:p-5">
                                    <form onSubmit={updatePassword} className="space-y-5">
                                        <div className="relative group">
                                            <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[7px] font-black text-red-500 uppercase tracking-[0.3em] italic z-10">Contraseña Actual</span>
                                            <input id="field_3" name="field_3" 
                                                type="password" 
                                                value={passwordForm.data.current_password}
                                                onChange={e => passwordForm.setData('current_password', e.target.value)}
                                                className="w-full bg-black/40 border border-white/5 rounded-xl px-4 py-3 text-xs text-white focus:ring-2 focus:ring-red-500/40 transition-all" 
                                            />
                                            {passwordForm.errors.current_password && <span className="text-red-500 text-[8px] font-black uppercase italic tracking-widest mt-1 block ml-2">{passwordForm.errors.current_password}</span>}
                                        </div>
                                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                            <div className="relative group">
                                                <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[7px] font-black text-white/40 uppercase tracking-[0.3em] italic z-10">Nueva Clave</span>
                                                <input id="field_2" name="field_2" 
                                                    type="password" 
                                                    value={passwordForm.data.new_password}
                                                    onChange={e => passwordForm.setData('new_password', e.target.value)}
                                                    className="w-full bg-black/40 border border-white/5 rounded-xl px-4 py-3 text-xs text-white focus:ring-2 focus:ring-[#F69239]/50 transition-all" 
                                                />
                                                {passwordForm.errors.new_password && <span className="text-red-500 text-[8px] font-black uppercase italic tracking-widest mt-1 block ml-2">{passwordForm.errors.new_password}</span>}
                                            </div>
                                            <div className="relative group">
                                                <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[7px] font-black text-white/40 uppercase tracking-[0.3em] italic z-10">Confirmación</span>
                                                <input id="field_1" name="field_1" 
                                                    type="password" 
                                                    value={passwordForm.data.new_password_confirmation}
                                                    onChange={e => passwordForm.setData('new_password_confirmation', e.target.value)}
                                                    className="w-full bg-black/40 border border-white/5 rounded-xl px-4 py-3 text-xs text-white focus:ring-2 focus:ring-[#F69239]/50 transition-all" 
                                                />
                                            </div>
                                        </div>
                                        <div className="pt-2">
                                            <button 
                                                type="submit" 
                                                disabled={passwordForm.processing}
                                                className="w-full sm:w-auto h-9 px-6 bg-white text-black text-[8px] font-black uppercase tracking-[0.2em] italic rounded-lg hover:bg-indigo-100 active:scale-95 transition-all shadow-md shadow-white/5"
                                            >
                                                {passwordForm.processing ? 'Actualizando...' : 'Actualizar Clave'}
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
