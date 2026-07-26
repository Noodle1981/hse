import { useState } from 'react';
import { useForm, usePage, Head } from '@inertiajs/react';

export default function Login() {
    const { branding } = usePage().props;
    const [showPassword, setShowPassword] = useState(false);
    const [showResetModal, setShowResetModal] = useState(false);

    const { data, setData, post, processing, errors } = useForm({
        email: '',
        password: '',
        remember: false,
    });

    const resetForm = useForm({
        email: '',
    });

    const submit = (e) => {
        e.preventDefault();
        post(route('login'));
    };

    const handleResetSubmit = (e) => {
        e.preventDefault();
        resetForm.post(route('password.reset-request.store'), {
            onSuccess: () => {
                setShowResetModal(false);
                resetForm.reset();
            }
        });
    };

    return (
        <>
            <Head title="Iniciar Sesión" />

            {/* Fondo con gradiente animado */}
            <div className="min-h-screen bg-[#11111d] flex items-center justify-center relative overflow-hidden px-4">

                {/* Orbes decorativos de fondo */}
                <div className="absolute top-[-10%] left-[-5%] w-96 h-96 bg-[#F69239]/10 rounded-full blur-3xl animate-pulse" />
                <div className="absolute bottom-[-10%] right-[-5%] w-96 h-96 bg-orange-600/10 rounded-full blur-3xl animate-pulse" style={{ animationDelay: '1s' }} />
                <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-[#F69239]/5 rounded-full blur-3xl" />

                {/* Card central */}
                <div className="relative z-10 w-full max-w-md">

                    {/* Logo + Marca */}
                    <div className="flex flex-col items-center mb-8">
                        <div className="w-48 h-48 flex items-center justify-center relative mb-4">
                            {/* Efecto de resplandor naranja de fondo */}
                            <div className="absolute w-36 h-36 bg-[#F69239]/30 rounded-full blur-2xl animate-pulse pointer-events-none" />
                            <img 
                                src="/images/Logo_login.png" 
                                alt="Industrias Xamanen Logo" 
                                className="w-full h-full object-contain relative z-10" 
                            />
                        </div>
                    </div>

                    {/* Formulario */}
                    <div className="bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-8 shadow-2xl shadow-black/40">
                        <h2 className="text-xl font-bold text-white mb-1">Bienvenido de vuelta</h2>
                        <p className="text-gray-400 text-sm mb-6">Ingresá tus credenciales para continuar</p>

                        <form onSubmit={submit} className="space-y-5">
                            {/* Email */}
                            <div>
                                <label className="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">
                                    Email
                                </label>
                                <div className="relative">
                                    <span className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">
                                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                        </svg>
                                    </span>
                                    <input
                                        id="email"
                                        type="email"
                                        value={data.email}
                                        onChange={e => setData('email', e.target.value)}
                                        autoComplete="username"
                                        placeholder="tu@email.com"
                                        className={`w-full pl-10 pr-4 py-3 bg-white/5 border rounded-xl text-white placeholder-gray-600 text-sm
                                            focus:outline-none focus:ring-2 focus:ring-[#F69239]/50 focus:border-[#F69239]/50 transition-all
                                            ${errors.email ? 'border-red-500/60 bg-red-500/5' : 'border-white/10 hover:border-white/20'}`}
                                    />
                                </div>
                                {errors.email && <p className="mt-1.5 text-xs text-red-400">{errors.email}</p>}
                            </div>

                            {/* Password */}
                            <div>
                                <label className="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">
                                    Contraseña
                                </label>
                                <div className="relative">
                                    <span className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">
                                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                    </span>
                                    <input
                                        id="password"
                                        type={showPassword ? 'text' : 'password'}
                                        value={data.password}
                                        onChange={e => setData('password', e.target.value)}
                                        autoComplete="current-password"
                                        placeholder="••••••••"
                                        className={`w-full pl-10 pr-10 py-3 bg-white/5 border rounded-xl text-white placeholder-gray-600 text-sm
                                            focus:outline-none focus:ring-2 focus:ring-[#F69239]/50 focus:border-[#F69239]/50 transition-all
                                            ${errors.password ? 'border-red-500/60 bg-red-500/5' : 'border-white/10 hover:border-white/20'}`}
                                    />
                                    <button
                                        type="button"
                                        onClick={() => setShowPassword(!showPassword)}
                                        className="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-300 transition-colors"
                                    >
                                        {showPassword ? (
                                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                            </svg>
                                        ) : (
                                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        )}
                                    </button>
                                </div>
                                {errors.password && <p className="mt-1.5 text-xs text-red-400">{errors.password}</p>}
                            </div>

                            {/* Remember me & Forgot password */}
                            <div className="flex items-center justify-between">
                                <label className="flex items-center cursor-pointer">
                                    <input
                                        id="remember"
                                        type="checkbox"
                                        checked={data.remember}
                                        onChange={e => setData('remember', e.target.checked)}
                                        className="w-4 h-4 text-[#F69239] bg-white/5 border-white/20 rounded focus:ring-[#F69239]/50 accent-[#F69239]"
                                    />
                                    <span className="ml-2 text-xs text-gray-400">Recordarme</span>
                                </label>
                                <button
                                    type="button"
                                    onClick={() => setShowResetModal(true)}
                                    className="text-xs text-[#F69239] hover:underline font-medium transition-colors"
                                >
                                    ¿Olvidaste tu contraseña?
                                </button>
                            </div>

                            {/* Botón submit */}
                            <button
                                type="submit"
                                disabled={processing}
                                className="w-full py-3 px-6 bg-gradient-to-r from-[#F69239] to-orange-500 text-white font-bold rounded-xl
                                    shadow-lg shadow-[#F69239]/20 hover:shadow-[#F69239]/40 hover:brightness-110
                                    disabled:opacity-50 disabled:cursor-not-allowed
                                    transition-all duration-200 flex items-center justify-center gap-2"
                            >
                                {processing ? (
                                    <>
                                        <svg className="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                            <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"/>
                                            <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                        </svg>
                                        Ingresando...
                                    </>
                                ) : 'Ingresar'}
                            </button>
                        </form>
                    </div>

                    <p className="text-center text-xs text-gray-500 mt-6 opacity-80">
                        Sistema de Gestión de Seguridad © {new Date().getFullYear()} • Versión 1.0.0
                    </p>
                </div>
            </div>

            {/* MODAL SOLICITUD DE RESETEO DE CONTRASEÑA */}
            {showResetModal && (
                <div className="fixed inset-0 z-50 bg-black/80 backdrop-blur-md flex items-center justify-center p-4 animate-fade-in">
                    <div className="bg-[#16162a] border border-white/10 rounded-2xl p-6 sm:p-8 max-w-md w-full shadow-2xl space-y-5">
                        <div className="flex items-center justify-between border-b border-white/10 pb-3">
                            <h3 className="text-sm font-bold text-white uppercase tracking-wider">
                                Solicitar Reseteo de Contraseña
                            </h3>
                            <button
                                type="button"
                                onClick={() => setShowResetModal(false)}
                                className="text-gray-400 hover:text-white text-lg font-bold"
                            >
                                ✕
                            </button>
                        </div>

                        <p className="text-xs text-gray-300 leading-relaxed">
                            Ingresá tu correo electrónico. Tu gestor o administrador recibirá una solicitud en la plataforma para blanquear tu clave.
                        </p>

                        <form onSubmit={handleResetSubmit} className="space-y-4">
                            <div>
                                <label className="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">
                                    Email de Usuario *
                                </label>
                                <input
                                    type="email"
                                    value={resetForm.data.email}
                                    onChange={e => resetForm.setData('email', e.target.value)}
                                    placeholder="usuario@ejemplo.com"
                                    className="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-3 text-sm text-white focus:border-[#F69239] focus:ring-0 font-mono"
                                    required
                                />
                                {resetForm.errors.email && <p className="text-xs text-red-400 mt-1">{resetForm.errors.email}</p>}
                            </div>

                            <div className="flex justify-end gap-3 pt-2">
                                <button
                                    type="button"
                                    onClick={() => setShowResetModal(false)}
                                    className="px-4 py-2.5 bg-white/5 border border-white/10 hover:bg-white/10 text-white/70 text-xs font-bold rounded-xl transition-all"
                                >
                                    Cancelar
                                </button>
                                <button
                                    type="submit"
                                    disabled={resetForm.processing}
                                    className="px-5 py-2.5 bg-[#F69239] hover:bg-orange-500 text-white text-xs font-bold rounded-xl transition-all shadow-lg shadow-[#F69239]/20"
                                >
                                    {resetForm.processing ? 'Enviando...' : 'Enviar Solicitud'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </>
    );
}
