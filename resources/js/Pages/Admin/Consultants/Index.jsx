import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router } from '@inertiajs/react';

export default function Index({ auth, consultants }) {
    const [showDeleteModal, setShowDeleteModal] = useState(false);
    const [deletingConsultant, setDeletingConsultant] = useState(null);

    const openDeleteModal = (consultant) => {
        setDeletingConsultant(consultant);
        setShowDeleteModal(true);
    };

    const confirmDelete = () => {
        if (deletingConsultant) {
            router.delete(route('admin.consultants.destroy', deletingConsultant.id), {
                onSuccess: () => {
                    setShowDeleteModal(false);
                    setDeletingConsultant(null);
                }
            });
        }
    };

    return (
        <AuthenticatedLayout
            user={auth.user}
            title="Súper Administración"
            header={
                <div className="flex flex-col">
                    <h1 className="text-xs font-black text-ep-primary uppercase tracking-[0.4em] italic leading-none mb-2">
                        System Architecture
                    </h1>
                    <h2 className="text-xl font-black text-white uppercase italic tracking-tighter leading-none">
                        Setup de <span className="text-indigo-400">Consultoras</span>
                    </h2>
                </div>
            }
        >
            <Head title="Setup de Consultoras" />

            <div className="py-12 bg-[#0f0f1a] min-h-screen text-white selection:bg-ep-primary/30 animate-fade-in">
                <div className="max-w-[98rem] mx-auto sm:px-6 lg:px-8 space-y-8">
                    
                    {/* Header Action */}
                    <div className="flex justify-between items-center">
                        <p className="text-[10px] font-bold text-white/30 uppercase tracking-widest italic">
                            Gestión de identidades corporativas y licencias.
                        </p>
                        
                        <Link 
                            href={route('admin.consultants.create')} 
                            className="group relative px-6 py-3 bg-ep-primary text-white text-[10px] font-black uppercase italic tracking-widest rounded-xl hover:brightness-110 active:scale-95 transition-all shadow-lg shadow-ep-primary/20 overflow-hidden inline-flex items-center gap-2"
                        >
                            <div className="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2.5"><path strokeLinecap="round" strokeLinejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                            Nueva Consultora Master
                        </Link>
                    </div>

                    {/* Grid of Consultoras */}
                    <div className="grid grid-cols-1 gap-8">
                        {consultants.data.map((c) => {
                            return (
                                <div key={c.id} className="group/card relative bg-[#16162a]/40 backdrop-blur-xl rounded-[2.5rem] border border-white/5 shadow-2xl overflow-hidden hover:border-ep-primary/30 transition-all duration-500">
                                    {/* Card Glow Effect */}
                                    <div className="absolute -right-20 -top-20 w-80 h-80 bg-ep-primary/5 rounded-full blur-[100px] group-hover/card:bg-ep-primary/10 transition-colors duration-1000"></div>
                                    
                                    <div className="relative z-10 px-8 py-8 sm:px-10 sm:py-10">
                                        <div className="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-8">
                                            <div className="flex flex-col md:flex-row items-center gap-6 text-center md:text-left">
                                                {/* Logo */}
                                                <div className="relative group/logo">
                                                    <div className="absolute inset-0 bg-ep-primary/20 rounded-2xl blur-xl opacity-0 group-hover/logo:opacity-100 transition-opacity"></div>
                                                    <div className="relative bg-[#1a1a2e] p-4 rounded-2xl border border-white/10 shadow-2xl transition-transform group-hover/logo:scale-105 duration-300 w-24 h-24 flex items-center justify-center overflow-hidden">
                                                        {c.logo_path ? (
                                                            <img src={`/storage/${c.logo_path}`} alt="Logo" className="max-h-full max-w-full object-contain brightness-110" />
                                                        ) : (
                                                            <div className="w-12 h-12 bg-ep-primary rounded-xl flex items-center justify-center font-black text-white text-xl">
                                                                {c.nombre.charAt(0)}
                                                            </div>
                                                        )}
                                                    </div>
                                                </div>
                                                
                                                <div>
                                                    <div className="flex flex-wrap items-center justify-center md:justify-start gap-3 mb-2">
                                                        <span className="bg-ep-primary/20 border border-ep-primary/30 text-ep-primary text-[8px] font-black uppercase tracking-[0.2em] italic px-3 py-1 rounded-full">
                                                            Unidad de Gestión Master
                                                        </span>
                                                        <span className={`border text-[8px] font-black uppercase tracking-[0.2em] italic px-3 py-1 rounded-full ${
                                                             c.estado_suscripcion === 'activa' ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-400' :
                                                             c.estado_suscripcion === 'morosa' ? 'bg-amber-500/10 border-amber-500/30 text-amber-400 animate-pulse' :
                                                             'bg-red-500/10 border-red-500/30 text-red-400'
                                                         }`}>
                                                             {c.estado_suscripcion === 'activa' ? 'Suscripción Activa' :
                                                              c.estado_suscripcion === 'morosa' ? 'Falta de Pago (Morosa)' :
                                                              'Baja del Servicio'}
                                                         </span>
                                                    </div>
                                                    <h3 className="text-2xl sm:text-3xl font-black text-white italic tracking-tighter uppercase mb-2">
                                                        {c.nombre}
                                                    </h3>
                                                    <div className="flex flex-wrap items-center justify-center md:justify-start gap-4">
                                                        <div className="flex items-center gap-2">
                                                            <span className="text-[8px] font-black text-white/20 uppercase tracking-widest italic">CUIT</span>
                                                            <span className="text-xs font-black text-indigo-300 italic tracking-wider">{c.cuit}</span>
                                                        </div>
                                                        <span className="text-white/10 hidden sm:inline">|</span>
                                                        <div className="flex items-center gap-2">
                                                            <span className="text-[8px] font-black text-white/20 uppercase tracking-widest italic">Alta</span>
                                                            <span className="text-xs font-black text-indigo-100/60 italic tracking-wider">
                                                                {new Date(c.created_at).toLocaleDateString('es-AR')}
                                                            </span>
                                                        </div>
                                                        <span className="text-white/10 hidden sm:inline">|</span>
                                                        <div className="flex items-center gap-2">
                                                            <span className="text-[8px] font-black text-white/20 uppercase tracking-widest italic">Creador</span>
                                                            <span className="text-xs font-black text-indigo-100/60 italic tracking-wider">
                                                                {c.suscripcion_logs?.[0]?.user?.full_name || c.suscripcion_logs?.[0]?.user?.name || 'Súper Administrador'}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            {/* Action Buttons */}
                                            <div className="flex flex-wrap items-center gap-3 w-full lg:w-auto justify-center lg:justify-end relative z-20">
                                                <Link 
                                                    href={route('admin.consultants.edit', c.id)} 
                                                    className="flex-1 lg:flex-none flex items-center justify-center px-6 py-3.5 bg-white/5 hover:bg-white/10 backdrop-blur-md text-white text-[9px] font-black uppercase tracking-widest italic rounded-xl border border-white/10 transition-all"
                                                >
                                                    <svg className="w-4 h-4 mr-2 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2.5"><path strokeLinecap="round" strokeLinejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>
                                                    Configuración
                                                </Link>
                                                {auth.user.is_super_admin && (
                                                    <>
                                                        <Link 
                                                            href={route('admin.consultants.users.index', c.id)} 
                                                            className="flex-1 lg:flex-none flex items-center justify-center px-6 py-3.5 bg-indigo-600 hover:bg-indigo-500 text-white text-[9px] font-black uppercase tracking-widest italic rounded-xl shadow-lg transition-all"
                                                        >
                                                            <svg className="w-4 h-4 mr-2 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2.5"><path strokeLinecap="round" strokeLinejoin="round" d="M18 18.72a9.004 9.004 0 00-12 0M12 10a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5A.75.75 0 0112 10zm0 3.75a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM12 6.25a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM3.375 7.5h.008v.008H3.375V7.5zm0 3.75h.008v.008H3.375v-.008zm0 3.75h.008v.008H3.375v-.008z" /></svg>
                                                            Gestionar Staff
                                                        </Link>
                                                        {c.users_count === 0 && (
                                                            <button 
                                                                onClick={() => openDeleteModal(c)}
                                                                className="h-11 w-11 flex items-center justify-center bg-red-500/10 hover:bg-red-500 text-red-500 hover:text-white rounded-xl border border-red-500/20 hover:border-red-500 transition-all"
                                                                title="Eliminar Consultora"
                                                            >
                                                                <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2.5"><path strokeLinecap="round" strokeLinejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                                                            </button>
                                                        )}
                                                    </>
                                                )}
                                            </div>
                                        </div>

                                        {/* Metrics Row */}
                                        <div className="mt-8 pt-8 border-t border-white/5 grid grid-cols-2 sm:grid-cols-4 gap-6">
                                            <div>
                                                <span className="text-[8px] font-black text-white/20 uppercase tracking-widest italic block mb-1">Técnicos Activos</span>
                                                <div className="flex items-center gap-3">
                                                    <span className="text-xl font-black text-indigo-300 italic">{c.tecnicos_count || 0} / {c.max_tecnicos || 5}</span>
                                                    <div className="flex-1 max-w-[100px] h-1.5 bg-black/40 rounded-full overflow-hidden border border-white/5">
                                                        <div className="h-full bg-ep-primary" style={{ width: `${Math.min(100, ((c.tecnicos_count || 0) / (c.max_tecnicos || 5)) * 100)}%` }}></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <span className="text-[8px] font-black text-white/20 uppercase tracking-widest italic block mb-1">Gestores Activos</span>
                                                <div className="flex items-center gap-3">
                                                    <span className="text-xl font-black text-indigo-300 italic">{c.gestores_count || 0} / {c.max_gestores || 2}</span>
                                                    <div className="flex-1 max-w-[100px] h-1.5 bg-black/40 rounded-full overflow-hidden border border-white/5">
                                                        <div className="h-full bg-emerald-500" style={{ width: `${Math.min(100, ((c.gestores_count || 0) / (c.max_gestores || 2)) * 100)}%` }}></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <span className="text-[8px] font-black text-white/20 uppercase tracking-widest italic block mb-1">Empresas Clientes</span>
                                                <div className="flex items-center gap-3">
                                                    <span className="text-xl font-black text-white italic">{c.empresas_count || 0} / {c.max_empresas || 5}</span>
                                                    <div className="flex-1 max-w-[100px] h-1.5 bg-black/40 rounded-full overflow-hidden border border-white/5">
                                                        <div className="h-full bg-amber-500" style={{ width: `${Math.min(100, ((c.empresas_count || 0) / (c.max_empresas || 5)) * 100)}%` }}></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <span className="text-[8px] font-black text-white/20 uppercase tracking-widest italic block mb-1">Contacto Central</span>
                                                <div className="text-[10px] font-bold text-white/60 italic leading-snug">
                                                    <div className="truncate">{c.email || 'N/A'}</div>
                                                    <div className="text-[9px] text-white/30 uppercase tracking-wider mt-0.5">{c.province || 'N/A'}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            );
                        })}
                    </div>

                    {/* Pagination */}
                    {consultants.links && consultants.links.length > 3 && (
                        <div className="flex justify-center gap-2 mt-8">
                            {consultants.links.map((link, i) => (
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

            {showDeleteModal && deletingConsultant && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-md animate-fade-in">
                    <div className="w-full max-w-md bg-[#16162a] border border-white/10 rounded-[2.5rem] p-8 sm:p-10 shadow-2xl relative animate-scale-up">
                        {/* Close button */}
                        <button 
                            onClick={() => setShowDeleteModal(false)}
                            className="absolute top-6 right-6 text-white/40 hover:text-white transition-colors text-lg"
                        >
                            ✕
                        </button>

                        <div className="mb-6">
                            <h3 className="text-xs font-black text-red-500 uppercase tracking-[0.4em] italic mb-1">
                                Confirmar Acción
                            </h3>
                            <h4 className="text-lg font-black text-white uppercase italic tracking-tighter leading-none">
                                Eliminar <span className="text-red-400">Consultora</span>
                            </h4>
                        </div>

                        <div className="space-y-6">
                            <p className="text-xs text-indigo-100/70 font-semibold leading-relaxed">
                                ¿Está seguro de que desea eliminar la consultora máster <strong className="text-white uppercase italic">{deletingConsultant.nombre}</strong>?
                            </p>
                            <div className="bg-red-500/10 border border-red-500/20 rounded-2xl p-4 text-[10px] font-bold text-red-400 uppercase tracking-widest italic">
                                ⚠️ Esta acción eliminará permanentemente la consultora de la base de datos de XamaSafe. Esta acción no se puede deshacer.
                            </div>

                            <div className="flex justify-end gap-3 pt-4 border-t border-white/5">
                                <button 
                                    type="button"
                                    onClick={() => setShowDeleteModal(false)}
                                    className="px-5 py-3.5 bg-white/5 hover:bg-white/10 text-white text-[10px] font-black uppercase italic tracking-widest rounded-xl transition-all border border-white/5"
                                >
                                    Cancelar
                                </button>
                                <button 
                                    type="button"
                                    onClick={confirmDelete}
                                    className="px-6 py-3.5 bg-red-600 hover:bg-red-500 text-white text-[10px] font-black uppercase italic tracking-widest rounded-xl transition-all shadow-lg shadow-red-600/20 active:scale-95"
                                >
                                    Eliminar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            )}
        </AuthenticatedLayout>
    );
}
