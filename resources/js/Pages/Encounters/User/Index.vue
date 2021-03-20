<template>
    <div>
        <search-patient-modal
            :type="encounterType"
            ref="searchPatientModal"
        />
        <h1>around 🤲🏻 about 🙌🏻 arrange</h1>
        <inertia-link
            class="block"
            v-for="encounter in encounters"
            :key="encounter.id"
            :href="`${$page.props.app.baseUrl}/encounters/${encounter.slug}`"
        >
            {{ encounter.key_no }}
        </inertia-link>
        <button @click="$refs.searchPatientModal.open()">
            add stay
        </button>
    </div>
</template>

<script>
import Layout from '@/Components/Layouts/Layout';
import SearchPatientModal from '@/Components/Forms/SearchPatientModal';
export default {
    components: { SearchPatientModal },
    layout: Layout,
    props: {
        encounters: { type: Array, required: true }
    },
    data () {
        return {
            actionClicked: '',
            encounterType: ''
        };
    },
    watch: {
        actionClicked (val) {
            this.handleAction(val);
            this.actionClicked = '';
        }
    },
    created () {
        // cannot call handleAction() directly because it call $refs
        // and at created / mounted the $refs not available yet
        // the wired thing is, it works on a full page load
        // but not works when revisit 😅
        this.eventBus.on('action-clicked', action => this.actionClicked = action);
    },
    methods: {
        handleAction(action) {
            switch (action) {
            case 'add-stay-case':
                this.$refs.searchPatientModal.open();
                this.encounterType = 'stay';
                break;

            default:
                break;
            }
        },
    }
};
</script>