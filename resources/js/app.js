// import '../css/app.css';
// import './bootstrap';

// import { createInertiaApp } from '@inertiajs/vue3';
// import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
// import { createApp, h } from 'vue';
// import { ZiggyVue } from '../../vendor/tightenco/ziggy';
// import Antd from 'ant-design-vue'; // Import Ant Design Vue
// //import 'ant-design-vue/dist/antd.css'; // Import Ant Design CSS
// import 'ant-design-vue/dist/reset.css';

// const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

// createInertiaApp({
//     title: (title) => `${title} - ${appName}`,
//     resolve: (name) =>
//         resolvePageComponent(
//             `./Pages/${name}.vue`,
//             import.meta.glob('./Pages/**/*.vue'),
//         ),
//     setup({ el, App, props, plugin }) {
//         return createApp({ render: () => h(App, props) })
//             .use(plugin)
//             .use(ZiggyVue)
//             .use(Antd) // Use Ant Design Vue plugin
//             .mount(el);
//     },
//     progress: {
//         color: '#4B5563',
//     },
// });

import '../css/app.css';
import './bootstrap';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import Antd from 'ant-design-vue';
import 'ant-design-vue/dist/reset.css';
import LoadingScreen from './Components/LoadingScreen.vue';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

// Minimum time to show loading screen (in milliseconds)
const MIN_LOADING_TIME = 2500; // 2.5 seconds

// Create loading element
const loadingEl = document.createElement('div');
loadingEl.id = 'app-loading';
document.body.appendChild(loadingEl);

// Track when we started loading
const loadingStartTime = Date.now();

// Create and mount loading component
const loadingApp = createApp({
  data() {
    return {
      progress: 0
    };
  },
  render() {
    return h(LoadingScreen, { progress: this.progress });
  }
});

loadingApp.use(Antd).mount('#app-loading');

// Function to update progress
function updateProgress(progress) {
  loadingApp._instance.proxy.progress = Math.min(progress, 100);
}

// Simulate progress with slower animation
const interval = setInterval(() => {
  const current = loadingApp._instance.proxy.progress;
  if (current < 90) {
    // Slower progress increment (5% instead of 10%)
    updateProgress(current + 5);
  }
}, 400); // Increased interval from 300ms to 400ms

createInertiaApp({
  title: (title) => `${title} - ${appName}`,
  resolve: (name) =>
    resolvePageComponent(
      `./Pages/${name}.vue`,
      import.meta.glob('./Pages/**/*.vue'),
    ),
  setup({ el, App, props, plugin }) {
    // Calculate remaining time to meet minimum loading duration
    const elapsed = Date.now() - loadingStartTime;
    const remainingTime = Math.max(0, MIN_LOADING_TIME - elapsed);
    
    setTimeout(() => {
      // Finish progress animation
      updateProgress(100);
      
      setTimeout(() => {
        clearInterval(interval);
        loadingEl.classList.add('fade-out');
        
        setTimeout(() => {
          document.body.removeChild(loadingEl);
          
          // Mount the actual app
          createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(Antd)
            .mount(el);
        }, 500); // Fade-out duration
      }, 500); // Time for progress bar to reach 100%
    }, remainingTime);
  },
  progress: {
    color: '#7e22ce',
  },
});

// import '../css/app.css';
// import './bootstrap';

// import { createInertiaApp } from '@inertiajs/vue3';
// import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
// import { createApp, h } from 'vue';
// import { ZiggyVue } from '../../vendor/tightenco/ziggy';

// const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

// createInertiaApp({
//     title: (title) => `${title} - ${appName}`,
//     resolve: (name) =>
//         resolvePageComponent(
//             `./Pages/${name}.vue`,
//             import.meta.glob('./Pages/**/*.vue'),
//         ),
//     setup({ el, App, props, plugin }) {
//         return createApp({ render: () => h(App, props) })
//             .use(plugin)
//             .use(ZiggyVue)
//             .mount(el);
//     },
//     progress: {
//         color: '#4B5563',
//     },
// });
