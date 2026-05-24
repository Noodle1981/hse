<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>XamaSafe | Inteligencia en Higiene y Seguridad Laboral</title>
    @vite(['resources/css/app.css'])
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        .glass-card { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(15px); border: 1px solid rgba(255, 255, 255, 0.05); transition: all 0.5s ease; }
        .glass-card:hover { border-color: rgba(246, 146, 57, 0.3); background: rgba(255, 255, 255, 0.05); transform: translateY(-5px); }
        .bg-grid { background-image: radial-gradient(circle at 2px 2px, rgba(255,255,255,0.02) 1px, transparent 0); background-size: 40px 40px; }
        .glow-text { text-shadow: 0 0 20px rgba(246, 146, 57, 0.3); }
        @keyframes scan { from { transform: translateY(-100%); } to { transform: translateY(100%); } }
        .scan-line { animation: scan 4s linear infinite; }
    </style>
</head>
<body class="bg-ep-dark text-white overflow-x-hidden font-sans">

    <!-- Nav HUD -->
    <nav class="fixed w-full z-50 top-0 px-6 py-6">
        <div class="max-w-[95rem] mx-auto flex justify-between items-center bg-ep-dark/60 backdrop-blur-xl border border-white/5 px-8 py-4 rounded-[2rem] shadow-2xl">
            <div class="flex items-center gap-4 group cursor-pointer">
                <div class="w-12 h-12 bg-ep-primary rounded-2xl flex items-center justify-center rotate-3 group-hover:rotate-0 transition-all duration-500 shadow-lg shadow-ep-primary/20 relative overflow-hidden">
                    <div class="absolute inset-0 bg-white/20 translate-y-12 group-hover:translate-y-0 transition-transform"></div>
                    <span class="text-white font-black text-2xl relative z-10">X</span>
                </div>
                <div class="flex flex-col">
                    <span class="font-black text-xl tracking-tighter italic leading-none">Xama<span class="text-ep-primary">Safe</span></span>
                    <span class="text-[8px] font-black text-white/20 uppercase tracking-[0.4em] italic mt-1">Ecosystem Core</span>
                </div>
            </div>
            
            <div class="hidden md:flex items-center gap-12 text-[10px] font-black uppercase tracking-widest italic text-white/40">
                <a href="#soluciones" class="hover:text-ep-primary transition-colors">Soluciones</a>
                <a href="#proceso" class="hover:text-ep-primary transition-colors">Operaciones</a>
                <a href="#contacto" class="hover:text-ep-primary transition-colors">Enlace</a>
            </div>

            <div class="flex items-center gap-4">
                <a href="{{ route('login') }}" class="bg-white/5 border border-white/10 hover:bg-ep-primary hover:border-ep-primary text-white px-8 py-3 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] italic transition-all shadow-xl">
                    ingresar
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section (Respetando el gusto del usuario pero integrando el fondo Dark) -->
    <section class="relative pt-48 pb-32 min-h-screen flex items-center bg-grid overflow-hidden">
        <div class="absolute top-[-10%] left-[-10%] w-[50%] h-[50%] bg-ep-primary/10 rounded-full blur-[120px] animate-pulse"></div>
        
        <div class="max-w-[95rem] mx-auto px-10 grid lg:grid-cols-2 gap-24 items-center relative z-10">
            <div class="animate-fade-in-up">
                <span class="inline-flex py-2 px-4 rounded-full bg-ep-primary/10 border border-ep-primary/20 text-ep-primary text-[10px] font-black uppercase tracking-[0.3em] italic mb-8">
                    SaaS de Seguridad Industrial v2.0
                </span>
                <h1 class="text-6xl lg:text-8xl font-black text-white leading-[0.9] mb-10 italic tracking-tighter">
                    GESTIÓN CON <br><span class="text-ep-primary glow-text">TRAZABILIDAD</span> <br>REAL.
                </h1>
                <p class="text-white/40 text-lg font-bold max-w-xl mb-12 leading-relaxed italic uppercase tracking-wide">
                    DIGITALIZA EL CICLO COMPLETO: VISITAS, CHECKLISTS DINÁMICOS E INFORMES MAESTROS EN SEGUNDOS.
                </p>
                <div class="flex flex-col sm:flex-row gap-6">
                    <a href="{{ route('login') }}" class="group bg-ep-primary hover:brightness-110 text-white px-10 py-5 rounded-[2rem] font-black text-xs uppercase tracking-widest italic transition-all flex items-center justify-center gap-3 shadow-2xl shadow-ep-primary/20">
                        Comenzar Operación
                        <svg class="w-5 h-5 group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                    </a>
                    <a href="#soluciones" class="bg-white/5 border border-white/10 text-white px-10 py-5 rounded-[2rem] font-black text-xs uppercase tracking-widest italic hover:bg-white/10 transition-all flex items-center justify-center">
                        Explorar Matriz
                    </a>
                </div>
            </div>
            
            <!-- Multi-Device Tactical Composition -->
            <div class="relative group/mock">
                <!-- Outer glow -->
                <div class="absolute -inset-20 bg-ep-primary/5 rounded-full blur-[120px] group-hover:bg-ep-primary/10 transition-all duration-1000"></div>
                
                <div class="relative h-[450px] md:h-[600px] flex items-center justify-center">
                    
                    <!-- 1. DESKTOP UNIT (Back/Center) -->
                    <div class="absolute w-[90%] md:w-full transform -translate-y-10 group-hover:-translate-y-16 transition-transform duration-700">
                        <div class="bg-ep-dark/60 border border-white/10 rounded-[2.5rem] p-2 shadow-2xl backdrop-blur-xl">
                            <div class="flex gap-1.5 mb-2 px-4 pt-1 opacity-40">
                                <div class="w-2 h-2 rounded-full bg-white/20"></div>
                                <div class="w-2 h-2 rounded-full bg-white/20"></div>
                                <div class="w-2 h-2 rounded-full bg-white/20"></div>
                            </div>
                            <img src="{{ asset('images/cap1.png') }}" alt="XamaSafe Desktop" class="rounded-[1.5rem] opacity-70 group-hover:opacity-90 transition-opacity">
                        </div>
                    </div>

                    <!-- 2. TABLET UNIT (Floating Left/Bottom) -->
                    <div class="absolute left-[-5%] bottom-10 w-[55%] md:w-[50%] transform -rotate-6 group-hover:-rotate-3 group-hover:-translate-x-4 transition-all duration-700 delay-75">
                        <div class="bg-ep-dark/80 border border-white/20 rounded-[2rem] p-2 shadow-[0_0_50px_rgba(0,0,0,0.5)] backdrop-blur-2xl ring-1 ring-ep-primary/20">
                            <img src="{{ asset('images/cap2.png') }}" alt="XamaSafe Tablet" class="rounded-[1.5rem] shadow-inner">
                        </div>
                    </div>

                    <!-- 3. MOBILE UNIT (Leading Right/Bottom) -->
                    <div class="absolute right-[-2%] bottom-[-5%] w-[35%] md:w-[30%] transform rotate-3 group-hover:rotate-0 group-hover:translate-x-4 transition-all duration-1000 delay-150">
                        <div class="bg-[#0a0a0f] border-4 border-ep-dark rounded-[3rem] p-1 shadow-[0_0_60px_rgba(0,0,0,0.6)] relative overflow-hidden ring-1 ring-white/10">
                            <div class="absolute top-0 left-1/2 -translate-x-1/2 w-20 h-5 bg-ep-dark rounded-b-2xl z-20"></div>
                            <img src="{{ asset('images/cap3.png') }}" alt="XamaSafe Mobile" class="rounded-[2.5rem]">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CAPACIDADES DEL SISTEMA (BENTO GRID HUD) -->
    <section id="soluciones" class="py-32 relative bg-grid">
        <div class="max-w-[95rem] mx-auto px-10">
            <div class="mb-24 text-center lg:text-left relative">
                <h2 class="text-ep-primary font-black tracking-[0.5em] mb-4 uppercase text-[10px] italic">Capacidades del Sistema</h2>
                <h3 class="text-5xl lg:text-6xl font-black italic tracking-tighter uppercase leading-none">
                    POTENCIA TU CONSULTORÍA CON <br><span class="text-white/20">DATOS, NO CON PAPELES.</span>
                </h3>
                <div class="absolute -left-10 top-0 w-2 h-20 bg-ep-primary rounded-full hidden lg:block shadow-[0_0_20px_#F69239]"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-12 gap-8 h-auto">
                <!-- Informe Maestro HUD -->
                <div class="md:col-span-8 lg:col-span-7 glass-card p-12 rounded-[3.5rem] relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-ep-primary/5 rounded-full blur-[80px]"></div>
                    <div class="scan-line absolute inset-x-0 h-1 bg-ep-primary/10 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    
                    <div class="relative z-10">
                        <div class="w-20 h-20 bg-ep-primary/10 rounded-3xl flex items-center justify-center mb-10 border border-ep-primary/20 shadow-lg">
                            <svg class="w-10 h-10 text-ep-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <h4 class="text-3xl font-black italic mb-6 uppercase tracking-tighter">Informe Maestro Automático</h4>
                        <p class="text-white/40 text-lg font-bold italic leading-relaxed uppercase tracking-wide max-w-2xl mb-12">
                            GENERA REPORTES PROFESIONALES INTEGRANDO VISITAS, HALLAZGOS Y CAPACITACIONES CON UN CLIC. ELIMINA EL 90% DEL TRABAJO DE ESCRITORIO.
                        </p>
                        
                        <!-- Visual representation of data flow -->
                        <div class="grid grid-cols-3 gap-6 opacity-30 group-hover:opacity-100 transition-opacity duration-1000">
                            @for($i=0; $i<3; $i++)
                            <div class="h-1.5 bg-ep-primary/20 rounded-full overflow-hidden">
                                <div class="h-full bg-ep-primary animate-[scan_2s_linear_infinite]" style="animation-delay: <?php echo $i * 0.5; ?>s"></div>
                            </div>
                            @endfor
                        </div>
                    </div>
                </div>

                <!-- Checklists HUD -->
                <div class="md:col-span-4 lg:col-span-5 bg-ep-primary rounded-[3.5rem] p-12 relative overflow-hidden group shadow-2xl shadow-ep-primary/20">
                    <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-10"></div>
                    <div class="relative z-10 flex flex-col h-full justify-between">
                        <div>
                            <h4 class="text-3xl font-black italic mb-4 uppercase tracking-tighter text-ep-dark leading-none">Checklists <br>Tácticos</h4>
                            <p class="text-ep-dark/60 font-black text-xs uppercase tracking-widest italic leading-relaxed">
                                INSPECCIONES DINÁMICAS PARA NORMAS HyS, ISO, BPA CON WIZARD OPTIMIZADO PARA CAMPO.
                            </p>
                        </div>
                        <div class="mt-12 scale-110 translate-x-10 translate-y-10 group-hover:translate-x-5 group-hover:translate-y-5 transition-transform duration-700">
                             <div class="bg-ep-dark/20 backdrop-blur-md rounded-2xl p-6 border border-white/10">
                                <div class="space-y-3">
                                    <div class="h-2 bg-ep-dark/40 rounded w-full"></div>
                                    <div class="h-2 bg-ep-dark/40 rounded w-4/5"></div>
                                    <div class="h-2 bg-white/20 rounded w-3/5"></div>
                                </div>
                             </div>
                        </div>
                    </div>
                </div>

                <!-- Hallazgos e Incidentes HUD -->
                <div class="md:col-span-6 glass-card p-10 rounded-[3rem] flex items-center justify-between group">
                    <div class="space-y-4">
                        <div class="w-12 h-12 bg-red-500/10 rounded-2xl flex items-center justify-center border border-red-500/20">
                            <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <h4 class="text-xl font-black italic uppercase leading-none">Terminal de Hallazgos</h4>
                        <p class="text-[10px] font-black text-white/30 uppercase tracking-widest italic">Evidencia fotográfica y trazabilidad de riesgos.</p>
                    </div>
                    <div class="w-24 h-24 bg-red-500/5 rounded-full blur-2xl group-hover:bg-red-500/20 transition-all"></div>
                </div>

                <!-- Firmas Digitales HUD -->
                <div class="md:col-span-6 glass-card p-10 rounded-[3rem] flex items-center justify-between group">
                    <div class="space-y-4">
                        <div class="w-12 h-12 bg-indigo-500/10 rounded-2xl flex items-center justify-center border border-indigo-500/20">
                            <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </div>
                        <h4 class="text-xl font-black italic uppercase leading-none">Validación Remota</h4>
                        <p class="text-[10px] font-black text-white/30 uppercase tracking-widest italic">Cierre de visitas con firma en pantalla o enlace encriptado.</p>
                    </div>
                    <div class="w-24 h-24 bg-indigo-500/5 rounded-full blur-2xl group-hover:bg-indigo-500/20 transition-all"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- OPERATIVIDAD: EL CICLO HUD -->
    <section id="proceso" class="py-32 relative overflow-hidden">
        <div class="absolute inset-0 bg-ep-dark"></div>
        <div class="max-w-[95rem] mx-auto px-10 relative z-10">
            <div class="text-center mb-32">
                <h2 class="text-[10px] font-black text-ep-primary tracking-[0.6em] uppercase italic mb-4">Flow Operativo</h2>
                <h3 class="text-5xl font-black italic tracking-tighter uppercase">EL CICLO <span class="text-white/20">XAMASAFE</span></h3>
            </div>
            
            <div class="grid md:grid-cols-3 gap-16 relative">
                <!-- Connector Line -->
                <div class="absolute top-10 left-0 w-full h-px bg-gradient-to-r from-transparent via-white/5 to-transparent hidden md:block"></div>
                
                <div class="relative group">
                    <div class="text-8xl font-black text-white/5 absolute -top-16 left-0 italic group-hover:text-ep-primary/10 transition-colors">01</div>
                    <div class="relative z-10 space-y-6">
                        <h4 class="text-2xl font-black italic uppercase tracking-tight text-white group-hover:text-ep-primary transition-colors">Planificación</h4>
                        <div class="h-1 w-12 bg-white/10 group-hover:w-24 group-hover:bg-ep-primary transition-all"></div>
                        <p class="text-xs font-bold text-white/40 uppercase tracking-widest italic leading-relaxed">GESTIONA EL CONTRATO ANUAL Y ASIGNA VISITAS A TÉCNICOS INTEGRANDO LA LOGÍSTICA CORPORATIVA.</p>
                    </div>
                </div>

                <div class="relative group">
                    <div class="text-8xl font-black text-white/5 absolute -top-16 left-0 italic group-hover:text-ep-primary/10 transition-colors">02</div>
                    <div class="relative z-10 space-y-6">
                        <h4 class="text-2xl font-black italic uppercase tracking-tight text-white group-hover:text-ep-primary transition-colors">Ejecución</h4>
                        <div class="h-1 w-12 bg-white/10 group-hover:w-24 group-hover:bg-ep-primary transition-all"></div>
                        <p class="text-xs font-bold text-white/40 uppercase tracking-widest italic leading-relaxed">OPERACIONES EN CAMPO: CHECKLISTS DINÁMICOS, HALLAZGOS Y REGISTRO FORENSE EN TIEMPO REAL.</p>
                    </div>
                </div>

                <div class="relative group">
                    <div class="text-8xl font-black text-white/5 absolute -top-16 left-0 italic group-hover:text-ep-primary/10 transition-colors">03</div>
                    <div class="relative z-10 space-y-6">
                        <h4 class="text-2xl font-black italic uppercase tracking-tight text-white group-hover:text-ep-primary transition-colors">Inteligencia</h4>
                        <div class="h-1 w-12 bg-white/10 group-hover:w-24 group-hover:bg-ep-primary transition-all"></div>
                        <p class="text-xs font-bold text-white/40 uppercase tracking-widest italic leading-relaxed">CONSOLIDACIÓN AUTOMÁTICA DE DATOS EN INFORMES MAESTROS CON RESPALDO DE TRAZABILIDAD SEllADA.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ACTIVACIÓN FINAL HUD -->
    <section id="contacto" class="py-40">
        <div class="max-w-6xl mx-auto px-10">
            <div class="relative bg-ep-primary rounded-[4rem] p-20 text-center overflow-hidden shadow-2xl shadow-ep-primary/20 group">
                <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-10"></div>
                <div class="absolute -top-40 -left-40 w-96 h-96 bg-white/20 rounded-full blur-[100px] group-hover:blur-[80px] transition-all duration-1000"></div>
                <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-ep-dark rounded-full blur-[100px] group-hover:blur-[80px] transition-all duration-1000"></div>
                
                <div class="relative z-10">
                    <h2 class="text-5xl md:text-7xl font-black text-ep-dark italic tracking-tighter uppercase leading-none mb-10">ELEVA EL ESTÁNDAR DE <br>TU CONSULTORA.</h2>
                    <p class="text-ep-dark/60 text-lg font-black uppercase tracking-widest italic mb-12 max-w-2xl mx-auto">ÚNETE A LA TRANSFORMACIÓN DIGITAL DE LA SEGURIDAD INDUSTRIAL. CONTROL TOTAL, CUMPLIMIENTO GARANTIZADO.</p>
                    <a href="{{ route('login') }}" class="inline-flex bg-ep-dark text-white px-12 py-6 rounded-[2.5rem] text-xs font-black uppercase tracking-[0.3em] italic hover:scale-110 active:scale-95 transition-all shadow-2xl">
                        Activar Acceso Plataforma
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer HUD -->
    <footer class="bg-ep-dark pt-32 pb-16 border-t border-white/5 relative overflow-hidden">
        <div class="max-w-[95rem] mx-auto px-10">
            <div class="grid md:grid-cols-4 gap-20 mb-24">
                <div class="col-span-2">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-10 h-10 bg-ep-primary rounded-xl flex items-center justify-center group">
                            <span class="text-white font-black text-xl italic">X</span>
                        </div>
                        <span class="font-black text-2xl tracking-tighter italic">Xama<span class="text-ep-primary">Safe</span></span>
                    </div>
                    <p class="text-white/20 text-xs font-bold uppercase tracking-widest italic max-w-sm leading-relaxed">REDEFINIENDO LA PREVENCIÓN ESTRATÉGICA MEDIANTE TECNOLOGÍA, DATOS Y TRAZABILIDAD SEllADA DESDE 2026.</p>
                </div>
                
                <div>
                    <h5 class="text-[10px] font-black text-white/10 uppercase tracking-[0.4em] italic mb-10">Recursos</h5>
                    <ul class="space-y-4 text-[10px] font-black uppercase tracking-widest italic text-white/40">
                        <li><a href="#soluciones" class="hover:text-ep-primary transition-colors">Módulos</a></li>
                        <li><a href="#proceso" class="hover:text-ep-primary transition-colors">Operaciones</a></li>
                        <li><a href="{{ route('login') }}" class="hover:text-ep-primary transition-colors">Terminal</a></li>
                    </ul>
                </div>

                <div>
                    <h5 class="text-[10px] font-black text-white/10 uppercase tracking-[0.4em] italic mb-10">Conectividad</h5>
                    <ul class="space-y-4 text-[10px] font-black uppercase tracking-widest italic text-white/40">
                        <li><a href="mailto:soporte@xamasafe.com" class="hover:text-ep-primary transition-colors">Soporte Central</a></li>
                        <li>Privacidad</li>
                    </ul>
                </div>
            </div>
            
            <div class="flex flex-col md:flex-row justify-between items-center gap-8 pt-12 border-t border-white/5">
                <p class="text-[9px] font-black text-white/10 uppercase tracking-[0.5em] italic">&copy; 2026 XamaSafe Ecosystem • Engineered for Prevention Experts</p>
                <div class="flex gap-10">
                    <div class="flex items-center gap-2">
                        <span class="w-1 h-1 bg-emerald-500 rounded-full animate-pulse shadow-[0_0_10px_#10b981]"></span>
                        <span class="text-[8px] font-black text-white/20 uppercase tracking-widest italic">Core Status: Operational</span>
                    </div>
                    <span class="text-[8px] font-black text-white/20 uppercase tracking-widest italic">Encrypted Connection Valid</span>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>