import { Controller } from '@hotwired/stimulus';
import Chart from 'chart.js/auto';

export default class extends Controller {
    static values = {
        datos: Object,
        type: { type: String, default: 'line' }
    }

    connect() {
        let chartOptions = {
            responsive: true,
            maintainAspectRatio: false,
        };

        // Solo añadimos los ejes (escalas) si es un gráfico de líneas o barras
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
