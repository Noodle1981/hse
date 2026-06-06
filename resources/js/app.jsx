import { createRoot } from 'react-dom/client';
import { createInertiaApp } from '@inertiajs/react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { route } from 'ziggy-js';
import '../css/app.css';

window.route = route;

createInertiaApp({
    // Nombre de la pestaña del browser: "Login | XamaSafe"
    title: (title) => `${title} | XamaSafe`,

    // Resuelve automáticamente Pages/Auth/Login.jsx, Pages/Visits/InProgress.jsx, etc.
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.jsx`,
            import.meta.glob('./Pages/**/*.jsx')
        ),

    setup({ el, App, props }) {
        createRoot(el).render(<App {...props} />);
    },

    progress: {
        color: '#F69239', // Color naranja de la marca XamaSafe
    },
});
