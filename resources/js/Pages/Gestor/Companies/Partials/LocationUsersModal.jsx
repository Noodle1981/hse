import React from 'react';

export default function LocationUsersModal({ show, selectedLocation, availableUsers, onToggleUser, onClose }) {
    if (!show) return null;

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 animate-fade-in">
            <div className="absolute inset-0 bg-black/85 backdrop-blur-md" onClick={onClose}></div>
            <div className="bg-[#16162a] border border-white/10 rounded-[3rem] p-10 max-w-2xl w-full relative z-10 shadow-2xl overflow-hidden">
                <div className="absolute inset-x-0 top-0 h-1 bg-blue-500/50"></div>
                <h3 className="text-xl font-black text-white italic mb-2 uppercase tracking-tight flex items-center gap-3">Asignación de Operadores</h3>
                <p className="text-[10px] font-black text-blue-400 uppercase tracking-widest italic mb-10 border-b border-white/5 pb-4">
                    Unidad: <span className="text-white">{selectedLocation?.nombre_sede}</span>
                </p>

                <div className="grid grid-cols-1 gap-4 max-h-[50vh] overflow-y-auto pr-4 custom-scrollbar">
                    {availableUsers.map(user => {
                        const isAssigned = selectedLocation?.users.some(u => u.id === user.id);
                        return (
                            <div key={user.id} className="flex items-center justify-between p-4 bg-white/5 border border-white/5 rounded-2xl hover:bg-white/10 transition-all group">
                                <div className="flex items-center gap-4">
                                    <div className="w-10 h-10 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400 text-xs font-black">
                                        {user.nombres.charAt(0)}{user.apellidos.charAt(0)}
                                    </div>
                                    <div>
                                        <div className="text-xs font-black text-white italic uppercase tracking-tight leading-none mb-1">
                                            {user.apellidos}, {user.nombres}
                                        </div>
                                        <div className="text-[9px] font-bold text-white/20 italic tracking-widest">MAT: {user.matricula || '---'}</div>
                                    </div>
                                </div>
                                <button
                                    onClick={() => onToggleUser(user.id)}
                                    className={`h-10 px-6 rounded-xl text-[9px] font-black uppercase tracking-widest italic transition-all ${
                                        isAssigned
                                            ? 'bg-red-600/10 text-red-500 border border-red-500/20 hover:bg-red-600 hover:text-white'
                                            : 'bg-emerald-600/10 text-emerald-500 border border-emerald-500/20 hover:bg-emerald-600 hover:text-white'
                                    }`}
                                >
                                    {isAssigned ? 'Desvincular' : 'Vincular'}
                                </button>
                            </div>
                        );
                    })}
                </div>

                <div className="flex justify-end mt-10">
                    <button onClick={onClose} className="px-10 py-4 bg-white/5 text-white/40 hover:text-white rounded-2xl text-[10px] font-black uppercase tracking-widest italic transition-all">
                        Cerrar Terminal
                    </button>
                </div>
            </div>
        </div>
    );
}
