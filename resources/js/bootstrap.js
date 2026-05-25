import axios from 'axios';
import ApexCharts from 'apexcharts';
import ApexGantt from './vendor/apexgantt-ptbr.js';

window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

window.ApexCharts = ApexCharts;
window.ApexGantt  = ApexGantt;
