<template>
    <div>
        <create-stay-case-modal
            ref="createStayCaseModal"
            :patient="patient"
        />
        <h1 class="font-fascinate-inline text-xl mb-4">
            <inertia-link
                :href="`${$page.props.app.baseUrl}/cases`"
                class=" text-dark-theme-light"
            >
                My Cases
            </inertia-link>
            <span class=" text-bitter-theme-light">>> Stay Cases</span>
        </h1>
        <div
            class="mt-2 p-2 rounded shadow bg-white"
            v-for="encounter in encounters"
            :key="encounter.id"
        >
            <div class="flex justify-between items-center">
                <inertia-link
                    class="block text-thick-theme-light font-semibold"
                    :href="`${$page.props.app.baseUrl}/cases/${encounter.slug}/notes`"
                >
                    {{ `${capitalize(encounter.type)} @ ${encounter.encountered_at}` }}
                </inertia-link>
                <button
                    class="block px-4 py-1 bg-dark-theme-light text-white font-semibold shadow-sm rounded-2xl"
                >
                    Add
                </button>
            </div>
            <!-- <span class="text-sm">{{ `${encounter.type}@${encounter.encountered_at}` }}</span> -->
            <!-- <span class="text-sm px-2 rounded border border-alt-theme-light text-alt-theme-light">{{ encounter.subscription.as.toUpperCase() }}</span> -->
        </div>
        <button @click="$refs.createStayCaseModal.open()">
            create
        </button>
    </div>
</template>
<script>
import Layout from '@/Components/Layouts/Layout';
import CreateStayCaseModal from '@/Components/forms/CreateStayCaseModal';
export default {
    layout: Layout,
    components: {
        CreateStayCaseModal
    },
    props: {
        patient: { type: Object, required: true },
        encounters: { type: Array, required: true }
    },
    data () {
        return {
            actionClicked: ''
        };
    },
    watch: {
        actionClicked (val) {
            this.handleAction(val);
            this.actionClicked = '';
        }
    },
    created () {
        this.eventBus.on('action-clicked', action => this.actionClicked = action);
    },
    methods: {
        handleAction (action) {
            switch (action) {
            case 'create-stay-case':
                this.$refs.createStayCaseModal.open();
                break;

            default:
                break;
            }
        },
        capitalize (word) {
            if (typeof word !== 'string') return '';
            return word.charAt(0).toUpperCase() + word.slice(1);
        }
    }
};
</script>