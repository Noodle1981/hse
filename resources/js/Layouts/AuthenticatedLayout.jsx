import { useState, useEffect } from 'react';
import { usePage, Link, router } from '@inertiajs/react';
import { route } from 'ziggy-js';

export default function AuthenticatedLayout({ children, title, header }) {
    const { props, url } = usePage();
    const { auth = {}, branding = {}, flash = {}, pendingResetRequests = 0 } = props;
    const user = auth?.user || {};
    const isAdminView = url.startsWith('/admin');

    const [sidebarOpen, setSidebarOpen] = useState(window.innerWidth >= 1024);
    const [mobileMenuOpen, setMobileMenuOpen] = useState(false);
    const [flashMsg, setFlashMsg] = useState(null);

    // Helper para rutas seguras (Evita que el layout colapse si Ziggy no encuentra una ruta)
    const safeRoute = (name, params = null) => {
        try {
            return route(name, params);
        } catch (e) {
            console.warn(`[Ziggy] Route "${name}" not found.`);
            return '#';
        }
    };

    // Manejar flash messages
    useEffect(() => {
        if (flash?.message || flash?.error || flash?.warning) {
            setFlashMsg(flash);
            const timer = setTimeout(() => setFlashMsg(null), 4000);
            return () => clearTimeout(timer);
        }
    }, [flash]);

    const logout = (e) => {
        e.preventDefault();
        router.post(safeRoute('logout'));
    };

    const isActive = (...routeNames) => {
        try {
            return routeNames.some((name) => route().current(name));
        } catch (e) { return false; }
    };

    return (
        <div 
            className="flex min-h-screen bg-[#11111d] text-white selection:bg-ep-primary/30"
            style={{ 
                '--ep-primary-color': branding?.primary_color || '#F69239',
                '--ep-dark-color': branding?.dark_color || '#1E1E2D',
            }}
        >
            {/* ── SIDEBAR ── */}
            <aside
                className={`fixed left-0 top-0 h-screen bg-[#16162a] z-40 flex flex-col
                    transition-all duration-300 ease-in-out lg:relative lg:translate-x-0
                    ${sidebarOpen || mobileMenuOpen ? 'w-64' : 'w-20'}
                    ${mobileMenuOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'}
                `}
            >
                {/* Marca / Logo */}
                <div className="h-16 flex items-center justify-between px-4 border-b border-white/10 overflow-hidden flex-shrink-0">
                    <Link href={safeRoute('dashboard')} className="flex items-center space-x-3 group">
                        <div className="w-10 h-10 bg-[#151528] p-1 rounded-lg flex items-center justify-center flex-shrink-0 shadow-lg border border-white/10 group-hover:scale-105 transition-transform duration-300">
                            {isAdminView ? (
                                <img src="/images/Logo_login.png" alt="Logo" className="max-h-full w-auto object-contain" />
                            ) : (user?.is_super_admin || user?.is_vendedor) ? (
                                <img src="/images/Logo_login.png" alt="Logo Industrias Xamanen" className="max-h-full w-auto object-contain" />
                            ) : branding?.logo ? (
                                <img src={branding.logo} alt="Logo" className="max-h-full w-auto object-contain" />
                            ) : (
                                <div className="w-6 h-6 bg-ep-primary rounded-sm shadow-inner" />
                            )}
                        </div>
                        {(sidebarOpen || mobileMenuOpen) && (
                            <div className="flex flex-col">
                                <span className="text-white font-black uppercase italic tracking-tighter text-sm leading-none truncate max-w-[150px]" title={(user?.is_super_admin || user?.rol === 'super_admin') ? 'Panel Súper Usuario' : user?.is_vendedor ? 'Panel Vendedor' : (branding?.name || 'EP')}>
                                    {(user?.is_super_admin || user?.rol === 'super_admin') ? 'Panel Súper Usuario' : user?.is_vendedor ? 'Panel Vendedor' : (branding?.name || 'EQUIPO PREVENTIVO')}
                                </span>
                                {isAdminView ? (
                                    <span className="font-bold uppercase italic tracking-[0.2em] text-[8px] mt-1 truncate max-w-[140px]">
                                        <span className="text-white">Industrias</span> <span className="text-ep-primary">Xamanen</span>
                                    </span>
                                ) : (
                                    <span className="font-bold uppercase italic tracking-[0.2em] text-[8px] mt-1 truncate max-w-[140px]">
                                        <span className="text-white">Xama</span><span className="text-ep-primary">Safe</span>
                                    </span>
                                )}
                            </div>
                        )}
                    </Link>
                    <button
                        onClick={() => setSidebarOpen(!sidebarOpen)}
                        className="hidden lg:block text-[#F69239] hover:text-orange-400 p-1 flex-shrink-0"
                    >
                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2}
                                d={sidebarOpen ? 'M11 19l-7-7 7-7m8 14l-7-7 7-7' : 'M13 5l7 7-7 7M5 5l7 7-7 7'} />
                        </svg>
                    </button>
                </div>

                {/* Menú */}
                <nav className="flex-1 overflow-y-auto py-2 scrollbar-thin scrollbar-thumb-[#F69239]/40 scrollbar-track-white/5">
                    <ul className="space-y-1 px-3">

                        {/* ── TÉCNICO ── */}
                        {user?.is_technician && (
                            <>
                                <NavItem href={safeRoute('tecnico.checklists.tasks.index')} active={isActive('tecnico.checklists.tasks.*')} open={sidebarOpen || mobileMenuOpen} icon={<IconTasks />} label="Mis Tareas" />
                                <NavItem href={safeRoute('tecnico.visits.index')} active={isActive('tecnico.visits.index', 'tecnico.visits.in-progress')} open={sidebarOpen || mobileMenuOpen} icon={<IconLocation />} label="Mis Visitas" />
                                <NavItem href={safeRoute('tecnico.hallazgos.index')} active={isActive('tecnico.hallazgos.index')} open={sidebarOpen || mobileMenuOpen} icon={<IconDoc />} label="Mis Hallazgos" />
                                <NavItem href={safeRoute('tecnico.incidentes.index')} active={isActive('tecnico.incidentes.index')} open={sidebarOpen || mobileMenuOpen} icon={<IconAlert />} label="Mis Incidentes" />
                            </>
                        )}

                        {/* ── ADMIN / GESTOR ── */}
                        {user?.is_admin && !user?.is_super_admin && (
                            <>
                                <NavItem href={safeRoute('gestor.operations.visits.index')} active={isActive('gestor.operations.visits.*')} open={sidebarOpen || mobileMenuOpen} icon={<IconLocation />} label="Control de Visitas" />
                                <NavItem href={safeRoute('gestor.inspections.index')} active={isActive('gestor.inspections.*')} open={sidebarOpen || mobileMenuOpen} icon={<IconClock />} label="Historial de Inspecciones" />
                                <NavItem href={safeRoute('gestor.checklists.index')} active={isActive('gestor.checklists.*', 'gestor.categories.*')} open={sidebarOpen || mobileMenuOpen} icon={<IconDoc />} label="Plantillas de Checklist" />
                                <NavItem href={safeRoute('gestor.assets.index')} active={isActive('gestor.assets.*', 'gestor.asset-types.*')} open={sidebarOpen || mobileMenuOpen} icon={<IconAssets />} label="Activos y Equipamiento" />
                                <NavItem href={safeRoute('gestor.trainings.index')} active={isActive('gestor.trainings.*')} open={sidebarOpen || mobileMenuOpen} icon={<IconTraining />} label="Capacitaciones" />
                                <NavItem href={safeRoute('gestor.hallazgos.index')} active={isActive('gestor.hallazgos.*')} open={sidebarOpen || mobileMenuOpen} icon={<IconWarning />} label="Hallazgos" />
                                <NavItem href={safeRoute('gestor.incidentes.index')} active={isActive('gestor.incidentes.*')} open={sidebarOpen || mobileMenuOpen} icon={<IconAlert />} label="Incidentes" />
                            </>
                        )}

                        {/* ── SUPER ADMIN y VENDEDOR ── */}
                        {(user?.is_super_admin || user?.is_vendedor) && (
                            <>
                                <NavItem href={safeRoute('admin.dashboard')} active={isActive('admin.dashboard')} open={sidebarOpen || mobileMenuOpen} icon={<IconDashboard />} label="Dashboard Global" />
                                <NavItem href={safeRoute('admin.consultants.index')} active={isActive('admin.consultants.*')} open={sidebarOpen || mobileMenuOpen} icon={<IconCompanies />} label="Consultoras" />
                                {user?.is_super_admin && (
                                    <NavItem href={safeRoute('admin.staff.index')} active={isActive('admin.staff.*')} open={sidebarOpen || mobileMenuOpen} icon={<IconStaff />} label="Personal / Staff" />
                                )}
                            </>
                        )}

                        {/* Solicitudes de Reseteo (Super Admin, Vendedor, Gestor Principal) */}
                        {(user?.is_super_admin || user?.is_vendedor || user?.is_primary_gestor || pendingResetRequests > 0) && (
                            <NavItem 
                                href={safeRoute('admin.password-reset-requests.index')} 
                                active={isActive('admin.password-reset-requests.*')} 
                                open={sidebarOpen || mobileMenuOpen} 
                                icon={<IconKey />} 
                                label="Solicitudes de Clave" 
                                badge={pendingResetRequests} 
                            />
                        )}

                        {/* Informe Maestro */}
                        {!user?.is_global_user && (
                            <NavItem
                                href={safeRoute('master-reports.index')}
                                active={isActive('master-reports.*')}
                                open={sidebarOpen || mobileMenuOpen}
                                icon={<IconDoc />}
                                label="Informe Maestro"
                            />
                        )}

                        {/* Contrato Anual */}
                        {!user?.is_global_user && (
                            <NavItem
                                href={safeRoute('annual-contract.index')}
                                active={isActive('annual-contract.*')}
                                open={sidebarOpen || mobileMenuOpen}
                                icon={<IconContract />}
                                label="Contrato Anual"
                            />
                        )}

                        {/* Tutoriales & Centro de Ayuda */}
                        <NavItem
                            href={safeRoute('tutorials.index')}
                            active={isActive('tutorials.*')}
                            open={sidebarOpen || mobileMenuOpen}
                            icon={<IconBook />}
                            label="Tutoriales"
                        />

                        {user?.is_admin && !user?.is_global_user && (
                            <NavItem href={safeRoute('gestor.administration.hub')} active={isActive('gestor.administration.hub', 'gestor.users.*', 'gestor.technicians.*', 'gestor.companies.*')} open={sidebarOpen || mobileMenuOpen} icon={<IconSettings />} label="Administración" />
                        )}

                        {/* Dashboard (solo no técnicos y no super admins) */}
                        {!user?.is_technician && !user?.is_global_user && (
                            <NavItem href={safeRoute('dashboard')} active={isActive('dashboard')} open={sidebarOpen || mobileMenuOpen} icon={<IconDashboard />} label="Dashboard" />
                        )}

                        <NavItem href={safeRoute('profile')} active={isActive('profile')} open={sidebarOpen || mobileMenuOpen} icon={<IconUser />} label="Mi Perfil" />
                    </ul>
                </nav>

                {/* Footer sidebar */}
                <div className="p-4 border-t border-white/10 flex-shrink-0">
                    <button
                        onClick={logout}
                        className={`w-full flex items-center justify-center gap-2 px-3 py-2 rounded-lg text-gray-400 hover:bg-gray-700 hover:text-white transition-colors`}
                    >
                        <svg className="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        {(sidebarOpen || mobileMenuOpen) && <span className="text-sm font-medium">Cerrar Sesión</span>}
                    </button>
                    {(sidebarOpen || mobileMenuOpen) && (
                        <div className="text-xs text-gray-600 text-center mt-3">XamaSafe v2.0</div>
                    )}
                </div>
            </aside>

            {/* Overlay móvil */}
            {mobileMenuOpen && (
                <div
                    className="fixed inset-0 bg-black/50 z-30 lg:hidden"
                    onClick={() => setMobileMenuOpen(false)}
                />
            )}

            {/* ── CONTENIDO PRINCIPAL ── */}
            <div className="flex-1 flex flex-col h-screen overflow-hidden">
                {/* Header */}
                <header className="h-16 bg-[#16162a]/80 backdrop-blur-md shadow-lg border-b border-white/5 flex items-center justify-between px-3 sm:px-6 lg:px-8 flex-shrink-0 z-20">
                    {/* Botón menú móvil */}
                    <button
                        onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
                        className="lg:hidden p-2 text-gray-400 hover:text-white"
                    >
                        <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    {/* Título de la página */}
                    <div className="flex-1 overflow-hidden min-w-0">
                        {header ? (
                            header
                        ) : (
                            <h1 className="text-sm sm:text-xl font-black text-ep-primary tracking-tight uppercase italic truncate max-w-[150px] sm:max-w-none">
                                {title ?? 'XamaSafe'}
                            </h1>
                        )}
                    </div>

                    {/* Lado derecho: role switcher + usuario */}
                    <div className="flex items-center space-x-3 md:space-x-6">
                        {/* Role Switcher */}
                        {(user?.rol === 'gestor' && !user?.is_super_admin && user?.rol !== 'super_admin') && (
                            user?.active_role === 'tecnico' ? (
                                <Link
                                    href={safeRoute('switch-role', 'gestor')}
                                    className="bg-[#F69239] text-white px-3 py-1.5 rounded-full text-xs font-bold shadow-md hover:brightness-110 transition-all flex items-center gap-1"
                                >
                                    <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={3} d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                                    </svg>
                                    Modo Gestor
                                </Link>
                            ) : (
                                <Link
                                    href={safeRoute('switch-role', 'tecnico')}
                                    className="bg-gray-200 text-gray-700 px-3 py-1.5 rounded-full text-xs font-bold shadow-sm hover:bg-gray-300 transition-all flex items-center gap-1 border border-gray-300"
                                >
                                    <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={3} d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                                    </svg>
                                    Modo Técnico
                                </Link>
                            )
                        )}

                        {/* Info usuario */}
                        <Link href={safeRoute('profile')} className="flex items-center space-x-2 hover:opacity-80 transition-opacity">
                            <div className="hidden sm:flex flex-col items-end mr-1">
                                <span className="text-xs font-black text-white uppercase italic tracking-wide">{user?.nombres || 'Usuario'}</span>
                                <span className="text-[9px] font-bold text-gray-400 uppercase tracking-widest">
                                    {(user?.is_super_admin || user?.rol === 'super_admin') ? 'Súper Admin' : user?.is_vendedor ? 'Vendedor' : (user?.active_role === 'tecnico' ? 'Modo Técnico' : 'Gestor')}
                                </span>
                            </div>
                            <div className="w-8 h-8 sm:w-9 sm:h-9 bg-[#F69239] rounded-full flex items-center justify-center text-white text-xs sm:text-sm font-black shadow-sm ring-2 ring-white/10">
                                {user?.nombres?.charAt(0) ?? 'U'}
                            </div>
                        </Link>
                    </div>
                </header>

                {/* Flash Messages */}
                {flashMsg && (
                    <div className={`mx-4 mt-3 px-4 py-3 rounded-lg text-sm font-medium flex items-center gap-2 ${
                        flashMsg.error   ? 'bg-red-500/20 border border-red-500/40 text-red-300' :
                        flashMsg.warning ? 'bg-yellow-500/20 border border-yellow-500/40 text-yellow-300' :
                                           'bg-green-500/20 border border-green-500/40 text-green-300'
                    }`}>
                        <span>{flashMsg.error || flashMsg.warning || flashMsg.message}</span>
                        <button onClick={() => setFlashMsg(null)} className="ml-auto opacity-70 hover:opacity-100">✕</button>
                    </div>
                )}

                {/* Área principal scrollable */}
                <main className="flex-1 overflow-y-auto bg-[#11111d]">
                    {children}
                </main>
            </div>
        </div>
    );
}

/* ── NavItem Component ── */
function NavItem({ href, active, open, icon, label, badge = null }) {
    if (!href || href === '#') return null; // No renderizar si la ruta no existe
    return (
        <li>
            <Link
                href={href}
                className={`flex items-center justify-between px-3 py-2 rounded-lg transition-colors ${
                    active
                        ? 'bg-gradient-to-r from-[#F69239]/30 to-[#F69239]/10 border-l-[3px] border-[#F69239] text-white'
                        : 'text-gray-400 hover:text-white hover:bg-white/5'
                }`}
            >
                <div className="flex items-center min-w-0">
                    <span className="flex-shrink-0 w-5 h-5 mr-3">{icon}</span>
                    {open && <span className="text-sm font-medium truncate">{label}</span>}
                </div>
                {badge !== null && badge > 0 && (
                    <span className="bg-red-500 text-white font-black text-[9px] px-2 py-0.5 rounded-full animate-pulse flex-shrink-0">
                        {badge}
                    </span>
                )}
            </Link>
        </li>
    );
}

/* ── Íconos SVG ── */
const IconTasks    = () => <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>;
const IconLocation = () => <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>;
const IconDoc      = () => <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>;
const IconAlert    = () => <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>;
const IconClock    = () => <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>;
const IconTraining = () => <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 14l9-5-9-5-9 5 9 5z"/><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>;
const IconWarning  = () => <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>;
const IconContract = () => <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>;
const IconSettings = () => <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>;
const IconDashboard= () => <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>;
const IconUser     = () => <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>;
const IconCompanies = () => <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>;
const IconStaff     = () => <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>;
const IconAssets    = () => <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>;
const IconKey       = () => <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z"/></svg>;
const IconBook      = () => <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>;

