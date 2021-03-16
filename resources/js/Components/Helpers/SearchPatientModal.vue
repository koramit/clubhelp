<template>
    <teleport to="body">
        <modal
            ref="modal"
            width-mode="form-cols-1"
            @closed="reset"
        >
            <template #header>
                <div class="font-semibold text-thick-theme-light">
                    Search Patient
                </div>
            </template>
            <template #body>
                <div class="py-4 my-2 md:py-6 md:my-4 border-t border-b border-bitter-theme-light">
                    <form-input
                        name="hn"
                        label="hn"
                        v-model="hn"
                        pattern="\d*"
                        type="number"
                        :error="errors.hn"
                    />
                    <spinner-button
                        :spin="busy"
                        class="btn-bitter w-full mt-2"
                        @click="search"
                        :disabled="!hn.length"
                    >
                        SEARCH
                    </spinner-button>
                    <hr class="my-4 md:my-6">
                    <div>
                        <span class="form-label block">Patient Data</span>
                        <div
                            v-if="!patient.hn"
                            class="bg-white rounded shadow p-2 lg:p-4 text-sm"
                            :class="{ 'animate-pulse': busy }"
                        >
                            <div class="mt-1">
                                <span class="bg-gray-100 text-gray-100 whitespace-nowrap">
                                    patient name placeholder
                                </span>
                            </div>
                            <div class="mt-1">
                                <span class="bg-gray-100 text-gray-100 whitespace-nowrap">
                                    gender placeholder
                                </span>
                            </div>
                            <div class="mt-1">
                                <span class="bg-gray-100 text-gray-100 whitespace-nowrap">
                                    age placeholder
                                </span>
                            </div>
                            <div class="mt-1">
                                <span class="bg-gray-100 text-gray-100 whitespace-nowrap">
                                    insurance placeholder
                                </span>
                            </div>
                            <div class="mt-1">
                                <span class="bg-gray-100 text-gray-100 whitespace-nowrap">
                                    recently admission placeholder
                                </span>
                            </div>
                        </div>
                        <div
                            v-else
                            class="bg-white rounded shadow p-2 lg:p-4 text-sm"
                        >
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
                            <p class="mt-1 whitespace-nowrap">
                                {{ patient.admission }}
                            </p>
                        </div>
                    </div>
                </div>
            </template>
            <template #footer>
                <div
                    class=" overflow-y-hidden transition-all duration-1000 ease-linear"
                    :class="{'max-h-0': !patient.hn, 'max-h-32': patient.hn}"
                >
                    <div class="flex justify-end items-center">
                        <button
                            class="btn btn-dark w-1/2"
                            @click="createNewCase"
                            :disabled="!patient.hn"
                        >
                            New Visit
                        </button>
                    </div>
                <!-- <div class="grid grid-cols-2 gap-2">
                    <button
                        class="btn btn-dark"
                        @click="createNewCase"
                        :disabled="!patient.hn"
                    >
                        Review
                    </button>
                    <button
                        class="btn btn-dark"
                        @click="createNewCase"
                        :disabled="!patient.hn"
                    >
                        New Visit
                    </button>
                </div> -->
                </div>
            </template>
        </modal>
    </teleport>
</template>

<script>
import Modal from '@/Components/Helpers/Modal';
import FormInput from '@/Components/Controls/FormInput';
import SpinnerButton from '@/Components/Controls/SpinnerButton';
import axios from 'axios';
export default {
    components: { Modal, FormInput, SpinnerButton },
    data () {
        return {
            hn: '',
            busy: false,
            patient: {
                hn: '',
                name: '',
                gender: '',
                age: '',
            },
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
                        this.patient = {...response.data};
                    } else {
                        this.errors.hn = response.data.message;
                    }
                }).catch((error) => {
                    console.log(error);
                    this.errors.hn = 'Service unavailable at the moment, please try again.';
                }).finally(() => this.busy = false);
        },
        reset () {

        },
        open () {
            this.$refs.modal.open();
        },
        close () {
            this.$refs.modal.close();
        }
    }
};
</script>