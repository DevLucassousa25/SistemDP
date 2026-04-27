import axios from 'axios';
import ApexCharts from 'apexcharts'

window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

window.ApexCharts = ApexCharts;
