/**
 * ====================================================
 * SCRIPT PARA GESTIÓN DE CITAS MÉDICAS
 * Sistema de Gestión de Clínica - Laravel + SQL Server
 * ====================================================
 */

// ====================================================
// 1. INICIALIZACIÓN AL CARGAR EL DOCUMENTO
// ====================================================
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Sistema de Citas Médicas cargado');

    // Inicializar componentes
    inicializarSelect2();
    inicializarFlatpickr();
    inicializarValidaciones();
    restaurarSeleccionDoctor();

    console.log('✅ Todos los componentes inicializados correctamente');
});

// ====================================================
// 2. INICIALIZAR SELECT2 PARA PACIENTES
// ====================================================
function inicializarSelect2() {
    $('#paciente-select').select2({
        placeholder: 'Buscar por DNI o nombre...',
        allowClear: true,
        language: {
            noResults: function() {
                return "No se encontraron pacientes";
            },
            searching: function() {
                return "Buscando...";
            },
            inputTooShort: function() {
                return "Escribe para buscar...";
            }
        },
        width: '100%'
    });

    console.log('✓ Select2 inicializado');
}

// ====================================================
// 3. INICIALIZAR FLATPICKR PARA FECHA
// ====================================================
function inicializarFlatpickr() {
    flatpickr("#fecha-cita", {
        locale: "es",
        dateFormat: "Y-m-d",
        minDate: "today",
        altInput: true,
        altFormat: "d/m/Y",
        onChange: function(selectedDates, dateStr, instance) {
            console.log('📅 Fecha seleccionada:', dateStr);
        }
    });

    console.log('✓ Flatpickr inicializado');
}

// ====================================================
// 4. INICIALIZAR VALIDACIONES DEL FORMULARIO
// ====================================================
function inicializarValidaciones() {
    const horaInicio = document.getElementById('hora-inicio');
    const horaFin = document.getElementById('hora-fin');

    if (horaInicio && horaFin) {
        // Validar que hora fin sea mayor que hora inicio
        horaFin.addEventListener('change', function() {
            if (horaInicio.value && horaFin.value) {
                if (horaFin.value <= horaInicio.value) {
                    alert('⚠️ La hora de fin debe ser mayor que la hora de inicio');
                    horaFin.value = '';
                }
            }
        });

        horaInicio.addEventListener('change', function() {
            if (horaInicio.value && horaFin.value) {
                if (horaFin.value <= horaInicio.value) {
                    alert('⚠️ La hora de fin debe ser mayor que la hora de inicio');
                    horaFin.value = '';
                }
            }
        });
    }

    // Validar formulario antes de enviar
    const form = document.getElementById('formCita');
    if (form) {
        form.addEventListener('submit', function(e) {
            const doctorId = document.getElementById('doctor-seleccionado').value;

            if (!doctorId) {
                e.preventDefault();
                alert('⚠️ Por favor, seleccione un doctor de la lista');
                document.getElementById('doctor-warning').classList.remove('hidden');
                return false;
            }

            if (horaInicio.value && horaFin.value && horaFin.value <= horaInicio.value) {
                e.preventDefault();
                alert('⚠️ La hora de fin debe ser mayor que la hora de inicio');
                return false;
            }

            console.log('✓ Formulario validado correctamente');
        });
    }

    console.log('✓ Validaciones inicializadas');
}

// ====================================================
// 5. SELECCIONAR DOCTOR
// ====================================================
function seleccionarDoctor(idDoctor, nombreDoctor, especialidad, idEmpleado) {
    console.log('👨‍⚕️ Seleccionando doctor:', {
        id: idDoctor,
        nombre: nombreDoctor,
        especialidad: especialidad,
        empleado: idEmpleado
    });

    // Actualizar campos hidden
    document.getElementById('doctor-seleccionado').value = idDoctor;
    document.getElementById('nombre-doctor-temp').value = nombreDoctor;
    document.getElementById('especialidad-doctor-temp').value = especialidad;

    // Actualizar textos visibles
    document.getElementById('nombre-doctor-text').textContent = nombreDoctor;
    document.getElementById('especialidad-doctor-text').textContent = especialidad;

    // Mostrar/ocultar secciones
    document.getElementById('doctor-info').classList.remove('hidden');
    document.getElementById('doctor-warning').classList.add('hidden');

    // Remover selección anterior
    document.querySelectorAll('.doctor-card').forEach(card => {
        card.classList.remove('border-green-500', 'bg-green-50');
        const indicator = card.querySelector('.selected-indicator');
        if (indicator) {
            indicator.classList.add('hidden');
        }
    });

    // Marcar el doctor seleccionado
    const cardSeleccionada = document.querySelector(`[data-doctor-id="${idDoctor}"]`);
    if (cardSeleccionada) {
        cardSeleccionada.classList.add('border-green-500', 'bg-green-50');
        const indicator = cardSeleccionada.querySelector('.selected-indicator');
        if (indicator) {
            indicator.classList.remove('hidden');
        }

        // Scroll suave hacia el doctor seleccionado
        cardSeleccionada.scrollIntoView({
            behavior: 'smooth',
            block: 'nearest'
        });
    }

    console.log('✅ Doctor seleccionado correctamente');
}

// ====================================================
// 6. LIMPIAR SELECCIÓN DE DOCTOR
// ====================================================
function limpiarSeleccionDoctor() {
    console.log('🔄 Limpiando selección de doctor');

    // Limpiar campos hidden
    document.getElementById('doctor-seleccionado').value = '';
    document.getElementById('nombre-doctor-temp').value = '';
    document.getElementById('especialidad-doctor-temp').value = '';

    // Ocultar/mostrar secciones
    document.getElementById('doctor-info').classList.add('hidden');
    document.getElementById('doctor-warning').classList.remove('hidden');

    // Remover selección visual
    document.querySelectorAll('.doctor-card').forEach(card => {
        card.classList.remove('border-green-500', 'bg-green-50');
        const indicator = card.querySelector('.selected-indicator');
        if (indicator) {
            indicator.classList.add('hidden');
        }
    });

    console.log('✅ Selección limpiada');
}

// ====================================================
// 7. RESETEAR FORMULARIO COMPLETO
// ====================================================
function resetearFormulario() {
    console.log('🔄 Reseteando formulario completo');

    // Limpiar Select2
    $('#paciente-select').val(null).trigger('change');

    // Limpiar flatpickr
    const fechaInput = document.querySelector('#fecha-cita');
    if (fechaInput && fechaInput._flatpickr) {
        fechaInput._flatpickr.clear();
    }

    // Limpiar campos de tiempo
    document.getElementById('hora-inicio').value = '';
    document.getElementById('hora-fin').value = '';

    // Limpiar tipo de cita
    document.getElementById('tipo-cita').value = '';

    // Limpiar modalidad
    document.getElementById('modalidad').value = '';

    // Limpiar motivo
    document.getElementById('motivo-cita').value = '';

    // Limpiar selección de doctor
    limpiarSeleccionDoctor();

    console.log('✅ Formulario reseteado');
}

// ====================================================
// 8. FILTRAR DOCTORES
// ====================================================
function filtrarDoctores() {
    const especialidad = document.getElementById('filtro-especialidad').value;
    const dia = document.getElementById('filtro-dia').value;

    console.log('🔍 Filtrando doctores:', { especialidad, dia });

    const cards = document.querySelectorAll('.doctor-card');
    let visibles = 0;

    cards.forEach(card => {
        let mostrar = true;

        // Filtrar por especialidad
        if (especialidad && card.dataset.especialidad !== especialidad) {
            mostrar = false;
        }

        // Filtrar por día
        if (dia) {
            const diasDoctor = card.dataset.dias || '';
            if (!diasDoctor.includes(dia)) {
                mostrar = false;
            }
        }

        // Mostrar/ocultar
        if (mostrar) {
            card.classList.remove('hidden');
            visibles++;
        } else {
            card.classList.add('hidden');
        }
    });

    // Mostrar mensaje si no hay resultados
    const mensaje = document.getElementById('mensaje-no-doctores');
    if (mensaje) {
        if (visibles === 0) {
            mensaje.classList.remove('hidden');
        } else {
            mensaje.classList.add('hidden');
        }
    }

    console.log(`✅ Filtrado completo: ${visibles} doctores visibles de ${cards.length}`);
}

// ====================================================
// 9. RESTAURAR SELECCIÓN DE DOCTOR (después de error de validación)
// ====================================================
function restaurarSeleccionDoctor() {
    const doctorId = document.getElementById('doctor-seleccionado').value;
    const nombreDoctor = document.getElementById('nombre-doctor-temp').value;
    const especialidad = document.getElementById('especialidad-doctor-temp').value;

    if (doctorId && nombreDoctor) {
        console.log('🔄 Restaurando selección de doctor después de error de validación');

        // Marcar visualmente el doctor
        const cardSeleccionada = document.querySelector(`[data-doctor-id="${doctorId}"]`);
        if (cardSeleccionada) {
            cardSeleccionada.classList.add('border-green-500', 'bg-green-50');
            const indicator = cardSeleccionada.querySelector('.selected-indicator');
            if (indicator) {
                indicator.classList.remove('hidden');
            }
        }

        console.log('✅ Selección restaurada');
    }
}

// ====================================================
// 10. OBTENER HORARIOS DISPONIBLES (función auxiliar para futuras mejoras)
// ====================================================
async function obtenerHorariosDisponibles(doctorId, fecha) {
    console.log('🕐 Obteniendo horarios disponibles:', { doctorId, fecha });

    try {
        const response = await fetch('/citas/horarios-disponibles', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                doctorid: doctorId,
                fecha: fecha
            })
        });

        const data = await response.json();

        if (data.success) {
            console.log('✅ Horarios obtenidos:', data.horarios);
            return data.horarios;
        } else {
            console.warn('⚠️', data.message);
            return [];
        }
    } catch (error) {
        console.error('❌ Error al obtener horarios:', error);
        return [];
    }
}

// ====================================================
// 11. UTILIDADES GENERALES
// ====================================================

/**
 * Formatear hora en formato 12 horas
 */
function formatearHora12(hora24) {
    const [horas, minutos] = hora24.split(':');
    const h = parseInt(horas);
    const periodo = h >= 12 ? 'PM' : 'AM';
    const hora12 = h % 12 || 12;
    return `${hora12}:${minutos} ${periodo}`;
}

/**
 * Mostrar notificación temporal
 */
function mostrarNotificacion(mensaje, tipo = 'info') {
    const colores = {
        success: 'bg-green-100 border-green-400 text-green-700',
        error: 'bg-red-100 border-red-400 text-red-700',
        warning: 'bg-yellow-100 border-yellow-400 text-yellow-700',
        info: 'bg-blue-100 border-blue-400 text-blue-700'
    };

    const notificacion = document.createElement('div');
    notificacion.className = `fixed top-4 right-4 p-4 rounded-md border ${colores[tipo]} shadow-lg z-50 transition-all`;
    notificacion.textContent = mensaje;

    document.body.appendChild(notificacion);

    setTimeout(() => {
        notificacion.style.opacity = '0';
        setTimeout(() => notificacion.remove(), 300);
    }, 3000);
}

/**
 * Validar formato de hora
 */
function validarFormatoHora(hora) {
    const regex = /^([01]\d|2[0-3]):([0-5]\d)$/;
    return regex.test(hora);
}

/**
 * Calcular duración entre dos horas
 */
function calcularDuracion(horaInicio, horaFin) {
    const [hi, mi] = horaInicio.split(':').map(Number);
    const [hf, mf] = horaFin.split(':').map(Number);

    const minutoInicio = hi * 60 + mi;
    const minutoFin = hf * 60 + mf;

    const diferencia = minutoFin - minutoInicio;
    const horas = Math.floor(diferencia / 60);
    const minutos = diferencia % 60;

    return { horas, minutos, totalMinutos: diferencia };
}

// ====================================================
// 12. LOGS Y DEBUGGING
// ====================================================
console.log(`
╔════════════════════════════════════════╗
║  SISTEMA DE GESTIÓN DE CITAS MÉDICAS  ║
║         Laravel + SQL Server           ║
╚════════════════════════════════════════╝
✓ Script cargado correctamente
✓ Funciones disponibles:
  - seleccionarDoctor()
  - limpiarSeleccionDoctor()
  - resetearFormulario()
  - filtrarDoctores()
  - obtenerHorariosDisponibles()
`);
