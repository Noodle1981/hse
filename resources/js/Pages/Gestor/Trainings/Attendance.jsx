import { Head, Link } from '@inertiajs/react';
import AuthenticatedLayout from '../../../Layouts/AuthenticatedLayout';

export default function Attendance({ session }) {
    const attendees = session.attendees || [];

    return (
        <AuthenticatedLayout title="Registro de Asistencia">
            <Head title={`Asistencia: ${session.title}`} />

            <div className="px-4 sm:px-6 lg:px-8 py-8 text-white selection:bg-blue-500/30">
                <div className="max-w-[100rem] mx-auto space-y-8 animate-fade-in">
                    
                    {/* HUB DE CABECERA */}
                    <div className="bg-[#16162a]/60 backdrop-blur-xl rounded-[3rem] border border-white/5 p-10 shadow-2xl flex flex-col lg:flex-row items-center justify-between gap-8 relative overflow-hidden animate-fade-in-down">
                        <div className="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-transparent via-blue-500 to-transparent"></div>
                        
                        <div className="flex items-center gap-6">
                            <Link href={route('gestor.trainings.index')} className="w-12 h-12 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-white/40 hover:text-white transition-all shadow-lg">
                                <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="3" d="M15 19l-7-7 7-7"/></svg>
                            </Link>
                            <div>
                                <h1 className="text-2xl font-black uppercase italic tracking-tighter leading-none">{session.title}</h1>
                                <p className="text-[10px] font-black text-white/20 uppercase tracking-[0.3em] italic mt-2">Sesión ejecutada el {new Date(session.created_at).toLocaleDateString()} en {session.visit?.sede?.nombre_sede}</p>
                            </div>
                        </div>

                        <div className="flex items-center gap-4">
                            <a href={route('gestor.trainings.pdf.preview', { id: session.id })} target="_blank" className="px-10 py-5 bg-red-600 text-white rounded-[2rem] text-[11px] font-black uppercase tracking-[0.2em] italic transition-all shadow-xl shadow-red-600/20 hover:brightness-110 active:scale-95">Descargar Acta PDF</a>
                        </div>
                    </div>

                    <div className="grid grid-cols-1 lg:grid-cols-12 gap-8">
                        
                        {/* COLUMNA IZQUIERDA: LISTA DE ALUMNOS */}
                        <div className="lg:col-span-12 space-y-12 animate-fade-in-up">
                            <div className="bg-[#16162a]/40 backdrop-blur-md rounded-[3rem] border border-white/5 overflow-hidden shadow-2xl">
                                <div className="px-10 py-6 border-b border-white/5 bg-white/5 flex items-center justify-between">
                                    <h3 className="text-sm font-black text-white italic uppercase tracking-[0.3em] flex items-center gap-3">
                                        <span className="w-1.5 h-6 bg-blue-500 rounded-full"></span>
                                        Nómina de Asistentes Certificados
                                    </h3>
                                    <span className="text-[10px] font-black text-white/20 uppercase tracking-widest italic">{attendees.length} PARTICIPANTES</span>
                                </div>
                                <div className="p-10">
                                    <div className="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
                                        {attendees.map((attendee, index) => (
                                            <div key={index} className="bg-black/20 border border-white/5 rounded-[2.5rem] p-8 flex flex-col items-center gap-6 group hover:border-blue-500/20 transition-all">
                                                <div className="w-full space-y-2 text-center">
                                                    <div className="text-lg font-black text-white uppercase italic tracking-tight">{attendee.name}</div>
                                                    <div className="text-[9px] font-black text-white/20 uppercase tracking-widest italic">{attendee.position || 'OPERARIO SECTOR'}</div>
                                                </div>
                                                
                                                <div className="w-full h-32 bg-white/5 rounded-2xl border border-white/5 flex items-center justify-center overflow-hidden p-4 group-hover:bg-white/10 transition-all">
                                                    {attendee.signature ? (
                                                        <img src={attendee.signature} alt={`Firma ${attendee.name}`} className="max-h-full brightness-0 invert opacity-40 group-hover:opacity-80 transition-opacity" />
                                                    ) : (
                                                        <span className="text-[8px] font-black text-white/10 uppercase italic">Sin Firma Digital</span>
                                                    )}
                                                </div>
                                                
                                                <div className="text-[8px] font-black text-white/10 uppercase tracking-widest italic">REGISTRADO: {attendee.timestamp ? new Date(attendee.timestamp).toLocaleTimeString() : 'N/A'} HRS</div>
                                            </div>
                                        ))}
                                        {attendees.length === 0 && (
                                            <div className="col-span-full py-20 text-center opacity-10 font-black uppercase italic tracking-[0.4em]">
                                                No se registraron asistentes en esta sesión
                                            </div>
                                        )}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

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
