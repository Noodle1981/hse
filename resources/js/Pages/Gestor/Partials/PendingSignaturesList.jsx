import { router } from '@inertiajs/react';
import { useState, useEffect } from 'react';

export default function PendingSignaturesList() {
    const [pendingUsers, setPendingUsers] = useState([]);
    const [loading, setLoading] = useState(true);

    // Custom Modal States
    const [showConfirmModal, setShowConfirmModal] = useState(false);
    const [confirmAction, setConfirmAction] = useState(null); // 'approve' | 'reject'
    const [targetUser, setTargetUser] = useState(null);

    const fetchPending = async () => {
        try {
            const response = await fetch(route('gestor.signatures.pending'));
            if (response.ok) {
                const data = await response.json();
                setPendingUsers(data);
            }
        } catch (error) {
            console.error('Error fetching pending signatures:', error);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchPending();
    }, []);

    const openConfirm = (user, action) => {
        setTargetUser(user);
        setConfirmAction(action);
        setShowConfirmModal(true);
    };

    const executeAction = () => {
        if (!targetUser || !confirmAction) return;

        const routeName = confirmAction === 'approve' 
            ? 'gestor.signatures.approve' 
            : 'gestor.signatures.reject';

        router.post(route(routeName, targetUser.id), {}, {
            preserveScroll: true,
            onStart: () => setShowConfirmModal(false),
            onSuccess: () => {
                setTargetUser(null);
                setConfirmAction(null);
                fetchPending();
            }
        });
    };

    if (loading) {
        return (
            <div className="bg-white/5 backdrop-blur-xl rounded-[2rem] border border-white/10 p-8 text-center animate-pulse">
                <span className="text-[10px] font-black uppercase tracking-[0.3em] text-white/30 italic">Buscando auditorías de firma...</span>
            </div>
        );
    }

    if (pendingUsers.length === 0) {
        return null; // Don't render anything if there are no pending reviews.
    }

    return (
        <div className="bg-[#1b1b36]/60 backdrop-blur-xl rounded-[2.5rem] border border-amber-500/20 p-6 sm:p-8 shadow-2xl relative overflow-hidden animate-fade-in-down">
            <div className="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-amber-500/50 to-transparent"></div>
            
            <div className="flex items-center gap-3 mb-6">
                <div className="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse shadow-[0_0_10px_#f59e0b]"></div>
                <h3 className="text-xs font-black text-amber-500 uppercase tracking-[0.2em] italic">
                    Auditoría de Firmas: {pendingUsers.length} Pendiente(s) de Aprobación
                </h3>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {pendingUsers.map(user => (
                    <div key={user.id} className="bg-white/5 border border-white/10 rounded-3xl p-5 flex flex-col justify-between gap-5 relative group hover:border-amber-500/20 transition-all duration-300">
                        <div className="flex justify-between items-start gap-4">
                            <div>
                                <h4 className="text-sm font-black text-white italic uppercase tracking-tight leading-none mb-1">
                                    {user.apellidos}, {user.nombres}
                                </h4>
                                <span className="text-[8px] font-black text-[#F69239] uppercase tracking-widest italic bg-[#F69239]/10 border border-[#F69239]/20 px-2.5 py-1 rounded-lg">
                                    {user.matricula || 'SIN MATRÍCULA'}
                                </span>
                            </div>
                        </div>

                        {/* IMAGEN DE FIRMA CON INVERSIÓN OBLIGATORIA */}
                        <div className="w-full h-48 bg-white/5 border border-white/5 rounded-2xl flex items-center justify-center p-4 relative overflow-hidden">
                            <img 
                                src={`/storage/${user.firma_temp}`} 
                                  alt="Propuesta de firma" 
                                className="max-h-full max-w-full brightness-0 invert opacity-90 transition-transform group-hover:scale-105 duration-500" 
                            />
                            <div className="absolute top-2 right-2 text-[7px] font-black text-white/20 uppercase tracking-widest italic">
                                Propuesta
                            </div>
                        </div>

                        {/* BOTONES TÁCTICOS (TACTICAL PILLS) */}
                        <div className="grid grid-cols-2 gap-3">
                            <button 
                                onClick={() => openConfirm(user, 'reject')}
                                className="h-10 border border-red-500/20 bg-red-500/5 hover:bg-red-500/15 text-red-400 hover:text-white rounded-xl text-[9px] font-black uppercase tracking-widest italic transition-all active:scale-95"
                            >
                                Rechazar
                            </button>
                            <button 
                                onClick={() => openConfirm(user, 'approve')}
                                className="h-10 border border-emerald-500/30 bg-emerald-500/5 hover:bg-emerald-500 text-emerald-400 hover:text-white rounded-xl text-[9px] font-black uppercase tracking-widest italic transition-all shadow-md hover:shadow-emerald-500/20 active:scale-95"
                            >
                                Aprobar
                            </button>
                        </div>
                    </div>
                ))}
            </div>

            {/* Premium Confirmation Modal */}
            {showConfirmModal && targetUser && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 animate-fade-in">
                    <div className="absolute inset-0 bg-black/85 backdrop-blur-sm" onClick={() => { setShowConfirmModal(false); setConfirmAction(null); setTargetUser(null); }}></div>
                    <div className="bg-[#16162a] border border-white/10 rounded-[2.5rem] p-8 max-w-md w-full relative z-10 shadow-2xl overflow-hidden text-center">
                        <div className={`absolute inset-x-0 top-0 h-1 ${confirmAction === 'approve' ? 'bg-emerald-500/50' : 'bg-red-500/50'}`}></div>
                        
                        <div className={`w-16 h-16 rounded-[1.5rem] ${confirmAction === 'approve' ? 'bg-emerald-500/10 border-emerald-500/20 text-emerald-400' : 'bg-red-500/10 border-red-500/20 text-red-500'} border mx-auto flex items-center justify-center mb-6 shadow-inner animate-pulse`}>
                            {confirmAction === 'approve' ? (
                                <svg className="w-8 h-8 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2.5">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            ) : (
                                <svg className="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2.5">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            )}
                        </div>

                        <h3 className="text-base font-black text-white uppercase italic tracking-widest mb-3">
                            {confirmAction === 'approve' ? 'Confirmar Aprobación' : 'Confirmar Rechazo'}
                        </h3>
                        <p className="text-[11px] text-white/40 font-bold uppercase tracking-wide leading-relaxed italic mb-8">
                            {confirmAction === 'approve' ? (
                                <>¿Estás seguro de inyectar y autorizar la firma digital propuesta para <span className="text-emerald-400">{targetUser.nombres} {targetUser.apellidos}</span> de forma oficial?</>
                            ) : (
                                <>¿Estás seguro de rechazar la propuesta de firma para <span className="text-red-400">{targetUser.nombres} {targetUser.apellidos}</span>? Se eliminará permanentemente el archivo temporal.</>
                            )}
                        </p>

                        <div className="flex justify-center gap-4">
                            <button 
                                onClick={() => { setShowConfirmModal(false); setConfirmAction(null); setTargetUser(null); }}
                                className="px-6 py-3 bg-white/5 border border-white/5 hover:border-white/10 text-white/60 hover:text-white rounded-xl text-[9px] font-black uppercase tracking-widest italic transition-all active:scale-95"
                            >
                                Cancelar
                            </button>
                            <button 
                                onClick={executeAction}
                                className={`px-8 py-3 ${confirmAction === 'approve' ? 'bg-emerald-500 hover:shadow-emerald-500/20' : 'bg-red-600 hover:shadow-red-500/20'} text-white rounded-xl text-[9px] font-black uppercase tracking-widest italic transition-all shadow-lg hover:brightness-110 active:scale-95`}
                            >
                                Confirmar Acción
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
}
