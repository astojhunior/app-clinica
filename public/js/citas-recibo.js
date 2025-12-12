
    // 2. Plantilla tipo constancia (similar a tu imagen)


function imprimirCita() {
    const d = window.citaData;

    const html = `
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <title>Cita Programada</title>
        <style>
            body{
                font-family: "Times New Roman", serif;
                margin:40px 60px;
                color:#000;
                font-size:13px;
            }
            h1{
                text-align:center;
                margin-bottom:25px;
                font-size:22px;
                text-transform:uppercase;
            }
            .fila{
                display:flex;
                justify-content:space-between;
                margin-bottom:8px;
            }
            .etiqueta{
                font-weight:bold;
            }
            .seccion{
                margin-top:18px;
            }
            .seccion-title{
                font-weight:bold;
                margin-bottom:5px;
            }
            .bloque-texto{
                margin-top:18px;
                text-align:justify;
                line-height:1.4;
            }
            hr{
                border:none;
                border-top:1px solid #c00;
                margin:25px 0;
            }
            .pie{
                margin-top:15px;
                font-size:11px;
            }
            .codigo{
                margin-top:15px;
                font-weight:bold;
            }
            @media print{
                @page{ margin:15mm 20mm; }
            }
        </style>
    </head>
    <body>
        <h1>CITA PROGRAMADA</h1>

        <div class="fila">
            <div>
                <span class="etiqueta">Clínica:</span> CLÍNICA CALIDAD<br>
                <span class="etiqueta">Oficina:</span> Consultorios Externos<br>
                <span class="etiqueta">Sede:</span> Dirección completa de la sede
            </div>
            <div style="text-align:right;">
                <span class="etiqueta">Fecha de la Cita:</span> ${fechaCita}<br>
                <span class="etiqueta">Hora de la Cita:</span> ${horaCita}<br>
                <span class="etiqueta">Código de Cita:</span> ${codigoCita}
            </div>
        </div>

        <div class="seccion">
            <div class="seccion-title">Datos del Paciente</div>
            <div><span class="etiqueta">Nombre y Apellidos:</span> ${nombrePaciente}</div>
            <div><span class="etiqueta">Documento de Identidad:</span> ${dniPaciente}</div>
            <div><span class="etiqueta">Edad:</span> ${edadPaciente}</div>
            <div><span class="etiqueta">Teléfono:</span> ${telPaciente}</div>
            <div><span class="etiqueta">Email:</span> ${emailPaciente}</div>
        </div>

        <div class="seccion">
            <div class="seccion-title">Datos del Doctor</div>
            <div><span class="etiqueta">Médico:</span> ${nombreDoctor}</div>
            <div><span class="etiqueta">Especialidad:</span> ${especialidad}</div>
            <div><span class="etiqueta">CMP:</span> ${cmpDoctor}</div>
        </div>

        <div class="seccion">
            <div class="seccion-title">Detalles de la Cita</div>
            <div><span class="etiqueta">Tipo de Cita:</span> ${tipoCita}</div>
            <div><span class="etiqueta">Modalidad:</span> ${modalidad}</div>
            <div><span class="etiqueta">Motivo:</span> ${motivo}</div>
            <div><span class="etiqueta">Estado de la Cita:</span> ${estadoCita}</div>
        </div>

        <div class="seccion">
            <div class="seccion-title">Información de Pago</div>
            <div><span class="etiqueta">Costo Total:</span> ${costoTotal}</div>
            <div><span class="etiqueta">Estado de Pago:</span> ${estadoPago}</div>
        </div>

        <div class="bloque-texto">
            <span class="etiqueta">Nota:</span> La asignación de esta cita es personal e intransferible. El paciente deberá presentarse en la clínica en la fecha y hora indicadas, portando su documento de identidad. En caso de no asistir o llegar fuera del horario establecido, la cita podrá ser reprogramada según la disponibilidad.
        </div>

        <div class="codigo">
            Código de seguridad: ${codigoCita}-${estadoCita}
        </div>

        <hr>

        <div class="pie">
            Documento generado electrónicamente el ${generadoEl}. No requiere firma manuscrita.
        </div>
    </body>
    </html>`;

    const win = window.open('', '_blank', 'width=800,height=900');
    win.document.open();
    win.document.write(html);
    win.document.close();
    win.focus();
    win.print();
    win.close();
};
