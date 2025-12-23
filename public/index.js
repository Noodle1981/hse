// Espera a que el DOM esté completamente cargado
document.addEventListener('DOMContentLoaded', function() {

    // 1. Inicializar AOS (Animate On Scroll) - Sin cambios
    AOS.init({
        duration: 800,
        once: true,
        offset: 50,
    });

    // 2. Actualizar dinámicamente el año en el footer - Sin cambios
    const yearSpan = document.getElementById('year');
    if (yearSpan) {
        yearSpan.textContent = new Date().getFullYear();
    }

    // 3. NAVEGACIÓN ACTIVA CON INTERSECTION OBSERVER (CORREGIDO)
    const sections = document.querySelectorAll('section[id]');
    // Seleccionamos solo los enlaces que apuntan a una sección de la misma página
    const navLinks = document.querySelectorAll('.navbar-nav .nav-link[href^="#"]');

    if (sections.length > 0 && navLinks.length > 0) {
        const observerOptions = {
            root: null,
            rootMargin: '0px',
            threshold: 0.4 
        };

        const sectionObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const sectionId = entry.target.id;
                    
                    navLinks.forEach(link => {
                        link.classList.remove('active');
                        // CORRECCIÓN: Comparamos el href del link con el ID de la sección
                        // ej. link.getAttribute('href') es "#nosotros" y '#' + sectionId también es "#nosotros"
                        if (link.getAttribute('href') === '#' + sectionId) {
                            link.classList.add('active');
                        }
                    });
                }
            });
        }, observerOptions);

        sections.forEach(section => {
            sectionObserver.observe(section);
        });
    }
    
    // 4. MANEJO DE FORMULARIOS REUTILIZABLE (MEJORADO)
    
    /**
     * Función genérica para manejar el envío de formularios de contacto.
     * @param {string} formId - El ID del formulario a manejar.
     * @param {string} feedbackId - El ID del elemento donde se mostrará el feedback.
     */
    const handleContactForm = (formId, feedbackId) => {
        const form = document.getElementById(formId);
        const feedbackEl = document.getElementById(feedbackId);
        const submitter = document.querySelector("button[type=submit]");
        
        // Si el formulario no existe en la página actual, no hacemos nada.
        if (!form || !feedbackEl) {
            return;
        }

        form.addEventListener('submit', function(e) {
            e.preventDefault(); // Prevenimos el envío tradicional
            const formData = new FormData(form);
            
            const dataToSend = Object.fromEntries(formData);
            console.log(dataToSend);
            const options = {
                method: 'POST', // or 'PUT', 'DELETE', etc.
                headers: {
                    'Content-Type': 'application/json' // Specify the content type of the data
                },
                body: JSON.stringify(dataToSend) // Convert the data object to a JSON string
            };

            feedbackEl.textContent = 'Enviando...';
            feedbackEl.className = 'mt-3'; // Resetea clases de color
            
            fetch('https://equipopreventivo.com.ar/api/consulta', options)
                .then(response => {
                    if (!response.ok) {
                        throw new Error("Error");
                    }
                })
                .then(data => {
                    feedbackEl.textContent = '¡Gracias! Nos pondremos en contacto contigo pronto.';
                    feedbackEl.style.color = '#198754'; // Verde de éxito
                })
                .catch(error => {
                    feedbackEl.textContent = 'Ocurrió un error. Por favor revisa los datos ingresados e intenta nuevamente.';
                    feedbackEl.style.color = '#dc3545'; // Rojo de error
                });
            
        });
    };

    // Aplicamos la función a TODOS los formularios que queramos manejar
    handleContactForm('contact-form-modal', 'form-feedback');               // Para la página principal
    handleContactForm('contact-form-agro', 'form-feedback-agro'); // Para la página de Agro
});