import { Controller } from '@hotwired/stimulus';
import Chart from 'chart.js/auto';

/**
 * Controlador de Stimulus encargado de inicializar y renderizar los gráficos de Chart.js.
 * Permite crear gráficos dinámicos (líneas, barras, donut, etc.) pasando los datos
 * a través de atributos HTML (data-chart-datos-value).
 */
export default class extends Controller {
    static values = {
        datos: Object,
        type: { type: String, default: 'line' }
    }

    /**
     * Método del ciclo de vida de Stimulus que se ejecuta al conectar el elemento al DOM.
     * Configura las opciones visuales del gráfico y lo inyecta en el elemento <canvas>.
     */
    connect() {
        const chartOptions = {
            responsive: true,
            maintainAspectRatio: false,
        };

        // Configuramos la escala del eje Y (0 a 10) estrictamente para gráficos de líneas o barras
        if (this.typeValue === 'line' || this.typeValue === 'bar') {
            chartOptions.scales = {
                y: {
                    beginAtZero: true,
                    max: 10,
                    ticks: { stepSize: 1 }
                }
            };
        }

        new Chart(this.element, {
            type: this.typeValue,
            data: this.datosValue,
            options: chartOptions
        });
    }
}
