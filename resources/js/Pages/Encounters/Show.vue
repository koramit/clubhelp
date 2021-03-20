<template>
    <div>
        <h1>around 🤲🏻 about 🙌🏻 arrange</h1>
        <text-editor
            v-model="note"
            name="note"
            @autosave="autosave"
        />
    </div>
</template>
<script>
import Layout from '@/Components/Layouts/Layout';
import TextEditor from '@/Components/Controls/TextEditor.vue';
export default {
    components: { TextEditor },
    layout: Layout,
    props: {
        encounter: { type: Object, required: true }
    },
    data () {
        return {
            note: '',
        };
    },
    methods: {
        autosave () {
            console.log('autosave');
            this.eventBus.emit('typing-stopped');
        },
    }
};
</script>