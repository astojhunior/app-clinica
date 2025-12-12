// public/js/pagos-index.js

document.addEventListener('DOMContentLoaded', () => {
    const inputBuscar = document.getElementById('buscar-pagos-live');
    const tabla       = document.getElementById('tabla-pagos');

    if (!inputBuscar || !tabla) return;

    inputBuscar.addEventListener('input', function () {
        const term = this.value.trim().toLowerCase();

        tabla.querySelectorAll('tr').forEach(tr => {
            const dni    = (tr.dataset.dni || '').toLowerCase();
            const idcita = (tr.dataset.idcita || '').toLowerCase();
            const idPago = (tr.children[0]?.textContent || '').toLowerCase();

            // Fila de "no hay pagos"
            if (!dni && !idcita && !idPago) return;

            const match =
                term === '' ||
                dni.includes(term) ||
                idcita.includes(term) ||
                idPago.includes(term);

            tr.classList.toggle('hidden', !match);
        });
    });
});
