<template>
    <div>
        <add-opd-case-modal
            ref="searchPatientModal"
            @confirmed="addOPDCase"
        />
        <h1>around 🤲🏻 about 🙌🏻 arrange</h1>
    </div>
</template>

<script>
import Layout from '@/Components/Layouts/Layout';
import AddOpdCaseModal from '@/Components/Forms/AddOpdCaseModal';
export default {
    components: { AddOpdCaseModal },
    layout: Layout,
    props: {
        cases: { type: Array, required: true }
    },
    data () {
        return {
            // encounterType: ''
        };
    },
    created () {
        this.eventBus.on('action-clicked', action => this.handleAction(action));
    },
    methods: {
        handleAction(action) {
            switch (action) {
            case 'add-opd-case':
                this.$refs.searchPatientModal.open();
                // this.encounterType = 'opd';
                break;

            default:
                break;
            }
        },
        addOPDCase(patient) {
            this.$refs.searchPatientModal.close();
            this.$inertia.post(
                `${this.$page.props.app.baseUrl}/cases`,
                { id: patient.id, type: 'opd', dateVisit: patient.date_visit },
                {
                    onError: () => {
                        // this.busy = false;
                    },
                }
            );

            console.log(patient.hn);
        }
    }
};
</script>