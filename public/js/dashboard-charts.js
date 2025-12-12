
document.addEventListener('DOMContentLoaded', () => {
    initPagosChart();
    initPacientesChart();
});

/**
 * Gráfico de línea: Pagos realizados
 */
function initPagosChart() {
    const canvas = document.getElementById('pagosChart');
    if (!canvas) return;

    const ctx = canvas.getContext('2d');

    // Datos desde atributos data-*
    let labels = canvas.getAttribute('data-labels');
    let values = canvas.getAttribute('data-values');

    try {
        labels = JSON.parse(labels);
        values = JSON.parse(values);
    } catch (e) {
        labels = [];
        values = [];
    }

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Pagos',
                data: values,
                borderColor: 'rgba(59,130,246,1)',     // azul
                backgroundColor: 'rgba(59,130,246,0.2)',
                pointBackgroundColor: 'rgba(59,130,246,1)',
                pointRadius: 4,
                pointHoverRadius: 6,
                tension: 0.35,                         // curva suave
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animations: {
                tension: {
                    duration: 1000,
                    easing: 'easeOutQuad',
                    from: 0.5,
                    to: 0.35,
                    loop: false
                },
                x: {
                    type: 'number',
                    easing: 'easeOutQuad',
                    duration: 800,
                    from: 0
                },
                y: {
                    type: 'number',
                    easing: 'easeOutCubic',
                    duration: 800,
                    from: 0
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: (ctx) => {
                            const value = ctx.parsed.y ?? 0;
                            return `S/ ${value.toFixed(2)}`;
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(209,213,219,0.5)'
                    }
                }
            }
        }
    });
}

/**
 * Gráfico pastel: Pacientes activos / inactivos
 */
function initPacientesChart() {
    const canvas = document.getElementById('pacientesChart');
    if (!canvas) return;

    const ctx = canvas.getContext('2d');

    const activos = parseInt(canvas.getAttribute('data-activos') || '0', 10);
    const inactivos = parseInt(canvas.getAttribute('data-inactivos') || '0', 10);

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Activos', 'Inactivos'],
            datasets: [{
                data: [activos, inactivos],
                backgroundColor: [
                    'rgba(16,185,129,0.85)', // verde
                    'rgba(239,68,68,0.85)'   // rojo
                ],
                borderColor: [
                    'rgba(16,185,129,1)',
                    'rgba(239,68,68,1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '60%',
            animations: {
                animateRotate: true,
                animateScale: true,
                rotation: {
                    duration: 1000,
                    easing: 'easeOutCubic'
                },
                scale: {
                    duration: 800,
                    easing: 'easeOutBack'
                }
            },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        pointStyle: 'circle'
                    }
                },
                tooltip: {
                    callbacks: {
                        label: (ctx) => {
                            const total = activos + inactivos || 1;
                            const value = ctx.parsed;
                            const percent = (value * 100 / total).toFixed(1);
                            return `${ctx.label}: ${value} (${percent}%)`;
                        }
                    }
                }
            }
        }
    });
}
