import React, { useState, useEffect } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, useForm, usePage, router } from '@inertiajs/react';

export default function Edit({ auth, consultant = null, primary_gestor = null, has_primary_gestor = false, suscripcion_logs = [], audit_logs = [] }) {
    const { flash } = usePage().props;
    const isEditing = !!consultant;
    const [activeTab, setActiveTab] = useState('audit'); // 'audit' or 'subscriptions'
    const [isEditingPrimary, setIsEditingPrimary] = useState(false);
    const [copied, setCopied] = useState(false);
    const [dismissedPassword, setDismissedPassword] = useState(false);

    // Primary Gestor Form
    const primaryForm = useForm({
        nombres: primary_gestor?.nombres || '',
        apellidos: primary_gestor?.apellidos || '',
        email: primary_gestor?.email || '',
        telefono: primary_gestor?.telefono || '',
        n_doc: primary_gestor?.n_doc || '',
        matricula: primary_gestor?.matricula || '',
    });

    useEffect(() => {
        if (primary_gestor) {
            primaryForm.setData({
                nombres: primary_gestor.nombres || '',
                apellidos: primary_gestor.apellidos || '',
                email: primary_gestor.email || '',
                telefono: primary_gestor.telefono || '',
                n_doc: primary_gestor.n_doc || '',
                matricula: primary_gestor.matricula || '',
            });
        }
    }, [primary_gestor]);

    const generatedPassword = !dismissedPassword && flash?.generated_password ? flash.generated_password : null;

    const copyToClipboard = (text) => {
        navigator.clipboard.writeText(text);
        setCopied(true);
        setTimeout(() => setCopied(false), 3000);
    };

    const handleSavePrimaryGestor = (e) => {
        e.preventDefault();
        primaryForm.post(route('admin.consultants.primary-gestor.save', consultant.id), {
            onSuccess: () => {
                setIsEditingPrimary(false);
            }
        });
    };

    const handleResetPrimaryPassword = () => {
        if (confirm('¿Estás seguro de resetear la contraseña del Gestor Principal? Se generará una nueva clave temporal de un solo uso.')) {
            setDismissedPassword(false);
            router.post(route('admin.consultants.primary-gestor.reset-password', consultant.id));
        }
    };
    
    const { data, setData, post, processing, errors } = useForm({
        nombre: consultant?.nombre || '',
        cuit: consultant?.cuit || '',
        max_tecnicos: consultant?.max_tecnicos || 5,
        max_gestores: consultant?.max_gestores || 2,
        max_empresas: consultant?.max_empresas || 5,
        description: consultant?.description || '',
        fiscal_address: consultant?.fiscal_address || '',
        province: consultant?.province || '',
        locality: consultant?.locality || '',
        email: consultant?.email || '',
        phone: consultant?.phone || '',
        color_primary: consultant?.color_primary || '#1e40af',
        color_secondary: consultant?.color_secondary || '#6b7280',
        color_accent: consultant?.color_accent || '#16a34a',
        estado_suscripcion: consultant?.estado_suscripcion || 'activa',
        logo: null,
    });

    const [logoPreview, setLogoPreview] = useState(consultant?.logo_path || null);

    const handleLogoChange = (e) => {
        const file = e.target.files[0];
        if (file) {
            setData('logo', file);
            setLogoPreview(URL.createObjectURL(file));
        }
    };

    const submit = (e) => {
        e.preventDefault();
        if (isEditing) {
            post(route('admin.consultants.update', consultant.id));
        } else {
            post(route('admin.consultants.store'));
        }
    };

    return (
        <AuthenticatedLayout
            user={auth.user}
            title="Súper Administración"
            header={
                <div className="flex flex-col">
                    <h1 className="text-xs font-black text-ep-primary uppercase tracking-[0.4em] italic leading-none mb-2">
                        System Setup
                    </h1>
                    <h2 className="text-xl font-black text-white uppercase italic tracking-tighter leading-none">
                        {isEditing ? 'Configurar' : 'Registrar'} <span className="text-indigo-400">Consultora</span>
                    </h2>
                </div>
            }
        >
            <Head title={isEditing ? 'Editar Consultora' : 'Nueva Consultora'} />

            <div className="py-12 bg-[#0f0f1a] min-h-screen text-white selection:bg-ep-primary/30 animate-fade-in">
                <div className={`${isEditing ? 'max-w-7xl' : 'max-w-4xl'} mx-auto sm:px-6 lg:px-8`}>
                    
                    {/* Return Link */}
                    <div className="mb-6">
                        <Link 
                            href={route('admin.consultants.index')} 
                            className="inline-flex items-center gap-2 text-xs font-black text-white/40 hover:text-white uppercase italic tracking-widest transition-colors"
                        >
                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2.5"><path strokeLinecap="round" strokeLinejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" /></svg>
                            Volver al Listado
                        </Link>
                    </div>

                    <div className={isEditing ? 'grid grid-cols-1 lg:grid-cols-3 gap-8 items-start' : ''}>
                        <div className={isEditing ? 'lg:col-span-2' : ''}>
                            <form onSubmit={submit} className="bg-[#16162a]/40 backdrop-blur-xl border border-white/5 rounded-[2.5rem] p-8 sm:p-10 shadow-2xl space-y-8">
                        
                        {/* IDENTIDAD CENTRAL */}
                        <div className="space-y-6">
                            <h3 className="text-sm font-black text-ep-primary uppercase tracking-widest italic border-b border-white/5 pb-3">
                                Identidad Corporativa
                            </h3>
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label htmlFor="nombre" className="block text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic mb-2">Nombre Consultora *</label>
                                    <input 
                                        type="text" 
                                        id="nombre"
                                        value={data.nombre} 
                                        onChange={e => setData('nombre', e.target.value)}
                                        className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:border-ep-primary focus:ring-0 transition-all font-black uppercase italic"
                                        required
                                    />
                                    {errors.nombre && <p className="text-xs text-red-400 mt-1">{errors.nombre}</p>}
                                </div>
                                <div>
                                    <label htmlFor="cuit" className="block text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic mb-2">CUIT *</label>
                                    <input 
                                        type="text" 
                                        id="cuit"
                                        value={data.cuit} 
                                        onChange={e => setData('cuit', e.target.value)}
                                        placeholder="XX-XXXXXXXX-X"
                                        className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:border-ep-primary focus:ring-0 transition-all font-black italic tracking-wider"
                                        required
                                    />
                                    {errors.cuit && <p className="text-xs text-red-400 mt-1">{errors.cuit}</p>}
                                </div>
                            </div>
                            <div>
                                <label htmlFor="description" className="block text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic mb-2">Descripción General</label>
                                <textarea 
                                    id="description"
                                    value={data.description} 
                                    onChange={e => setData('description', e.target.value)}
                                    rows="3"
                                    className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:border-ep-primary focus:ring-0 transition-all font-bold"
                                />
                                {errors.description && <p className="text-xs text-red-400 mt-1">{errors.description}</p>}
                            </div>
                        </div>

                        {/* LICENCIAS E INFRAESTRUCTURA */}
                        <div className="space-y-6">
                            <h3 className="text-sm font-black text-ep-primary uppercase tracking-widest italic border-b border-white/5 pb-3">
                                Infraestructura & Licencias
                            </h3>
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label htmlFor="max_tecnicos" className="block text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic mb-2">Límite de Licencias de Técnicos (Escuadrón) *</label>
                                    <input 
                                        type="number" 
                                        id="max_tecnicos"
                                        value={data.max_tecnicos} 
                                        onChange={e => setData('max_tecnicos', parseInt(e.target.value) || 0)}
                                        min="1"
                                        className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:border-ep-primary focus:ring-0 transition-all font-black italic"
                                        required
                                    />
                                    {errors.max_tecnicos && <p className="text-xs text-red-400 mt-1">{errors.max_tecnicos}</p>}
                                </div>
                                <div>
                                    <label htmlFor="max_gestores" className="block text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic mb-2">Límite de Licencias de Gestores *</label>
                                    <input 
                                        type="number" 
                                        id="max_gestores"
                                        value={data.max_gestores} 
                                        onChange={e => setData('max_gestores', parseInt(e.target.value) || 0)}
                                        min="1"
                                        className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:border-ep-primary focus:ring-0 transition-all font-black italic"
                                        required
                                    />
                                    {errors.max_gestores && <p className="text-xs text-red-400 mt-1">{errors.max_gestores}</p>}
                                </div>
                                <div>
                                    <label htmlFor="max_empresas" className="block text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic mb-2">Límite de Empresas Clientes *</label>
                                    <input 
                                        type="number" 
                                        id="max_empresas"
                                        value={data.max_empresas} 
                                        onChange={e => setData('max_empresas', parseInt(e.target.value) || 0)}
                                        min="1"
                                        className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:border-ep-primary focus:ring-0 transition-all font-black italic"
                                        required
                                    />
                                    {errors.max_empresas && <p className="text-xs text-red-400 mt-1">{errors.max_empresas}</p>}
                                </div>
                                <div>
                                    <label htmlFor="estado_suscripcion" className="block text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic mb-2">Estado de Suscripción *</label>
                                    <select 
                                        id="estado_suscripcion"
                                        value={data.estado_suscripcion}
                                        onChange={e => setData('estado_suscripcion', e.target.value)}
                                        className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:border-ep-primary focus:ring-0 transition-all font-black uppercase italic"
                                    >
                                        <option value="activa" className="bg-[#16162a]">Suscripción Activa</option>
                                        <option value="morosa" className="bg-[#16162a]">Morosa (Falta de pago)</option>
                                        <option value="baja" className="bg-[#16162a]">Baja del Servicio</option>
                                    </select>
                                    {errors.estado_suscripcion && <p className="text-xs text-red-400 mt-1">{errors.estado_suscripcion}</p>}
                                </div>
                            </div>
                        </div>

                        {/* DATOS DE CONTACTO */}
                        <div className="space-y-6">
                            <h3 className="text-sm font-black text-ep-primary uppercase tracking-widest italic border-b border-white/5 pb-3">
                                Ubicación & Contacto
                            </h3>
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label htmlFor="email" className="block text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic mb-2">Email Central</label>
                                    <input 
                                        type="email" 
                                        id="email"
                                        value={data.email} 
                                        onChange={e => setData('email', e.target.value)}
                                        className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:border-ep-primary focus:ring-0 transition-all font-bold"
                                    />
                                    {errors.email && <p className="text-xs text-red-400 mt-1">{errors.email}</p>}
                                </div>
                                <div>
                                    <label htmlFor="phone" className="block text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic mb-2">Teléfono de Enlace</label>
                                    <input 
                                        type="text" 
                                        id="phone"
                                        value={data.phone} 
                                        onChange={e => setData('phone', e.target.value)}
                                        className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:border-ep-primary focus:ring-0 transition-all font-bold"
                                    />
                                    {errors.phone && <p className="text-xs text-red-400 mt-1">{errors.phone}</p>}
                                </div>
                            </div>
                            <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div className="md:col-span-1">
                                    <label htmlFor="province" className="block text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic mb-2">Provincia</label>
                                    <input 
                                        type="text" 
                                        id="province"
                                        value={data.province} 
                                        onChange={e => setData('province', e.target.value)}
                                        className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:border-ep-primary focus:ring-0 transition-all font-black uppercase italic"
                                    />
                                    {errors.province && <p className="text-xs text-red-400 mt-1">{errors.province}</p>}
                                </div>
                                <div className="md:col-span-1">
                                    <label htmlFor="locality" className="block text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic mb-2">Localidad</label>
                                    <input 
                                        type="text" 
                                        id="locality"
                                        value={data.locality} 
                                        onChange={e => setData('locality', e.target.value)}
                                        className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:border-ep-primary focus:ring-0 transition-all font-black uppercase italic"
                                    />
                                    {errors.locality && <p className="text-xs text-red-400 mt-1">{errors.locality}</p>}
                                </div>
                                <div className="md:col-span-1">
                                    <label htmlFor="fiscal_address" className="block text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic mb-2">Dirección Fiscal</label>
                                    <input 
                                        type="text" 
                                        id="fiscal_address"
                                        value={data.fiscal_address} 
                                        onChange={e => setData('fiscal_address', e.target.value)}
                                        className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:border-ep-primary focus:ring-0 transition-all font-black uppercase italic"
                                    />
                                    {errors.fiscal_address && <p className="text-xs text-red-400 mt-1">{errors.fiscal_address}</p>}
                                </div>
                            </div>
                        </div>

                        {/* BRANDING Y APARIENCIA */}
                        <div className="space-y-6">
                            <h3 className="text-sm font-black text-ep-primary uppercase tracking-widest italic border-b border-white/5 pb-3">
                                Branding & Identidad Visual
                            </h3>
                            <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label htmlFor="color_primary" className="block text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic mb-2">Color Primario</label>
                                    <div className="flex gap-2 items-center">
                                        <input 
                                            type="color" 
                                            id="color_primary"
                                            value={data.color_primary} 
                                            onChange={e => setData('color_primary', e.target.value)}
                                            className="w-12 h-12 bg-transparent border-0 cursor-pointer p-0"
                                        />
                                        <span className="text-xs font-black tracking-wider text-white/55 font-mono uppercase">{data.color_primary}</span>
                                    </div>
                                    {errors.color_primary && <p className="text-xs text-red-400 mt-1">{errors.color_primary}</p>}
                                </div>
                                <div>
                                    <label htmlFor="color_secondary" className="block text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic mb-2">Color Secundario</label>
                                    <div className="flex gap-2 items-center">
                                        <input 
                                            type="color" 
                                            id="color_secondary"
                                            value={data.color_secondary} 
                                            onChange={e => setData('color_secondary', e.target.value)}
                                            className="w-12 h-12 bg-transparent border-0 cursor-pointer p-0"
                                        />
                                        <span className="text-xs font-black tracking-wider text-white/55 font-mono uppercase">{data.color_secondary}</span>
                                    </div>
                                    {errors.color_secondary && <p className="text-xs text-red-400 mt-1">{errors.color_secondary}</p>}
                                </div>
                                <div>
                                    <label htmlFor="color_accent" className="block text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic mb-2">Color de Acento</label>
                                    <div className="flex gap-2 items-center">
                                        <input 
                                            type="color" 
                                            id="color_accent"
                                            value={data.color_accent} 
                                            onChange={e => setData('color_accent', e.target.value)}
                                            className="w-12 h-12 bg-transparent border-0 cursor-pointer p-0"
                                        />
                                        <span className="text-xs font-black tracking-wider text-white/55 font-mono uppercase">{data.color_accent}</span>
                                    </div>
                                    {errors.color_accent && <p className="text-xs text-red-400 mt-1">{errors.color_accent}</p>}
                                </div>
                            </div>
                            
                            <div>
                                <label className="block text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic mb-3">Logotipo Corporativo</label>
                                <div className="flex flex-col sm:flex-row items-center gap-6">
                                    <div className="relative bg-[#1a1a2e] p-4 rounded-2xl border border-white/10 shadow-2xl w-24 h-24 flex items-center justify-center overflow-hidden">
                                        {logoPreview ? (
                                            <img src={logoPreview} alt="Vista previa del logo" className="max-h-full max-w-full object-contain brightness-110" />
                                        ) : (
                                            <div className="w-10 h-10 bg-white/5 border border-white/10 rounded-xl flex items-center justify-center text-white/20">
                                                No logo
                                            </div>
                                        )}
                                    </div>
                                    
                                    <div className="flex-1 w-full">
                                        <input 
                                            type="file" 
                                            id="logo"
                                            accept="image/*"
                                            onChange={handleLogoChange}
                                            className="hidden"
                                        />
                                        <label 
                                            htmlFor="logo" 
                                            className="px-6 py-3 bg-white/5 border border-white/10 hover:bg-white/10 text-white text-[10px] font-black uppercase italic tracking-widest rounded-xl transition-all cursor-pointer inline-flex items-center gap-2"
                                        >
                                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2.5"><path strokeLinecap="round" strokeLinejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" /></svg>
                                            Seleccionar Imagen
                                        </label>
                                        <p className="text-[9px] font-bold text-white/20 uppercase tracking-widest italic mt-2">Soporta JPG, PNG, SVG o JPEG. Máx. 2MB.</p>
                                    </div>
                                </div>
                                {errors.logo && <p className="text-xs text-red-400 mt-1">{errors.logo}</p>}
                            </div>
                        </div>

                        {/* BOTÓN SUBMIT */}
                        <div className="flex justify-end pt-6 border-t border-white/5">
                            <button 
                                type="submit" 
                                disabled={processing}
                                className="group relative px-8 py-4 bg-ep-primary text-white text-[11px] font-black uppercase italic tracking-widest rounded-xl hover:brightness-110 active:scale-95 transition-all shadow-lg shadow-ep-primary/20 overflow-hidden inline-flex items-center gap-2"
                            >
                                <div className="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2.5"><path strokeLinecap="round" strokeLinejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                {processing ? 'Procesando...' : isEditing ? 'Guardar Cambios' : 'Registrar Consultora'}
                            </button>
                        </div>
                            </form>

                            {/* SECCIÓN GESTOR PRINCIPAL */}
                            {isEditing && (
                                <div className="bg-[#16162a]/40 backdrop-blur-xl border border-white/5 rounded-[2.5rem] p-8 sm:p-10 shadow-2xl space-y-6 mt-8">
                                    <div className="flex items-center justify-between border-b border-white/5 pb-4">
                                        <div>
                                            <h3 className="text-sm font-black text-ep-primary uppercase tracking-widest italic">
                                                Gestor Principal de Consultora
                                            </h3>
                                            <p className="text-[10px] font-bold text-white/40 uppercase italic tracking-wider mt-0.5">
                                                {has_primary_gestor ? 'Usuario Administrador de la Consultora' : 'Asignar Administrador Principal'}
                                            </p>
                                        </div>
                                        {has_primary_gestor && !isEditingPrimary && (
                                            <div className="flex items-center gap-2">
                                                <button
                                                    type="button"
                                                    onClick={() => setIsEditingPrimary(true)}
                                                    className="px-4 py-2 bg-white/5 border border-white/10 hover:bg-white/10 text-white text-[10px] font-black uppercase italic tracking-widest rounded-xl transition-all"
                                                >
                                                    Editar Datos
                                                </button>
                                                <button
                                                    type="button"
                                                    onClick={handleResetPrimaryPassword}
                                                    className="px-4 py-2 bg-amber-500/10 border border-amber-500/30 hover:bg-amber-500/20 text-amber-400 text-[10px] font-black uppercase italic tracking-widest rounded-xl transition-all flex items-center gap-1.5"
                                                >
                                                    <svg className="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2.5"><path strokeLinecap="round" strokeLinejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" /></svg>
                                                    Resetear Clave
                                                </button>
                                            </div>
                                        )}
                                    </div>

                                    {/* SI TIENE GESTOR Y NO ESTÁ EDITANDO */}
                                    {has_primary_gestor && !isEditingPrimary && primary_gestor && (
                                        <div className="bg-black/30 border border-white/5 rounded-2xl p-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                                            <div className="flex items-center gap-4">
                                                <div className="w-12 h-12 rounded-xl bg-ep-primary/20 border border-ep-primary/40 flex items-center justify-center text-ep-primary font-black text-lg">
                                                    {primary_gestor.nombres?.charAt(0)}{primary_gestor.apellidos?.charAt(0)}
                                                </div>
                                                <div>
                                                    <div className="flex items-center gap-2">
                                                        <h4 className="text-sm font-black text-white uppercase italic tracking-wider">
                                                            {primary_gestor.nombres} {primary_gestor.apellidos}
                                                        </h4>
                                                        <span className="text-[9px] font-black uppercase tracking-widest px-2 py-0.5 rounded bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                                                            Gestor Principal
                                                        </span>
                                                    </div>
                                                    <p className="text-xs text-white/60 font-mono mt-0.5">{primary_gestor.email}</p>
                                                </div>
                                            </div>

                                            <div className="flex flex-wrap items-center gap-3">
                                                {primary_gestor.must_change_password && (
                                                    <span className="text-[9px] font-black uppercase tracking-wider px-2.5 py-1 rounded-full bg-amber-500/10 text-amber-400 border border-amber-500/30 animate-pulse">
                                                        ⚠️ Pendiente cambio de contraseña
                                                    </span>
                                                )}
                                                <span className={`text-[9px] font-black uppercase tracking-wider px-2.5 py-1 rounded-full border ${
                                                    primary_gestor.estado === 'activo'
                                                        ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30'
                                                        : 'bg-red-500/10 text-red-400 border-red-500/30'
                                                }`}>
                                                    {primary_gestor.estado}
                                                </span>
                                            </div>
                                        </div>
                                    )}

                                    {/* SI NO TIENE GESTOR O ESTÁ EDITANDO */}
                                    {(!has_primary_gestor || isEditingPrimary) && (
                                        <form onSubmit={handleSavePrimaryGestor} className="space-y-4">
                                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div>
                                                    <label className="block text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic mb-1">Nombres *</label>
                                                    <input
                                                        type="text"
                                                        value={primaryForm.data.nombres}
                                                        onChange={e => primaryForm.setData('nombres', e.target.value)}
                                                        className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-2.5 text-xs text-white focus:border-ep-primary focus:ring-0 transition-all font-black uppercase italic"
                                                        required
                                                    />
                                                    {primaryForm.errors.nombres && <p className="text-xs text-red-400 mt-1">{primaryForm.errors.nombres}</p>}
                                                </div>

                                                <div>
                                                    <label className="block text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic mb-1">Apellidos *</label>
                                                    <input
                                                        type="text"
                                                        value={primaryForm.data.apellidos}
                                                        onChange={e => primaryForm.setData('apellidos', e.target.value)}
                                                        className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-2.5 text-xs text-white focus:border-ep-primary focus:ring-0 transition-all font-black uppercase italic"
                                                        required
                                                    />
                                                    {primaryForm.errors.apellidos && <p className="text-xs text-red-400 mt-1">{primaryForm.errors.apellidos}</p>}
                                                </div>

                                                <div>
                                                    <label className="block text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic mb-1">Email (Usuario) *</label>
                                                    <input
                                                        type="email"
                                                        value={primaryForm.data.email}
                                                        onChange={e => primaryForm.setData('email', e.target.value)}
                                                        className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-2.5 text-xs text-white focus:border-ep-primary focus:ring-0 transition-all font-mono"
                                                        required
                                                    />
                                                    {primaryForm.errors.email && <p className="text-xs text-red-400 mt-1">{primaryForm.errors.email}</p>}
                                                </div>

                                                <div>
                                                    <label className="block text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic mb-1">Teléfono</label>
                                                    <input
                                                        type="text"
                                                        value={primaryForm.data.telefono}
                                                        onChange={e => primaryForm.setData('telefono', e.target.value)}
                                                        className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-2.5 text-xs text-white focus:border-ep-primary focus:ring-0 transition-all font-mono"
                                                    />
                                                    {primaryForm.errors.telefono && <p className="text-xs text-red-400 mt-1">{primaryForm.errors.telefono}</p>}
                                                </div>
                                            </div>

                                            <div className="flex justify-end gap-3 pt-2">
                                                {isEditingPrimary && (
                                                    <button
                                                        type="button"
                                                        onClick={() => setIsEditingPrimary(false)}
                                                        className="px-5 py-2.5 bg-white/5 border border-white/10 text-white/60 hover:text-white text-[10px] font-black uppercase italic tracking-widest rounded-xl transition-all"
                                                    >
                                                        Cancelar
                                                    </button>
                                                )}
                                                <button
                                                    type="submit"
                                                    disabled={primaryForm.processing}
                                                    className="px-6 py-2.5 bg-ep-primary hover:brightness-110 text-white text-[10px] font-black uppercase italic tracking-widest rounded-xl shadow-lg shadow-ep-primary/20 transition-all flex items-center gap-2"
                                                >
                                                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2.5"><path strokeLinecap="round" strokeLinejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                                    {primaryForm.processing ? 'Guardando...' : has_primary_gestor ? 'Actualizar Datos' : 'Crear Gestor Principal'}
                                                </button>
                                            </div>
                                        </form>
                                    )}
                                </div>
                            )}
                        </div>

                        {/* Trazabilidad de Suscripción & Auditoría */}
                        {isEditing && (
                            <div className="bg-[#16162a]/40 backdrop-blur-xl border border-white/5 rounded-[2.5rem] p-8 sm:p-10 shadow-2xl space-y-6">
                                <div className="border-b border-white/5 pb-3">
                                    <h3 className="text-sm font-black text-ep-primary uppercase tracking-widest italic mb-4">
                                        Historial & Trazabilidad
                                    </h3>
                                    <div className="flex gap-2 p-1 bg-black/25 rounded-xl border border-white/5">
                                        <button
                                            type="button"
                                            onClick={() => setActiveTab('audit')}
                                            className={`flex-1 py-2 text-[9px] font-black uppercase italic tracking-widest rounded-lg transition-all ${
                                                activeTab === 'audit'
                                                    ? 'bg-ep-primary text-white shadow-md'
                                                    : 'text-white/40 hover:text-white/80'
                                            }`}
                                        >
                                            Auditoría
                                        </button>
                                        <button
                                            type="button"
                                            onClick={() => setActiveTab('subscriptions')}
                                            className={`flex-1 py-2 text-[9px] font-black uppercase italic tracking-widest rounded-lg transition-all ${
                                                activeTab === 'subscriptions'
                                                    ? 'bg-ep-primary text-white shadow-md'
                                                    : 'text-white/40 hover:text-white/80'
                                            }`}
                                        >
                                            Suscripciones
                                        </button>
                                    </div>
                                </div>

                                {activeTab === 'audit' && (
                                    <div className="relative border-l border-white/10 pl-6 ml-2 space-y-8 max-h-[500px] overflow-y-auto pr-2 scrollbar-thin scrollbar-thumb-white/10 scrollbar-track-transparent">
                                        {audit_logs.length === 0 ? (
                                            <p className="text-[10px] font-bold text-white/30 uppercase tracking-widest italic py-4">Sin registros de auditoría.</p>
                                        ) : (
                                            audit_logs.map((log, index) => {
                                                const isCreate = log.action === 'create';
                                                return (
                                                    <div key={log.id || index} className="relative">
                                                        <span className={`absolute -left-[31px] top-1.5 flex h-4.5 w-4.5 items-center justify-center rounded-full border ${
                                                            isCreate ? 'bg-indigo-500/10 border-indigo-500 text-indigo-400' : 'bg-amber-500/10 border-amber-500 text-amber-400'
                                                        }`}>
                                                            <span className={`h-1.5 w-1.5 rounded-full ${
                                                                isCreate ? 'bg-indigo-500' : 'bg-amber-500'
                                                            }`} />
                                                        </span>
                                                        
                                                        <div className="space-y-1.5">
                                                            <div className="flex items-center justify-between gap-2">
                                                                <span className={`text-[9px] font-black uppercase tracking-[0.2em] italic px-2 py-0.5 rounded ${
                                                                    isCreate ? 'bg-indigo-500/10 text-indigo-400 border border-indigo-500/20' : 'bg-amber-500/10 text-amber-400 border border-amber-500/20'
                                                                }`}>
                                                                    {isCreate ? 'Alta de Consultora' : 'Modificación de Accesos'}
                                                                </span>
                                                                <span className="text-[8px] font-black text-white/30 uppercase tracking-wider">
                                                                    {log.created_at_formatted}
                                                                </span>
                                                            </div>
                                                            
                                                            <div className="text-[10px] font-bold text-white/60 italic">
                                                                {log.user ? (
                                                                    <span>
                                                                        Por: {log.user.name} <span className="text-[8px] uppercase tracking-wider text-indigo-300">({log.user.rol === 'super_admin' ? 'Súper Admin' : 'Vendedor'})</span>
                                                                    </span>
                                                                ) : (
                                                                    <span>Por: Sistema</span>
                                                                )}
                                                            </div>

                                                            {/* Render diff details */}
                                                            <div className="bg-black/30 border border-white/5 rounded-xl p-3 space-y-2 mt-2">
                                                                {log.payload && Object.keys(log.payload).length > 0 ? (
                                                                    Object.entries(log.payload).map(([field, diff]) => {
                                                                        const fieldLabels = {
                                                                            estado_suscripcion: 'Estado Suscripción',
                                                                            max_tecnicos: 'Límite Técnicos',
                                                                            max_gestores: 'Límite Gestores',
                                                                            max_empresas: 'Límite Empresas'
                                                                        };
                                                                        const label = fieldLabels[field] || field;

                                                                        const formatVal = (val) => {
                                                                            if (val === 'activa') return 'Activa';
                                                                            if (val === 'morosa') return 'Morosa';
                                                                            if (val === 'baja') return 'Baja';
                                                                            return val;
                                                                        };

                                                                        return (
                                                                            <div key={field} className="flex flex-col gap-0.5 text-[9px] border-b border-white/5 last:border-b-0 pb-1.5 last:pb-0">
                                                                                <span className="text-white/40 font-bold uppercase tracking-wider text-[8px]">{label}</span>
                                                                                <div className="flex items-center font-mono">
                                                                                    {isCreate ? (
                                                                                        <span className="text-emerald-400 font-black">{formatVal(diff.new)}</span>
                                                                                    ) : (
                                                                                        <div className="flex items-center gap-1.5">
                                                                                            <span className="text-red-400/60 line-through">{formatVal(diff.old)}</span>
                                                                                            <span className="text-white/30">➔</span>
                                                                                            <span className="text-emerald-400 font-black">{formatVal(diff.new)}</span>
                                                                                        </div>
                                                                                    )}
                                                                                </div>
                                                                            </div>
                                                                        );
                                                                    })
                                                                ) : (
                                                                    <span className="text-white/30 italic text-[9px]">Sin cambios en accesos.</span>
                                                                )}
                                                            </div>
                                                        </div>
                                                    </div>
                                                );
                                            })
                                        )}
                                    </div>
                                )}

                                {activeTab === 'subscriptions' && (
                                    <div className="relative border-l border-white/10 pl-6 ml-2 space-y-8 max-h-[500px] overflow-y-auto pr-2 scrollbar-thin scrollbar-thumb-white/10 scrollbar-track-transparent">
                                        {suscripcion_logs.map((log, index) => {
                                            const isActiva = log.estado_nuevo === 'activa';
                                            const isMorosa = log.estado_nuevo === 'morosa';
                                            const isBaja = log.estado_nuevo === 'baja';
                                            
                                            return (
                                                <div key={log.id || index} className="relative">
                                                    <span className={`absolute -left-[31px] top-1.5 flex h-4.5 w-4.5 items-center justify-center rounded-full border ${
                                                        isActiva ? 'bg-emerald-500/10 border-emerald-500 text-emerald-400' :
                                                        isMorosa ? 'bg-amber-500/10 border-amber-500 text-amber-400 animate-pulse' :
                                                        'bg-red-500/10 border-red-500 text-red-400'
                                                    }`}>
                                                        <span className={`h-1.5 w-1.5 rounded-full ${
                                                            isActiva ? 'bg-emerald-500' :
                                                            isMorosa ? 'bg-amber-500' :
                                                            'bg-red-500'
                                                        }`} />
                                                    </span>
                                                    
                                                    <div className="space-y-1.5">
                                                        <div className="flex items-center justify-between gap-2">
                                                            <span className={`text-[9px] font-black uppercase tracking-[0.2em] italic px-2 py-0.5 rounded ${
                                                                isActiva ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' :
                                                                isMorosa ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' :
                                                                'bg-red-500/10 text-red-400 border border-red-500/20'
                                                            }`}>
                                                                {isActiva ? 'Suscripción Activa' :
                                                                 isMorosa ? 'Estado Moroso' :
                                                                 'Baja del Servicio'}
                                                            </span>
                                                            {log.is_current && (
                                                                <span className="bg-indigo-500/15 border border-indigo-500/30 text-indigo-300 text-[7px] font-black uppercase tracking-[0.2em] italic px-2 py-0.5 rounded-full">
                                                                    Actual
                                                                </span>
                                                            )}
                                                        </div>
                                                        
                                                        <div className="text-[10px] font-bold text-white/50 italic leading-snug">
                                                            {log.duration && (
                                                                <span className="text-white font-black block not-italic">
                                                                    {log.is_current ? 'Activo hace: ' : 'Duración: '}
                                                                    <span className="text-indigo-300 uppercase tracking-wide text-[9px]">{log.duration}</span>
                                                                </span>
                                                            )}
                                                            <div className="mt-1">Iniciado: {log.created_at_formatted}</div>
                                                            {log.user && (
                                                                <div className="text-white/30 text-[8px] uppercase tracking-wider mt-0.5">
                                                                    Por: {log.user.name} ({log.user.email})
                                                                </div>
                                                            )}
                                                        </div>
                                                    </div>
                                                </div>
                                            );
                                        })}
                                    </div>
                                )}
                            </div>
                        )}
                    </div>
                </div>
            </div>
            {/* MODAL CONTRASEÑA GENERADA (UNA SOLA VEZ) */}
            {generatedPassword && (
                <div className="fixed inset-0 z-50 bg-black/80 backdrop-blur-md flex items-center justify-center p-4 animate-fade-in">
                    <div className="bg-[#16162a] border border-amber-500/40 rounded-3xl p-8 max-w-md w-full shadow-2xl space-y-6">
                        <div className="flex items-center gap-3 text-amber-400 border-b border-white/10 pb-4">
                            <div className="w-10 h-10 rounded-xl bg-amber-500/20 border border-amber-500/30 flex items-center justify-center">
                                <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2"><path strokeLinecap="round" strokeLinejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" /></svg>
                            </div>
                            <div>
                                <h3 className="text-base font-black uppercase italic tracking-tight text-white">
                                    Contraseña Temporal Generada
                                </h3>
                                <p className="text-[10px] font-bold text-amber-400 uppercase tracking-widest italic">
                                    Un solo uso · Cambio obligatorio
                                </p>
                            </div>
                        </div>

                        <p className="text-xs text-gray-300 leading-relaxed">
                            Se ha asignado la siguiente clave temporal al Gestor Principal. Proporciónasela para que pueda ingresar por primera vez:
                        </p>

                        <div className="bg-black/60 border border-amber-500/30 rounded-2xl p-4 flex items-center justify-between gap-3">
                            <span className="font-mono text-lg font-black text-amber-400 tracking-wider select-all">
                                {generatedPassword}
                            </span>
                            <button
                                type="button"
                                onClick={() => copyToClipboard(generatedPassword)}
                                className="px-4 py-2 bg-amber-500 text-black hover:bg-amber-400 text-[10px] font-black uppercase italic tracking-widest rounded-xl transition-all flex items-center gap-1.5 shadow-md"
                            >
                                {copied ? '¡Copiada!' : 'Copiar'}
                            </button>
                        </div>

                        <div className="bg-red-500/10 border border-red-500/20 rounded-xl p-3 text-[10px] font-bold text-red-400 leading-normal flex items-start gap-2">
                            <span className="text-sm">⚠️</span>
                            <span>
                                <strong>IMPORTANTE:</strong> Esta contraseña NO se volverá a mostrar en pantalla por motivos de seguridad. Guárdala antes de cerrar.
                            </span>
                        </div>

                        <div className="pt-2">
                            <button
                                type="button"
                                onClick={() => setDismissedPassword(true)}
                                className="w-full py-3 bg-white/10 hover:bg-white/20 text-white text-[11px] font-black uppercase italic tracking-widest rounded-xl transition-all"
                            >
                                Entendido, ya la copié
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </AuthenticatedLayout>
    );
}
