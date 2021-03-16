<template>
    <div class="flex flex-col justify-center items-center w-full min-h-screen p-4">
        <div
            class="flex flex-col justify-center items-center w-28 h-28 rounded-full shadow-sm font-fascinate-inline bg-bitter-theme-light text-dark-theme-light text-3xl z-10"
        >
            <div class=" text-thick-theme-light">
                Club
            </div>
            <div class=" text-soft-theme-light">
                HELP
            </div>
        </div>
        <div class="mt-4 px-4 py-8 w-80 bg-white rounded shadow transform -translate-y-16">
            <div class="font-semibold text-xl mt-8 text-center">
                Register
            </div>
            <small class="block text-center text-dark-theme-light">with {{ `${socialProfile.name}@${socialProfile.provider}` }}</small>
            <div v-if="!profile.found">
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
            <div v-else>
                <form-input
                    class="mt-6"
                    name="name"
                    label="display name"
                    v-model="form.name"
                    :error="$page.props.errors.name"
                />
                <small>feel free to choose any name you like</small>
                <form-input
                    class="mt-4"
                    name="full_name"
                    label="name"
                    v-model="form.full_name"
                    :readonly="true"
                    :error="$page.props.errors.org_id"
                />
                <form-input
                    class="mt-4"
                    type="tel"
                    name="tel_no"
                    label="tel no"
                    v-model="form.tel_no"
                    :error="$page.props.errors.tel_no"
                />
                <form-input
                    v-if="!socialProfile.email"
                    class="mt-4"
                    name="email"
                    label="email"
                    v-model="form.email"
                    :error="$page.props.errors.email"
                />
                <form-checkbox
                    class="mt-4"
                    v-model="form.agreement_accepted"
                    label="Accept Policies"
                    :toggler="true"
                />
                <a
                    :href="`${$page.props.app.baseUrl}/policies`"
                    class="mt-4 block text-thick-theme-light"
                    target="_blank"
                >Policies and Terms of use</a>
                <spinner-button
                    :spin="busy"
                    class="btn-dark mt-6 w-full"
                    @click="register"
                    :disabled="!form.name || !form.email || !form.tel_no || !form.agreement_accepted"
                >
                    REGISTER
                </spinner-button>
            </div>
        </div>
    </div>
</template>

<script>
import FormCheckbox from '@/Components/Controls/FormCheckbox.vue';
import FormInput from '@/Components/Controls/FormInput';
import SpinnerButton from '@/Components/Controls/SpinnerButton';
import axios from 'axios';
export default {
    components: { FormInput, FormCheckbox, SpinnerButton },
    props: {
        socialProfile: { type: Object, required: true }
    },
    data () {
        return {
            credential: {
                login: '',
                password: '',
            },
            errors: {
                activate: ''
            },
            busy: false,
            profile: {},
            form: {
                email: this.socialProfile.email ?? '',
                full_name: '',
                name: '',
                tel_no: '',
                agreement_accepted:false
            },
        };
    },
    created () {
        document.title = 'Register';
    },
    mounted() {
        this.$nextTick(function () {
            const pageLoadingIndicator = document.getElementById('page-loading-indicator');
            if (pageLoadingIndicator) {
                pageLoadingIndicator.remove();
            }
        });
    },
    methods: {
        activate () {
            this.busy = true;
            this.errors.activate = '';
            axios.post(`${this.$page.props.app.baseUrl}/activate`, this.credential)
                .then(response => {
                    if (response.data.found) {
                        this.profile = {...response.data};
                        this.form.full_name = this.profile.name;
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
        register () {
            this.form.org_id = this.profile.org_id;
            this.form.remark = this.profile.remark;
            this.form.login = this.profile.login;
            this.form.full_name_en = this.profile.name_en;
            this.form.password_expires_in_days = this.profile.password_expires_in_days;

            this.busy = true;
            this.$inertia.post(
                `${this.$page.props.app.baseUrl}/register`,
                this.form,
                {
                    onError: () => {
                        this.busy = false;
                    },
                }
            );
        }
    }
};
</script>
