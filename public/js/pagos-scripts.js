// public/js/pagos-scripts.js

document.addEventListener('DOMContentLoaded', () => {

    // BÚSQUEDA INCREMENTAL (DNI o ID CITA)

    const inputBuscar = document.getElementById('buscar-cita-live');
    const tabla       = document.getElementById('tabla-citas');

    if (inputBuscar && tabla) {
        inputBuscar.addEventListener('input', function () {
            const term = this.value.trim().toLowerCase();

            tabla.querySelectorAll('tr').forEach(tr => {
                // Datos en atributos data-*
                const dni    = (tr.dataset.dni || '').toLowerCase();
                const idcita = (tr.dataset.idcita || '').toLowerCase();

                // Fila sin datos reales (por ejemplo, "No hay citas...")
                if (!dni && !idcita) return;

                const match =
                    term === '' ||
                    dni.includes(term) ||
                    idcita.includes(term);

                tr.classList.toggle('hidden', !match);
            });
        });
    }
    // 2. SELECCIONAR CITA Y CÁLCULOS DE PAGO

    const montoTotalInput   = document.getElementById('input-monto-cita'); // Monto de la cita (readonly)
    const montoInput        = document.getElementById('monto-pago');       // Monto que el paciente paga ahora
    const estadoPagoInput   = document.getElementById('estado-pago');      // EstadoPago
    const saldoPendienteLbl = document.getElementById('saldo-pendiente');  // Texto "Saldo pendiente"
    let   abonadoActual     = 0;                                           // Lo ya pagado en pagos previos

    function actualizarSaldo() {
        const total      = parseFloat(montoTotalInput.value) || 0;
        const pagadoNow  = parseFloat(montoInput.value) || 0;
        const saldoAntes = total - abonadoActual;
        const saldoDesp  = saldoAntes - pagadoNow;

        // Actualizar leyenda de saldo pendiente
        if (saldoPendienteLbl) {
            const saldoMostrar = Math.max(saldoDesp, 0);
            saldoPendienteLbl.textContent =
                `Saldo pendiente: S/ ${saldoMostrar.toFixed(2)}`;
        }

        // Actualizar EstadoPago: Pagado si cubre todo el saldo, en otro caso Pendiente
        if (estadoPagoInput) {
            if (saldoDesp <= 0.009) {
                estadoPagoInput.value = 'Pagado';
            } else {
                estadoPagoInput.value = 'Pendiente';
            }
        }
    }

    // Manejar click en "Seleccionar" de cada fila
    document.querySelectorAll('.select-cita').forEach(btn => {
        btn.addEventListener('click', function () {
            const id    = this.dataset.id;
            const monto = parseFloat(this.dataset.monto) || 0;
            abonadoActual = parseFloat(this.dataset.abonado) || 0;

            // Llenar campos ocultos / visibles
            const inputIdCita = document.getElementById('input-id-cita');
            const descCita    = document.getElementById('desc-cita');
            const infoCitaBox = document.getElementById('info-cita');

            if (inputIdCita) inputIdCita.value = id;
            if (montoTotalInput) montoTotalInput.value = monto.toFixed(2);

            if (descCita) {
                descCita.textContent =
                    `${this.dataset.paciente} | Fecha: ${this.dataset.fecha} | Monto: S/ ${monto.toFixed(2)}`;
            }

            if (infoCitaBox) infoCitaBox.classList.remove('hidden');

            // Resetea el monto a pagar actual y estado
            if (montoInput) montoInput.value = '';
            if (estadoPagoInput) estadoPagoInput.value = 'Pendiente';

            // Calcula saldo inicial
            actualizarSaldo();
        });
    });

    // Recalcular saldo y estado cada vez que se escribe el monto de pago
    if (montoInput) {
        montoInput.addEventListener('input', actualizarSaldo);
    }


   window.printArea = function () {
    // 1. Obtener elementos (paciente / cita / pago)
    const pacienteNombreEl    = document.getElementById('paciente_nombre');     // Nombre del cliente
    const pacienteDniEl       = document.getElementById('paciente_dni');        // DNI
    const pacienteEdadEl      = document.getElementById('paciente_edad');       // Edad
    const pacienteDireccionEl = document.getElementById('paciente_direccion');  // Dirección

    const fechaPagoEl         = document.getElementById('fecha_pago');          // Fecha pago (puede ser igual a fecha_recibo)
    const numReciboEl         = document.getElementById('num_recibo');          // Código de pago (IdPago)
    const numPedidoEl         = document.getElementById('num_pedido');          // Código de cita (IdCita)

    const doctorNombreEl      = document.getElementById('doctor_nombre');       // Nombre del doctor
    const especialidadEl      = document.getElementById('especialidad_nombre'); // Especialidad
    const costoEspecialidadEl = document.getElementById('costo_especialidad');  // Costo tipo de cita/especialidad

    const subtotalEl          = document.getElementById('subtotal');            // Subtotal
    const igvEl               = document.getElementById('igv');                 // IGV
    const totalPagoEl         = document.getElementById('total_pago');          // Total

    // Debug opcional
    console.log({
        pacienteNombreEl,
        pacienteDniEl,
        pacienteEdadEl,
        pacienteDireccionEl,
        fechaPagoEl,
        numReciboEl,
        numPedidoEl,
        doctorNombreEl,
        especialidadEl,
        costoEspecialidadEl,
        subtotalEl,
        igvEl,
        totalPagoEl,
    });

    // 2. Validar que existan los esenciales
    if (
        !pacienteNombreEl || !pacienteDniEl || !numReciboEl || !numPedidoEl ||
        !doctorNombreEl || !especialidadEl || !subtotalEl || !igvEl || !totalPagoEl
    ) {
        alert('Faltan datos en el comprobante (revisa los ids en la vista pagos.show).');
        return;
    }

    // 3. Tomar valores
    const clienteNombre    = pacienteNombreEl.innerText.trim();
    const clienteDni       = pacienteDniEl.innerText.trim();
    const clienteEdad      = pacienteEdadEl ? pacienteEdadEl.innerText.trim() : '';
    const clienteDireccion = pacienteDireccionEl ? pacienteDireccionEl.innerText.trim() : '';

    const fechaPago  = fechaPagoEl ? fechaPagoEl.innerText.trim() : '';
    const numRecibo  = numReciboEl.innerText.trim();    // Código de pago
    const numPedido  = numPedidoEl.innerText.trim();    // Código de cita
    const fechaVenc  = fechaPago;                       // Si quieres otra lógica, cámbiala

    const doctorNombre = doctorNombreEl.innerText.trim();
    const especialidad = especialidadEl.innerText.trim();
    const costoEspe    = costoEspecialidadEl ? costoEspecialidadEl.innerText.trim() : '';

    const subtotal = subtotalEl.innerText.trim();
    const iva      = igvEl.innerText.trim();
    const total    = totalPagoEl.innerText.trim();

    // 4. Items desde detalles (Cantidad / Descripción / Precio / Importe)
    const filas = document.querySelectorAll('#tabla-items tbody tr');
    let filasHtml = '';
    filas.forEach((tr) => {
        const cant    = tr.querySelector('.col-cant')?.innerText.trim()    ?? '';
        const desc    = tr.querySelector('.col-desc')?.innerText.trim()    ?? '';
        const precio  = tr.querySelector('.col-precio')?.innerText.trim()  ?? '';
        const importe = tr.querySelector('.col-importe')?.innerText.trim() ?? '';

        filasHtml += `
            <tr>
                <td style="text-align:center;">${cant}</td>
                <td>${desc}</td>
                <td style="text-align:right;">${precio}</td>
                <td style="text-align:right;">${importe}</td>
            </tr>
        `;
    });

    // 5. Plantilla HTML del recibo (con todos los campos nuevos)
    const reciboHTML = `
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Recibo de pago</title>
    <style>
        body{
            font-family: Arial, sans-serif;
            margin:40px;
            color:#333;
            font-size:12px;
        }
        h1{
            font-size:32px;
            margin:0 0 10px 0;
            color:#1b2a4e;
        }
        .encabezado{
            display:flex;
            justify-content:space-between;
            align-items:flex-start;
        }
        .empresa{
            max-width:45%;
        }
        .logo{
            width:80px;
            height:80px;
            border-radius:50%;
            background:#ccc;
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:14px;
            overflow:hidden;
        }
        .logo img{
            width:100%;
            height:100%;
            object-fit:cover;
        }
        .secciones{
            display:flex;
            justify-content:space-between;
            margin-top:20px;
            font-size:11px;
        }
        .bloque{
            width:32%;
        }
        .bloque h3{
            margin:0 0 5px 0;
            font-size:11px;
            text-transform:uppercase;
            color:#1b2a4e;
        }
        .bloque p{
            margin:0;
        }
        .datos-recibo{
            width:35%;
            text-align:right;
        }
        .datos-recibo p{
            margin:2px 0;
        }
        table{
            width:100%;
            border-collapse:collapse;
            margin-top:25px;
        }
        th, td{
            padding:6px 4px;
        }
        thead th{
            border-bottom:2px solid #c0392b;
            color:#1b2a4e;
            font-size:11px;
        }
        tbody td{
            border-bottom:1px solid #eee;
        }
        .totales{
            margin-top:15px;
            width:100%;
            text-align:right;
        }
        .totales p{
            margin:2px 0;
        }
        .total{
            font-weight:bold;
            font-size:14px;
            margin-top:5px;
        }
        .firma{
            margin-top:40px;
            text-align:right;
            font-family:"Brush Script MT", cursive;
            font-size:24px;
        }
        @media print{
            @page{ margin:15mm; }
        }
    </style>
</head>
<body>
    <div class="encabezado">
        <div class="empresa">
            <h1>RECIBO</h1>
            <p><strong>Clínica</strong></p>
            <p>Dirección de la clínica</p>
        </div>

        <div class="logo">
            <!-- si quieres puedes reemplazar por ruta absoluta -->
            <img src="${window.location.origin}/images/login-medicos01.png" alt="Logo clínica">
        </div>
    </div>

    <div class="secciones">
        <div class="bloque">
            <h3>Paciente</h3>
            <p><strong>Nombre:</strong> ${clienteNombre}</p>
            <p><strong>DNI:</strong> ${clienteDni}</p>
            <p><strong>Edad:</strong> ${clienteEdad}</p>
            <p><strong>Dirección:</strong> ${clienteDireccion}</p>
        </div>

        <div class="bloque">
            <h3>Cita / Atención</h3>
            <p><strong>Código cita:</strong> ${numPedido}</p>
            <p><strong>Doctor:</strong> ${doctorNombre}</p>
            <p><strong>Especialidad:</strong> ${especialidad}</p>
            <p><strong>Costo especialidad:</strong> ${costoEspe}</p>
        </div>

        <div class="datos-recibo">
            <p><strong>Código de pago:</strong> ${numRecibo}</p>
            <p><strong>Fecha de pago:</strong> ${fechaPago}</p>
            <p><strong>Fecha venc.:</strong> ${fechaVenc}</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:10%;">CANT.</th>
                <th style="width:50%;">DETALLE DE PAGO</th>
                <th style="width:20%; text-align:right;">PRECIO UNITARIO</th>
                <th style="width:20%; text-align:right;">IMPORTE</th>
            </tr>
        </thead>
        <tbody>
            ${filasHtml}
        </tbody>
    </table>

    <div class="totales">
        <p>Sub total S/ ${subtotal}</p>
        <p>IGV 18% S/ ${iva}</p>
        <p class="total">TOTAL S/ ${total}</p>
    </div>

    <div class="firma">
        Firma
    </div>
</body>
</html>
    `;

    // 6. Ventana de impresión
    const win = window.open('', '_blank', 'width=800,height=600');
    win.document.open();
    win.document.write(reciboHTML);
    win.document.close();
    win.focus();
    win.print();
    win.close();
};
});
