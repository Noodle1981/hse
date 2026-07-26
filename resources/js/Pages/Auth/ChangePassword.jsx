import React, { useState } from 'react';
import { useForm, usePage, Head, router } from '@inertiajs/react';

export default function ChangePassword() {
    const { auth } = usePage().props;
    const [showPassword, setShowPassword] = useState(false);
    const [showConfirmPassword, setShowConfirmPassword] = useState(false);

    const { data, setData, post, processing, errors } = useForm({
        password: '',
        password_confirmation: '',
    });

    // Password strength calculation
    const getPasswordStrength = (pwd) => {
        if (!pwd) return { score: 0, label: '', color: 'bg-gray-700' };
        let score = 0;
        if (pwd.length >= 8) score += 1;
        if (pwd.length >= 12) score += 1;
        if (/[A-Z]/.test(pwd)) score += 1;
        if (/[0-9]/.test(pwd)) score += 1;
        if (/[^A-Za-z0-9]/.test(pwd)) score += 1;

        if (score <= 2) return { score, label: 'Débil', color: 'bg-red-500' };
        if (score <= 4) return { score, label: 'Media', color: 'bg-amber-500' };
        return { score, label: 'Fuerte', color: 'bg-emerald-500' };
    };

    const strength = getPasswordStrength(data.password);

    const submit = (e) => {
        e.preventDefault();
        post(route('password.change.update'));
    };

    const handleLogout = (e) => {
        e.preventDefault();
        router.post(route('logout'));
    };

    return (
        <>
            <Head title="Cambio Obligatorio de Contraseña" />

            <div className="min-h-screen bg-[#11111d] flex items-center justify-center relative overflow-hidden px-4">
                {/* Orbes decorativos de fondo */}
                <div className="absolute top-[-10%] left-[-5%] w-96 h-96 bg-[#F69239]/10 rounded-full blur-3xl animate-pulse" />
                <div className="absolute bottom-[-10%] right-[-5%] w-96 h-96 bg-orange-600/10 rounded-full blur-3xl animate-pulse" style={{ animationDelay: '1s' }} />

                <div className="relative z-10 w-full max-w-md">
                    {/* Header Logo */}
                    <div className="flex flex-col items-center mb-6">
                        <div className="w-24 h-24 flex items-center justify-center relative mb-2">
                            <div className="absolute w-20 h-20 bg-[#F69239]/30 rounded-full blur-xl animate-pulse" />
                            <img src="/images/Logo_login.png" alt="Logo" className="w-full h-full object-contain relative z-10" />
                        </div>
                    </div>

                    {/* Card container */}
                    <div className="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-8 shadow-2xl shadow-black/50 space-y-6">
                        <div className="text-center space-y-2">
                            <div className="inline-flex items-center justify-center w-12 h-12 bg-amber-500/10 border border-amber-500/30 rounded-xl text-amber-400 mb-2">
                                <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" strokeWidth="2">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                </svg>
                            </div>
                            <h2 className="text-xl font-black text-white uppercase italic tracking-tight">
                                Establécete tu nueva contraseña
                            </h2>
                            <p className="text-xs text-gray-400">
                                Por motivos de seguridad, debes ingresar tu nueva clave personal antes de ingresar al sistema.
                            </p>
                        </div>

                        <form onSubmit={submit} className="space-y-5">
                            {/* Nueva Contraseña */}
                            <div>
                                <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest italic mb-2">
                                    Nueva Contraseña *
                                </label>
                                <div className="relative">
                                    <input 
                                        type={showPassword ? 'text' : 'password'}
                                        value={data.password}
                                        onChange={e => setData('password', e.target.value)}
                                        placeholder="••••••••••••"
                                        className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:border-ep-primary focus:ring-0 transition-all font-mono"
                                        required
                                    />
                                    <button
                                        type="button"
                                        onClick={() => setShowPassword(!showPassword)}
                                        className="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white"
                                    >
                                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            {showPassword ? (
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.048 10.048 0 013.682-.863c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m-4.692-4.692a3 3 0 00-4.243-4.243m4.243 4.243L3 3l18 18" />
                                            ) : (
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            )}
                                        </svg>
                                    </button>
                                </div>
                                {errors.password && <p className="text-xs text-red-400 mt-1">{errors.password}</p>}

                                {/* Indicator bar */}
                                {data.password && (
                                    <div className="mt-2 space-y-1">
                                        <div className="flex justify-between items-center text-[10px] font-bold text-gray-400 uppercase">
                                            <span>Fortaleza</span>
                                            <span className={strength.score <= 2 ? 'text-red-400' : strength.score <= 4 ? 'text-amber-400' : 'text-emerald-400'}>
                                                {strength.label}
                                            </span>
                                        </div>
                                        <div className="w-full bg-gray-800 rounded-full h-1.5 overflow-hidden">
                                            <div 
                                                className={`h-full transition-all duration-300 ${strength.color}`} 
                                                style={{ width: `${(strength.score / 5) * 100}%` }}
                                            />
                                        </div>
                                    </div>
                                )}
                            </div>

                            {/* Confirmar Contraseña */}
                            <div>
                                <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest italic mb-2">
                                    Confirmar Nueva Contraseña *
                                </label>
                                <div className="relative">
                                    <input 
                                        type={showConfirmPassword ? 'text' : 'password'}
                                        value={data.password_confirmation}
                                        onChange={e => setData('password_confirmation', e.target.value)}
                                        placeholder="••••••••••••"
                                        className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:border-ep-primary focus:ring-0 transition-all font-mono"
                                        required
                                    />
                                    <button
                                        type="button"
                                        onClick={() => setShowConfirmPassword(!showConfirmPassword)}
                                        className="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white"
                                    >
                                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            {showConfirmPassword ? (
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.048 10.048 0 013.682-.863c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m-4.692-4.692a3 3 0 00-4.243-4.243m4.243 4.243L3 3l18 18" />
                                            ) : (
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            )}
                                        </svg>
                                    </button>
                                </div>
                                {errors.password_confirmation && <p className="text-xs text-red-400 mt-1">{errors.password_confirmation}</p>}
                            </div>

                            {/* Submit button */}
                            <button
                                type="submit"
                                disabled={processing}
                                className="w-full bg-gradient-to-r from-ep-primary to-orange-600 hover:from-orange-600 hover:to-ep-primary text-white font-black uppercase italic tracking-wider py-3.5 px-4 rounded-xl shadow-lg shadow-ep-primary/25 hover:shadow-ep-primary/40 transition-all duration-300 transform active:scale-[0.99] disabled:opacity-50"
                            >
                                {processing ? 'Guardando...' : 'Guardar y Continuar'}
                            </button>
                        </form>

                        {/* Botón salir */}
                        <div className="text-center pt-2 border-t border-white/5">
                            <button
                                onClick={handleLogout}
                                className="text-xs text-gray-500 hover:text-gray-300 font-medium transition-colors"
                            >
                                Cerrar sesión
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
}
