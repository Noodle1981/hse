import { router, Head, Link, usePage } from '@inertiajs/react';
import AuthenticatedLayout from '../../../Layouts/AuthenticatedLayout';
import { useState, useEffect, useMemo } from 'react';

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

export default function Index({ assets, kpis, filters, empresas = [], sedes = [], assetTypes = [] }) {
    const { flash } = usePage().props;

    const [search, setSearch] = useState(filters.search || '');
    const [empresaId, setEmpresaId] = useState(filters.empresa_id || '');
    const [sedeId, setSedeId] = useState(filters.sede_id || '');
    const [assetTypeId, setAssetTypeId] = useState(filters.asset_type_id || '');
    const [status, setStatus] = useState(filters.status || 'all');
    const [viewMode, setViewMode] = useState(filters.view_mode || 'grouped');

    const updateFilters = () => {
        router.get(route('gestor.assets.index'), {
            search,
            empresa_id: empresaId,
            sede_id: sedeId,
            asset_type_id: assetTypeId,
            status,
            view_mode: viewMode,
        }, {
            preserveState: true,
            replace: true,
        });
    };

    useEffect(() => {
        const timeout = setTimeout(() => {
            if (
                search !== (filters.search || '') ||
                empresaId !== (filters.empresa_id || '') ||
                sedeId !== (filters.sede_id || '') ||
                assetTypeId !== (filters.asset_type_id || '') ||
                status !== (filters.status || 'all') ||
                viewMode !== (filters.view_mode || 'grouped')
            ) {
                updateFilters();
            }
        }, 400);
        return () => clearTimeout(timeout);
    }, [search, empresaId, sedeId, assetTypeId, status, viewMode]);

    const handleDelete = (asset) => {
        if (confirm(`¿Dar de baja el activo ${asset.name} (${asset.identifier})?`)) {
            router.delete(route('gestor.assets.destroy', asset.id));
        }
    };

    const availableSedes = empresaId
        ? sedes.filter(s => s.empresa_id.toString() === empresaId.toString())
        : sedes;

    // Estado para registrar qué grupos están expandidos (por defecto el primero expandido)
    const [expandedGroups, setExpandedGroups] = useState({ 'first-run': true });

    // Estado para mostrar el modal de información de un checklist
    const [infoChecklist, setInfoChecklist] = useState(null);

    const toggleGroup = (id) => {
        setExpandedGroups(prev => {
            const next = { ...prev };
            if (next['first-run']) {
                delete next['first-run'];
                // Inicializar todos como colapsados excepto el clickeado
            }
            next[id] = !next[id];
            return next;
        });
    };

    // Agrupación de activos por checklist en memoria
    const groupedAssets = useMemo(() => {
        if (viewMode !== 'grouped') return [];

        const groups = {}; // id -> { checklist, assets }
        const noChecklist = [];

        assets.data.forEach(asset => {
            const checklists = asset.asset_type?.category?.checklists || [];
            if (checklists.length === 0) {
                noChecklist.push(asset);
            } else {
                checklists.forEach(c => {
                    if (!groups[c.id]) {
                        groups[c.id] = {
                            id: c.id,
                            title: c.title,
                            description: c.description,
                            registry_code: c.registry_code,
                            category: asset.asset_type.category,
                            assets: [],
                        };
                    }
                    groups[c.id].assets.push(asset);
                });
            }
        });

        const list = Object.values(groups);
        
        if (noChecklist.length > 0) {
            list.push({
                id: 'no-checklist',
                title: 'Activos sin Checklist Asociado',
                description: 'Estos activos no están vinculados a ninguna plantilla de inspección.',
                registry_code: null,
                category: null,
                assets: noChecklist,
            });
        }

        // Por defecto, expandir el primer grupo si es la primera vez que se renderiza y hay grupos
        if (expandedGroups['first-run'] && list.length > 0) {
            setExpandedGroups({ [list[0].id]: true });
        }

        return list;
    }, [assets.data, viewMode]);

    const toggleAllGroups = (expand) => {
        const newExpanded = {};
        if (expand) {
            groupedAssets.forEach(g => {
                newExpanded[g.id] = true;
            });
        }
        setExpandedGroups(newExpanded);
    };

    const getGroupKpis = (groupAssets) => {
        const stats = { total: groupAssets.length, operativo: 0, alerta: 0, fuera_de_servicio: 0 };
        groupAssets.forEach(a => {
            if (a.status === 'operativo') stats.operativo++;
            else if (a.status === 'alerta') stats.alerta++;
            else if (a.status === 'fuera_de_servicio') stats.fuera_de_servicio++;
        });
        return stats;
    };

    const getStatusBadge = (asset) => {
        const st = asset.status;
        switch (st) {
            case 'operativo':
                return (
                    <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                        <span className="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Operativo
                    </span>
                );
            case 'alerta':
                return (
                    <div className="relative inline-block group/tooltip">
                        <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-500/10 text-amber-400 border border-amber-500/20 cursor-help">
                            <span className="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                            Alerta
                        </span>
                        
                        {asset.hallazgos && asset.hallazgos.length > 0 && (
                            <div className="absolute left-1/2 top-full mt-2 -translate-x-1/2 w-64 bg-[#141426] border border-amber-500/30 p-4 rounded-2xl shadow-2xl text-[10px] text-indigo-100 hidden group-hover/tooltip:block z-50 pointer-events-none transition-all">
                                <div className="absolute top-[-6px] left-1/2 -translate-x-1/2 w-3 h-3 bg-[#141426] border-l border-t border-amber-500/30 rotate-45" />
                                <span className="text-[10px] font-black text-amber-400 uppercase tracking-widest block mb-2 border-b border-white/5 pb-1">⚠️ DETALLES DE ALERTA</span>
                                <div className="space-y-2">
                                    {asset.hallazgos.map(h => {
                                        let desc = h.descripcion || '';
                                        let label = h.titulo.split(' - ')[1] || 'Alerta';
                                        if (label === 'fecha_de_proxima_carga_vencimiento') {
                                            label = 'Vencimiento Carga';
                                        } else if (label === 'vencimiento_prueba_hidraulica') {
                                            label = 'Prueba Hidráulica';
                                        } else {
                                            label = label.replace(/_/g, ' ').toUpperCase();
                                        }

                                        let valor = '';
                                        let dias = '';
                                        const valMatch = desc.match(/Valor:\s*(\S+)/i);
                                        const daysMatch = desc.match(/vence en\s*(\d+\s*días)/i);
                                        
                                        if (valMatch) valor = valMatch[1];
                                        if (daysMatch) dias = daysMatch[1];

                                        return (
                                            <div key={h.id} className="space-y-0.5 border-b border-white/5 pb-1.5 last:border-0 last:pb-0 text-left">
                                                <span className="text-[9px] font-black text-white/60 block uppercase tracking-wide">{label}</span>
                                                <div className="flex justify-between font-mono text-[9px] text-amber-300">
                                                    <span>Vence: {valor ? valor : 'N/D'}</span>
                                                    <span className="font-bold text-red-400">({dias ? dias : 'Vencido'})</span>
                                                </div>
                                            </div>
                                        );
                                    })}
                                </div>
                            </div>
                        )}
                    </div>
                );
            case 'fuera_de_servicio':
                return (
                    <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-red-500/10 text-red-400 border border-red-500/20">
                        <span className="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
                        Fuera de Servicio
                    </span>
                );
            default:
                return (
                    <span className="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-gray-500/10 text-gray-400 border border-gray-500/20">
                        {st}
                    </span>
                );
        }
    };

    return (
        <AuthenticatedLayout title="Bóveda Universal de Activos">
            <Head title="Gestión de Activos" />

            <div className="min-h-screen bg-[#11111d] text-white p-4 sm:p-8">
                <div className="max-w-7xl mx-auto space-y-8">
                    
                    {/* HEADER */}
                    <div className="flex flex-col lg:flex-row lg:items-center justify-between gap-6 bg-[#16162a] p-6 rounded-[2rem] border border-white/5 shadow-2xl">
                        <div>
                            <span className="text-[10px] font-black text-orange-500 uppercase tracking-[0.3em] italic">Módulo de Trazabilidad</span>
                            <h1 className="text-2xl font-black text-white uppercase tracking-tight italic">Activos y Equipamiento</h1>
                        </div>
                        
                        {/* Selector de modo de vista tipo pastilla HUD */}
                        <div className="flex bg-black/40 border border-white/10 p-1 rounded-xl items-center self-start lg:self-center">
                            <button
                                onClick={() => setViewMode('list')}
                                className={`px-4 py-2.5 text-[10px] font-black uppercase tracking-wider rounded-lg transition-all cursor-pointer ${viewMode === 'list' ? 'bg-gradient-to-r from-orange-500 to-amber-600 text-white shadow-lg' : 'text-white/40 hover:text-white/70'}`}
                            >
                                📋 Lista Plana
                            </button>
                            <button
                                onClick={() => setViewMode('grouped')}
                                className={`px-4 py-2.5 text-[10px] font-black uppercase tracking-wider rounded-lg transition-all cursor-pointer ${viewMode === 'grouped' ? 'bg-gradient-to-r from-orange-500 to-amber-600 text-white shadow-lg' : 'text-white/40 hover:text-white/70'}`}
                            >
                                🗂️ Agrupado por Checklist
                            </button>
                        </div>

                        <div className="flex flex-wrap gap-3">
                            <Link
                                href={route('gestor.asset-types.index')}
                                className="inline-flex items-center justify-center gap-2 px-5 py-3 bg-white/5 border border-white/10 text-white font-bold text-xs uppercase tracking-wider rounded-xl hover:bg-white/10 transition-all active:scale-95"
                            >
                                🔗 Vincular Checklists
                            </Link>
                            <Link
                                href={route('gestor.assets.create')}
                                className="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-orange-500 to-amber-600 text-white font-black text-xs uppercase tracking-widest rounded-xl shadow-lg hover:brightness-110 transition-all active:scale-95"
                            >
                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M12 4v16m8-8H4"/></svg>
                                Registrar Activos
                            </Link>
                        </div>
                    </div>

                    {/* FLASH MESSAGE */}
                    {flash?.message && (
                        <div className="p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl text-emerald-400 text-xs font-bold flex items-center justify-between">
                            <span>{flash.message}</span>
                        </div>
                    )}

                    {/* KPIS SUMMARY */}
                    <div className="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <div className="bg-[#16162a] p-5 rounded-2xl border border-white/5 shadow-xl">
                            <span className="text-[9px] font-black text-white/40 uppercase tracking-widest block mb-1">Total Activos</span>
                            <span className="text-2xl font-black text-white italic">{kpis.total}</span>
                        </div>
                        <div className="bg-[#16162a] p-5 rounded-2xl border border-emerald-500/10 shadow-xl">
                            <span className="text-[9px] font-black text-emerald-400 uppercase tracking-widest block mb-1">Operativos</span>
                            <span className="text-2xl font-black text-emerald-400 italic">{kpis.operativos}</span>
                        </div>
                        <div className="bg-[#16162a] p-5 rounded-2xl border border-amber-500/10 shadow-xl">
                            <span className="text-[9px] font-black text-amber-400 uppercase tracking-widest block mb-1">En Alerta</span>
                            <span className="text-2xl font-black text-amber-400 italic">{kpis.alerta}</span>
                        </div>
                        <div className="bg-[#16162a] p-5 rounded-2xl border border-red-500/10 shadow-xl">
                            <span className="text-[9px] font-black text-red-400 uppercase tracking-widest block mb-1">Fuera de Servicio</span>
                            <span className="text-2xl font-black text-red-400 italic">{kpis.fuera_de_servicio}</span>
                        </div>
                    </div>

                    {/* FILTROS */}
                    <div className="bg-[#16162a] p-6 rounded-[2rem] border border-white/5 space-y-4 shadow-xl">
                        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                            <div>
                                <label className="block text-[9px] font-black text-white/30 uppercase tracking-widest mb-1">Búsqueda</label>
                                <input
                                    type="text"
                                    value={search}
                                    onChange={e => setSearch(e.target.value)}
                                    placeholder="Nombre o Identificador/QR..."
                                    className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-2.5 text-xs text-white placeholder-white/20 focus:outline-none focus:border-orange-500"
                                />
                            </div>
                            <div>
                                <label className="block text-[9px] font-black text-white/30 uppercase tracking-widest mb-1">Empresa</label>
                                <select
                                    value={empresaId}
                                    onChange={e => { setEmpresaId(e.target.value); setSedeId(''); }}
                                    className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-orange-500"
                                >
                                    <option value="">Todas las Empresas</option>
                                    {empresas.map(e => (
                                        <option key={e.id} value={e.id}>{e.nombre}</option>
                                    ))}
                                </select>
                            </div>
                            <div>
                                <label className="block text-[9px] font-black text-white/30 uppercase tracking-widest mb-1">Sede</label>
                                <select
                                    value={sedeId}
                                    onChange={e => setSedeId(e.target.value)}
                                    className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-orange-500"
                                >
                                    <option value="">Todas las Sedes</option>
                                    {availableSedes.map(s => (
                                        <option key={s.id} value={s.id}>{s.nombre_sede}</option>
                                    ))}
                                </select>
                            </div>
                            <div>
                                <label className="block text-[9px] font-black text-white/30 uppercase tracking-widest mb-1">Tipo de Activo</label>
                                <select
                                    value={assetTypeId}
                                    onChange={e => setAssetTypeId(e.target.value)}
                                    className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-orange-500"
                                >
                                    <option value="">Todos los Tipos</option>
                                    {assetTypes.map(t => (
                                        <option key={t.id} value={t.id}>{t.name}</option>
                                    ))}
                                </select>
                            </div>
                            <div>
                                <label className="block text-[9px] font-black text-white/30 uppercase tracking-widest mb-1">Estado</label>
                                <select
                                    value={status}
                                    onChange={e => setStatus(e.target.value)}
                                    className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-orange-500"
                                >
                                    <option value="all">Todos los Estados</option>
                                    <option value="operativo">Operativo</option>
                                    <option value="alerta">En Alerta</option>
                                    <option value="fuera_de_servicio">Fuera de Servicio</option>
                                </select>
                            </div>
                        </div>
                    </div>

                     {/* TABLA DE ACTIVOS CONDICIONAL */}
                    {viewMode === 'grouped' ? (
                        <div className="space-y-6">
                            {/* Controles de expansión masiva */}
                            <div className="flex justify-end gap-2 text-[10px] font-black uppercase tracking-wider">
                                <button
                                    onClick={() => toggleAllGroups(true)}
                                    className="px-3 py-1.5 bg-white/5 border border-white/10 rounded-lg hover:bg-white/10 transition-all text-white/60 hover:text-white cursor-pointer"
                                >
                                    ➕ Expandir Todos
                                </button>
                                <button
                                    onClick={() => toggleAllGroups(false)}
                                    className="px-3 py-1.5 bg-white/5 border border-white/10 rounded-lg hover:bg-white/10 transition-all text-white/60 hover:text-white cursor-pointer"
                                >
                                    ➖ Colapsar Todos
                                </button>
                            </div>

                            {groupedAssets.length === 0 ? (
                                <div className="bg-[#16162a] p-12 rounded-[2rem] border border-white/5 text-center text-white/30 text-xs uppercase tracking-widest italic">
                                    No se encontraron activos registrados con los filtros aplicados.
                                </div>
                            ) : (
                                groupedAssets.map(group => {
                                    const isExpanded = !!expandedGroups[group.id];
                                    const stats = getGroupKpis(group.assets);
                                    
                                    return (
                                        <div 
                                            key={group.id} 
                                            className="bg-[#16162d] rounded-[2rem] border border-white/5 overflow-hidden shadow-xl transition-all"
                                        >
                                            {/* Cabecera del Acordeón */}
                                            <div 
                                                onClick={() => toggleGroup(group.id)}
                                                className="p-6 flex flex-col md:flex-row md:items-center justify-between gap-4 cursor-pointer hover:bg-white/[0.02] transition-colors select-none"
                                            >
                                                <div className="flex items-center gap-4">
                                                    {/* Icono de Categoría */}
                                                    <div 
                                                        className="h-12 w-12 rounded-2xl bg-white/5 border flex items-center justify-center shadow-inner flex-shrink-0 transition-transform duration-300"
                                                        style={{ 
                                                            borderColor: group.category ? `${group.category.color}40` : 'rgba(255,255,255,0.1)',
                                                            color: group.category ? group.category.color : '#a5b4fc'
                                                        }}
                                                    >
                                                        <svg 
                                                            className="w-6 h-6" 
                                                            fill="none" 
                                                            stroke="currentColor" 
                                                            viewBox="0 0 24 24" 
                                                            strokeWidth="2" 
                                                            dangerouslySetInnerHTML={{ 
                                                                __html: group.category ? (ICONS[group.category.icon] || ICONS['tag']) : ICONS['doc'] 
                                                            }} 
                                                        />
                                                    </div>
                                                    
                                                    <div>
                                                        <div className="flex flex-wrap items-center gap-2">
                                                            <h3 className="text-sm font-black text-white uppercase tracking-wider italic">
                                                                {group.title}
                                                            </h3>
                                                            {group.registry_code && (
                                                                <span className="px-2 py-0.5 bg-black/40 border border-white/10 rounded-md text-[8px] font-mono text-orange-400 uppercase tracking-widest">
                                                                    {group.registry_code}
                                                                </span>
                                                            )}
                                                            {/* Lupa para visualizar información completa del checklist en modal */}
                                                            {group.id !== 'no-checklist' && (
                                                                <button
                                                                    onClick={(e) => {
                                                                        e.stopPropagation();
                                                                        setInfoChecklist(group);
                                                                    }}
                                                                    className="w-5 h-5 rounded-lg flex items-center justify-center bg-white/5 border border-white/10 text-indigo-300 hover:bg-white/10 hover:text-white transition-all cursor-pointer"
                                                                    title="Ver especificación del checklist"
                                                                >
                                                                    <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2.5">
                                                                        <path strokeLinecap="round" strokeLinejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.603 10.603z" />
                                                                    </svg>
                                                                </button>
                                                            )}
                                                        </div>
                                                        <p className="text-[10px] text-white/40 mt-1 uppercase tracking-wider font-medium line-clamp-1 max-w-[200px] sm:max-w-md lg:max-w-xl">
                                                            {group.description || 'Sin descripción disponible.'}
                                                        </p>
                                                    </div>
                                                </div>

                                                {/* Contadores y Estado */}
                                                <div className="flex flex-wrap items-center gap-4 md:self-center self-end">
                                                    {/* Total Badge */}
                                                    <div className="bg-black/30 px-3 py-1.5 rounded-xl border border-white/5 flex items-center gap-2">
                                                        <span className="text-[9px] font-black text-white/30 uppercase tracking-widest">Activos:</span>
                                                        <span className="text-xs font-black text-indigo-300 italic">{stats.total}</span>
                                                    </div>

                                                    {/* Semáforo de Estado de Salud de Activos (HUD) */}
                                                    <div className="flex items-center gap-1 bg-black/30 p-1 rounded-full border border-white/5">
                                                        {/* Operativos (Verde) */}
                                                        <div 
                                                            className="w-6 h-6 rounded-full bg-emerald-500 text-black font-black text-[10px] flex items-center justify-center shadow-[0_0_10px_rgba(16,185,129,0.4)]"
                                                            title={`${stats.operativo} Operativos`}
                                                        >
                                                            {stats.operativo}
                                                        </div>
                                                        {/* En Alerta (Amarillo) */}
                                                        <div 
                                                            className="w-6 h-6 rounded-full bg-amber-500 text-black font-black text-[10px] flex items-center justify-center shadow-[0_0_10px_rgba(245,158,11,0.4)]"
                                                            title={`${stats.alerta} en Alerta`}
                                                        >
                                                            {stats.alerta}
                                                        </div>
                                                        {/* Fuera de Servicio (Rojo) */}
                                                        <div 
                                                            className="w-6 h-6 rounded-full bg-red-500 text-white font-black text-[10px] flex items-center justify-center shadow-[0_0_10px_rgba(239,68,68,0.4)]"
                                                            title={`${stats.fuera_de_servicio} Fuera de Servicio`}
                                                        >
                                                            {stats.fuera_de_servicio}
                                                        </div>
                                                    </div>

                                                    {/* Chevron Indicator */}
                                                    <div className="text-white/40 hover:text-white transition-colors ml-2">
                                                        <svg 
                                                            className={`w-5 h-5 transition-transform duration-300 ${isExpanded ? 'rotate-180' : ''}`} 
                                                            fill="none" 
                                                            stroke="currentColor" 
                                                            viewBox="0 0 24 24" 
                                                            strokeWidth="2.5"
                                                        >
                                                            <path strokeLinecap="round" strokeLinejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                                        </svg>
                                                    </div>
                                                </div>
                                            </div>

                                            {/* Contenido Desplegable (Tabla de Activos) */}
                                            {isExpanded && (
                                                <div className="border-t border-white/5 bg-black/10 overflow-x-auto custom-scrollbar">
                                                    <table className="w-full text-left border-collapse">
                                                        <thead>
                                                            <tr className="bg-black/40 border-b border-white/5 text-[9px] font-black text-orange-500 uppercase tracking-widest italic">
                                                                <th className="p-3 pl-6">Activo / Identificador</th>
                                                                <th className="p-3">Tipo de Activo</th>
                                                                <th className="p-3">Sede / Empresa</th>
                                                                <th className="p-3">Estado</th>
                                                                <th className="p-3 pr-6 text-right">Acciones</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody className="divide-y divide-white/5 text-xs">
                                                            {group.assets.map(asset => (
                                                                <tr key={asset.id} className="hover:bg-white/[0.01] transition-colors">
                                                                    <td className="p-3 pl-6">
                                                                        <div className="font-bold text-white uppercase italic">{asset.name}</div>
                                                                        <div className="text-[10px] text-orange-400 font-mono tracking-wider">QR/ID: {asset.identifier}</div>
                                                                    </td>
                                                                    <td className="p-3">
                                                                        <span className="px-2 py-0.5 bg-white/5 border border-white/10 rounded-lg text-[9px] font-black text-indigo-200 uppercase tracking-wide">
                                                                            {asset.asset_type?.name || 'Genérico'}
                                                                        </span>
                                                                    </td>
                                                                    <td className="p-3">
                                                                        <div className="text-white font-bold">{asset.sede?.nombre_sede}</div>
                                                                        <div className="text-[10px] text-white/40">{asset.sede?.empresa?.nombre}</div>
                                                                    </td>
                                                                    <td className="p-3 relative hover:z-30">
                                                                        {getStatusBadge(asset)}
                                                                    </td>
                                                                    <td className="p-3 pr-6 text-right space-x-2">
                                                                        <Link
                                                                            href={route('gestor.assets.show', asset.id)}
                                                                            className="px-3 py-1.5 bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 rounded-xl text-[10px] font-black uppercase hover:bg-indigo-500/20 transition-all inline-block"
                                                                        >
                                                                            Ficha
                                                                        </Link>
                                                                        <Link
                                                                            href={route('gestor.assets.edit', asset.id)}
                                                                            className="px-3 py-1.5 bg-white/5 text-white/70 border border-white/10 rounded-xl text-[10px] font-black uppercase hover:bg-white/10 transition-all inline-block"
                                                                        >
                                                                            Editar
                                                                        </Link>
                                                                        <button
                                                                            onClick={() => handleDelete(asset)}
                                                                            className="px-3 py-1.5 bg-red-500/10 text-red-400 border border-red-500/20 rounded-xl text-[10px] font-black uppercase hover:bg-red-500/20 transition-all cursor-pointer inline-block"
                                                                        >
                                                                            Dar de Baja
                                                                        </button>
                                                                    </td>
                                                                </tr>
                                                            ))}
                                                        </tbody>
                                                    </table>
                                                </div>
                                            )}
                                        </div>
                                    );
                                })
                            )}
                        </div>
                    ) : (
                        <div className="bg-[#16162a] rounded-[2rem] border border-white/5 overflow-hidden shadow-2xl">
                            <div className="overflow-x-auto">
                                <table className="w-full text-left border-collapse">
                                    <thead>
                                        <tr className="bg-black/20 border-b border-white/5 text-[9px] font-black text-white/30 uppercase tracking-widest">
                                            <th className="p-4 pl-6">Activo / Identificador</th>
                                            <th className="p-4">Tipo</th>
                                            <th className="p-4">Sede / Empresa</th>
                                            <th className="p-4">Estado</th>
                                            <th className="p-4 pr-6 text-right">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-white/5 text-xs">
                                        {assets.data.length === 0 ? (
                                            <tr>
                                                <td colSpan="5" className="p-12 text-center text-white/30 text-xs uppercase tracking-widest italic">
                                                    No se encontraron activos registrados con los filtros aplicados.
                                                </td>
                                            </tr>
                                        ) : (
                                            assets.data.map(asset => (
                                                <tr key={asset.id} className="hover:bg-white/[0.02] transition-colors">
                                                    <td className="p-4 pl-6">
                                                        <div className="font-bold text-white uppercase italic">{asset.name}</div>
                                                        <div className="text-[10px] text-orange-400 font-mono tracking-wider">QR/ID: {asset.identifier}</div>
                                                    </td>
                                                    <td className="p-4">
                                                        <div className="flex flex-col gap-2">
                                                            <div className="flex items-center gap-2">
                                                                {asset.asset_type?.category && (
                                                                    <div className="w-6 h-6 rounded-lg flex items-center justify-center border transition-all text-xs flex-shrink-0" style={{ backgroundColor: `${asset.asset_type.category.color}15`, borderColor: `${asset.asset_type.category.color}40`, color: asset.asset_type.category.color }} title={`Categoría: ${asset.asset_type.category.name}`}>
                                                                        <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" dangerouslySetInnerHTML={{ __html: ICONS[asset.asset_type.category.icon] || ICONS['tag'] }} />
                                                                    </div>
                                                                )}
                                                                <span className="px-2 py-0.5 bg-white/5 border border-white/10 rounded-lg text-[9px] font-black text-indigo-200 uppercase tracking-wide">
                                                                    {asset.asset_type?.name || 'Genérico'}
                                                                </span>
                                                            </div>
                                                            
                                                            {/* Checklists asociados */}
                                                            {asset.asset_type?.category?.checklists && asset.asset_type.category.checklists.length > 0 ? (
                                                                <div className="flex flex-col gap-0.5 pl-1">
                                                                    <span className="text-[7px] font-black text-white/20 uppercase tracking-widest italic">Checklists asociados</span>
                                                                    <div className="flex flex-wrap gap-x-2 gap-y-1">
                                                                        {asset.asset_type.category.checklists.map(c => (
                                                                            <span key={c.id} className="inline-flex items-center gap-1 text-[9px] text-white/50 font-bold hover:text-orange-400 transition-colors uppercase italic">
                                                                                📋 {c.title}
                                                                            </span>
                                                                        ))}
                                                                    </div>
                                                                </div>
                                                            ) : (
                                                                <span className="text-[8px] text-white/20 italic pl-1">Sin checklists asociados</span>
                                                            )}
                                                        </div>
                                                    </td>
                                                    <td className="p-4">
                                                        <div className="text-white font-bold">{asset.sede?.nombre_sede}</div>
                                                        <div className="text-[10px] text-white/40">{asset.sede?.empresa?.nombre}</div>
                                                    </td>
                                                    <td className="p-4 relative hover:z-30">
                                                        {getStatusBadge(asset)}
                                                    </td>
                                                    <td className="p-4 pr-6 text-right space-x-2">
                                                        <Link
                                                            href={route('gestor.assets.show', asset.id)}
                                                            className="px-3 py-1.5 bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 rounded-xl text-[10px] font-black uppercase hover:bg-indigo-500/20 transition-all"
                                                        >
                                                            Ficha
                                                        </Link>
                                                        <Link
                                                            href={route('gestor.assets.edit', asset.id)}
                                                            className="px-3 py-1.5 bg-white/5 text-white/70 border border-white/10 rounded-xl text-[10px] font-black uppercase hover:bg-white/10 transition-all"
                                                        >
                                                            Editar
                                                        </Link>
                                                        <button
                                                            onClick={() => handleDelete(asset)}
                                                            className="px-3 py-1.5 bg-red-500/10 text-red-400 border border-red-500/20 rounded-xl text-[10px] font-black uppercase hover:bg-red-500/20 transition-all"
                                                        >
                                                            Dar de Baja
                                                        </button>
                                                    </td>
                                                </tr>
                                            ))
                                        )}
                                    </tbody>
                                </table>
                            </div>

                            {/* PAGINACIÓN */}
                            {assets.links && assets.links.length > 3 && (
                                <div className="p-4 bg-black/20 border-t border-white/5 flex items-center justify-between">
                                    <div className="text-[10px] text-white/40 uppercase font-bold">
                                        Mostrando {assets.from} a {assets.to} de {assets.total} activos
                                    </div>
                                    <div className="flex gap-1">
                                        {assets.links.map((link, i) => (
                                            <Link
                                                key={i}
                                                href={link.url || '#'}
                                                dangerouslySetInnerHTML={{ __html: link.label }}
                                                className={`px-3 py-1 rounded-lg text-xs font-bold ${link.active ? 'bg-orange-500 text-white' : 'bg-white/5 text-white/40 hover:bg-white/10'}`}
                                            />
                                        ))}
                                    </div>
                                </div>
                            )}
                        </div>
                    )}

                    {/* MODAL INFORMATIVO DETALLADO (ESPECIFICACIÓN DEL CHECKLIST) */}
                    {infoChecklist && (
                        <div className="fixed inset-0 bg-black/60 backdrop-blur-md flex items-center justify-center p-4 z-50 animate-fadeIn" onClick={() => setInfoChecklist(null)}>
                            <div 
                                className="bg-[#16162d] border border-white/10 w-full max-w-lg rounded-[2.5rem] shadow-2xl relative overflow-hidden p-8"
                                onClick={(e) => e.stopPropagation()}
                            >
                                {/* Header del Modal */}
                                <div className="flex items-center gap-4 mb-6">
                                    <div 
                                        className="h-12 w-12 rounded-2xl bg-white/5 border flex items-center justify-center shadow-inner flex-shrink-0"
                                        style={{ 
                                            borderColor: infoChecklist.category ? `${infoChecklist.category.color}40` : 'rgba(255,255,255,0.1)',
                                            color: infoChecklist.category ? infoChecklist.category.color : '#a5b4fc'
                                        }}
                                    >
                                        <svg 
                                            className="w-6 h-6" 
                                            fill="none" 
                                            stroke="currentColor" 
                                            viewBox="0 0 24 24" 
                                            strokeWidth="2" 
                                            dangerouslySetInnerHTML={{ 
                                                __html: infoChecklist.category ? (ICONS[infoChecklist.category.icon] || ICONS['tag']) : ICONS['doc'] 
                                            }} 
                                        />
                                    </div>
                                    <div>
                                        <span className="text-[9px] font-black text-orange-500 uppercase tracking-[0.3em] italic">Especificación Técnica</span>
                                        <h2 className="text-lg font-black text-white uppercase tracking-tight italic">
                                            {infoChecklist.title}
                                        </h2>
                                    </div>
                                </div>

                                {/* Detalles del Checklist */}
                                <div className="space-y-4 mb-8">
                                    {infoChecklist.registry_code && (
                                        <div className="bg-black/30 p-3 rounded-xl border border-white/5 flex items-center justify-between">
                                            <span className="text-[9px] font-black text-white/30 uppercase tracking-widest">Código Normativo:</span>
                                            <span className="text-xs font-mono font-bold text-orange-400">{infoChecklist.registry_code}</span>
                                        </div>
                                    )}

                                    <div className="space-y-1.5">
                                        <span className="text-[9px] font-black text-white/30 uppercase tracking-widest block">Descripción del Protocolo:</span>
                                        <div className="bg-black/20 p-4 rounded-2xl border border-white/5 text-xs text-indigo-200 font-medium leading-relaxed max-h-60 overflow-y-auto custom-scrollbar">
                                            {infoChecklist.description || 'Este checklist no cuenta con una descripción detallada registrada.'}
                                        </div>
                                    </div>
                                </div>

                                {/* Footer / Cierre */}
                                <div className="flex justify-end">
                                    <button
                                        onClick={() => setInfoChecklist(null)}
                                        className="px-6 py-3 bg-gradient-to-r from-orange-500 to-amber-600 text-white text-[10px] font-black uppercase italic tracking-widest rounded-xl hover:brightness-110 active:scale-95 transition-all shadow-lg cursor-pointer"
                                    >
                                        Entendido
                                    </button>
                                </div>
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
