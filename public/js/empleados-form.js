console.log('JS FUNCIONA');

// ================== FLATPICKR (CREATE + EDIT) ==================
document.addEventListener('DOMContentLoaded', function () {
    if (window.flatpickr) {
        flatpickr("#fecha_nacimiento", {
            dateFormat: "Y-m-d",
            maxDate: "today",
            locale: "es",
            altInput: true,
            altFormat: "d/m/Y",
            theme: "material_blue"
        });
        flatpickr("#fecha_ingreso", {
            dateFormat: "Y-m-d",
            locale: "es",
            altInput: true,
            altFormat: "d/m/Y",
            theme: "material_blue"
        });
    }
});


// ================== MOSTRAR / OCULTAR SECCIÓN DOCTOR (CREATE) ==================
document.addEventListener('DOMContentLoaded', function () {
    const cargoSelect        = document.getElementById('cargo_select');
    const seccionDoctor      = document.getElementById('seccion_doctor');
    const esDoctorInput      = document.getElementById('es_doctor');
    const numeroColegiatura  = document.getElementById('numero_colegiatura');
    const especialidadSelect = document.getElementById('especialidad_select');

    if (!cargoSelect || !seccionDoctor || !esDoctorInput) return;

    function actualizarDoctor() {
        const opt = cargoSelect.options[cargoSelect.selectedIndex];
        const esDoctor = opt.dataset.esDoctor === '1';

        if (esDoctor) {
            seccionDoctor.classList.remove('hidden');
            esDoctorInput.value = '1';

            if (numeroColegiatura)  numeroColegiatura.required  = true;
            if (especialidadSelect) especialidadSelect.required = true;
        } else {
            seccionDoctor.classList.add('hidden');
            esDoctorInput.value = '0';

            if (numeroColegiatura) {
                numeroColegiatura.required = false;
                numeroColegiatura.value    = '';
            }
            if (especialidadSelect) {
                especialidadSelect.required = false;
                especialidadSelect.value    = '';
            }
            const bio = document.querySelector('textarea[name="Biografia"]');
            if (bio) bio.value = '';

            const horariosContainer = document.getElementById('horarios-container');
            if (horariosContainer) {
                horariosContainer.innerHTML = '';
            }
        }
    }

    // Inicializar estado (por si hay old)
    actualizarDoctor();

    // Cambiar cuando seleccione cargo
    cargoSelect.addEventListener('change', actualizarDoctor);

    // Botón reset
    const form = document.getElementById('formEmpleado');
    if (form) {
        const limpiarBtn = form.querySelector('button[type="reset"]');
        if (limpiarBtn) {
            limpiarBtn.addEventListener('click', function () {
                seccionDoctor.classList.add('hidden');
                esDoctorInput.value = '0';
                const horariosContainer = document.getElementById('horarios-container');
                if (horariosContainer) horariosContainer.innerHTML = '';
            });
        }
    }
});

// ================== EDICIÓN: HORARIOS EXISTENTES + NUEVOS ==================
document.addEventListener('DOMContentLoaded', function () {
    let horariosEliminados = [];
    const contenedorHorariosEdit = document.getElementById('horarios-edit-container');
    const inputEliminados        = document.getElementById('horarios_eliminados');

    if (contenedorHorariosEdit && inputEliminados) {
        contenedorHorariosEdit.addEventListener('click', function (e) {
            if (e.target.classList.contains('eliminar-horario-existente')) {
                const item = e.target.closest('.horario-edit-item');
                const idInput = item.querySelector('input[name^="horarios_existentes"][name$="[id]"]');
                if (idInput) {
                    horariosEliminados.push(idInput.value);
                    inputEliminados.value = horariosEliminados.join(',');
                }
                item.remove();
            }
        });
    }

    const btnAgregarHorario       = document.getElementById('btn-agregar-horario');
    const contenedorHorariosNuevos= document.getElementById('horarios-nuevos-container');
    let idxNuevo = 0;

    if (btnAgregarHorario && contenedorHorariosNuevos) {
        btnAgregarHorario.addEventListener('click', function () {
            const div = document.createElement('div');
            div.className = 'horario-nuevo-item rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 shadow-sm space-y-3';

            div.innerHTML = `
                <div class="flex items-center justify-between">
                    <p class="text-xs font-medium text-slate-700">
                        Nuevo bloque de horario
                    </p>
                    <button type="button"
                            class="eliminar-horario-nuevo inline-flex items-center gap-1 rounded-full bg-red-50 px-2 py-1 text-[11px] font-medium text-red-600 hover:bg-red-100">
                        ✕ Eliminar
                    </button>
                </div>

                <div>
                    <p class="text-[11px] font-medium text-slate-600 mb-1">
                        Días de atención *
                    </p>
                    <div class="flex flex-wrap gap-2 text-xs">
                        ${['Lun','Mar','Mié','Jue','Vie','Sáb','Dom'].map((label, i) => {
                            const valores = ['lun','mar','mie','jue','vie','sab','dom'];
                            return `
                            <label class="inline-flex items-center gap-1 rounded-full border border-slate-200 bg-white px-2.5 py-1 cursor-pointer hover:border-indigo-400 hover:text-indigo-700">
                                <input
                                    type="checkbox"
                                    name="horarios_nuevos[${idxNuevo}][dias][]"
                                    value="${valores[i]}"
                                    class="h-3 w-3 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                >
                                <span>${label}</span>
                            </label>`;
                        }).join('')}
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[11px] font-medium text-slate-600 mb-1">Hora inicio *</label>
                        <input
                            type="time"
                            name="horarios_nuevos[${idxNuevo}][hora_inicio]"
                            class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm
                                focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                        >
                    </div>
                    <div>
                        <label class="block text-[11px] font-medium text-slate-600 mb-1">Hora fin *</label>
                        <input
                            type="time"
                            name="horarios_nuevos[${idxNuevo}][hora_fin]"
                            class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm
                                focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500"
                        >
                    </div>
                </div>
            `;
            contenedorHorariosNuevos.appendChild(div);
            idxNuevo++;
        });

        contenedorHorariosNuevos.addEventListener('click', function (e) {
            if (e.target.closest('.eliminar-horario-nuevo')) {
                const item = e.target.closest('.horario-nuevo-item');
                if (item) item.remove();
            }
        });
    }
});

// ================== NUEVO DISEÑO DE HORARIOS (CREATE) ==================
document.addEventListener('DOMContentLoaded', () => {
    const btnAdd     = document.getElementById('add-horario');
    const container  = document.getElementById('horarios-container');
    const template   = document.getElementById('horario-template');
    const selectCargo= document.getElementById('cargo_select');
    const inputEsDoctor = document.getElementById('es_doctor');

    if (!btnAdd || !container || !template || !selectCargo || !inputEsDoctor) return;

    let index = 0;

    function addHorario() {
        const clone = template.content.cloneNode(true);

        const inputs = clone.querySelectorAll('input[type="checkbox"], input[type="time"]');
        inputs.forEach(input => {
            if (input.name === '__NAME__DIAS__') {
                input.name = `horarios[${index}][dias][]`;
            } else if (input.name === '__NAME__HORA_INICIO__') {
                input.name = `horarios[${index}][hora_inicio]`;
            } else if (input.name === '__NAME__HORA_FIN__') {
                input.name = `horarios[${index}][hora_fin]`;
            }
        });

        container.appendChild(clone);
        index++;
    }

    function actualizarSegunCargo() {
        const opt = selectCargo.options[selectCargo.selectedIndex];
        const esDoctor = opt.dataset.esDoctor === '1';
        inputEsDoctor.value = esDoctor ? 1 : 0;

        if (!esDoctor) {
            // no es doctor: limpiar horarios
            container.innerHTML = '';
            index = 0;
        } else {
            // si es doctor y no hay horarios aún, crear uno por defecto
            if (container.children.length === 0) {
                addHorario();
            }
        }
    }

    btnAdd.addEventListener('click', addHorario);

    container.addEventListener('click', (e) => {
        const btn = e.target.closest('.btn-remove-horario');
        if (btn) {
            const item = btn.closest('.horario-item');
            if (item) item.remove();
        }
    });

    // Inicializar estado al cargar
    actualizarSegunCargo();
    selectCargo.addEventListener('change', actualizarSegunCargo);
});
