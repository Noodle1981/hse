import React from 'react';

export default function MetadataFieldEditor({
    field,
    index,
    metadataSchema,
    checklists,
    onUpdateSchema,
    onUpdateField,
    onRemoveField,
}) {
    const typeColor = field.type === 'date'
        ? (field.alert_days ? 'text-amber-400 bg-amber-500/10 border-amber-500/20' : 'text-indigo-400 bg-indigo-500/10 border-indigo-500/20')
        : field.type === 'number'
            ? 'text-cyan-400 bg-cyan-500/10 border-cyan-500/20'
            : 'text-white/50 bg-white/5 border-white/10';

    const hasSreRules = field.maintenance_rules && field.maintenance_rules.length > 0;

    const updateRule = (ruleIndex, key, value) => {
        const updatedSchema = [...metadataSchema];
        const rules = [...(field.maintenance_rules || [])];
        rules[ruleIndex] = { ...rules[ruleIndex], [key]: value };
        updatedSchema[index] = { ...field, maintenance_rules: rules };
        onUpdateSchema(updatedSchema);
    };

    const addRule = () => {
        const updatedSchema = [...metadataSchema];
        updatedSchema[index] = {
            ...field,
            maintenance_rules: [...(field.maintenance_rules || []), { nombre: '', frecuencia: '', margen_pre_alerta: '', checklist_resolucion_id: '' }],
        };
        onUpdateSchema(updatedSchema);
    };

    const removeRule = (ruleIndex) => {
        const updatedSchema = [...metadataSchema];
        const rules = [...(field.maintenance_rules || [])];
        rules.splice(ruleIndex, 1);
        updatedSchema[index] = { ...field, maintenance_rules: rules };
        onUpdateSchema(updatedSchema);
    };

    return (
        <div className={`rounded-[1.5rem] border overflow-hidden transition-all ${field._imported ? 'bg-indigo-500/5 border-indigo-500/20' : 'bg-black/30 border-white/10'}`}>
            <div className="flex items-center gap-4 px-6 py-4">
                <div className="w-8 h-8 rounded-xl bg-orange-500/20 flex items-center justify-center text-[11px] font-black text-orange-400 italic border border-orange-500/20 flex-shrink-0">
                    {index + 1}
                </div>

                <div className="flex-1">
                    {field._imported ? (
                        <div>
                            <div className="text-sm font-black text-white/95 italic uppercase select-none">{field.label}</div>
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
                                onChange={e => onUpdateField(index, 'label', e.target.value)}
                                placeholder="Nombre del atributo manual..."
                                className="w-full bg-transparent border-none text-sm font-black text-white italic uppercase focus:ring-0 p-0 placeholder-white/20"
                                required
                            />
                            <div className="flex items-center gap-2 mt-1">
                                <span className="text-[8px] text-white/30 font-mono uppercase">ID:</span>
                                <input
                                    type="text"
                                    value={field.name}
                                    onChange={e => onUpdateField(index, 'name', e.target.value)}
                                    placeholder="clave_interna"
                                    className="bg-transparent border-none text-[9px] font-mono text-orange-400/60 focus:ring-0 p-0 placeholder-white/20 w-40"
                                    required
                                />
                            </div>
                        </div>
                    )}
                </div>

                <select
                    value={field.type === 'date' ? (field.alert_days ? 'date_alert' : 'date_info') : (field.type || 'text')}
                    onChange={e => {
                        const val = e.target.value;
                        const updated = [...metadataSchema];
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
                        onUpdateSchema(updated);
                    }}
                    className={`border rounded-xl px-3 py-1.5 text-[9px] font-black uppercase tracking-wider focus:outline-none cursor-pointer [color-scheme:dark] ${typeColor}`}
                >
                    <option value="text" className="bg-[#16162a] text-white">📝 Texto</option>
                    <option value="number" className="bg-[#16162a] text-white">🔢 Número</option>
                    <option value="date_alert" className="bg-[#16162a] text-white">🔔 Fecha Venc.</option>
                    <option value="date_info" className="bg-[#16162a] text-white">🗓️ Fecha Info</option>
                </select>

                <div className="flex items-center gap-1 flex-shrink-0">
                    <select
                        value={field.role || 'verificacion'}
                        onChange={e => {
                            const newRole = e.target.value;
                            const updated = [...metadataSchema];
                            updated[index].role = newRole;
                            if (newRole === 'identificador') updated[index].show_in_create = true;
                            onUpdateSchema(updated);
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

                <label className="flex items-center gap-1.5 cursor-pointer select-none flex-shrink-0">
                    <input
                        type="checkbox"
                        checked={!!field.show_in_create}
                        onChange={e => onUpdateField(index, 'show_in_create', e.target.checked)}
                        className="rounded border-white/20 bg-black/50 text-blue-500 focus:ring-blue-500 h-3.5 w-3.5"
                    />
                    <span className={`text-[9px] font-black uppercase tracking-wider ${field.show_in_create ? 'text-blue-400' : 'text-white/30'}`}>📌 Alta</span>
                </label>

                {!field._imported && (
                    <button
                        type="button"
                        onClick={() => onRemoveField(index)}
                        className="h-7 w-7 bg-red-500/10 hover:bg-red-500 text-red-400 hover:text-white border border-red-500/20 rounded-lg flex items-center justify-center transition-all flex-shrink-0"
                        title="Quitar atributo"
                    >
                        <svg className="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" strokeWidth="2.5" strokeLinecap="round" /></svg>
                    </button>
                )}
            </div>

            {(field.alert_days || field.type === 'number') && (
                <div className="px-6 pb-4 flex flex-wrap items-center gap-3 border-t border-white/5 pt-3">
                    {field.type === 'date' && field.alert_days && (
                        <div className="flex items-center gap-2">
                            <span className="text-[9px] font-black text-amber-400 uppercase tracking-wider">Alerta a los</span>
                            <input
                                type="number"
                                value={field.alert_days || ''}
                                onChange={e => onUpdateField(index, 'alert_days', e.target.value)}
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
                                onChange={e => onUpdateField(index, 'alert_threshold', e.target.value)}
                                placeholder="Ej: 150000"
                                className="w-28 bg-black/50 border border-cyan-500/20 rounded-lg px-2 py-1 text-xs text-cyan-300 font-mono focus:outline-none focus:border-cyan-500"
                            />
                            <span className="text-[9px] text-white/30">/ Frecuencia recurrente:</span>
                            <input
                                type="number"
                                step="any"
                                value={field.alert_interval || ''}
                                onChange={e => onUpdateField(index, 'alert_interval', e.target.value)}
                                placeholder="Ej: 15000"
                                className="w-28 bg-black/50 border border-indigo-500/20 rounded-lg px-2 py-1 text-xs text-indigo-300 font-mono focus:outline-none focus:border-indigo-500"
                            />
                        </div>
                    )}
                    <input
                        type="text"
                        value={field.alert_message || ''}
                        onChange={e => onUpdateField(index, 'alert_message', e.target.value)}
                        placeholder="💬 Mensaje de alerta personalizado (opcional)..."
                        className="flex-1 min-w-48 bg-black/40 border border-amber-500/10 rounded-lg px-3 py-1.5 text-[11px] text-amber-200 placeholder-white/20 focus:outline-none focus:border-amber-500/40 italic"
                    />
                </div>
            )}

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
                            onClick={addRule}
                            className="px-3 py-1 bg-indigo-500/20 hover:bg-indigo-500/40 text-indigo-300 border border-indigo-500/30 rounded-lg text-[8px] font-black uppercase tracking-wider transition-all cursor-pointer"
                        >
                            + Agregar Regla
                        </button>
                    </div>

                    {(field.maintenance_rules || []).length > 0 ? (
                        <div className="px-6 pb-4 space-y-3 pt-3">
                            {(field.maintenance_rules || []).map((rule, ruleIndex) => (
                                <div key={ruleIndex} className="bg-black/40 rounded-2xl border border-indigo-500/10 p-4 space-y-3">
                                    <div className="flex items-center gap-3 flex-wrap">
                                        <div className="flex-1 min-w-40">
                                            <label className="block text-[8px] font-black text-white/30 uppercase mb-0.5">Nombre de la Regla</label>
                                            <input
                                                type="text"
                                                value={rule.nombre || ''}
                                                onChange={e => updateRule(ruleIndex, 'nombre', e.target.value)}
                                                placeholder="Ej: Cambio de Aceite"
                                                className="w-full bg-black/40 border border-white/10 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-indigo-500"
                                                required
                                            />
                                        </div>
                                        <div className="w-36">
                                            <label className="block text-[8px] font-black text-white/30 uppercase mb-0.5">
                                                {field.type === 'date' ? 'Cada (días)' : 'Frecuencia (km/h)'}
                                            </label>
                                            <input
                                                type="number"
                                                value={rule.frecuencia || ''}
                                                onChange={e => updateRule(ruleIndex, 'frecuencia', e.target.value)}
                                                placeholder={field.type === 'date' ? '365' : '15000'}
                                                className="w-full bg-black/40 border border-white/10 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-indigo-500"
                                                required
                                            />
                                        </div>
                                        <div className="w-36">
                                            <label className="block text-[8px] font-black text-white/30 uppercase mb-0.5">Margen Pre-Alerta</label>
                                            <input
                                                type="number"
                                                value={rule.margen_pre_alerta || ''}
                                                onChange={e => updateRule(ruleIndex, 'margen_pre_alerta', e.target.value)}
                                                placeholder={field.type === 'date' ? '30 días' : '1000'}
                                                className="w-full bg-black/40 border border-white/10 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-indigo-500"
                                            />
                                        </div>
                                        <div className="flex-1 min-w-44">
                                            <label className="block text-[8px] font-black text-white/30 uppercase mb-0.5">Checklist de Resolución</label>
                                            <select
                                                value={rule.checklist_resolucion_id || ''}
                                                onChange={e => updateRule(ruleIndex, 'checklist_resolucion_id', e.target.value)}
                                                className="w-full bg-black/40 border border-white/10 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-indigo-500"
                                                required
                                            >
                                                <option value="">— Seleccionar Checklist —</option>
                                                {checklists.map(c => (
                                                    <option key={c.id} value={c.id}>{c.title}</option>
                                                ))}
                                            </select>
                                        </div>
                                        <button
                                            type="button"
                                            onClick={() => removeRule(ruleIndex)}
                                            className="h-8 w-8 bg-red-500/10 hover:bg-red-500 hover:text-white text-red-400 border border-red-500/20 rounded-xl flex items-center justify-center transition-all flex-shrink-0 cursor-pointer self-end"
                                            title="Quitar regla"
                                        >
                                            ✕
                                        </button>
                                    </div>
                                </div>
                            ))}
                        </div>
                    ) : (
                        <p className="px-6 py-3 text-[10px] text-white/20 italic">Sin reglas SRE configuradas para esta métrica.</p>
                    )}
                </div>
            )}
        </div>
    );
}
