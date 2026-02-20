import { Controller } from '@hotwired/stimulus';
import Chart from 'chart.js/auto';

export default class extends Controller {
    connect() {
        console.log('📈 Stimulus ha detectado el canvas. ¡Pintando gráfica!');

        // "this.element" hace referencia al <canvas> donde hemos puesto el data-controller
        new Chart(this.element, {
            type: 'bar',
            data: {
                labels: ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'],
                datasets: [{
                    label: 'Nivel de Energía (Prueba)',
                    data: [6, 9, 3, 5, 8],
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
}
