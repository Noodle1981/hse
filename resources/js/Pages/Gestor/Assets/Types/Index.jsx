import { Head, Link, useForm, usePage, router } from '@inertiajs/react';
import AuthenticatedLayout from '../../../../Layouts/AuthenticatedLayout';
import { useState, useMemo } from 'react';

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

export default function Index({ assetTypes = [], categories = [], empresas = [], checklists = [] }) {
    const { flash } = usePage().props;
    const [editingType, setEditingType] = useState(null);
    const [showModal, setShowModal] = useState(false);
    const [importing, setImporting] = useState(false);
    
    // Filtros de subcategoría para importar desde checklist
    const [filterSubcategoryId, setFilterSubcategoryId] = useState('');
    const [importChecklistId, setImportChecklistId] = useState('');
    const [importError, setImportError] = useState('');
    const [expandedAttributes, setExpandedAttributes] = useState({});

    const toggleAttributes = (typeId) => {
        setExpandedAttributes(prev => ({
            ...prev,
            [typeId]: !prev[typeId]
        }));
    };

    const form = useForm({
        id: null,
        name: '',
        empresa_id: '',
        category_id: '',
        description: '',
        group_label: 'Grupo',
        subgroup_label: 'Subgrupo',
        metadata_schema: [],
        photo_schema: [],
    });

    const openCreateModal = () => {
        setEditingType(null);
        setFilterSubcategoryId('');
        setImportChecklistId('');
        setImportError('');
        form.setData({
            id: null,
            name: '',
            empresa_id: '',
            category_id: '',
            description: '',
            group_label: 'Grupo',
            subgroup_label: 'Subgrupo',
            metadata_schema: [],
            photo_schema: [
                { key: 'foto_principal', label: 'Foto Principal / Vista General', required: true },
            ],
        });
        setShowModal(true);
    };

    const openEditModal = (type) => {
        setEditingType(type);
        setFilterSubcategoryId('');
        setImportChecklistId('');
        setImportError('');
        form.setData({
            id: type.id,
            name: type.name || '',
            empresa_id: type.empresa_id || '',
            category_id: type.category_id || '',
            description: type.description || '',
            group_label: type.group_label || 'Grupo',
            subgroup_label: type.subgroup_label || 'Subgrupo',
            metadata_schema: type.metadata_schema || [],
            photo_schema: type.photo_schema || [
                { key: 'foto_principal', label: 'Foto Principal / Vista General', required: true },
            ],
        });
        setShowModal(true);
    };

    // Subcategorías disponibles según la categoría seleccionada en Info General
    const availableSubcategories = useMemo(() => {
        const catId = form.data.category_id;
        if (!catId) return [];
        const cat = categories.find(c => String(c.id) === String(catId));
        return cat?.subcategories || [];
    }, [form.data.category_id, categories]);

    // Checklists filtrados en cascada por la Categoría principal y Subcategoría
    const filteredChecklists = useMemo(() => {
        const catId = form.data.category_id;
        return checklists.filter(c => {
            if (catId && String(c.category_id) !== String(catId)) {
                return false;
            }
            if (filterSubcategoryId && String(c.subcategory_id) !== String(filterSubcategoryId)) {
                return false;
            }
            return true;
        });
    }, [checklists, form.data.category_id, filterSubcategoryId]);

    const addField = () => {
        form.setData('metadata_schema', [
            ...form.data.metadata_schema,
            { name: '', label: '', type: 'text', show_in_create: false, role: 'verificacion', alert_days: '', alert_threshold: '' },
        ]);
    };

    const updateField = (index, key, value) => {
        const updated = [...form.data.metadata_schema];
        updated[index][key] = value;
        form.setData('metadata_schema', updated);
    };

    const removeField = (index) => {
        const updated = [...form.data.metadata_schema];
        updated.splice(index, 1);
        form.setData('metadata_schema', updated);
    };

    const addPhotoField = () => {
        form.setData('photo_schema', [
            ...(form.data.photo_schema || []),
            { key: '', label: '', required: false },
        ]);
    };

    const updatePhotoField = (index, key, value) => {
        const updated = [...(form.data.photo_schema || [])];
        updated[index][key] = value;
        form.setData('photo_schema', updated);
    };

    const removePhotoField = (index) => {
        const updated = [...(form.data.photo_schema || [])];
        updated.splice(index, 1);
        form.setData('photo_schema', updated);
    };

    /**
     * Llama al endpoint de importación y fusiona los campos retornados con los existentes.
     */
    const handleImportFromChecklist = async () => {
        if (!importChecklistId) {
            setImportError('Seleccioná un checklist primero.');
            return;
        }
        setImportError('');
        setImporting(true);
        try {
            const url = route('gestor.asset-types.import-checklist', importChecklistId);
            const res = await fetch(url, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin',
            });
            if (!res.ok) throw new Error('Error al consultar el checklist.');
            const data = await res.json();

            if (!data.fields || data.fields.length === 0) {
                setImportError('Este checklist no tiene preguntas vinculadas a metadata. Abrí el Builder del checklist y completá el campo "Vincular a Metadata Activo" en cada pregunta relevante.');
                setImporting(false);
                return;
            }

            // Mantener solo atributos creados manualmente, limpiando los importados del checklist anterior
            const manualFields = form.data.metadata_schema.filter(f => !f._imported);
            const merged = [...manualFields, ...data.fields];
            form.setData('metadata_schema', merged);

            setImportError('');
        } catch (e) {
            setImportError('No se pudo conectar con el servidor. Verificá tu sesión.');
        }
        setImporting(false);
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        form.post(route('gestor.asset-types.store'), {
            onSuccess: () => {
                setShowModal(false);
                form.reset();
            },
        });
    };

    const handleDelete = (type) => {
        if (confirm(`¿Eliminar tipo de activo '${type.name}'?`)) {
            router.delete(route('gestor.asset-types.destroy', type.id));
        }
    };

    return (
        <AuthenticatedLayout title="Tipos de Activos y Atributos">
            <Head title="Tipos de Activos" />

            <div className="min-h-screen bg-[#11111d] text-white p-4 sm:p-8 space-y-8">
                <div className="max-w-7xl mx-auto space-y-8">

                    {/* HEADER */}
                    <div className="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-[#16162a] p-6 rounded-[2rem] border border-white/5 shadow-2xl">
                        <div>
                            <span className="text-[10px] font-black text-orange-500 uppercase tracking-[0.3em] italic">Configuración de Inventario de Checklist Dinámicos</span>
                            <h1 className="text-2xl font-black text-white uppercase italic">Tipos de Checklist Dinámicos y Atributos</h1>
                            <p className="text-sm text-white mt-1.5">
                                Definí la ficha técnica de cada categoría de activo asociando a un Checklists previamente creado. Podés importar los atributos desde un checklist ya configurado y definir indices y reglas de atributos.
                            </p>
                        </div>
                        <div className="flex gap-3">
                            <button
                                onClick={openCreateModal}
                                className="px-6 py-3 bg-gradient-to-r from-orange-500 to-amber-600 text-white font-black text-xs uppercase tracking-widest rounded-xl shadow-lg hover:brightness-110 transition-all active:scale-95 flex items-center gap-2"
                            >
                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M12 4v16m8-8H4"/></svg>
                                Nuevo tipo de Checklist Dinámico
                            </button>
                            <Link
                                href={route('gestor.assets.index')}
                                className="px-4 py-3 bg-white/5 border border-white/10 text-white/70 font-bold text-xs uppercase tracking-wider rounded-xl hover:bg-white/10 transition-all"
                            >
                                ← Volver
                            </Link>
                        </div>
                    </div>

                    {/* FLASH MESSAGE */}
                    {flash?.message && (
                        <div className="p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl text-emerald-400 text-xs font-bold">
                            {flash.message}
                        </div>
                    )}

                    {/* GRID DE TIPOS */}
                    {assetTypes.length === 0 ? (
                        <div className="text-center py-20 text-white/30">
                            <svg className="w-16 h-16 mx-auto mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            <p className="text-sm font-bold">No hay tipos de activos configurados aún.</p>
                            <p className="text-xs mt-1">Creá uno y vinculalo a un checklist para importar sus atributos automáticamente.</p>
                        </div>
                    ) : (
                        <div className="flex flex-col gap-4">
                            {assetTypes.map(type => (
                                <div key={type.id} className="bg-[#16162a] p-6 rounded-[2rem] border border-white/5 shadow-xl hover:border-orange-500/20 transition-all flex flex-col gap-4">
                                    <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                        <div className="flex items-center gap-3">
                                            <h3 className="text-base font-black text-white uppercase italic">{type.name}</h3>
                                            {type.category ? (
                                                <div className="flex items-center gap-1.5">
                                                    <div className="w-5 h-5 rounded-md flex items-center justify-center border transition-all text-xs" style={{ backgroundColor: `${type.category.color}15`, borderColor: `${type.category.color}40`, color: type.category.color }} title={`Categoría: ${type.category.name}`}>
                                                        <svg className="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" dangerouslySetInnerHTML={{ __html: ICONS[type.category.icon] || ICONS['tag'] }} />
                                                    </div>
                                                    <span className="px-1.5 py-0.5 rounded-lg text-[9px] font-black uppercase tracking-wider italic" style={{ color: type.category.color }}>
                                                        {type.category.name}
                                                    </span>
                                                </div>
                                            ) : (
                                                <span className="px-2.5 py-1 bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 rounded-lg text-[10px] font-bold">
                                                    General
                                                </span>
                                            )}
                                        </div>
                                        
                                        <div className="flex items-center gap-2">
                                            <button
                                                onClick={() => openEditModal(type)}
                                                className="px-3 py-1.5 bg-white/5 text-white/70 border border-white/10 rounded-xl text-[10px] font-black uppercase hover:bg-white/10 transition-all cursor-pointer"
                                            >
                                                Editar Atributos
                                            </button>
                                            <button
                                                onClick={() => handleDelete(type)}
                                                className="px-3 py-1.5 bg-red-500/10 text-red-400 border border-red-500/20 rounded-xl text-[10px] font-black uppercase hover:bg-red-500/20 transition-all cursor-pointer"
                                            >
                                                Eliminar
                                            </button>
                                        </div>
                                    </div>

                                    {type.description && <p className="text-xs text-white/40">{type.description}</p>}

                                    <div className="bg-black/30 p-4 rounded-xl border border-white/5 space-y-2">
                                        <button
                                            type="button"
                                            onClick={() => toggleAttributes(type.id)}
                                            className="w-full flex items-center justify-between text-[9px] font-black text-orange-400 uppercase tracking-widest hover:text-orange-300 transition-colors focus:outline-none cursor-pointer"
                                        >
                                            <span>Atributos de Ficha Técnica ({type.metadata_schema?.length || 0})</span>
                                            <svg 
                                                className={`w-3.5 h-3.5 transition-transform duration-200 ${expandedAttributes[type.id] ? 'rotate-180' : ''}`}
                                                fill="none" 
                                                stroke="currentColor" 
                                                viewBox="0 0 24 24" 
                                                strokeWidth="2.5"
                                            >
                                                <path strokeLinecap="round" strokeLinejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                            </svg>
                                        </button>
                                        
                                        {expandedAttributes[type.id] && (
                                            <div className="pt-2 animate-fadeIn">
                                                {type.metadata_schema && type.metadata_schema.length > 0 ? (
                                                    <div className="flex flex-wrap gap-1.5">
                                                        {type.metadata_schema.map((f, i) => {
                                                            const isConditional = (f.maintenance_rules && f.maintenance_rules.length > 0) || f.alert_days || f.alert_threshold || f.alert_interval;
                                                            const isCreate = f.show_in_create;
                                                            const isSre = f.maintenance_rules && f.maintenance_rules.length > 0;
                                                            
                                                            let badgeClass = "bg-white/5 border-white/10 text-gray-300 hover:border-white/20";
                                                            if (isConditional) {
                                                                badgeClass = "bg-amber-500/10 border-amber-500/30 text-amber-300 font-bold hover:bg-amber-500/15";
                                                            } else if (isCreate) {
                                                                badgeClass = "bg-blue-500/10 border-blue-500/30 text-blue-300 font-bold hover:bg-blue-500/15";
                                                            }
                                                            
                                                            return (
                                                                <span key={i} title={`Clave: ${f.name}`} className={`px-2.5 py-1 border rounded-lg text-[10px] font-mono cursor-help transition-all duration-200 ${badgeClass}`}>
                                                                    {isCreate && <span className="mr-1 text-[9px] text-blue-400 font-bold">📌 Alta</span>}
                                                                    {isCreate && isConditional && <span className="mx-1 text-white/20">|</span>}
                                                                    {isConditional && (
                                                                        <span className="mr-1 text-[9px] text-amber-400 font-bold">
                                                                            {isSre ? '⚡ Regla SRE' : '🔔 Alerta'}
                                                                        </span>
                                                                    )}
                                                                    {((isCreate || isConditional) && <span className="mx-1 text-white/20">|</span>)}
                                                                    <strong className="text-white">{f.label || f.name}</strong>
                                                                    <span className="ml-1 text-white/30">
                                                                        {f.type === 'date'
                                                                            ? '🗓️'
                                                                            : (f.type === 'number' ? '🔢' : '📝')}
                                                                    </span>
                                                                </span>
                                                            );
                                                        })}
                                                    </div>
                                                ) : (
                                                    <p className="text-[10px] text-white/30 italic">Sin atributos definidos.</p>
                                                )}
                                            </div>
                                        )}
                                    </div>
                                </div>
                            ))}
                        </div>
                    )}

                    {/* ══════════════════════════════════════════ */}
                    {/* MODAL CONFIGURADOR */}
                    {/* ══════════════════════════════════════════ */}
                    {showModal && (
                        <div className="fixed inset-0 bg-black/80 backdrop-blur-md flex items-start justify-center p-4 z-50 overflow-y-auto">
                            <div className="bg-[#16162a] border border-white/10 rounded-[2.5rem] p-8 max-w-3xl w-full space-y-6 shadow-2xl my-8">

                                <div className="flex items-center justify-between border-b border-white/5 pb-4">
                                    <div>
                                        <span className="text-[10px] font-black text-orange-400 uppercase tracking-widest italic">
                                            {editingType ? 'Editar Tipo de Activo' : 'Nuevo tipo de Checklist Dinámico'}
                                        </span>
                                        <h2 className="text-xl font-black text-white uppercase italic">
                                            {editingType ? editingType.name : 'Configurar Ficha Técnica'}
                                        </h2>
                                    </div>
                                    <button onClick={() => setShowModal(false)} className="text-white/40 hover:text-white font-bold text-lg">✕</button>
                                </div>

                                <form onSubmit={handleSubmit} className="space-y-6">
                                    {/* ASOCIAR CHECKLIST Y CONFIGURAR ATRIBUTOS */}
                                    <div className="bg-indigo-500/5 border border-indigo-500/20 rounded-2xl p-5 space-y-4">
                                        <div className="flex items-start gap-3">
                                            {/* Ícono Técnico según visual.md */}
                                            <div className="h-12 w-12 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-orange-500 shadow-inner rotate-3 hover:rotate-0 transition-all duration-500 flex-shrink-0">
                                                <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2">
                                                    <path strokeLinecap="round" strokeLinejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <h4 className="text-xs font-black text-indigo-300 uppercase tracking-wider">Asociar Checklist y configurar Atributos</h4>
                                                <p className="text-xs text-white mt-0.5">
                                                    Seleccioná la categoría, subcategoría y el checklist. El nombre del tipo de activo se auto-asignará a partir del checklist seleccionado.
                                                </p>
                                            </div>
                                        </div>

                                        {/* FILTROS EN CASCADA: CATEGORÍA -> SUBCATEGORÍA -> CHECKLIST */}
                                        <div className="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-1">
                                            <div>
                                                <label className="block text-[9px] font-black text-indigo-200/50 uppercase tracking-widest mb-1 italic">1. Categoría *</label>
                                                <select
                                                    value={form.data.category_id}
                                                    onChange={e => {
                                                        form.setData('category_id', e.target.value);
                                                        setFilterSubcategoryId('');
                                                        setImportChecklistId('');
                                                        setImportError('');
                                                    }}
                                                    className="w-full bg-black/40 border border-indigo-500/30 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-indigo-400"
                                                    required
                                                >
                                                    <option value="">— Seleccionar Categoría —</option>
                                                    {categories.map(c => (
                                                        <option key={c.id} value={c.id}>{c.name}</option>
                                                    ))}
                                                </select>
                                            </div>

                                            <div>
                                                <label className="block text-[9px] font-black text-indigo-200/50 uppercase tracking-widest mb-1 italic">2. Subcategoría (Opcional)</label>
                                                <select
                                                    value={filterSubcategoryId}
                                                    onChange={e => {
                                                        setFilterSubcategoryId(e.target.value);
                                                        setImportChecklistId('');
                                                        setImportError('');
                                                    }}
                                                    disabled={!form.data.category_id || availableSubcategories.length === 0}
                                                    className="w-full bg-black/40 border border-indigo-500/30 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-indigo-400 disabled:opacity-30 disabled:cursor-not-allowed"
                                                >
                                                    <option value="">
                                                        {!form.data.category_id
                                                            ? '— Seleccioná categoría primero —'
                                                            : availableSubcategories.length === 0
                                                                ? '— Sin subcategorías disponibles —'
                                                                : '— Todas las Subcategorías —'}
                                                    </option>
                                                    {availableSubcategories.map(s => (
                                                        <option key={s.id} value={s.id}>{s.name}</option>
                                                    ))}
                                                </select>
                                            </div>

                                            <div>
                                                <label className="block text-[9px] font-black text-indigo-200/50 uppercase tracking-widest mb-1 italic">3. Checklist Asociado *</label>
                                                <select
                                                    value={importChecklistId}
                                                    onChange={e => {
                                                        const cid = e.target.value;
                                                        setImportChecklistId(cid);
                                                        setImportError('');
                                                        const checklistSelect = checklists.find(c => String(c.id) === String(cid));
                                                        if (checklistSelect) {
                                                            form.setData(d => ({
                                                                ...d,
                                                                name: checklistSelect.title
                                                            }));
                                                        }
                                                    }}
                                                    disabled={!form.data.category_id}
                                                    className="w-full bg-black/40 border border-indigo-500/30 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-indigo-400 disabled:opacity-30 disabled:cursor-not-allowed"
                                                    required={!editingType}
                                                >
                                                    <option value="">
                                                        {!form.data.category_id
                                                            ? '— Seleccioná categoría primero —'
                                                            : filteredChecklists.length === 0
                                                                ? '— No hay checklists disponibles —'
                                                                : `— Seleccionar Checklist (${filteredChecklists.length}) —`}
                                                    </option>
                                                    {filteredChecklists.map(c => (
                                                        <option key={c.id} value={c.id}>
                                                            {c.title}{c.registry_code ? ` (${c.registry_code})` : ''}
                                                        </option>
                                                    ))}
                                                </select>
                                            </div>
                                        </div>

                                        {/* SELECCIÓN DE CHECKLIST E IMPORTACIÓN ACCIÓN */}
                                        <div className="flex justify-between items-center pt-2">
                                            {/* Previsualización dinámica de Categoría e Icono */}
                                            {form.data.category_id ? (() => {
                                                const cat = categories.find(c => String(c.id) === String(form.data.category_id));
                                                if (!cat) return <div />;
                                                return (
                                                    <div className="flex items-center gap-1.5 p-2 bg-white/5 rounded-lg border border-white/5 w-fit animate-fade-in">
                                                        <div className="w-5 h-5 rounded-md flex items-center justify-center border text-xs" style={{ backgroundColor: `${cat.color}15`, borderColor: `${cat.color}40`, color: cat.color }}>
                                                            <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" dangerouslySetInnerHTML={{ __html: ICONS[cat.icon] || ICONS['tag'] }} />
                                                        </div>
                                                        <span className="text-[9px] font-black uppercase tracking-wider italic" style={{ color: cat.color }}>
                                                            Categoría: {cat.name}
                                                        </span>
                                                    </div>
                                                );
                                            })() : <div />}

                                            <button
                                                type="button"
                                                onClick={handleImportFromChecklist}
                                                disabled={importing || !importChecklistId}
                                                className="px-5 py-2.5 bg-indigo-600 text-white font-black text-[10px] uppercase tracking-wider rounded-xl hover:bg-indigo-500 disabled:opacity-40 disabled:cursor-not-allowed transition-all flex items-center gap-1.5 flex-shrink-0 shadow-lg"
                                            >
                                                {importing ? (
                                                    <>
                                                        <svg className="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"/><path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                                        Cargando Campos...
                                                    </>
                                                ) : (
                                                    <>
                                                        <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                                        Cargar Atributos
                                                    </>
                                                )}
                                            </button>
                                        </div>

                                        {importError && (
                                            <p className="text-[10px] text-amber-400 bg-amber-500/10 border border-amber-500/20 rounded-xl px-3 py-2">
                                                ⚠️ {importError}
                                            </p>
                                        )}
                                    </div>

                                    {/* CONFIGURACIÓN DE AGRUPACIÓN (FLOTA/PISO) */}
                                    <div className="bg-orange-500/5 border border-orange-500/20 rounded-2xl p-5 space-y-4">
                                        <div className="flex items-start gap-3">
                                            <div className="h-10 w-10 rounded-xl bg-orange-500/10 border border-orange-500/20 flex items-center justify-center text-orange-400 shadow-inner flex-shrink-0">
                                                <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2.5">
                                                    <path strokeLinecap="round" strokeLinejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                                </svg>
                                            </div>
                                            <div className="flex-1">
                                                <h4 className="text-xs font-black text-orange-400 uppercase tracking-wider">Etiquetas de Agrupación Jerárquica</h4>
                                                <p className="text-[10px] text-white/50 mt-0.5">
                                                    Personalizá cómo los gestores y técnicos clasificarán y verán los activos de este tipo (ej: Flota/Sub-flota o Piso/Sector).
                                                </p>
                                            </div>
                                        </div>

                                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                            <div>
                                                <label className="block text-[9px] font-black text-white/40 uppercase tracking-widest mb-1 italic">Nivel 1: Grupo Principal (ej: Flota, Piso, Nave) *</label>
                                                <input
                                                    type="text"
                                                    value={form.data.group_label}
                                                    onChange={e => form.setData('group_label', e.target.value)}
                                                    placeholder="Ej: Piso, Flota, Nave"
                                                    className="w-full bg-black/40 border border-white/10 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-orange-500"
                                                    required
                                                />
                                            </div>

                                            <div>
                                                <label className="block text-[9px] font-black text-white/40 uppercase tracking-widest mb-1 italic">Nivel 2: Subgrupo / Sector (ej: Sub-flota, Sector)</label>
                                                <input
                                                    type="text"
                                                    value={form.data.subgroup_label}
                                                    onChange={e => form.setData('subgroup_label', e.target.value)}
                                                    placeholder="Ej: Sector, Sub-flota, Oficina"
                                                    className="w-full bg-black/40 border border-white/10 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-orange-500"
                                                />
                                            </div>
                                        </div>
                                    </div>

                                    {/* ─────────────────────────────────────────── */}
                                    {/* LISTA DE ATRIBUTOS (ESTILO CHECKLIST) */}
                                    {/* ─────────────────────────────────────────── */}
                                    <div className="space-y-3">
                                        <div className="flex items-center justify-between px-1">
                                            <div>
                                                <h4 className="text-xs font-black text-orange-400 uppercase tracking-wider italic">
                                                    Preguntas / Atributos de Ficha Técnica
                                                </h4>
                                                <p className="text-[10px] text-white/30 mt-0.5">
                                                    Los campos importados se marcan con 🔗. Podés editarlos o agregar más manualmente.
                                                </p>
                                            </div>
                                            <button
                                                type="button"
                                                onClick={addField}
                                                className="h-9 px-5 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 hover:bg-emerald-500 hover:text-white rounded-xl text-[8px] font-black uppercase tracking-widest italic transition-all cursor-pointer flex-shrink-0"
                                            >
                                                Anclar Atributo
                                            </button>
                                        </div>

                                        <div className="space-y-3 pb-2">
                                            {form.data.metadata_schema.map((field, index) => {
                                                const typeLabel = field.type === 'date'
                                                    ? (field.alert_days ? '🔔 Fecha Venc.' : '🗓️ Fecha Info')
                                                    : field.type === 'number' ? '🔢 Número' : '📝 Texto';

                                                const typeColor = field.type === 'date'
                                                    ? (field.alert_days ? 'text-amber-400 bg-amber-500/10 border-amber-500/20' : 'text-indigo-400 bg-indigo-500/10 border-indigo-500/20')
                                                    : field.type === 'number' ? 'text-cyan-400 bg-cyan-500/10 border-cyan-500/20'
                                                    : 'text-white/50 bg-white/5 border-white/10';

                                                const hasSreRules = field.maintenance_rules && field.maintenance_rules.length > 0;

                                                return (
                                                    <div
                                                        key={index}
                                                        className={`rounded-[1.5rem] border overflow-hidden transition-all ${
                                                            field._imported
                                                                ? 'bg-indigo-500/5 border-indigo-500/20'
                                                                : 'bg-black/30 border-white/10'
                                                        }`}
                                                    >
                                                        {/* CABECERA DE LA PREGUNTA */}
                                                        <div className="flex items-center gap-4 px-6 py-4">
                                                            {/* Número de orden */}
                                                            <div className="w-8 h-8 rounded-xl bg-orange-500/20 flex items-center justify-center text-[11px] font-black text-orange-400 italic border border-orange-500/20 flex-shrink-0">
                                                                {index + 1}
                                                            </div>

                                                            {/* Label editable o texto estático si es importado */}
                                                            <div className="flex-1">
                                                                {field._imported ? (
                                                                    <div>
                                                                        <div className="text-sm font-black text-white/95 italic uppercase select-none">
                                                                            {field.label}
                                                                        </div>
                                                                        <div className="flex items-center gap-1 mt-0.5 select-none">
                                                                           <span className="text-[8px] text-white/30 font-mono uppercase">Clave:</span>
                                                                           <span className="text-[9px] font-mono text-orange-400/80">{field.name}</span>
                                                                       </div>
                                                                    </div>
                                                                ) : (
                                                                    <div>
                                                                        <input
                                                                            type="text"
                                                                            value={field.label}
                                                                            onChange={e => updateField(index, 'label', e.target.value)}
                                                                            placeholder="Nombre del atributo manual..."
                                                                            className="w-full bg-transparent border-none text-sm font-black text-white italic uppercase focus:ring-0 p-0 placeholder-white/20"
                                                                            required
                                                                        />
                                                                        <div className="flex items-center gap-2 mt-1">
                                                                            <span className="text-[8px] text-white/30 font-mono uppercase">ID:</span>
                                                                            <input
                                                                                type="text"
                                                                                value={field.name}
                                                                                onChange={e => updateField(index, 'name', e.target.value)}
                                                                                placeholder="clave_interna"
                                                                                className="bg-transparent border-none text-[9px] font-mono text-orange-400/60 focus:ring-0 p-0 placeholder-white/20 w-40"
                                                                                required
                                                                            />
                                                                        </div>
                                                                    </div>
                                                                )}
                                                            </div>

                                                            {/* Tipo de dato como badge */}
                                                            <select
                                                                value={
                                                                    field.type === 'date'
                                                                        ? (field.alert_days ? 'date_alert' : 'date_info')
                                                                        : (field.type || 'text')
                                                                }
                                                                onChange={e => {
                                                                    const val = e.target.value;
                                                                    const updated = [...form.data.metadata_schema];
                                                                    if (val === 'date_alert') {
                                                                        updated[index].type = 'date';
                                                                        if (!updated[index].alert_days) updated[index].alert_days = 30;
                                                                    } else if (val === 'date_info') {
                                                                        updated[index].type = 'date';
                                                                        updated[index].alert_days = '';
                                                                    } else {
                                                                        updated[index].type = val;
                                                                        updated[index].alert_days = '';
                                                                        updated[index].alert_threshold = '';
                                                                    }
                                                                    form.setData('metadata_schema', updated);
                                                                }}
                                                                className={`border rounded-xl px-3 py-1.5 text-[9px] font-black uppercase tracking-wider focus:outline-none cursor-pointer [color-scheme:dark] ${typeColor}`}
                                                            >
                                                                <option value="text" className="bg-[#16162a] text-white">📝 Texto</option>
                                                                <option value="number" className="bg-[#16162a] text-white">🔢 Número</option>
                                                                <option value="date_alert" className="bg-[#16162a] text-white">🔔 Fecha Venc.</option>
                                                                <option value="date_info" className="bg-[#16162a] text-white">🗓️ Fecha Info</option>
                                                            </select>

                                                             {/* Selector de Rol */}
                                                             <div className="flex items-center gap-1 flex-shrink-0">
                                                                 <select
                                                                     value={field.role || 'verificacion'}
                                                                     onChange={e => {
                                                                         const newRole = e.target.value;
                                                                         const updated = [...form.data.metadata_schema];
                                                                         updated[index].role = newRole;
                                                                         if (newRole === 'identificador') {
                                                                             updated[index].show_in_create = true;
                                                                         }
                                                                         form.setData('metadata_schema', updated);
                                                                     }}
                                                                     className={`border rounded-xl px-2 py-1 text-[9px] font-black uppercase tracking-wider focus:outline-none cursor-pointer [color-scheme:dark] bg-[#16162a] ${
                                                                         field.role === 'identificador' ? 'border-blue-500/40 text-blue-400' :
                                                                         field.role === 'dato_estatico' ? 'border-green-500/40 text-green-400' :
                                                                         field.role === 'dato_con_regla' ? 'border-orange-500/40 text-orange-400' :
                                                                         'border-white/10 text-white/55'
                                                                     }`}
                                                                 >
                                                                     <option value="identificador">🔵 Identificador</option>
                                                                     <option value="dato_estatico">🟢 Estático</option>
                                                                     <option value="dato_con_regla">🟠 Con Regla</option>
                                                                     <option value="verificacion">⚫ Verificación</option>
                                                                 </select>
                                                             </div>

                                                            {/* Badge Alta */}
                                                            <label className="flex items-center gap-1.5 cursor-pointer select-none flex-shrink-0">
                                                                <input
                                                                    type="checkbox"
                                                                    checked={!!field.show_in_create}
                                                                    onChange={e => updateField(index, 'show_in_create', e.target.checked)}
                                                                    className="rounded border-white/20 bg-black/50 text-blue-500 focus:ring-blue-500 h-3.5 w-3.5"
                                                                />
                                                                <span className={`text-[9px] font-black uppercase tracking-wider ${field.show_in_create ? 'text-blue-400' : 'text-white/30'}`}>
                                                                    📌 Alta
                                                                </span>
                                                            </label>

                                                            {/* Quitar */}
                                                            {!field._imported && (
                                                                <button
                                                                    type="button"
                                                                    onClick={() => removeField(index)}
                                                                    className="h-7 w-7 bg-red-500/10 hover:bg-red-500 text-red-400 hover:text-white border border-red-500/20 rounded-lg flex items-center justify-center transition-all flex-shrink-0"
                                                                    title="Quitar atributo"
                                                                >
                                                                    <svg className="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" strokeWidth="2.5" strokeLinecap="round"/></svg>
                                                                </button>
                                                            )}
                                                        </div>

                                                        {/* CONFIGURACIÓN CONDICIONAL: días de alerta / umbral */}
                                                        {(field.alert_days || field.type === 'number') && (
                                                            <div className="px-6 pb-4 flex flex-wrap items-center gap-3 border-t border-white/5 pt-3">
                                                                {field.type === 'date' && field.alert_days && (
                                                                    <div className="flex items-center gap-2">
                                                                        <span className="text-[9px] font-black text-amber-400 uppercase tracking-wider">Alerta a los</span>
                                                                        <input
                                                                            type="number"
                                                                            value={field.alert_days || ''}
                                                                            onChange={e => updateField(index, 'alert_days', e.target.value)}
                                                                            placeholder="30"
                                                                            className="w-20 bg-black/50 border border-amber-500/20 rounded-lg px-2 py-1 text-xs text-amber-300 font-mono focus:outline-none focus:border-amber-500"
                                                                        />
                                                                        <span className="text-[9px] text-white/30">días antes del vencimiento</span>
                                                                    </div>
                                                                )}
                                                                {field.type === 'number' && (
                                                                    <div className="flex items-center gap-2 flex-wrap">
                                                                        <span className="text-[9px] font-black text-cyan-400 uppercase tracking-wider">Umbral fijo:</span>
                                                                        <input
                                                                            type="number"
                                                                            step="any"
                                                                            value={field.alert_threshold || ''}
                                                                            onChange={e => updateField(index, 'alert_threshold', e.target.value)}
                                                                            placeholder="Ej: 150000"
                                                                            className="w-28 bg-black/50 border border-cyan-500/20 rounded-lg px-2 py-1 text-xs text-cyan-300 font-mono focus:outline-none focus:border-cyan-500"
                                                                        />
                                                                        <span className="text-[9px] text-white/30">/ Frecuencia recurrente:</span>
                                                                        <input
                                                                            type="number"
                                                                            step="any"
                                                                            value={field.alert_interval || ''}
                                                                            onChange={e => updateField(index, 'alert_interval', e.target.value)}
                                                                            placeholder="Ej: 15000"
                                                                            className="w-28 bg-black/50 border border-indigo-500/20 rounded-lg px-2 py-1 text-xs text-indigo-300 font-mono focus:outline-none focus:border-indigo-500"
                                                                        />
                                                                    </div>
                                                                )}
                                                                {/* Mensaje personalizado */}
                                                                <input
                                                                    type="text"
                                                                    value={field.alert_message || ''}
                                                                    onChange={e => updateField(index, 'alert_message', e.target.value)}
                                                                    placeholder="💬 Mensaje de alerta personalizado (opcional)..."
                                                                    className="flex-1 min-w-48 bg-black/40 border border-amber-500/10 rounded-lg px-3 py-1.5 text-[11px] text-amber-200 placeholder-white/20 focus:outline-none focus:border-amber-500/40 italic"
                                                                />
                                                            </div>
                                                        )}

                                                        {/* REGLAS SRE — solo para campos date/number */}
                                                        {(field.type === 'number' || field.type === 'date') && (
                                                            <div className="border-t border-white/5">
                                                                <div className="px-6 py-3 flex items-center justify-between bg-indigo-500/5">
                                                                    <span className="text-[9px] font-black text-indigo-400 uppercase tracking-widest flex items-center gap-1.5">
                                                                        ⚡ Reglas SRE
                                                                        {hasSreRules && (
                                                                            <span className="px-1.5 py-0.5 rounded-full bg-indigo-500/20 text-indigo-300 text-[8px]">
                                                                                {field.maintenance_rules.length} regla{field.maintenance_rules.length !== 1 ? 's' : ''}
                                                                            </span>
                                                                        )}
                                                                    </span>
                                                                    <button
                                                                        type="button"
                                                                        onClick={() => {
                                                                            const rules = field.maintenance_rules || [];
                                                                            const updatedSchema = [...form.data.metadata_schema];
                                                                            updatedSchema[index] = {
                                                                                ...field,
                                                                                maintenance_rules: [
                                                                                    ...rules,
                                                                                    { nombre: '', frecuencia: '', margen_pre_alerta: '', checklist_resolucion_id: '' }
                                                                                ]
                                                                            };
                                                                            form.setData('metadata_schema', updatedSchema);
                                                                        }}
                                                                        className="px-3 py-1 bg-indigo-500/20 hover:bg-indigo-500/40 text-indigo-300 border border-indigo-500/30 rounded-lg text-[8px] font-black uppercase tracking-wider transition-all cursor-pointer"
                                                                    >
                                                                        + Agregar Regla
                                                                    </button>
                                                                </div>

                                                                {(field.maintenance_rules || []).length > 0 && (
                                                                    <div className="px-6 pb-4 space-y-3 pt-3">
                                                                        {(field.maintenance_rules || []).map((rule, ruleIndex) => (
                                                                            <div key={ruleIndex} className="bg-black/40 rounded-2xl border border-indigo-500/10 p-4 space-y-3">
                                                                                <div className="flex items-center gap-3 flex-wrap">
                                                                                    {/* Nombre de la regla */}
                                                                                    <div className="flex-1 min-w-40">
                                                                                        <label className="block text-[8px] font-black text-white/30 uppercase mb-0.5">Nombre de la Regla</label>
                                                                                        <input
                                                                                            type="text"
                                                                                            value={rule.nombre || ''}
                                                                                            onChange={e => {
                                                                                                const updatedSchema = [...form.data.metadata_schema];
                                                                                                const rules = [...(field.maintenance_rules || [])];
                                                                                                rules[ruleIndex].nombre = e.target.value;
                                                                                                updatedSchema[index].maintenance_rules = rules;
                                                                                                form.setData('metadata_schema', updatedSchema);
                                                                                            }}
                                                                                            placeholder="Ej: Cambio de Aceite"
                                                                                            className="w-full bg-black/40 border border-white/10 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-indigo-500"
                                                                                            required
                                                                                        />
                                                                                    </div>
                                                                                    {/* Frecuencia */}
                                                                                    <div className="w-36">
                                                                                        <label className="block text-[8px] font-black text-white/30 uppercase mb-0.5">
                                                                                            {field.type === 'date' ? 'Cada (días)' : 'Frecuencia (km/h)'}
                                                                                        </label>
                                                                                        <input
                                                                                            type="number"
                                                                                            value={rule.frecuencia || ''}
                                                                                            onChange={e => {
                                                                                                const updatedSchema = [...form.data.metadata_schema];
                                                                                                const rules = [...(field.maintenance_rules || [])];
                                                                                                rules[ruleIndex].frecuencia = e.target.value;
                                                                                                updatedSchema[index].maintenance_rules = rules;
                                                                                                form.setData('metadata_schema', updatedSchema);
                                                                                            }}
                                                                                            placeholder={field.type === 'date' ? "365" : "15000"}
                                                                                            className="w-full bg-black/40 border border-white/10 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-indigo-500"
                                                                                            required
                                                                                        />
                                                                                    </div>
                                                                                    {/* Margen pre-alerta */}
                                                                                    <div className="w-36">
                                                                                        <label className="block text-[8px] font-black text-white/30 uppercase mb-0.5">Margen Pre-Alerta</label>
                                                                                        <input
                                                                                            type="number"
                                                                                            value={rule.margen_pre_alerta || ''}
                                                                                            onChange={e => {
                                                                                                const updatedSchema = [...form.data.metadata_schema];
                                                                                                const rules = [...(field.maintenance_rules || [])];
                                                                                                rules[ruleIndex].margen_pre_alerta = e.target.value;
                                                                                                updatedSchema[index].maintenance_rules = rules;
                                                                                                form.setData('metadata_schema', updatedSchema);
                                                                                            }}
                                                                                            placeholder={field.type === 'date' ? "30 días" : "1000"}
                                                                                            className="w-full bg-black/40 border border-white/10 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-indigo-500"
                                                                                        />
                                                                                    </div>
                                                                                    {/* Checklist de resolución */}
                                                                                    <div className="flex-1 min-w-44">
                                                                                        <label className="block text-[8px] font-black text-white/30 uppercase mb-0.5">Checklist de Resolución</label>
                                                                                        <select
                                                                                            value={rule.checklist_resolucion_id || ''}
                                                                                            onChange={e => {
                                                                                                const updatedSchema = [...form.data.metadata_schema];
                                                                                                const rules = [...(field.maintenance_rules || [])];
                                                                                                rules[ruleIndex].checklist_resolucion_id = e.target.value;
                                                                                                updatedSchema[index].maintenance_rules = rules;
                                                                                                form.setData('metadata_schema', updatedSchema);
                                                                                            }}
                                                                                            className="w-full bg-black/40 border border-white/10 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-indigo-500"
                                                                                            required
                                                                                        >
                                                                                            <option value="">— Seleccionar Checklist —</option>
                                                                                            {checklists.map(c => (
                                                                                                <option key={c.id} value={c.id}>{c.title}</option>
                                                                                            ))}
                                                                                        </select>
                                                                                    </div>
                                                                                    {/* Quitar regla */}
                                                                                    <button
                                                                                        type="button"
                                                                                        onClick={() => {
                                                                                            const updatedSchema = [...form.data.metadata_schema];
                                                                                            const rules = [...(field.maintenance_rules || [])];
                                                                                            rules.splice(ruleIndex, 1);
                                                                                            updatedSchema[index].maintenance_rules = rules;
                                                                                            form.setData('metadata_schema', updatedSchema);
                                                                                        }}
                                                                                        className="h-8 w-8 bg-red-500/10 hover:bg-red-500 hover:text-white text-red-400 border border-red-500/20 rounded-xl flex items-center justify-center transition-all flex-shrink-0 cursor-pointer self-end"
                                                                                        title="Quitar regla"
                                                                                    >
                                                                                        ✕
                                                                                    </button>
                                                                                </div>
                                                                            </div>
                                                                        ))}
                                                                    </div>
                                                                )}

                                                                {(field.maintenance_rules || []).length === 0 && (
                                                                    <p className="px-6 py-3 text-[10px] text-white/20 italic">
                                                                        Sin reglas SRE configuradas para esta métrica.
                                                                    </p>
                                                                )}
                                                            </div>
                                                        )}
                                                    </div>
                                                );
                                            })}
                                            {form.data.metadata_schema.length === 0 && (
                                                <div className="text-center py-12 text-white/20 border border-dashed border-white/10 rounded-[2rem]">
                                                    <p className="text-2xl mb-2">📋</p>
                                                    <p className="text-xs italic">Sin atributos aún. Importá desde un checklist o anclá uno manualmente.</p>
                                                </div>
                                            )}
                                        </div>
                                    </div>

                                    <div className="flex justify-end gap-3 pt-4 border-t border-white/5">
                                        <button
                                            type="button"
                                            onClick={() => setShowModal(false)}
                                            className="px-6 py-2.5 bg-white/5 border border-white/10 text-white/70 rounded-xl text-xs font-bold hover:bg-white/10 transition-all"
                                        >
                                            Cancelar
                                        </button>
                                        <button
                                            type="submit"
                                            disabled={form.processing}
                                            className="px-6 py-2.5 bg-gradient-to-r from-orange-500 to-amber-600 text-white font-black text-xs uppercase tracking-widest rounded-xl shadow-lg hover:brightness-110 transition-all"
                                        >
                                            {form.processing ? 'Guardando...' : 'Guardar Tipo de Activo'}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
