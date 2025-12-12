import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();
window.mostrarNotificacion = function(mensaje, tipo = 'success') {
    const colores = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        info: 'bg-blue-500',
        warning: 'bg-yellow-500'
    };

    const iconos = {
        success: '✅',
        error: '❌',
        info: 'ℹ️',
        warning: '⚠️'
    };

    const notificacion = document.createElement('div');
    notificacion.className = `toast-notification fixed top-4 right-4 ${colores[tipo]} text-white px-6 py-3 rounded-lg shadow-lg z-50 flex items-center gap-2`;
    notificacion.innerHTML = `
        <span class="text-xl">${iconos[tipo]}</span>
        <span>${mensaje}</span>
    `;

    document.body.appendChild(notificacion);

    setTimeout(() => {
        notificacion.classList.add('hide');
        setTimeout(() => {
            notificacion.remove();
        }, 300);
    }, 3000);
}

// Función para limpiar formulario con notificación
window.limpiarConNotificacion = function() {
    const form = document.getElementById('formPaciente');
    if (!form) return;

    // Verificar si hay datos en el formulario
    const inputs = form.querySelectorAll('input, select, textarea');
    let tieneDatos = false;

    inputs.forEach(input => {
        if (input.value && input.value !== '') {
            tieneDatos = true;
        }
    });

    if (!tieneDatos) {
        mostrarNotificacion('No hay datos para limpiar', 'info');
        return;
    }

    // Limpiar formulario
    form.reset();

    // Limpiar Flatpickr
    const fechaInput = document.getElementById('fecha_nacimiento');
    if (fechaInput && fechaInput._flatpickr) {
        fechaInput._flatpickr.clear();
    }

    // Limpiar mensajes de error
    document.querySelectorAll('.text-red-500').forEach(el => el.remove());

    // Enfocar primer campo
    const primerCampo = form.querySelector('input[name="Dni"]');
    if (primerCampo) primerCampo.focus();

    // Mostrar notificación
    mostrarNotificacion('Formulario limpiado correctamente', 'success');
}
