import { Controller } from '@hotwired/stimulus';
import Chart from 'chart.js/auto';

export default class extends Controller {
    connect() {
        console.log('Chart.js preparado!');
    }
}
