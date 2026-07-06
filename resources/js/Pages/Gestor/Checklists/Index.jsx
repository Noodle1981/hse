import { router, Head, Link, usePage } from '@inertiajs/react';
import AuthenticatedLayout from '../../../Layouts/AuthenticatedLayout';
import { useState, useEffect } from 'react';

const ICONS = {
    'shield-check': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />',
    'helmet': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />',
    'alert': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />',
    'fire': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z" />',
    'truck': '<path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 100-4 2 2 0 000 4zm10 0a2 2 0 100-4 2 2 0 000 4z" />',
    'tool': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />',
    'doc': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />',
    'eye': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />',
    'environment': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />',
    'tag': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2zM9 5h6" />',
    'extintor': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5h6M12 5v2M9 9h6v11a2 2 0 01-2 2h-2a2 2 0 01-2-2V9zm3-4a3 3 0 013-3h1M7 9a2 2 0 012-2h1M18 10v4a1 1 0 01-1 1h-2" />',
    'mineria': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2 17h20M3 17l2-8h14l2 8M6 12h12" />',
    'fabrica': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V9l-5 3V9l-5 3V9l-5 3v9h15zM17 5v4" />',
    'construccion': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 20h16M7 20l4-14a1 1 0 012 0l4 14M9 12h6M10 8h4" />',
    'bodega': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m-8-14v14" />',
    'colectivo': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 18v2a1 1 0 001 1h2a1 1 0 001-1v-2h8v2a1 1 0 001 1h2a1 1 0 001-1v-2M4 6h16a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V8a2 2 0 012-2zm2 3h4v4H6V9zm8 0h4v4h-4V9zM6 15h12M6 15h.01M18 15h.01" />',
    'camioneta': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 14h18M6 17a2 2 0 11-4 0 2 2 0 014 0zm12 0a2 2 0 11-4 0 2 2 0 014 0zm-12-3v-3a1 1 0 011-1h5l3 3h5v4M7 10h4v3H7v-3z" />',
    'corazon': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />',
    'rayo': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />',
    'quimica': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.228 17.116L14 9V4h1M10 4h1v5l-5.228 8.116A2 2 0 007.464 20h9.072a2 2 0 001.692-2.884z" />',
    'computadora': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />',
    'camara': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316A2.192 2.192 0 0014.502 4H9.498c-.524 0-1.007.269-1.29.705l-.822 1.316zM15 13.5a3 3 0 11-6 0 3 3 0 016 0z" />',
    'candado': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.25a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />',
    'llave': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 11-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" />',
    'gas': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 18.5V21M3 21h12m-6-3V7a2 2 0 012-2h4a2 2 0 012 2v11M3 10h12M6 6h6M19 14c1.5 0 2-1 2-2.5V8" />',
    'reciclar': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />',
    'hoja': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v18M12 3C8 8.5 6 12.5 6 16c0 3.314 2.686 6 6 6s6-2.686 6-6c0-3.5-2-7.5-6-13z" />',
    'basura': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />',
    'comida': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 3v7a3 3 0 003 3h1V3M12 3v10m-2-10v5m6-5v17m2-17v3a4 4 0 01-4 4v10" />',
    'gota': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />',
    'engranaje': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15a3 3 0 100-6 3 3 0 000 6z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 11-2.83 2.83l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 11-2.83-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 112.83-2.83l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 112.83 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z" />',
    'ruido': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2a9 9 0 00-9 9v7a3 3 0 003 3h1a2 2 0 002-2v-5a2 2 0 00-2-2H5v-1a7 7 0 0114 0v1h-2a2 2 0 00-2 2v5a2 2 0 002 2h1a3 3 0 003-3v-7a9 9 0 00-9-9z" />',
    'calavera': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2a5 5 0 00-5 5v3a5 5 0 002 4v4a2 2 0 002 2h2a2 2 0 002-2v-4a5 5 0 002-4V7a5 5 0 00-5-5zM9 8h.01M15 8h.01M10 12h4" />',
    'escalera': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 3v18M16 3v18M8 7h8M8 11h8M8 15h8M8 19h8" />',
    'arbol': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2L3 17h6v4h6v-4h6z" />',
    'frio': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2v20M2 12h20M12 12l7 7M12 12L5 5M12 12l7-7M12 12L5 19" />',
    'calor': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m0-12.728l.707.707m11.314 11.314l.707.707M12 7a5 5 0 100 10 5 5 0 000-10z" />',
    'bateria': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 10.5a1.5 1.5 0 010 3M3 7h14a2 2 0 012 2v6a2 2 0 01-2 2H3a2 2 0 01-2-2V9a2 2 0 012-2zM6 11v2M10 11v2M14 11v2" />',
    'telefono': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-2.824-1.802-5.14-4.118-6.942-6.94l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />',
    'maletin': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125zM9 3.75V2.25A.75.75 0 019.75 1.5h4.5a.75.75 0 01.75.75v1.5" />',
    'alarma': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.7c0 2.01-.76 3.86-1.926 5.272a23.856 23.856 0 005.455 1.31m5.737 1.5c-.327 1.077-1.314 1.848-2.484 1.848s-2.157-.77-2.484-1.848m9.73-.244a24.12 24.12 0 01-9.73 0m9.73 0a24.12 24.12 0 00-9.73 0" />',
    'presion': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 12l3-3M9 12a3 3 0 116 0 3 3 0 01-6 0z" />',
    'soldadura': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 21h14M12 17v-4M8 13h8M12 9V3M10 5l2-2 2 2" />',
    'radio': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 21h8M12 17v-4M10 9h4M6 5v12a2 2 0 002 2h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2zM9 3v2" />',
    'ancla': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5V3m0 18v-3m0 0a6 6 0 01-6-6h1m5 6a6 6 0 006-6h-1m-10 0a4 4 0 018 0M12 5a2 2 0 100 4 2 2 0 000-4z" />',
    'quimico': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6c0 1.66 3.58 3 8 3s8-1.34 8-3-3.58-3-8-3-8 1.34-8 3zm0 0v12c0 1.66 3.58 3 8 3s8-1.34 8-3V6m-16 6c0 1.66 3.58 3 8 3s8-1.34 8-3" />',
    'elevador': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 12H5M5 8h7M8 4h4M3 16h18M19 16v5M17 21a2 2 0 100-4 2 2 0 000 4zM7 21a2 2 0 100-4 2 2 0 000 4z" />',
    'bandera': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21V3m0 2.25h13.5a.75.75 0 01.54 1.275L14.25 9.75l2.79 3.225a.75.75 0 01-.54 1.275H3" />',
};

export default function Index({ checklists, categories, filters }) {
    const { auth, flash } = usePage().props;
    
    // ESTADOS TÁCTICOS DE FILTRADO
    const [search, setSearch] = useState(filters.search || '');
    const [categoryFilter, setCategoryFilter] = useState(filters.categoryFilter || '');
    const [statusFilter, setStatusFilter] = useState(filters.statusFilter || 'all');

    const updateFilters = () => {
        router.get(route('gestor.checklists.index'), { search, categoryFilter, statusFilter }, {
            preserveState: true,
            replace: true,
        });
    };

    useEffect(() => {
        const timeout = setTimeout(() => {
            if (search !== (filters.search || '')) updateFilters();
        }, 500);
        return () => clearTimeout(timeout);
    }, [search]);

    useEffect(() => {
        if (categoryFilter !== (filters.categoryFilter || '') || statusFilter !== (filters.statusFilter || 'all')) {
            updateFilters();
        }
    }, [categoryFilter, statusFilter]);

    // OPERACIONES MAESTRAS
    const handleToggleStatus = (id) => {
        router.post(route('gestor.checklists.toggle', id), {}, {
            preserveScroll: true,
        });
    };

    const handleDuplicate = (id) => {
        if (confirm('SISTEMA: ¿Desea duplicar este protocolo como borrador?')) {
            router.post(route('gestor.checklists.duplicate', id));
        }
    };

    return (
        <AuthenticatedLayout 
            title="Gestión de Checklists"
            header={
                <div className="flex flex-col">
                    <h1 className="text-xs font-black text-ep-primary uppercase tracking-widest italic leading-none mb-1">
                        Gestión de <span className="text-white">Checklists</span>
                    </h1>
                    <h2 className="text-sm font-bold text-indigo-100 uppercase tracking-tight leading-none italic">
                        Diseño y Gestión de Guías de Inspección
                    </h2>
                </div>
            }
        >
            <Head title="Listado de Checklists" />

            <div className="px-4 sm:px-6 lg:px-8 py-8 text-white selection:bg-orange-500/30">
                <div className="max-w-[100rem] mx-auto space-y-8 animate-fade-in">
                    
                    {/* BUSCADOR Y FILTROS TÁCTICOS (Estilo Legacy) */}
                    <div className="bg-[#16162a]/60 backdrop-blur-xl rounded-[2.5rem] border border-white/5 p-8 shadow-2xl relative overflow-hidden animate-fade-in-down group/filters">
                        <div className="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-transparent via-orange-500 to-transparent opacity-50"></div>
                        
                        <div className="grid grid-cols-1 md:grid-cols-12 gap-6 items-end relative z-10">
                            
                            {/* Buscador */}
                            <div className="md:col-span-4 relative group">
                                <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-orange-500 uppercase tracking-[0.3em] italic z-10">Buscador de Checklists</span>
                                <div className="relative">
                                    <div className="absolute inset-y-0 left-5 flex items-center pointer-events-none text-white/20 group-focus-within:text-orange-500">
                                        <svg className="w-5 h-5 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                    </div>
                                    <input id="search" name="search" 
                                        type="text" 
                                        value={search}
                                        onChange={e => setSearch(e.target.value)}
                                        className="w-full bg-black/40 border border-white/10 rounded-2xl pl-14 pr-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-orange-500/50 transition-all placeholder-white/5 italic"
                                        placeholder="Filtrar por Título o Código..."
                                    />
                                </div>
                            </div>

                            {/* Categoría */}
                            <div className="md:col-span-3 relative group">
                                <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-indigo-400 uppercase tracking-[0.3em] italic z-10">Categoría</span>
                                <select id="categoryFilter" name="categoryFilter" 
                                    value={categoryFilter}
                                    onChange={e => setCategoryFilter(e.target.value)}
                                    className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-indigo-500/50 transition-all [color-scheme:dark] appearance-none cursor-pointer italic uppercase"
                                    title="Seleccione una categoría"
                                >
                                    <option value="">TODAS LAS CATEGORÍAS</option>
                                    {categories.map(cat => <option key={cat.id} value={cat.id}>{cat.name}</option>)}
                                </select>
                            </div>

                            {/* Estado Operativo */}
                            <div className="md:col-span-2 relative group">
                                <span className="absolute -top-2.5 left-4 bg-[#11111d] px-2 text-[8px] font-black text-emerald-400 uppercase tracking-[0.3em] italic z-10">Estado</span>
                                <select id="statusFilter" name="statusFilter" 
                                    value={statusFilter}
                                    onChange={e => setStatusFilter(e.target.value)}
                                    className="w-full bg-black/40 border border-white/10 rounded-2xl px-6 py-4 text-sm font-bold text-white focus:ring-2 focus:ring-emerald-500/50 transition-all [color-scheme:dark] appearance-none cursor-pointer italic uppercase"
                                    title="Seleccione un estado"
                                >
                                    <option value="all">TODOS</option>
                                    <option value="active">ACTIVOS</option>
                                    <option value="inactive">INACTIVOS</option>
                                </select>
                            </div>

                            {/* Botones de Acción */}
                            <div className="md:col-span-3 flex gap-3">
                                <Link 
                                    href={route('gestor.categories.index')} 
                                    className="flex-1 flex h-14 bg-white/5 border border-white/10 text-white hover:bg-white/10 hover:border-white/20 active:scale-95 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] italic transition-all items-center justify-center gap-2 shadow-lg"
                                    title="Administrar Categorías y Subcategorías"
                                >
                                    <svg className="w-5 h-5 text-[#F69239]" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2"><path d="M7 7h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2zM9 5h6"/></svg>
                                    <span>Categorías</span>
                                </Link>
                                <Link 
                                    href={route('gestor.checklists.create')} 
                                    className="flex-1 flex h-14 bg-orange-600 text-white hover:brightness-110 active:scale-95 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] italic transition-all items-center justify-center gap-2 shadow-[0_0_25px_rgba(246,146,57,0.3)] group/btn"
                                >
                                    <svg className="w-5 h-5 group-hover:rotate-90 transition-transform duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="3"><path d="M12 4v16m8-8H4"/></svg>
                                    <span>Nuevo</span>
                                </Link>
                            </div>
                        </div>
                    </div>

                    {/* MATRIZ MAESTRA (TABLA TÉCNICA CLONE) */}
                    <div className="bg-[#16162a]/60 backdrop-blur-xl rounded-[3rem] border border-white/5 shadow-2xl relative overflow-hidden group/matrix mb-20 animate-fade-in-up">
                        <div className="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-indigo-500/20 to-transparent"></div>
                        
                        <div className="overflow-x-auto">
                            <table className="table-auto w-full border-collapse">
                                <thead>
                                    <tr className="bg-black/40 text-left">
                                        <th className="px-10 py-8"><span className="text-[11px] font-black text-white/40 uppercase tracking-[0.4em] italic leading-none">Diseño de Checklist</span></th>
                                        <th className="hidden md:table-cell px-10 py-8"><span className="text-[11px] font-black text-white/40 uppercase tracking-[0.4em] italic leading-none">Categorización</span></th>
                                        <th className="hidden lg:table-cell px-10 py-8 text-center"><span className="text-[11px] font-black text-white/40 uppercase tracking-[0.4em] italic leading-none">Variables</span></th>
                                        <th className="px-10 py-8 text-center"><span className="text-[11px] font-black text-white/40 uppercase tracking-[0.4em] italic leading-none">Estado</span></th>
                                        <th className="px-10 py-8 text-right"><span className="text-[11px] font-black text-white/40 uppercase tracking-[0.4em] italic leading-none">Comandos</span></th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-white/5">
                                    {checklists.data.map(checklist => (
                                        <tr key={checklist.id} className="group/row hover:bg-white/5 transition-all duration-500 relative">
                                            {/* Indicador lateral legacy */}
                                            <td className="px-10 py-8 relative">
                                                <div className={`absolute left-0 top-1/2 -translate-y-1/2 w-1.5 h-12 ${checklist.is_active ? 'bg-orange-500 shadow-[0_0_15px_#f69239]' : 'bg-white/10'} rounded-r-full transition-all opacity-0 group-hover/row:opacity-100`}></div>
                                                
                                                <div className="flex flex-col">
                                                    <div className="text-sm font-black text-white italic uppercase tracking-tight leading-tight mb-2 group-hover/row:text-orange-500 transition-colors">
                                                        {checklist.title}
                                                    </div>
                                                    {checklist.registry_code && (
                                                        <div className="inline-flex items-center px-3 py-1 bg-indigo-500/10 border border-indigo-500/20 rounded-lg text-[8px] font-black text-indigo-400 italic w-fit uppercase tracking-widest leading-none">
                                                            CODE: {checklist.registry_code}
                                                        </div>
                                                    )}
                                                </div>
                                            </td>

                                            <td className="hidden md:table-cell px-10 py-8">
                                                <div className="flex flex-col gap-2">
                                                    {checklist.category_relation && (
                                                        <div className="inline-flex items-center gap-2">
                                                            <div className="w-6 h-6 rounded-lg flex items-center justify-center border transition-all" style={{ backgroundColor: `${checklist.category_relation.color}15`, borderColor: `${checklist.category_relation.color}40`, color: checklist.category_relation.color }}>
                                                                <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" dangerouslySetInnerHTML={{ __html: ICONS[checklist.category_relation.icon] || ICONS['tag'] }} />
                                                            </div>
                                                            <span className="text-[10px] font-black text-white/60 uppercase tracking-widest italic">{checklist.category_relation.name}</span>
                                                        </div>
                                                    )}
                                                    {checklist.description && (
                                                        <span className="text-[9px] text-white/20 font-bold uppercase tracking-wider truncate max-w-[200px] italic">
                                                            {checklist.description}
                                                        </span>
                                                    )}
                                                </div>
                                            </td>

                                            <td className="hidden lg:table-cell px-10 py-8 text-center">
                                                <div className="inline-flex flex-col items-center">
                                                    <div className="text-2xl font-black text-white italic leading-none mb-1">{checklist.questions_count}</div>
                                                    <div className="text-[8px] font-black text-white/20 uppercase tracking-[0.2em] italic">Reactivos</div>
                                                </div>
                                            </td>

                                            <td className="px-10 py-8 text-center">
                                                <button 
                                                    onClick={() => handleToggleStatus(checklist.id)}
                                                    className={`px-4 py-2.5 rounded-2xl text-[9px] font-black uppercase tracking-[0.2em] italic transition-all border flex items-center gap-3 mx-auto active:scale-95 ${checklist.is_active ? 'bg-emerald-500/10 border-emerald-500/40 text-emerald-400 shadow-[0_0_15px_rgba(16,185,129,0.1)]' : 'bg-red-500/10 border-red-500/20 text-red-500'}`}
                                                >
                                                    <div className={`w-2 h-2 rounded-full ${checklist.is_active ? 'bg-emerald-400 shadow-[0_0_8px_#10b981] animate-pulse' : 'bg-red-500'}`}></div>
                                                    <span>{checklist.is_active ? 'ONLINE' : 'OFFLINE'}</span>
                                                </button>
                                            </td>

                                            <td className="px-10 py-8 text-right">
                                                <div className="flex justify-end gap-3 transition-all duration-500">
                                                    {/* BOTÓN EMULADOR (MOBILE STYLE) */}
                                                    <Link 
                                                        href={route('tecnico.checklists.fill', { id: checklist.id })}
                                                        className="h-11 w-11 bg-white/5 border border-white/10 text-white hover:bg-white/20 rounded-xl flex items-center justify-center transition-all group/emu shadow-lg"
                                                        title="Emulación Técnica"
                                                    >
                                                        <svg className="w-5 h-5 group-hover/emu:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                                    </Link>

                                                    <button 
                                                        onClick={() => handleDuplicate(checklist.id)}
                                                        className="h-11 w-11 bg-white/5 border border-white/10 text-white hover:bg-orange-600 hover:text-white rounded-xl flex items-center justify-center transition-all shadow-lg"
                                                        title="Duplicar"
                                                    >
                                                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2"><path d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/></svg>
                                                    </button>

                                                    <Link 
                                                        href={route('gestor.checklists.edit', checklist.id)}
                                                        className="h-11 px-6 bg-white/5 border border-white/10 text-white hover:bg-orange-500 hover:text-white rounded-xl text-[9px] font-black uppercase tracking-widest italic flex items-center gap-3 transition-all shadow-lg"
                                                    >
                                                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                        <span>Editar</span>
                                                    </Link>
                                                </div>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>

                        {/* PAGINACIÓN TÁCTICA */}
                        <div className="px-10 py-8 border-t border-white/5 bg-black/20 flex flex-col sm:flex-row items-center justify-between gap-6 font-black italic">
                            <div className="text-[10px] text-white/10 uppercase tracking-[0.5em]">
                                Dynamic Checklists Engine v4.2 • Secure System
                            </div>
                            <div className="flex gap-2">
                                {checklists.links.map((link, i) => (
                                    <Link 
                                        key={i}
                                        href={link.url || '#'}
                                        dangerouslySetInnerHTML={{ __html: link.label }}
                                        className={`px-4 py-2 rounded-xl border text-[9px] ${link.active ? 'bg-orange-500/10 border-orange-500/30 text-orange-400' : 'bg-white/5 border-white/5 text-white/20 hover:text-white'} ${!link.url && 'opacity-20 cursor-not-allowed'}`}
                                    />
                                ))}
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
