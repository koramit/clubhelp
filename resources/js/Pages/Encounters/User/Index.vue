<template>
    <div>
        <search-patient-modal
            :type="encounterType"
            ref="searchPatientModal"
        />
        <div
            v-if="requests.length"
            :class="{'animate-pulse': requestNotify}"
        >
            <h1 class="font-fascinate-inline text-xl text-dark-theme-light">
                Service Requests
            </h1>
            <div
                class="mt-2 p-2 rounded border-b-4 border-bitter-theme-light shadow bg-white"
                v-for="request in requests"
                :key="request.id"
            >
                <div class="flex items-center justify-between">
                    <div>
                        <inertia-link
                            class="block text-thick-theme-light font-semibold"
                            :href="`${$page.props.app.baseUrl}/cases/${request.slug}/notes`"
                        >
                            {{ `${request.patient.first_name} HN ${request.patient.hn}` }}
                        </inertia-link>
                        <span class="text-sm">{{ `${request.type}@${request.encountered_at}` }}</span>
                    </div>
                    <button
                        class="block px-4 py-1 bg-dark-theme-light text-white font-semibold shadow-sm rounded-2xl"
                        @click="pickupConsultCase(request.id, 'consultant')"
                    >
                        Pickup
                    </button>
                </div>
            </div>
        </div>
        <div
            v-for="encounter in encounters"
            :key="encounter.id"
            class="mt-2 "
        >
            <div class="h-0.5 w-full bg-gray-200" />
            <div class="p-2 rounded-b shadow bg-white">
                <inertia-link
                    class="block text-thick-theme-light font-semibold"
                    :href="`${$page.props.app.baseUrl}/cases/${encounter.slug}/notes`"
                >
                    {{ `${encounter.patient.first_name} HN ${encounter.patient.hn}` }}
                </inertia-link>
                <div class="flex justify-between items-center">
                    <span class="text-sm">{{ `${encounter.type}@${encounter.encountered_at}` }}</span>
                    <span class="text-sm px-2 rounded border border-alt-theme-light text-alt-theme-light">{{ encounter.subscription.as.toUpperCase() }}</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Layout from '@/Components/Layouts/Layout';
import SearchPatientModal from '@/Components/Forms/SearchPatientModal';
export default {
    components: { SearchPatientModal },
    layout: Layout,
    props: {
        encounters: { type: Array, required: true },
        requests: { type: Array, default: () => [] },
    },
    data () {
        return {
            actionClicked: '',
            encounterType: '',
            requestNotify: true,
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
    mounted () {
        if (! this.requests.length) {
            return;
        }

        this.$nextTick(() => {
            setTimeout(() => this.requestNotify = false, 3000);
        });
    },
    methods: {
        handleAction (action) {
            switch (action) {
            case 'add-stay-case':
                this.$refs.searchPatientModal.open();
                this.encounterType = 'stay';
                break;

            default:
                break;
            }
        },
        pickupConsultCase (id, asRole) {
            this.$inertia.post(`${this.$page.props.app.baseUrl}/cases`, { id: id, as: asRole });
        }
    }
};
</script>