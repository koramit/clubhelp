<template>
    <div>
        <h1>around 🤲🏻 about 🙌🏻 arrange</h1>
    </div>
</template>

<script>
import Layout from '@/Components/Layouts/Layout';
export default {
    layout: Layout,
    created () {
        this.eventBus.on('action-clicked', action => this.handleAction(action));
    },
    methods: {
        handleAction(action) {
            alert(action);
        }
    }
};
</script>