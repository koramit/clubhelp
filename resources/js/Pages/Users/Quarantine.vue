<template>
    <div class="flex flex-col justify-center items-center w-full p-4">
        <div class="mt-4 px-4 py-8 w-80 bg-white rounded shadow">
            <div class="font-semibold font-fascinate-inline text-thick-theme-light text-xl text-center">
                You are Quarantined.
            </div>
            <div v-if="mode === 'notification'">
                <div class="font-semibold text-center mt-4 text-dark-theme-light">
                    {{ setupNotificationLabel }}
                    <div class="mt-4 flex justify-center">
                        <a
                            class="flex justify-center items-center cursor-pointer w-full rounded-sm shadow-sm text-center text-gray-100 p-2"
                            :class="{ 'bg-line': socialProvider === 'line', 'bg-telegram': socialProvider === 'telegram' }"
                            target="_blank"
                            :href="botLink"
                        >
                            <icon
                                :name="`${socialProvider}-app`"
                                class="w-6 h-6 mr-2"
                            />Add {{ socialProvider === 'line' ? 'Friend' : 'Bot' }}
                        </a>
                    </div>
                </div>
            </div>
            <div v-else-if="mode === 'reactivation'">
                <div class="font-semibold mt-4 text-dark-theme-light">
                    <span class="block text-center">Please reactivate your account</span>
                    <div>
                        <form-input
                            class="mt-8"
                            name="login"
                            label="login"
                            v-model="credential.login"
                            :error="errors.activate"
                        />
                        <form-input
                            class="mt-6"
                            type="password"
                            name="password"
                            label="password"
                            v-model="credential.password"
                        />
                        <spinner-button
                            :spin="busy"
                            class="btn-dark mt-6 w-full"
                            @click="activate"
                            :disabled="!credential.login || !credential.password"
                        >
                            ACTIVATE
                        </spinner-button>
                    </div>
                </div>
            </div>
            <div v-else-if="mode === 'no_role'">
                <div class="font-semibold mt-4 text-dark-theme-light">
                    <span class="block italic text-center">Your membership is pending.</span>
                    <span class="block text-center mt-4">Our club managers will get in touch with you shortly.</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Icon from '@/Components/Helpers/Icon.vue';
import axios from 'axios';
import Layout from '@/Components/Layouts/Layout';
import FormInput from '@/Components/Controls/FormInput';
import SpinnerButton from '@/Components/Controls/SpinnerButton';
export default {
    components: { Icon, FormInput, SpinnerButton },
    layout: Layout,
    props: {
        mode: { type: String, required: true },
        socialProvider: { type: String, required: true },
        botLink: { type: String, default: '' },
        redirectTo: { type: String, default: 'cases' }
    },
    computed: {
        setupNotificationLabel () {
            return this.socialProvider === 'line' ?
                'Please add LINE bot' :
                'Please add Telegram bot';
        }
    },
    data () {
        return {
            pollingCount: 0,
            credential: {
                login: '',
                password: '',
            },
            errors: {
                activate: ''
            },
            busy: false,
        };
    },
    methods: {
        checkNotificationChannel () {
            axios.get(`${this.$page.props.app.baseUrl}/quarantine/notification`)
                .then(response => {
                    if (response.data) {
                        console.log(response.data);
                        this.$inertia.get(`${this.$page.props.app.baseUrl}/${this.redirectTo}`);
                    } else {
                        if (this.pollingCount < 100) {
                            this.pollingCount++;
                            setTimeout(this.checkNotificationChannel, 5000);
                        }
                    }
                }).catch(error => {
                    console.log(error);
                });
        },
        activate () {
            this.busy = true;
            this.errors.activate = '';
            axios.post(`${this.$page.props.app.baseUrl}/quarantine`, this.credential)
                .then(response => {
                    if (response.data.found) {
                        this.$inertia.get(`${this.$page.props.app.baseUrl}/${this.redirectTo}`);
                    } else {
                        this.errors.activate = response.data.message;
                        this.credential.password = '';
                    }
                    this.busy = false;
                }).catch(error => {
                    this.errors.activate = 'Service unavailable at the moment, please try again.';
                    this.busy = false;
                });
        },
    }
};
</script>

<style scoped>
    .bg-line {
        background-color: #00b900;
    }
    .bg-telegram {
        background-color: #54a9eb;
    }
</style>