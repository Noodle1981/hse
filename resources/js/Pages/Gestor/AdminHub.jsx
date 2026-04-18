import { Head, Link, usePage } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function Hub({ consultoraName }) {
    const { branding } = usePage().props;

    return (
        <AuthenticatedLayout title="Control Central">
            <Head title="HUB de Administración" />

            <div className="px-4 sm:px-6 lg:px-8 py-12 text-white selection:bg-[#F69239]/30">
                <div className="max-w-[95rem] mx-auto space-y-12 animate-fade-in">
                    
                    {/* Central Intelligence Block */}
                    <div className="relative overflow-hidden bg-[#16162a]/40 backdrop-blur-md rounded-[3rem] border border-white/5 p-10 shadow-2xl group/hub">
                        <div className="absolute -top-24 -right-24 w-96 h-96 bg-[#F69239]/10 rounded-full blur-[100px] opacity-0 group-hover/hub:opacity-100 transition-opacity duration-1000"></div>
                        <div className="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                            <div>
                                <h2 className="text-3xl font-black text-white italic tracking-tight mb-2 uppercase">
                                    HUB DE MANDO <span className="text-[#F69239]">{consultoraName}</span>
                                </h2>
                                <p className="text-sm text-white/40 max-w-xl font-medium tracking-wide">
                                    Acceso unificado a la gestión de activos, identidades y recursos corporativos. Seleccione un módulo para iniciar operaciones de alto nivel.
                                </p>
                            </div>
                            <div className="flex items-center gap-3">
                                <div className="h-3 w-3 bg-emerald-500 rounded-full animate-pulse shadow-[0_0_10px_#10b981]"></div>
                                <span className="text-[10px] font-black text-emerald-400 uppercase tracking-widest italic leading-none">Sistema Online</span>
                            </div>
                        </div>
                    </div>

                    {/* Operational Modules Grid */}
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        
                        {/* Usuarios Module */}
                        <ModuleCard 
                            href={route('gestor.users.index')}
                            title="Usuarios"
                            description="GESTIÓN CENTRALIZADA DE IDENTIDADES Y MATRIZ DE PERMISOS"
                            icon={<IconUsers />}
                            gradient="from-purple-500/20 to-purple-900/40"
                            accentColor="text-purple-400"
                            borderHover="group-hover/card:bg-purple-500"
                            shadow="shadow-[0_0_30px_rgba(168,85,247,0.2)]"
                            buttonText="INICIAR TERMINAL"
                            buttonTheme="text-purple-500 bg-purple-500/10 border-purple-500/20 shadow-purple-500/20"
                        />

                        {/* Técnicos Module */}
                        <ModuleCard 
                            href={route('gestor.technicians.index')}
                            title="Escuadrón"
                            description="CONTROL DE TÉCNICOS Y ASIGNACIÓN DE RECURSOS EN CAMPO"
                            icon={<IconTechnicians />}
                            gradient="from-indigo-500/20 to-indigo-900/40"
                            accentColor="text-indigo-400"
                            borderHover="group-hover/card:bg-indigo-500"
                            shadow="shadow-[0_0_30px_rgba(99,102,241,0.2)]"
                            buttonText="DESPLEGAR UNIDADES"
                            buttonTheme="text-indigo-500 bg-indigo-500/10 border-indigo-500/20 shadow-indigo-500/20"
                        />

                        {/* Empresas Module */}
                        <ModuleCard 
                            href={route('gestor.companies.index')}
                            title="Corporativo"
                            description="INTELIGENCIA EMPRESARIAL Y GESTIÓN DE SEDES GLOBALES"
                            icon={<IconCompanies />}
                            gradient="from-blue-500/20 to-blue-900/40"
                            accentColor="text-blue-400"
                            borderHover="group-hover/card:bg-blue-500"
                            shadow="shadow-[0_0_30px_rgba(59,130,246,0.2)]"
                            buttonText="RECURSOS CLIENTE"
                            buttonTheme="text-blue-500 bg-blue-500/10 border-blue-500/20 shadow-blue-500/20"
                        />
                    </div>

                    {/* Footer Message */}
                    <div className="text-center pt-8 animate-fade-in">
                        <p className="text-[9px] font-black text-white/5 uppercase tracking-[0.8em] italic leading-relaxed">
                            XamaSafe Ecosystem Core • Global Administration Matrix • Pro-Efficiency HUD
                        </p>
                    </div>
                </div>
            </div>

            <style>{`
                .animate-fade-in { animation: fade-in 1s ease-out forwards; }
                @keyframes fade-in { from { opacity: 0; } to { opacity: 1; } }
            `}</style>
        </AuthenticatedLayout>
    );
}

function ModuleCard({ href, title, description, icon, gradient, accentColor, borderHover, shadow, buttonText, buttonTheme }) {
    return (
        <Link href={href} 
           className="group/card relative bg-[#131326]/30 backdrop-blur-md border border-white/5 rounded-[2.5rem] p-8 hover:bg-white/5 transition-all duration-500 shadow-xl overflow-hidden">
            <div className={`absolute inset-0 bg-gradient-to-br from-white/5 to-transparent opacity-0 group-hover/card:opacity-100 transition-opacity duration-500`}></div>
            <div className="relative z-10 flex flex-col items-center text-center">
                <div className={`w-24 h-24 bg-gradient-to-br ${gradient} rounded-3xl flex items-center justify-center mb-8 border border-white/10 group-hover/card:scale-110 transition-transform duration-500 ${shadow}`}>
                    {icon}
                </div>
                
                <h3 className={`text-2xl font-black text-white italic tracking-tight mb-3 ${accentColor} transition-colors uppercase`}>
                    {title}
                </h3>
                
                <p className="text-xs text-white/40 mb-8 max-w-[200px] leading-relaxed font-bold tracking-wide italic uppercase">
                    {description}
                </p>
                
                <div className={`flex items-center text-xs font-black uppercase tracking-widest gap-2 ${buttonTheme} px-6 py-3 rounded-full border group-hover/card:brightness-125 transition-all duration-300`}>
                    {buttonText}
                    <svg className="w-4 h-4 transform group-hover/card:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={3} d="M9 5l7 7-7 7"/>
                    </svg>
                </div>
            </div>
        </Link>
    );
}

const IconUsers = () => (
    <svg className="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
    </svg>
);

const IconTechnicians = () => (
    <svg className="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
    </svg>
);

const IconCompanies = () => (
    <svg className="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
    </svg>
);
