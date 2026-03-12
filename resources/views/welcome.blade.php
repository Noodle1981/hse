<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>XamaSafe | Gestión Integral de Higiene y Seguridad Laboral</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] },
                    colors: {
                        'brand': {
                            50: '#fff7ed',
                            100: '#ffedd5',
                            500: '#F69239', // Naranja XamaSafe
                            600: '#e17d2a',
                            900: '#111827', // Azul Oscuro Sidebar
                        },
                        'surface': '#fafafa'
                    }
                }
            }
        }
    </script>
    <style>
        .glass { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.1); }
        .bento-card { transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
        .bento-card:hover { transform: translateY(-5px); border-color: #F69239; }
        .bg-mesh {
            background-color: #111827;
            background-image: radial-gradient(at 0% 0%, rgba(246, 146, 57, 0.15) 0px, transparent 50%),
                              radial-gradient(at 100% 100%, rgba(246, 146, 57, 0.1) 0px, transparent 50%);
        }
        .phone-mockup {
            box-shadow: 0 0 0 12px #111827, 0 0 0 14px rgba(255,255,255,0.05);
            border-radius: 40px;
        }
    </style>
</head>
<body class="bg-surface text-brand-900 overflow-x-hidden">

    <!-- Nav Minimalista -->
    <nav class="fixed w-full z-50 top-0 px-6 py-4">
        <div class="max-w-7xl mx-auto flex justify-between items-center bg-white/80 backdrop-blur-md border border-gray-100 px-6 py-3 rounded-2xl shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-brand-500 rounded-xl flex items-center justify-center rotate-3 hover:rotate-0 transition-transform cursor-pointer">
                    <img src="{{ asset('images/logo.png') }}" alt="X" class="w-6 h-6 object-contain hidden" onerror="this.style.display='none'; this.nextElementSibling.style.display='block'">
                    <span class="text-white font-extrabold text-xl">X</span>
                </div>
                <span class="font-extrabold text-xl tracking-tighter">Xama<span class="text-brand-500">Safe</span></span>
            </div>
            <div class="hidden md:flex items-center gap-8 font-medium text-sm text-gray-500">
                <a href="#soluciones" class="hover:text-brand-500 transition-colors">Soluciones</a>
                <a href="#proceso" class="hover:text-brand-500 transition-colors">Proceso</a>
                <a href="#contacto" class="hover:text-brand-500 transition-colors">Contacto</a>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('login') }}" class="text-sm font-semibold hover:text-brand-500">Log in</a>
                <a href="{{ route('login') }}" class="bg-brand-900 text-white px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-brand-600 transition-all shadow-lg shadow-brand-900/10">
                    Acceso Clientes
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 bg-mesh min-h-screen flex items-center">
        <div class="max-w-7xl mx-auto px-6 grid lg:grid-cols-2 gap-16 items-center">
            <div class="relative z-10">
                <span class="inline-block py-1 px-3 rounded-full bg-brand-500/10 border border-brand-500/20 text-brand-500 text-xs font-bold uppercase tracking-widest mb-6">
                    SaaS de Higiene y Seguridad v2.0
                </span>
                <h1 class="text-5xl lg:text-7xl font-extrabold text-white leading-[1.1] mb-8">
                    Gestión integral con <span class="text-brand-500 italic">trazabilidad</span> real.
                </h1>
                <p class="text-gray-400 text-lg md:text-xl max-w-xl mb-10 leading-relaxed">
                    Digitaliza el ciclo completo de seguridad laboral: visitas de campo, checklists dinámicos e informes maestros generados en segundos.
                </p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('login') }}" class="bg-brand-500 hover:bg-brand-600 text-white px-8 py-4 rounded-2xl font-bold text-lg transition-all flex items-center justify-center gap-2">
                        Comenzar Ahora
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                    </a>
                    <a href="#soluciones" class="glass text-white px-8 py-4 rounded-2xl font-bold text-lg hover:bg-white/10 transition-all flex items-center justify-center">
                        Explorar Funciones
                    </a>
                </div>
            </div>
            
            <!-- Screenshot Mockup -->
            <div class="relative">
                <div class="absolute -inset-4 bg-brand-500/20 rounded-3xl blur-3xl"></div>
                <div class="relative">
                    <!-- Desktop Screenshot -->
                    <div class="bg-brand-900/40 border border-white/10 rounded-2xl p-2 shadow-2xl relative overflow-hidden">
                        <div class="flex gap-1.5 mb-2 px-2">
                            <div class="w-2.5 h-2.5 rounded-full bg-white/20"></div>
                            <div class="w-2.5 h-2.5 rounded-full bg-white/20"></div>
                            <div class="w-2.5 h-2.5 rounded-full bg-white/20"></div>
                        </div>
                        <img src="{{ asset('images/cap1.png') }}" alt="XamaSafe Desktop" class="rounded-lg shadow-inner opacity-90">
                    </div>
                    
                    <!-- Mobile Screenshot Overlay -->
                    <div class="absolute -bottom-10 -right-6 md:-right-12 w-48 md:w-64 phone-mockup bg-brand-900 p-0 overflow-hidden hidden md:block border-4 border-[#111827]">
                        <img src="{{ asset('images/cap3.png') }}" alt="XamaSafe Mobile" class="w-full">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Bento Grid -->
    <section id="soluciones" class="py-24 max-w-7xl mx-auto px-6">
        <div class="mb-16 max-w-2xl">
            <h2 class="text-brand-500 font-bold tracking-tight mb-2 uppercase text-sm">Capacidades del Sistema</h2>
            <p class="text-4xl font-extrabold tracking-tight">Potencia tu consultoría con datos, no con papeles.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-6 md:grid-rows-2 gap-4 h-auto md:h-[600px]">
            <!-- Informe Maestro -->
            <div class="md:col-span-3 md:row-span-2 bento-card bg-white p-8 rounded-[2rem] border border-gray-100 shadow-sm flex flex-col justify-between overflow-hidden relative">
                <div>
                    <div class="w-14 h-14 bg-brand-50 rounded-2xl flex items-center justify-center mb-6">
                        <svg class="w-7 h-7 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <h3 class="text-2xl font-extrabold mb-4 italic">Informe Maestro Automático</h3>
                    <p class="text-gray-500 leading-relaxed">Genera reportes mensuales profesionales integrando visitas, hallazgos y capacitaciones con un solo clic. Reducción de tiempo administrativo en un 90%.</p>
                </div>
                <div class="mt-8">
                     <div class="h-48 bg-brand-50 rounded-t-3xl border-t border-x border-brand-100 p-4 overflow-hidden">
                        <div class="space-y-3">
                            <div class="h-4 bg-brand-500/20 rounded w-1/2"></div>
                            <div class="h-4 bg-gray-200 rounded w-5/6"></div>
                            <div class="h-4 bg-gray-200 rounded w-4/6"></div>
                            <div class="h-20 bg-white rounded-xl border border-brand-100 shadow-sm"></div>
                        </div>
                     </div>
                </div>
            </div>

            <!-- Checklists Dinámicos -->
            <div class="md:col-span-3 bento-card bg-brand-900 p-8 rounded-[2rem] text-white flex flex-col justify-center relative overflow-hidden">
                <div class="relative z-10">
                    <h3 class="text-2xl font-bold mb-2">Checklists al Instante</h3>
                    <p class="text-brand-100/60">Crea y asigna inspecciones dinámicas para cualquier tipo de norma (HyS, ISO, BPA) con wizard de llenado optimizado para el campo.</p>
                </div>
                <div class="absolute right-0 top-0 w-32 h-32 bg-brand-500/10 blur-3xl"></div>
            </div>

            <!-- Hallazgos e Incidentes -->
            <div class="md:col-span-1.5 bento-card bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm flex flex-col justify-between">
                <div class="w-10 h-10 bg-orange-50 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-brand-500" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"></path></svg>
                </div>
                <h4 class="font-bold mt-4">Hallazgos y Fotos</h4>
                <p class="text-xs text-gray-400 mt-2">Seguimiento de estados y evidencia fotográfica.</p>
            </div>

            <!-- Firmas Digitales -->
            <div class="md:col-span-1.5 bento-card bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm flex flex-col justify-between">
                <div class="w-10 h-10 bg-emerald-50 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path></svg>
                </div>
                <h4 class="font-bold mt-4">Firma Remota</h4>
                <p class="text-xs text-gray-400 mt-2">Cierre de visitas con firma en pantalla o enlace remoto.</p>
            </div>
        </div>
    </section>

    <!-- Proceso -->
    <section id="proceso" class="py-20 bg-gray-50/50 relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-extrabold mb-4">El ciclo de XamaSafe</h2>
                <div class="w-20 h-1.5 bg-brand-500 mx-auto rounded-full"></div>
            </div>
            
            <div class="grid md:grid-cols-3 gap-12 text-center">
                <div class="space-y-4">
                    <div class="text-5xl font-black text-brand-500/10">01</div>
                    <h3 class="text-xl font-bold">Planifica</h3>
                    <p class="text-gray-500 text-sm">Gestiona el contrato anual y asigna visitas a técnicos de forma estratégica.</p>
                </div>
                <div class="space-y-4">
                    <div class="text-5xl font-black text-brand-500/10">02</div>
                    <h3 class="text-xl font-bold">Ejecuta</h3>
                    <p class="text-gray-500 text-sm">El técnico realiza la visita, completa checklists y registra hallazgos en tiempo real.</p>
                </div>
                <div class="space-y-4">
                    <div class="text-5xl font-black text-brand-500/10">03</div>
                    <h3 class="text-xl font-bold">Reporta</h3>
                    <p class="text-gray-500 text-sm">El sistema consolida todo en informes profesionales con branding personalizado.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section id="contacto" class="py-20">
        <div class="max-w-5xl mx-auto px-6">
            <div class="bg-brand-500 rounded-[3rem] p-12 text-center relative overflow-hidden shadow-2xl shadow-brand-500/20">
                <div class="relative z-10">
                    <h2 class="text-4xl md:text-5xl font-extrabold text-white mb-6">Eleva el estándar de <br>tu consultora hoy.</h2>
                    <p class="text-brand-100 text-lg mb-10 max-w-xl mx-auto">Únete a la transformación digital de la Seguridad Industrial. Control total, cumplimiento garantizado.</p>
                    <a href="{{ route('login') }}" class="inline-block bg-brand-900 text-white px-10 py-4 rounded-2xl font-bold hover:scale-105 transition-transform">
                        Ingresar a la Plataforma
                    </a>
                </div>
                <!-- Decoración -->
                <div class="absolute -top-24 -left-24 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-24 -right-24 w-64 h-64 bg-brand-600 rounded-full blur-3xl"></div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white pt-20 pb-10 border-t border-gray-100">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex flex-col md:flex-row justify-between items-start gap-12 mb-16">
                <div class="max-w-xs">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-8 h-8 bg-brand-900 rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold">X</span>
                        </div>
                        <span class="font-extrabold text-lg tracking-tight">XamaSafe</span>
                    </div>
                    <p class="text-gray-500 text-sm mb-6">Redefiniendo la gestión de higiene y seguridad laboral mediante tecnología, datos y trazabilidad sellada.</p>
                </div>
                <div class="grid grid-cols-2 gap-20">
                    <div>
                        <h5 class="font-bold mb-6 text-sm">Plataforma</h5>
                        <ul class="space-y-4 text-sm text-gray-500">
                            <li>Checklists</li>
                            <li>Informes</li>
                            <li>Visitas</li>
                        </ul>
                    </div>
                    <div>
                        <h5 class="font-bold mb-6 text-sm">Compañía</h5>
                        <ul class="space-y-4 text-sm text-gray-500">
                            <li><a href="mailto:soporte@xamasafe.com" class="hover:text-brand-500">Soporte</a></li>
                            <li>Privacidad</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-100 pt-8 flex flex-col md:flex-row justify-between items-center gap-4 text-gray-400 text-xs">
                <p>&copy; 2026 XamaSafe. Diseñado para expertos en prevención.</p>
                <div class="flex gap-6">
                    <span>Estado: Operacional</span>
                    <span>v2.0.0.0</span>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>