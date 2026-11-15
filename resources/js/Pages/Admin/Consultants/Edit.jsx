import React, { useState, useEffect } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, useForm, usePage, router } from '@inertiajs/react';
import PrimaryGestorCard from './Partials/PrimaryGestorCard';
import ConsultantHistorySidebar from './Partials/ConsultantHistorySidebar';
import GeneratedPasswordModal from './Partials/GeneratedPasswordModal';

export default function Edit({ auth, consultant = null, primary_gestor = null, has_primary_gestor = false, suscripcion_logs = [], audit_logs = [] }) {
    const { flash } = usePage().props;
    const isEditing = !!consultant;
    const [activeTab, setActiveTab] = useState('audit');
    const [isEditingPrimary, setIsEditingPrimary] = useState(false);
    const [copied, setCopied] = useState(false);
    const [dismissedPassword, setDismissedPassword] = useState(false);

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
            onSuccess: () => setIsEditingPrimary(false),
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
        post(isEditing ? route('admin.consultants.update', consultant.id) : route('admin.consultants.store'));
    };

    return (
        <AuthenticatedLayout
            user={auth.user}
            title="Súper Administración"
            header={
                <div className="flex flex-col">
                    <h1 className="text-xs font-black text-ep-primary uppercase tracking-[0.4em] italic leading-none mb-2">System Setup</h1>
                    <h2 className="text-xl font-black text-white uppercase italic tracking-tighter leading-none">
                        {isEditing ? 'Configurar' : 'Registrar'} <span className="text-indigo-400">Consultora</span>
                    </h2>
                </div>
            }
        >
            <Head title={isEditing ? 'Editar Consultora' : 'Nueva Consultora'} />
            <div className="py-12 bg-[#0f0f1a] min-h-screen text-white selection:bg-ep-primary/30 animate-fade-in">
                <div className={`${isEditing ? 'max-w-7xl' : 'max-w-4xl'} mx-auto sm:px-6 lg:px-8`}>
                    <div className="mb-6">
                        <Link href={route('admin.consultants.index')} className="inline-flex items-center gap-2 text-xs font-black text-white/40 hover:text-white uppercase italic tracking-widest transition-colors">
                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2.5"><path strokeLinecap="round" strokeLinejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" /></svg>
                            Volver al Listado
                        </Link>
                    </div>

                    <div className={isEditing ? 'grid grid-cols-1 lg:grid-cols-3 gap-8 items-start' : ''}>
                        <div className={isEditing ? 'lg:col-span-2' : ''}>
                            <form onSubmit={submit} className="bg-[#16162a]/40 backdrop-blur-xl border border-white/5 rounded-[2.5rem] p-8 sm:p-10 shadow-2xl space-y-8">
                                <div className="space-y-6">
                                    <h3 className="text-sm font-black text-ep-primary uppercase tracking-widest italic border-b border-white/5 pb-3">Identidad Corporativa</h3>
                                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label htmlFor="nombre" className="block text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic mb-2">Nombre Consultora *</label>
                                            <input type="text" id="nombre" value={data.nombre} onChange={e => setData('nombre', e.target.value)} className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:border-ep-primary focus:ring-0 transition-all font-black uppercase italic" required />
                                            {errors.nombre && <p className="text-xs text-red-400 mt-1">{errors.nombre}</p>}
                                        </div>
                                        <div>
                                            <label htmlFor="cuit" className="block text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic mb-2">CUIT *</label>
                                            <input type="text" id="cuit" value={data.cuit} onChange={e => setData('cuit', e.target.value)} placeholder="XX-XXXXXXXX-X" className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:border-ep-primary focus:ring-0 transition-all font-black italic tracking-wider" required />
                                            {errors.cuit && <p className="text-xs text-red-400 mt-1">{errors.cuit}</p>}
                                        </div>
                                    </div>
                                    <div>
                                        <label htmlFor="description" className="block text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic mb-2">Descripción General</label>
                                        <textarea id="description" value={data.description} onChange={e => setData('description', e.target.value)} rows="3" className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:border-ep-primary focus:ring-0 transition-all font-bold" />
                                        {errors.description && <p className="text-xs text-red-400 mt-1">{errors.description}</p>}
                                    </div>
                                </div>

                                <div className="space-y-6">
                                    <h3 className="text-sm font-black text-ep-primary uppercase tracking-widest italic border-b border-white/5 pb-3">Infraestructura & Licencias</h3>
                                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        {[
                                            { id: 'max_tecnicos', label: 'Límite de Licencias de Técnicos (Escuadrón) *' },
                                            { id: 'max_gestores', label: 'Límite de Licencias de Gestores *' },
                                            { id: 'max_empresas', label: 'Límite de Empresas Clientes *' },
                                        ].map(f => (
                                            <div key={f.id}>
                                                <label htmlFor={f.id} className="block text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic mb-2">{f.label}</label>
                                                <input type="number" id={f.id} value={data[f.id]} onChange={e => setData(f.id, parseInt(e.target.value) || 0)} min="1" className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:border-ep-primary focus:ring-0 transition-all font-black italic" required />
                                                {errors[f.id] && <p className="text-xs text-red-400 mt-1">{errors[f.id]}</p>}
                                            </div>
                                        ))}
                                        <div>
                                            <label htmlFor="estado_suscripcion" className="block text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic mb-2">Estado de Suscripción *</label>
                                            <select id="estado_suscripcion" value={data.estado_suscripcion} onChange={e => setData('estado_suscripcion', e.target.value)} className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:border-ep-primary focus:ring-0 transition-all font-black uppercase italic">
                                                <option value="activa" className="bg-[#16162a]">Suscripción Activa</option>
                                                <option value="morosa" className="bg-[#16162a]">Morosa (Falta de pago)</option>
                                                <option value="baja" className="bg-[#16162a]">Baja del Servicio</option>
                                            </select>
                                            {errors.estado_suscripcion && <p className="text-xs text-red-400 mt-1">{errors.estado_suscripcion}</p>}
                                        </div>
                                    </div>
                                </div>

                                <div className="space-y-6">
                                    <h3 className="text-sm font-black text-ep-primary uppercase tracking-widest italic border-b border-white/5 pb-3">Ubicación & Contacto</h3>
                                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label htmlFor="email" className="block text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic mb-2">Email Central</label>
                                            <input type="email" id="email" value={data.email} onChange={e => setData('email', e.target.value)} className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:border-ep-primary focus:ring-0 transition-all font-bold" />
                                            {errors.email && <p className="text-xs text-red-400 mt-1">{errors.email}</p>}
                                        </div>
                                        <div>
                                            <label htmlFor="phone" className="block text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic mb-2">Teléfono de Enlace</label>
                                            <input type="text" id="phone" value={data.phone} onChange={e => setData('phone', e.target.value)} className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:border-ep-primary focus:ring-0 transition-all font-bold" />
                                            {errors.phone && <p className="text-xs text-red-400 mt-1">{errors.phone}</p>}
                                        </div>
                                    </div>
                                    <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                                        {[{ id: 'province', label: 'Provincia' }, { id: 'locality', label: 'Localidad' }, { id: 'fiscal_address', label: 'Dirección Fiscal' }].map(f => (
                                            <div key={f.id}>
                                                <label htmlFor={f.id} className="block text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic mb-2">{f.label}</label>
                                                <input type="text" id={f.id} value={data[f.id]} onChange={e => setData(f.id, e.target.value)} className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:border-ep-primary focus:ring-0 transition-all font-black uppercase italic" />
                                                {errors[f.id] && <p className="text-xs text-red-400 mt-1">{errors[f.id]}</p>}
                                            </div>
                                        ))}
                                    </div>
                                </div>

                                <div className="space-y-6">
                                    <h3 className="text-sm font-black text-ep-primary uppercase tracking-widest italic border-b border-white/5 pb-3">Branding & Identidad Visual</h3>
                                    <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                                        {[{ id: 'color_primary', label: 'Color Primario' }, { id: 'color_secondary', label: 'Color Secundario' }, { id: 'color_accent', label: 'Color de Acento' }].map(c => (
                                            <div key={c.id}>
                                                <label htmlFor={c.id} className="block text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic mb-2">{c.label}</label>
                                                <div className="flex gap-2 items-center">
                                                    <input type="color" id={c.id} value={data[c.id]} onChange={e => setData(c.id, e.target.value)} className="w-12 h-12 bg-transparent border-0 cursor-pointer p-0" />
                                                    <span className="text-xs font-black tracking-wider text-white/55 font-mono uppercase">{data[c.id]}</span>
                                                </div>
                                                {errors[c.id] && <p className="text-xs text-red-400 mt-1">{errors[c.id]}</p>}
                                            </div>
                                        ))}
                                    </div>
                                    <div>
                                        <label className="block text-[10px] font-black text-indigo-100/40 uppercase tracking-[0.2em] italic mb-3">Logotipo Corporativo</label>
                                        <div className="flex flex-col sm:flex-row items-center gap-6">
                                            <div className="relative bg-[#1a1a2e] p-4 rounded-2xl border border-white/10 shadow-2xl w-24 h-24 flex items-center justify-center overflow-hidden">
                                                {logoPreview ? <img src={logoPreview} alt="Vista previa del logo" className="max-h-full max-w-full object-contain brightness-110" /> : <div className="w-10 h-10 bg-white/5 border border-white/10 rounded-xl flex items-center justify-center text-white/20">No logo</div>}
                                            </div>
                                            <div className="flex-1 w-full">
                                                <input type="file" id="logo" accept="image/*" onChange={handleLogoChange} className="hidden" />
                                                <label htmlFor="logo" className="px-6 py-3 bg-white/5 border border-white/10 hover:bg-white/10 text-white text-[10px] font-black uppercase italic tracking-widest rounded-xl transition-all cursor-pointer inline-flex items-center gap-2">
                                                    Seleccionar Imagen
                                                </label>
                                                <p className="text-[9px] font-bold text-white/20 uppercase tracking-widest italic mt-2">Soporta JPG, PNG, SVG o JPEG. Máx. 2MB.</p>
                                            </div>
                                        </div>
                                        {errors.logo && <p className="text-xs text-red-400 mt-1">{errors.logo}</p>}
                                    </div>
                                </div>

                                <div className="flex justify-end pt-6 border-t border-white/5">
                                    <button type="submit" disabled={processing} className="px-8 py-4 bg-ep-primary text-white text-[11px] font-black uppercase italic tracking-widest rounded-xl hover:brightness-110 active:scale-95 transition-all shadow-lg shadow-ep-primary/20 inline-flex items-center gap-2">
                                        {processing ? 'Procesando...' : isEditing ? 'Guardar Cambios' : 'Registrar Consultora'}
                                    </button>
                                </div>
                            </form>

                            {isEditing && (
                                <PrimaryGestorCard
                                    hasPrimaryGestor={has_primary_gestor}
                                    primaryGestor={primary_gestor}
                                    isEditingPrimary={isEditingPrimary}
                                    setIsEditingPrimary={setIsEditingPrimary}
                                    primaryForm={primaryForm}
                                    onSavePrimaryGestor={handleSavePrimaryGestor}
                                    onResetPassword={handleResetPrimaryPassword}
                                />
                            )}
                        </div>

                        {isEditing && (
                            <ConsultantHistorySidebar
                                activeTab={activeTab}
                                setActiveTab={setActiveTab}
                                auditLogs={audit_logs}
                                suscripcionLogs={suscripcion_logs}
                            />
                        )}
                    </div>
                </div>
            </div>

            <GeneratedPasswordModal
                password={generatedPassword}
                copied={copied}
                onCopy={copyToClipboard}
                onDismiss={() => setDismissedPassword(true)}
            />
        </AuthenticatedLayout>
    );
}
