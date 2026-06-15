import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, router } from '@inertiajs/react';

export default function Dashboard({ auth, consultoraName, consultants, kpis, companies, filters }) {
    const [searchTerm, setSearchTerm] = useState('');

    const updateFilters = (newConsultoraId, newFrom, newTo) => {
        router.get(route('admin.dashboard'), { 
            consultora_id: newConsultoraId, 
            from: newFrom, 
            to: newTo 
        }, { preserveScroll: true });
    };

    // Filtrar empresas por buscador en tiempo real
    const filteredCompanies = companies.filter(company => 
        company.nombre.toLowerCase().includes(searchTerm.toLowerCase()) ||
        (company.cuit && company.cuit.includes(searchTerm)) ||
        (company.consultora?.nombre && company.consultora.nombre.toLowerCase().includes(searchTerm.toLowerCase())) ||
        company.id.toString().includes(searchTerm)
    );

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={<h2 className="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Dashboard Administrativo</h2>}
        >
            <Head title="Control Operativo" />

            <div className="py-12 bg-[#11111D] min-h-screen text-white selection:bg-amber-500/30">
                <div className="max-w-[98rem] mx-auto sm:px-6 lg:px-8 space-y-8">
                    
                    {/* ENCABEZADO Y FILTROS HUD */}
                    <div className="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 bg-[#1E1E2D]/60 p-8 rounded-[2rem] border border-white/5 backdrop-blur-xl">
                        <div>
                            <span className="text-[10px] font-black text-amber-500 uppercase tracking-[0.2em] italic">Consola de Control Global</span>
                            <h1 className="text-3xl font-black text-white italic uppercase tracking-tighter mt-1">
                                {consultoraName}
                            </h1>
                            <p className="text-xs font-bold text-white/40 uppercase tracking-widest mt-1">Métricas de desempeño y volumen operativo</p>
                        </div>

                        <div className="flex flex-col md:flex-row items-stretch md:items-center gap-4 bg-white/5 p-3 rounded-2xl border border-white/10 backdrop-blur-md w-full lg:w-auto">
                            {/* Selector de Consultora */}
                            <div className="flex flex-col px-2 min-w-[12rem] border-b md:border-b-0 md:border-r border-white/10 pb-2 md:pb-0">
                                <label className="text-[8px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic mb-1">Consultora</label>
                                <select
                                    value={filters.consultora_id}
                                    onChange={e => updateFilters(e.target.value, filters.from, filters.to)}
                                    className="bg-transparent border-none text-xs font-black text-white italic focus:ring-0 p-0 [color-scheme:dark] cursor-pointer"
                                >
                                    <option value="all" className="bg-[#1E1E2D] text-white">Todas las Consultoras</option>
                                    {consultants.map(c => (
                                        <option key={c.id} value={c.id} className="bg-[#1E1E2D] text-white">
                                            {c.nombre}
                                        </option>
                                    ))}
                                </select>
                            </div>

                            {/* Fechas */}
                            <div className="flex flex-row items-center gap-4 w-full md:w-auto pt-2 md:pt-0">
                                <div className="flex flex-col px-2">
                                    <label className="text-[8px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic mb-1">Desde</label>
                                    <input 
                                        id="field_from" 
                                        name="field_from" 
                                        type="date" 
                                        value={filters.from}
                                        onChange={e => updateFilters(filters.consultora_id, e.target.value, filters.to)}
                                        className="bg-transparent border-none text-xs font-black text-white italic focus:ring-0 p-0 [color-scheme:dark]"
                                    />
                                </div>
                                <span className="text-white/10">|</span>
                                <div className="flex flex-col px-2">
                                    <label className="text-[8px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic mb-1">Hasta</label>
                                    <input 
                                        id="field_to" 
                                        name="field_to" 
                                        type="date" 
                                        value={filters.to}
                                        onChange={e => updateFilters(filters.consultora_id, filters.from, e.target.value)}
                                        className="bg-transparent border-none text-xs font-black text-white italic focus:ring-0 p-0 [color-scheme:dark]"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* METRICAS DE ESTRUCTURA Y LICENCIA */}
                    <div className="space-y-4">
                        <div className="flex items-center gap-3">
                            <div className="w-1 h-4 bg-indigo-500 rounded-full"></div>
                            <h3 className="text-xs font-black text-indigo-400/80 uppercase italic tracking-[0.2em]">Estructura del Ecosistema</h3>
                        </div>
                        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                            <KpiCard 
                                title="Empresas Clientes" 
                                value={kpis.empresas_count} 
                                subtitle="Cuentas Activas" 
                                color="indigo" 
                                icon={<svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2"><path strokeLinecap="round" strokeLinejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.68 0-5.302.2-7.854.582V21M3 21h18M12 6.75h.008v.008H12V6.75z" /></svg>}
                            />
                            <KpiCard 
                                title="Usuarios Gestores" 
                                value={kpis.gestores_count} 
                                subtitle="Administradores" 
                                color="emerald" 
                                icon={<svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2"><path strokeLinecap="round" strokeLinejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" /></svg>}
                            />
                            <KpiCard 
                                title="Usuarios Técnicos" 
                                value={kpis.tecnicos_count} 
                                subtitle="Operadores de Campo" 
                                color="blue" 
                                icon={<svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2"><path strokeLinecap="round" strokeLinejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091.683.078 1.37-.151 2.022M10.42 15.17l-3.18-3.18" /></svg>}
                            />
                            <KpiCard 
                                title="Checklists Creados" 
                                value={kpis.checklists_templates_count} 
                                subtitle="Plantillas de Control" 
                                color="purple" 
                                icon={<svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2"><path strokeLinecap="round" strokeLinejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.03 0 1.9.693 2.166 1.638m-7.377 0A48.536 48.536 0 0112 3m0 0c2.917 0 5.747.294 8.5.862m-21 1.402v11.494c0 1.135.845 2.098 1.976 2.192.53.044 1.061.078 1.593.102m0 0l.823-1.645A1.5 1.5 0 015.65 18H9.75M3.75 16.5h16.5M6 18a1 1 0 100-2 1 1 0 000 2z" /></svg>}
                            />
                        </div>
                    </div>

                    {/* RENDIMIENTO DEL PERIODO */}
                    <div className="space-y-4">
                        <div className="flex items-center gap-3">
                            <div className="w-1 h-4 bg-amber-500 rounded-full"></div>
                            <h3 className="text-xs font-black text-amber-500/80 uppercase italic tracking-[0.2em]">Rendimiento del Periodo (Filtrado)</h3>
                        </div>
                        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <KpiCard 
                                title="Checklists Generados" 
                                value={kpis.inspections_count} 
                                subtitle="Inspecciones Completadas" 
                                color="orange" 
                                icon={<svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2"><path strokeLinecap="round" strokeLinejoin="round" d="M10.125 2.25h-4.5c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125v-9M10.125 2.25h.375a9 9 0 019 9v.375M10.125 2.25A3.375 3.375 0 0113.5 5.625v1.5c0 .621.504 1.125 1.125 1.125h1.5a3.375 3.375 0 013.375 3.375M9 15l2.25 2.25L15 12" /></svg>}
                            />
                            <KpiCard 
                                title="Capacitaciones Dictadas" 
                                value={kpis.trainings_count} 
                                subtitle="Sesiones Finalizadas" 
                                color="amber" 
                                icon={<svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2"><path strokeLinecap="round" strokeLinejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.62 48.62 0 0112 20.904a48.62 48.62 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 019.918 5.833 50.45 50.45 0 00-2.658.813m-11.12.014A48.667 48.667 0 0112 10.5c1.78 0 3.512-.288 5.14-.817a48.667 48.667 0 01-11.12.014Z" /></svg>}
                            />
                            <KpiCard 
                                title="Informes Consolidados" 
                                value={kpis.master_reports_count} 
                                subtitle="Informes Maestros" 
                                color="red" 
                                icon={<svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2"><path strokeLinecap="round" strokeLinejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v5.625c0 .621-.504 1.125-1.125 1.125h-2.25A1.125 1.125 0 013 18.75v-5.625zM16.5 13.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 12 21 12.504 21 13.125v5.625c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125v-5.625zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v10.125c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625z" /></svg>}
                            />
                        </div>
                    </div>

                    {/* TABLA DE EMPRESAS Y DESGLOSE */}
                    <div className="bg-[#1E1E2D]/60 backdrop-blur-xl rounded-[2.5rem] border border-white/5 p-8 shadow-2xl space-y-6">
                        <div className="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 pb-6 border-b border-white/5">
                            <div className="flex items-center gap-4">
                                <div className="w-1.5 h-6 bg-amber-500 rounded-full"></div>
                                <div>
                                    <h3 className="text-xl font-black text-white uppercase italic tracking-tighter">Desglose de Rendimiento por Empresa</h3>
                                    <p className="text-[10px] font-bold text-indigo-100/40 uppercase tracking-widest italic mt-1">Resultados filtrados en el periodo</p>
                                </div>
                            </div>
                            
                            {/* Buscador Técnico */}
                            <div className="relative w-full md:w-80">
                                <span className="absolute inset-y-0 left-0 flex items-center pl-4 text-white/30">
                                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                </span>
                                <input 
                                    type="text" 
                                    placeholder="Buscar por empresa, CUIT o consultora..."
                                    value={searchTerm}
                                    onChange={e => setSearchTerm(e.target.value)}
                                    className="w-full bg-white/5 border border-white/10 rounded-2xl py-3 pl-11 pr-4 text-xs font-black text-white italic placeholder:text-white/20 focus:ring-1 focus:ring-amber-500 focus:border-amber-500 transition-all"
                                />
                            </div>
                        </div>

                        {/* Listado Desktop */}
                        <div className="hidden md:block overflow-x-auto rounded-[1.5rem] border border-white/5 bg-black/10">
                            <table className="w-full text-left border-collapse">
                                <thead>
                                    <tr className="bg-black/20 text-[#FFD700] italic font-black uppercase text-[10px] tracking-widest border-b border-white/5">
                                        <th className="px-6 py-4">ID</th>
                                        <th className="px-6 py-4">Empresa</th>
                                        <th className="px-6 py-4">CUIT</th>
                                        <th className="px-6 py-4">Consultora</th>
                                        <th className="px-6 py-4 text-center">Sedes</th>
                                        <th className="px-6 py-4 text-center">Checklists Generados</th>
                                        <th className="px-6 py-4 text-center">Capacitaciones Dictadas</th>
                                        <th className="px-6 py-4 text-center">Informes Consolidados</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {filteredCompanies.length > 0 ? (
                                        filteredCompanies.map(company => (
                                            <tr key={company.id} className="border-b border-white/5 hover:bg-white/[0.04] transition-all group">
                                                <td className="px-6 py-4">
                                                    <span className="text-[11px] font-black text-indigo-100/50 uppercase tracking-widest italic">
                                                        EMP-{String(company.id).padStart(4, '0')}
                                                    </span>
                                                </td>
                                                <td className="px-6 py-4">
                                                    <span className="text-sm font-black text-white uppercase italic group-hover:text-amber-500 transition-colors">
                                                        {company.nombre}
                                                    </span>
                                                </td>
                                                <td className="px-6 py-4">
                                                    <span className="text-xs font-bold text-white/50">{company.cuit || 'N/A'}</span>
                                                </td>
                                                <td className="px-6 py-4">
                                                    <span className="text-xs font-black text-indigo-400 uppercase tracking-wider italic">
                                                        {company.consultora?.nombre || 'N/A'}
                                                    </span>
                                                </td>
                                                <td className="px-6 py-4 text-center">
                                                    <span className="text-xs font-black bg-white/5 text-indigo-200 border border-white/10 px-3 py-1 rounded-xl">
                                                        {company.sedes_count}
                                                    </span>
                                                </td>
                                                <td className="px-6 py-4 text-center">
                                                    <span className="inline-flex items-center justify-center min-w-[3rem] text-xs font-black bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 px-3 py-1 rounded-xl shadow-[0_0_15px_rgba(16,185,129,0.05)]">
                                                        {company.inspecciones_count}
                                                    </span>
                                                </td>
                                                <td className="px-6 py-4 text-center">
                                                    <span className="inline-flex items-center justify-center min-w-[3rem] text-xs font-black bg-blue-500/10 text-blue-400 border border-blue-500/20 px-3 py-1 rounded-xl">
                                                        {company.trainings_count}
                                                    </span>
                                                </td>
                                                <td className="px-6 py-4 text-center">
                                                    <span className="inline-flex items-center justify-center min-w-[3rem] text-xs font-black bg-purple-500/10 text-purple-400 border border-purple-500/20 px-3 py-1 rounded-xl">
                                                        {company.informes_count}
                                                    </span>
                                                </td>
                                            </tr>
                                        ))
                                    ) : (
                                        <tr>
                                            <td colSpan="8" className="px-6 py-8 text-center text-xs font-black text-white/20 uppercase tracking-widest italic">
                                                No se encontraron empresas operativas
                                            </td>
                                        </tr>
                                    )}
                                </tbody>
                            </table>
                        </div>

                        {/* Listado Responsive para Móviles (Tarjetas HUD) */}
                        <div className="md:hidden space-y-4">
                            {filteredCompanies.length > 0 ? (
                                filteredCompanies.map(company => (
                                    <div key={company.id} className="bg-black/20 p-6 rounded-2xl border border-white/5 space-y-4">
                                        <div className="flex justify-between items-start">
                                            <div>
                                                <span className="text-[9px] font-black text-indigo-100/50 tracking-widest uppercase italic">
                                                    EMP-{String(company.id).padStart(4, '0')}
                                                </span>
                                                <h4 className="text-sm font-black text-white uppercase italic mt-1">{company.nombre}</h4>
                                                <span className="text-[10px] text-white/40 block mt-0.5">CUIT: {company.cuit || 'N/A'}</span>
                                                <span className="text-[10px] text-indigo-400 font-bold block mt-1 uppercase tracking-wider">
                                                    Consultora: {company.consultora?.nombre || 'N/A'}
                                                </span>
                                            </div>
                                            <span className="text-xs font-black bg-white/5 text-indigo-200 border border-white/10 px-3 py-1 rounded-xl">
                                                {company.sedes_count} Sedes
                                            </span>
                                        </div>
                                        
                                        <div className="grid grid-cols-3 gap-2 pt-2 border-t border-white/5">
                                            <div className="bg-emerald-500/5 border border-emerald-500/10 p-2.5 rounded-xl text-center">
                                                <span className="block text-[8px] font-black text-emerald-400/60 uppercase tracking-wider italic">Checklists</span>
                                                <span className="text-sm font-black text-emerald-400 italic mt-0.5 block">{company.inspecciones_count}</span>
                                            </div>
                                            <div className="bg-blue-500/5 border border-blue-500/10 p-2.5 rounded-xl text-center">
                                                <span className="block text-[8px] font-black text-blue-400/60 uppercase tracking-wider italic">Capacitac.</span>
                                                <span className="text-sm font-black text-blue-400 italic mt-0.5 block">{company.trainings_count}</span>
                                            </div>
                                            <div className="bg-purple-500/5 border border-purple-500/10 p-2.5 rounded-xl text-center">
                                                <span className="block text-[8px] font-black text-purple-400/60 uppercase tracking-wider italic">Informes</span>
                                                <span className="text-sm font-black text-purple-400 italic mt-0.5 block">{company.informes_count}</span>
                                            </div>
                                        </div>
                                    </div>
                                ))
                            ) : (
                                <div className="text-center py-8 text-xs font-black text-white/20 uppercase tracking-widest italic">
                                    No se encontraron empresas operativas
                                </div>
                            )}
                        </div>
                    </div>

                </div>
            </div>
        </AuthenticatedLayout>
    );
}

function KpiCard({ title, value, subtitle, color, icon }) {
    const colorSchemes = {
        indigo: 'text-indigo-400 bg-indigo-500/10 border-indigo-500/10 hover:border-indigo-500/30',
        emerald: 'text-emerald-400 bg-emerald-500/10 border-emerald-500/10 hover:border-emerald-500/30 shadow-[0_0_20px_rgba(16,185,129,0.02)]',
        blue: 'text-blue-400 bg-blue-500/10 border-blue-500/10 hover:border-blue-500/30',
        purple: 'text-purple-400 bg-purple-500/10 border-purple-500/10 hover:border-purple-500/30',
        orange: 'text-orange-400 bg-orange-500/10 border-orange-500/10 hover:border-orange-500/30',
        amber: 'text-amber-400 bg-amber-500/10 border-amber-500/10 hover:border-amber-500/30',
        red: 'text-red-400 bg-red-500/10 border-red-500/10 hover:border-red-500/30'
    };

    return (
        <div className={`p-8 rounded-[2rem] border border-white/5 bg-[#1E1E2D]/60 backdrop-blur-xl shadow-2xl relative overflow-hidden group hover:scale-[1.02] hover:-translate-y-0.5 transition-all duration-300 ${colorSchemes[color]?.split(' ').slice(2).join(' ')}`}>
            {/* Resplandor HUD */}
            <div className="absolute top-0 right-0 p-8 opacity-5 group-hover:opacity-10 group-hover:scale-110 transition-all duration-500">
                {icon}
            </div>
            
            <div className="relative z-10 space-y-4">
                <div className="flex items-center gap-3">
                    <div className={`h-11 w-11 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center shadow-inner rotate-3 group-hover:rotate-0 transition-all duration-500 ${colorSchemes[color]?.split(' ').slice(0, 2).join(' ')}`}>
                        {icon}
                    </div>
                    <span className="text-[10px] font-black text-indigo-100/40 uppercase italic tracking-[0.2em]">{title}</span>
                </div>
                
                <div className="space-y-1">
                    <div className="text-4xl font-black text-white italic tracking-tight">{value}</div>
                    <span className="text-[8px] font-black text-amber-500/80 uppercase italic tracking-widest block">{subtitle}</span>
                </div>
            </div>
        </div>
    );
}
