<template>
    <div>
        <search-patient-modal ref="searchPatientModal" />
        <h1>around 🤲🏻 about 🙌🏻 arrange</h1>
    </div>
</template>

<script>
import Layout from '@/Components/Layouts/Layout';
import SearchPatientModal from '@/Components/Helpers/SearchPatientModal';
export default {
    components: { SearchPatientModal },
    layout: Layout,
    created () {
        this.eventBus.on('action-clicked', action => this.handleAction(action));
    },
    methods: {
        handleAction(action) {
            switch (action) {
            case 'add-opd-case':
                this.$refs.searchPatientModal.open();
                break;

            default:
                break;
            }
        }
    }
};
</script>