import '../css/app.css';
import './bootstrap';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h, nextTick } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import Antd from 'ant-design-vue';
import 'ant-design-vue/dist/reset.css';
import LoadingScreen from './Components/LoadingScreen.vue';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';
const MIN_LOADING_TIME = 2500; // 2.5 seconds

// Create loading element
const loadingEl = document.createElement('div');
loadingEl.id = 'app-loading';
document.body.appendChild(loadingEl);

// Track loading start time
const loadingStartTime = Date.now();

// Create loading component
const loadingApp = createApp({
  data() {
    return { progress: 0 };
  },
  render() {
    return h(LoadingScreen, { progress: this.progress });
  },
});

loadingApp.use(Antd).mount('#app-loading');

// Safe progress update
function updateProgress(progress) {
  if (loadingApp._instance?.proxy) {
    loadingApp._instance.proxy.progress = Math.min(progress, 100);
  }
}

// Start progress simulation **after Vue has rendered the loading screen**
let interval;
nextTick(() => {
  interval = setInterval(() => {
    const current = loadingApp._instance?.proxy?.progress || 0;
    if (current < 90) {
      updateProgress(current + 5);
    }
  }, 400);
});

createInertiaApp({
  title: (title) => `${title} - ${appName}`,
  resolve: (name) =>
    resolvePageComponent(
      `./Pages/${name}.vue`,
      import.meta.glob('./Pages/**/*.vue'),
    ),
  setup({ el, App, props, plugin }) {
    // Calculate remaining loading duration
    const elapsed = Date.now() - loadingStartTime;
    const remainingTime = Math.max(0, MIN_LOADING_TIME - elapsed);

    // Wait before mounting actual app
    setTimeout(() => {
      updateProgress(100); // Full progress bar

      // Give 500ms to animate progress to 100%
      setTimeout(() => {
        clearInterval(interval);
        loadingEl.classList.add('fade-out');

        // After fade-out, remove loading and mount app
        setTimeout(() => {
          document.body.removeChild(loadingEl);

          createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(Antd)
            .mount(el);
        }, 500); // Match fade-out duration
      }, 500);
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
// import { createApp, h, nextTick } from 'vue';
// import { ZiggyVue } from '../../vendor/tightenco/ziggy';
// import Antd from 'ant-design-vue';
// import 'ant-design-vue/dist/reset.css';
// import LoadingScreen from './Components/LoadingScreen.vue';

// const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

// // Minimum time to show loading screen (in milliseconds)
// const MIN_LOADING_TIME = 2500; // 2.5 seconds

// // Create loading element
// const loadingEl = document.createElement('div');
// loadingEl.id = 'app-loading';
// document.body.appendChild(loadingEl);

// // Track when we started loading
// const loadingStartTime = Date.now();

// // Create and mount loading component
// const loadingApp = createApp({
//   data() {
//     return {
//       progress: 0,
//     };
//   },
//   render() {
//     return h(LoadingScreen, { progress: this.progress });
//   },
// });

// loadingApp.use(Antd).mount('#app-loading');

// // Function to update progress safely
// function updateProgress(progress) {
//   if (loadingApp._instance && loadingApp._instance.proxy) {
//     loadingApp._instance.proxy.progress = Math.min(progress, 100);
//   }
// }

// // Simulate progress with slower animation
// let interval = null;
// nextTick(() => {
//   interval = setInterval(() => {
//     const current =
//       (loadingApp._instance?.proxy?.progress ?? 0);
//     if (current < 90) {
//       updateProgress(current + 5);
//     }
//   }, 400);
// });

// createInertiaApp({
//   title: (title) => `${title} - ${appName}`,
//   resolve: (name) =>
//     resolvePageComponent(
//       `./Pages/${name}.vue`,
//       import.meta.glob('./Pages/**/*.vue'),
//     ),
//   setup({ el, App, props, plugin }) {
//     const elapsed = Date.now() - loadingStartTime;
//     const remainingTime = Math.max(0, MIN_LOADING_TIME - elapsed);

//     setTimeout(() => {
//       updateProgress(100);

//       setTimeout(() => {
//         clearInterval(interval);
//         loadingEl.classList.add('fade-out');

//         setTimeout(() => {
//           document.body.removeChild(loadingEl);

//           // Mount the actual app
//           createApp({ render: () => h(App, props) })
//             .use(plugin)
//             .use(ZiggyVue)
//             .use(Antd)
//             .mount(el);
//         }, 500); // Fade-out duration
//       }, 500); // Time for progress bar to reach 100%
//     }, remainingTime);
//   },
//   progress: {
//     color: '#7e22ce',
//   },
// });



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
