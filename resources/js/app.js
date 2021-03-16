window._ = require('lodash');

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

import { createApp, h } from 'vue';
import { App, plugin } from '@inertiajs/inertia-vue3';
import { InertiaProgress } from '@inertiajs/progress';
import mitt from 'mitt';

InertiaProgress.init({
    delay: 200,
    color: '#94af76'
});

const el = document.getElementById('app');

const app = createApp({
    render: () => h(App, {
        initialPage: JSON.parse(el.dataset.page),
        resolveComponent: name => import(`@/Pages/${name}`).then(module => module.default),
    })
}).use(plugin);


const eventBus = mitt();
app.config.globalProperties.eventBus = eventBus;

app.mount(el);