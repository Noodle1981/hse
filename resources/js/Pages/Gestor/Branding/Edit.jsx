import React from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, useForm, usePage } from '@inertiajs/react';

export default function BrandingEdit({ consultora }) {
    const { flash } = usePage().props;

    const { data, setData, post, processing, errors } = useForm({
        email: consultora?.email || '',
        phone: consultora?.phone || '',
        description: consultora?.description || '',
        color_primary: consultora?.color_primary || '#ea580c',
        color_secondary: consultora?.color_secondary || '#334155',
        color_accent: consultora?.color_accent || '#10b981',
        logo: null,
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route('gestor.administration.branding.update'), {
            forceFormData: true,
        });
    };

    return (
        <AuthenticatedLayout>
            <Head title="Branding de Consultora" />
            <div className="max-w-3xl mx-auto py-8 px-4 space-y-6">
                <div>
                    <h1 className="text-2xl font-bold text-slate-900 dark:text-white">
                        Identidad Visual — {consultora?.nombre}
                    </h1>
                    <p className="text-sm text-slate-500">
                        Personaliza los colores institucionales y logotipo reflejados en reportes PDF y constancias.
                    </p>
                </div>

                {flash?.message && (
                    <div className="p-3 rounded-xl bg-emerald-500/15 border border-emerald-500/40 text-emerald-700 dark:text-emerald-300 text-sm">
                        {flash.message}
                    </div>
                )}

                <form
                    onSubmit={handleSubmit}
                    className="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 space-y-5"
                >
                    <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        {[
                            { key: 'color_primary', label: 'Color Primario' },
                            { key: 'color_secondary', label: 'Color Secundario' },
                            { key: 'color_accent', label: 'Color Acento' },
                        ].map(({ key, label }) => (
                            <div key={key}>
                                <label className="block text-xs font-semibold uppercase text-slate-500 mb-1">{label}</label>
                                <div className="flex items-center gap-2">
                                    <input
                                        type="color"
                                        value={data[key]}
                                        onChange={(e) => setData(key, e.target.value)}
                                        className="h-10 w-14 rounded border border-slate-300 dark:border-slate-700"
                                    />
                                    <input
                                        type="text"
                                        value={data[key]}
                                        onChange={(e) => setData(key, e.target.value)}
                                        className="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-950 text-sm font-mono"
                                    />
                                </div>
                                {errors[key] && <p className="text-xs text-red-500 mt-1">{errors[key]}</p>}
                            </div>
                        ))}
                    </div>

                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label className="block text-xs font-semibold uppercase text-slate-500 mb-1">Email Institucional</label>
                            <input
                                type="email"
                                value={data.email}
                                onChange={(e) => setData('email', e.target.value)}
                                className="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-950 text-sm"
                            />
                        </div>
                        <div>
                            <label className="block text-xs font-semibold uppercase text-slate-500 mb-1">Teléfono</label>
                            <input
                                type="text"
                                value={data.phone}
                                onChange={(e) => setData('phone', e.target.value)}
                                className="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-950 text-sm"
                            />
                        </div>
                    </div>

                    <div>
                        <label className="block text-xs font-semibold uppercase text-slate-500 mb-1">Descripción / Pie de Reportes</label>
                        <textarea
                            rows={3}
                            value={data.description}
                            onChange={(e) => setData('description', e.target.value)}
                            className="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-950 text-sm"
                        />
                    </div>

                    <div>
                        <label className="block text-xs font-semibold uppercase text-slate-500 mb-1">Logotipo (PNG/JPG/SVG)</label>
                        {consultora?.logo_url && (
                            <img src={consultora.logo_url} alt="Logo actual" className="h-14 mb-2 rounded border border-slate-200 p-1 bg-white" />
                        )}
                        <input
                            type="file"
                            accept="image/png,image/jpeg,image/svg+xml"
                            onChange={(e) => setData('logo', e.target.files?.[0] || null)}
                            className="text-sm"
                        />
                        {errors.logo && <p className="text-xs text-red-500 mt-1">{errors.logo}</p>}
                    </div>

                    <button
                        type="submit"
                        disabled={processing}
                        className="px-6 py-2.5 rounded-xl bg-orange-600 hover:bg-orange-500 text-white font-semibold text-sm disabled:opacity-50"
                    >
                        {processing ? 'Guardando...' : 'Guardar Identidad Visual'}
                    </button>
                </form>
            </div>
        </AuthenticatedLayout>
    );
}
