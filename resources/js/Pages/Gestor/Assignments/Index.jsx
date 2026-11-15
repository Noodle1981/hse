import React, { useMemo } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, router, useForm } from '@inertiajs/react';

export default function AssignmentsIndex({ assignments, empresas = [], checklists = [], tecnicos = [], filters = {} }) {
    const { data, setData, post, processing, reset, errors } = useForm({
        empresa_id: '',
        sede_id: '',
        dynamic_checklist_id: '',
        user_id: '',
        due_date: new Date().toISOString().slice(0, 10),
        frequency: 'once',
        notes: '',
    });

    const availableSedes = useMemo(() => {
        const company = empresas.find((e) => String(e.id) === String(data.empresa_id));
        return company?.sedes || [];
    }, [empresas, data.empresa_id]);

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route('gestor.assignments.store'), {
            onSuccess: () => reset('dynamic_checklist_id', 'notes'),
        });
    };

    const handleDelete = (id) => {
        if (confirm('¿Eliminar esta asignación?')) {
            router.delete(route('gestor.assignments.destroy', id));
        }
    };

    return (
        <AuthenticatedLayout>
            <Head title="Centro de Asignaciones" />
            <div className="max-w-7xl mx-auto py-6 px-4 sm:px-6 space-y-6">
                <div>
                    <h1 className="text-2xl font-bold text-slate-900 dark:text-white">
                        Centro de Despacho de Asignaciones
                    </h1>
                    <p className="text-sm text-slate-500">
                        Asigna checklists operativos a sedes y técnicos con fecha objetivo.
                    </p>
                </div>

                <form
                    onSubmit={handleSubmit}
                    className="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-5 grid grid-cols-1 md:grid-cols-3 gap-4"
                >
                    <div>
                        <label className="block text-xs font-semibold uppercase text-slate-500 mb-1">Empresa</label>
                        <select
                            value={data.empresa_id}
                            onChange={(e) => setData({ ...data, empresa_id: e.target.value, sede_id: '' })}
                            className="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-950 text-sm"
                            required
                        >
                            <option value="">Seleccionar empresa...</option>
                            {empresas.map((emp) => (
                                <option key={emp.id} value={emp.id}>{emp.nombre}</option>
                            ))}
                        </select>
                    </div>

                    <div>
                        <label className="block text-xs font-semibold uppercase text-slate-500 mb-1">Sede</label>
                        <select
                            value={data.sede_id}
                            onChange={(e) => setData('sede_id', e.target.value)}
                            className="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-950 text-sm"
                            required
                        >
                            <option value="">Seleccionar sede...</option>
                            {availableSedes.map((s) => (
                                <option key={s.id} value={s.id}>{s.nombre_sede}</option>
                            ))}
                        </select>
                        {errors.sede_id && <p className="text-xs text-red-500 mt-1">{errors.sede_id}</p>}
                    </div>

                    <div>
                        <label className="block text-xs font-semibold uppercase text-slate-500 mb-1">Checklist</label>
                        <select
                            value={data.dynamic_checklist_id}
                            onChange={(e) => setData('dynamic_checklist_id', e.target.value)}
                            className="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-950 text-sm"
                            required
                        >
                            <option value="">Seleccionar checklist...</option>
                            {checklists.map((c) => (
                                <option key={c.id} value={c.id}>{c.registry_code ? `[${c.registry_code}] ` : ''}{c.title}</option>
                            ))}
                        </select>
                    </div>

                    <div>
                        <label className="block text-xs font-semibold uppercase text-slate-500 mb-1">Técnico (Opcional)</label>
                        <select
                            value={data.user_id}
                            onChange={(e) => setData('user_id', e.target.value)}
                            className="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-950 text-sm"
                        >
                            <option value="">Abierta (cualquier técnico de la sede)</option>
                            {tecnicos.map((t) => (
                                <option key={t.id} value={t.id}>{t.apellidos}, {t.nombres}</option>
                            ))}
                        </select>
                    </div>

                    <div>
                        <label className="block text-xs font-semibold uppercase text-slate-500 mb-1">Fecha Vencimiento</label>
                        <input
                            type="date"
                            value={data.due_date}
                            onChange={(e) => setData('due_date', e.target.value)}
                            className="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-950 text-sm"
                            required
                        />
                    </div>

                    <div className="flex items-end">
                        <button
                            type="submit"
                            disabled={processing}
                            className="w-full py-2.5 px-4 rounded-xl bg-orange-600 hover:bg-orange-500 text-white font-semibold text-sm disabled:opacity-50"
                        >
                            {processing ? 'Despachando...' : 'Despachar Asignación'}
                        </button>
                    </div>
                </form>

                <div className="hidden lg:block bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl overflow-hidden">
                    <table className="w-full text-sm text-left">
                        <thead className="bg-slate-50 dark:bg-slate-800/60 text-xs uppercase text-slate-500">
                            <tr>
                                <th className="px-4 py-3">Checklist</th>
                                <th className="px-4 py-3">Empresa / Sede</th>
                                <th className="px-4 py-3">Técnico</th>
                                <th className="px-4 py-3">Vence</th>
                                <th className="px-4 py-3">Estado</th>
                                <th className="px-4 py-3 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-slate-200 dark:divide-slate-800">
                            {(assignments?.data || []).map((item) => (
                                <tr key={item.id}>
                                    <td className="px-4 py-3 font-medium">{item.checklist?.title}</td>
                                    <td className="px-4 py-3 text-slate-500">{item.sede?.empresa?.nombre} — {item.sede?.nombre_sede}</td>
                                    <td className="px-4 py-3">{item.user ? `${item.user.nombres} ${item.user.apellidos}` : 'Sin asignar'}</td>
                                    <td className="px-4 py-3">{item.due_date?.slice(0, 10)}</td>
                                    <td className="px-4 py-3 uppercase text-xs font-bold">{item.status}</td>
                                    <td className="px-4 py-3 text-right">
                                        <button onClick={() => handleDelete(item.id)} className="text-red-500 hover:underline text-xs">
                                            Eliminar
                                        </button>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>

                <div className="lg:hidden space-y-3">
                    {(assignments?.data || []).map((item) => (
                        <div key={item.id} className="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-4 space-y-1">
                            <div className="font-semibold text-sm">{item.checklist?.title}</div>
                            <div className="text-xs text-slate-500">{item.sede?.empresa?.nombre} — {item.sede?.nombre_sede}</div>
                            <div className="flex justify-between items-center pt-2 text-xs">
                                <span className="font-bold uppercase">{item.status}</span>
                                <button onClick={() => handleDelete(item.id)} className="text-red-500">Eliminar</button>
                            </div>
                        </div>
                    ))}
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
