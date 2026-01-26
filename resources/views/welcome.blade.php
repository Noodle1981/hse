<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>XamaSafe | Seguridad Industrial de Próxima Generación</title>
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
                            500: '#F69239', // Tu naranja original
                            600: '#ea580c',
                            900: '#111827',
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
    </style>
</head>
<body class="bg-surface text-brand-900 overflow-x-hidden">

    <!-- Nav Minimalista -->
    <nav class="fixed w-full z-50 top-0 px-6 py-4">
        <div class="max-w-7xl mx-auto flex justify-between items-center bg-white/80 backdrop-blur-md border border-gray-100 px-6 py-3 rounded-2xl shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-brand-500 rounded-xl flex items-center justify-center rotate-3 hover:rotate-0 transition-transform cursor-pointer">
                    <span class="text-white font-extrabold text-xl">X</span>
                </div>
                <span class="font-extrabold text-xl tracking-tighter">Xama<span class="text-brand-500">Safe</span></span>
            </div>
            <div class="hidden md:flex items-center gap-8 font-medium text-sm text-gray-500">
                <a href="#" class="hover:text-brand-500 transition-colors">Producto</a>
                <a href="#" class="hover:text-brand-500 transition-colors">Soluciones</a>
                <a href="#" class="hover:text-brand-500 transition-colors">Precios</a>
            </div>
            <div class="flex items-center gap-4">
                <a href="#" class="text-sm font-semibold hover:text-brand-500">Log in</a>
                <a href="#" class="bg-brand-900 text-white px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-brand-600 transition-all shadow-lg shadow-brand-900/10">
                    Acceso Clientes
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section Asimétrico -->
    <section class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 bg-mesh min-h-screen flex items-center">
        <div class="max-w-7xl mx-auto px-6 grid lg:grid-cols-2 gap-16 items-center">
            <div class="relative z-10">
                <span class="inline-block py-1 px-3 rounded-full bg-brand-500/10 border border-brand-500/20 text-brand-500 text-xs font-bold uppercase tracking-widest mb-6">
                    Nueva Versión 2.0 disponible
                </span>
                <h1 class="text-5xl lg:text-7xl font-extrabold text-white leading-[1.1] mb-8">
                    La seguridad no es opcional, la <span class="text-brand-500 italic">trazabilidad</span> sí.
                </h1>
                <p class="text-gray-400 text-lg md:text-xl max-w-xl mb-10 leading-relaxed">
                    Digitaliza el cumplimiento normativo y gestiona riesgos en tiempo real con una interfaz diseñada para el campo, no solo para la oficina.
                </p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <button class="bg-brand-500 hover:bg-brand-600 text-white px-8 py-4 rounded-2xl font-bold text-lg transition-all flex items-center justify-center gap-2">
                        Empezar ahora
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                    </button>
                    <button class="glass text-white px-8 py-4 rounded-2xl font-bold text-lg hover:bg-white/10 transition-all">
                        Ver Demo
                    </button>
                </div>
            </div>
            
            <!-- Elemento Decorativo "Dashboard Abstracto" -->
            <div class="relative">
                <div class="absolute -inset-4 bg-brand-500/20 rounded-3xl blur-3xl"></div>
                <div class="relative glass p-6 rounded-3xl border border-white/10 shadow-2xl scale-105 rotate-2">
                    <div class="flex items-center gap-2 mb-6">
                        <div class="w-3 h-3 rounded-full bg-red-400"></div>
                        <div class="w-3 h-3 rounded-full bg-yellow-400"></div>
                        <div class="w-3 h-3 rounded-full bg-green-400"></div>
                    </div>
                    <div class="space-y-4">
                        <div class="h-8 bg-white/5 rounded-lg w-3/4 animate-pulse"></div>
                        <div class="grid grid-cols-3 gap-4">
                            <div class="h-20 bg-brand-500/20 rounded-xl border border-brand-500/30 flex flex-col items-center justify-center">
                                <span class="text-brand-500 font-bold text-xl">98%</span>
                                <span class="text-white/40 text-[10px] uppercase">Cumplimiento</span>
                            </div>
                            <div class="h-20 bg-white/5 rounded-xl border border-white/5"></div>
                            <div class="h-20 bg-white/5 rounded-xl border border-white/5"></div>
                        </div>
                        <div class="h-32 bg-white/5 rounded-xl border border-white/5"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Bento Grid -->
    <section class="py-24 max-w-7xl mx-auto px-6">
        <div class="mb-16 max-w-2xl">
            <h2 class="text-brand-500 font-bold tracking-tight mb-2 uppercase text-sm">Capacidades</h2>
            <p class="text-4xl font-extrabold tracking-tight">Menos papeleo, más acción preventiva.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-6 md:grid-rows-2 gap-4 h-auto md:h-[600px]">
            <!-- Grande -->
            <div class="md:col-span-3 md:row-span-2 bento-card bg-white p-8 rounded-[2rem] border border-gray-100 shadow-sm flex flex-col justify-between overflow-hidden relative">
                <div>
                    <div class="w-14 h-14 bg-brand-50 rounded-2xl flex items-center justify-center mb-6">
                        <svg class="w-7 h-7 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                    <h3 class="text-2xl font-extrabold mb-4 italic">Matrices IPERC Dinámicas</h3>
                    <p class="text-gray-500 leading-relaxed">Las matrices de riesgo dejan de ser documentos estáticos para convertirse en organismos vivos que se alimentan de los datos diarios del personal en campo.</p>
                </div>
                <div class="mt-8 -mb-12 -mr-12">
                     <!-- Imagen o ilustración aquí -->
                     <div class="h-48 bg-brand-50 rounded-tl-3xl border-t border-l border-brand-100"></div>
                </div>
            </div>

            <!-- Mediano superior -->
            <div class="md:col-span-3 bento-card bg-brand-900 p-8 rounded-[2rem] text-white flex flex-col justify-center relative overflow-hidden">
                <div class="relative z-10">
                    <h3 class="text-2xl font-bold mb-2">Checklists Inteligentes</h3>
                    <p class="text-brand-100/60">Flujos de trabajo condicionales que guían al inspector según la criticidad del hallazgo.</p>
                </div>
                <div class="absolute right-0 top-0 w-32 h-32 bg-brand-500/10 blur-3xl"></div>
            </div>

            <!-- Pequeño 1 -->
            <div class="md:col-span-1.5 bento-card bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm flex flex-col justify-between">
                <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"></path></svg>
                </div>
                <h4 class="font-bold mt-4">Analítica Real</h4>
            </div>

            <!-- Pequeño 2 -->
            <div class="md:col-span-1.5 bento-card bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm flex flex-col justify-between">
                <div class="w-10 h-10 bg-green-50 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"></path></svg>
                </div>
                <h4 class="font-bold mt-4">Offline Ready</h4>
            </div>
        </div>
    </section>

    <!-- CTA Diferente -->
    <section class="py-20">
        <div class="max-w-5xl mx-auto px-6">
            <div class="bg-brand-500 rounded-[3rem] p-12 text-center relative overflow-hidden shadow-2xl shadow-brand-500/20">
                <div class="relative z-10">
                    <h2 class="text-4xl md:text-5xl font-extrabold text-white mb-6">¿Tu empresa es segura o <br>solo tienes los papeles?</h2>
                    <p class="text-brand-100 text-lg mb-10 max-w-xl mx-auto">Únete a la transformación digital de Higiene y Seguridad. Reportes automáticos, equipos protegidos.</p>
                    <button class="bg-brand-900 text-white px-10 py-4 rounded-2xl font-bold hover:scale-105 transition-transform">
                        Agenda una Auditoría Gratis
                    </button>
                </div>
                <!-- Decoración circular -->
                <div class="absolute -top-24 -left-24 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-24 -right-24 w-64 h-64 bg-brand-600 rounded-full blur-3xl"></div>
            </div>
        </div>
    </section>

    <!-- Footer Moderno -->
    <footer class="bg-white pt-20 pb-10 border-t border-gray-100">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-12 mb-16">
                <div class="col-span-2">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-8 h-8 bg-brand-900 rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold">X</span>
                        </div>
                        <span class="font-extrabold text-lg tracking-tight">XamaSafe</span>
                    </div>
                    <p class="text-gray-500 max-w-xs mb-6">Redefiniendo el estándar de seguridad industrial en Latinoamérica a través de tecnología y datos.</p>
                    <div class="flex gap-4">
                        <div class="w-8 h-8 bg-gray-100 rounded-full"></div>
                        <div class="w-8 h-8 bg-gray-100 rounded-full"></div>
                        <div class="w-8 h-8 bg-gray-100 rounded-full"></div>
                    </div>
                </div>
                <div>
                    <h5 class="font-bold mb-6">Producto</h5>
                    <ul class="space-y-4 text-sm text-gray-500">
                        <li><a href="#" class="hover:text-brand-500">Checklists</a></li>
                        <li><a href="#" class="hover:text-brand-500">Gestión de Riesgos</a></li>
                        <li><a href="#" class="hover:text-brand-500">Incidentes</a></li>
                    </ul>
                </div>
                <div>
                    <h5 class="font-bold mb-6">Compañía</h5>
                    <ul class="space-y-4 text-sm text-gray-500">
                        <li><a href="#" class="hover:text-brand-500">Sobre nosotros</a></li>
                        <li><a href="#" class="hover:text-brand-500">Carreras</a></li>
                        <li><a href="#" class="hover:text-brand-500">Contacto</a></li>
                    </ul>
                </div>
                <div>
                    <h5 class="font-bold mb-6">Legal</h5>
                    <ul class="space-y-4 text-sm text-gray-500">
                        <li><a href="#" class="hover:text-brand-500">Privacidad</a></li>
                        <li><a href="#" class="hover:text-brand-500">Términos</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-100 pt-8 flex flex-col md:flex-row justify-between items-center gap-4 text-gray-400 text-xs">
                <p>&copy; 2024 XamaSafe. Made with precision for safety experts.</p>
                <div class="flex gap-6">
                    <span>Status: Operacional</span>
                    <span>v2.0.4</span>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>