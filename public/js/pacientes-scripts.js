document.addEventListener('DOMContentLoaded', function () {
    // Inicializar flatpickr para fecha de nacimiento
    if (window.flatpickr) {
        flatpickr("#fecha_nacimiento", {
            dateFormat: "Y-m-d",
            maxDate: "today",
            locale: "es",
            altInput: true,
            altFormat: "d/m/Y",
            theme: "material_blue"
        });
    }

    // Función global para el botón "Limpiar Campos"
    window.limpiarConNotificacion = function () {
        const form = document.getElementById('formPaciente');
        if (!form) return;

        form.reset();

        const inputFecha = document.getElementById('fecha_nacimiento');
        if (inputFecha && inputFecha._flatpickr) {
            inputFecha._flatpickr.clear();
        }

        alert('Formulario limpiado.');
    };
});
