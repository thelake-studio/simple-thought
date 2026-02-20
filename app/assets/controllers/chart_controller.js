import { Controller } from '@hotwired/stimulus';
import Chart from 'chart.js/auto';

export default class extends Controller {
    // 1. Definimos qué valores esperamos recibir desde Twig
    static values = {
        datos: Object
    }

    connect() {
        console.log('📊 Renderizando gráfica con datos desde PHP:', this.datosValue);

        new Chart(this.element, {
            type: 'line',
            data: this.datosValue,
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 10,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
    }
}
