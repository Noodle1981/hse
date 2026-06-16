import { useState } from 'react';
import { useForm, usePage, Head } from '@inertiajs/react';

export default function Login() {
    const { branding } = usePage().props;
    const [showPassword, setShowPassword] = useState(false);

    const { data, setData, post, processing, errors } = useForm({
        email: '',
        password: '',
        remember: false,
    });

    const submit = (e) => {
        e.preventDefault();
        post(route('login'));
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
                        <div className="w-16 h-16 flex items-center justify-center rotate-3 hover:rotate-0 transition-transform duration-500 shadow-lg shadow-ep-primary/20 mb-4">
                            <img src="/images/logo.svg" alt="Industrias Xamanen Logo" className="w-full h-full object-contain scale-[1.3]" />
                        </div>
                        <span className="font-black text-3xl md:text-4xl tracking-tighter italic leading-none text-white">
                            Xama<span className="text-[#F69239]">Safe</span>
                        </span>
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

                            {/* Remember me */}
                            <div className="flex items-center">
                                <input
                                    id="remember"
                                    type="checkbox"
                                    checked={data.remember}
                                    onChange={e => setData('remember', e.target.checked)}
                                    className="w-4 h-4 text-[#F69239] bg-white/5 border-white/20 rounded focus:ring-[#F69239]/50 accent-[#F69239]"
                                />
                                <label htmlFor="remember" className="ml-2 text-sm text-gray-400 cursor-pointer">
                                    Recordarme
                                </label>
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
                        XamaSafe — Sistema de Gestión de Seguridad © {new Date().getFullYear()} • Versión 1.0.0
                    </p>
                </div>
            </div>
        </>
    );
}
