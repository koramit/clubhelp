<template>
    <teleport to="body">
        <modal
            ref="modal"
            width-mode="form-cols-1"
            @closed="closed"
        >
            <template #header>
                <div class="font-semibold text-thick-theme-light">
                    Create Stay Case
                </div>
            </template>
            <template #body>
                <div class="py-4 my-2 md:py-6 md:my-4 border-t border-b border-bitter-theme-light">
                    <div>
                        <span class="form-label block">Patient Data</span>
                        <div class="bg-white rounded shadow p-2 lg:p-4 text-sm">
                            <p class="mt-1 whitespace-nowrap">
                                HN {{ patient.hn }}
                            </p>
                            <p class="mt-1 whitespace-nowrap">
                                {{ patient.name }}
                            </p>
                            <p class="mt-1 whitespace-nowrap">
                                {{ patient.gender }}
                            </p>
                            <p class="mt-1 whitespace-nowrap">
                                {{ patient.age }}
                            </p>
                            <p class="mt-1 whitespace-nowrap">
                                {{ patient.insurance }}
                            </p>
                        </div>
                    </div>
                    <hr class="my-4 md:my-6">
                    <form-datetime
                        name="date_visit"
                        label="Stay @"
                        v-model="date_visit"
                    />
                </div>
            </template>
            <template #footer>
                <!-- <div
                    class=" overflow-y-hidden transition-all duration-300 ease-linear"
                    :class="{'max-h-0': !patient.hn, 'max-h-32': patient.hn}"
                > -->
                <div class="flex justify-end items-center mt-2">
                    <button
                        class="btn btn-dark w-1/2"
                        @click="confirmed"
                        :disabled="!patient.hn"
                    >
                        CONFIRM
                    </button>
                </div>
                <!-- </div> -->
            </template>
        </modal>
    </teleport>
</template>

<script>
import Modal from '@/Components/Helpers/Modal';
import FormInput from '@/Components/Controls/FormInput';
import FormDatetime from '@/Components/Controls/FormDatetime';
import SpinnerButton from '@/Components/Controls/SpinnerButton';
import axios from 'axios';
export default {
    components: { Modal, FormInput, FormDatetime, SpinnerButton },
    props: {
        // type: { type: String, default: '' },
        patient: { type: Object, required: true },

    },
    data () {
        return {
            confirm: false,
            hn: '',
            busy: false,
            date_visit: '',
            // patient: {
            //     hn: '',
            //     name: '',
            //     gender: '',
            //     age: '',
            //     date_visit: '',
            // },
            errors: {
                hn: ''
            }
        };
    },
    watch: {
        hn (val) {
            if (val == '') {
                this.errors.hn = '';
            }
        }
    },
    methods: {
        search () {
            this.busy = true;
            this.patient.hn = '';
            this.errors.hn = '';
            axios.post(`${this.$page.props.app.baseUrl}/search-patient/${this.hn}`)
                .then(response => {
                    if (response.data.found) {
                        this.patient.id = response.data.id;
                        this.patient.slug = response.data.slug;
                        this.patient.hn = response.data.hn;
                        this.patient.name = response.data.name;
                        this.patient.gender = response.data.gender;
                        this.patient.age = response.data.age;
                        this.patient.admission = response.data.admission;
                    } else {
                        this.errors.hn = response.data.message;
                    }
                }).catch(() => {
                    this.errors.hn = 'Service unavailable at the moment, please try again.';
                }).finally(() => this.busy = false);
        },
        confirmed () {
            this.confirm = true;
            this.$refs.modal.close();
        },
        open () {
            this.confirm = false;
            // this.hn = '';
            // this.patient.hn = '';
            this.date_visit = new Date().toISOString().slice(0, 10);
            this.$refs.modal.open();
        },
        closed () {
            if (! this.confirm) {
                return;
            }

            // this.$inertia.get(`${this.$page.props.app.baseUrl}/patients/${this.patient.slug}/cases`, { type: this.type });
            this.$inertia.post(`${this.$page.props.app.baseUrl}/patients/${this.patient.slug}/cases`, { type: 'stay', date_visit: this.date_visit });
            // this.$refs.modal.close();
        }
    }
};
</script>