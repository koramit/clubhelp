<template>
    <div class="flex flex-col justify-center items-center w-full min-h-screen">
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
            <span class="block font-fascinate-inline text-xl text-thick-theme-light mt-8 text-center">Drop-in consult note</span>
            <a
                :href="`${$page.props.app.baseUrl}/login/line`"
                class="flex justify-center items-center mt-8 cursor-pointer w-full rounded-sm shadow-sm bg-line text-center text-gray-100 p-2"
            >
                <icon
                    name="line-app"
                    class="w-6 h-6 mr-2"
                />Log in with LINE
            </a>
            <button
                class="flex justify-center items-center mt-8 cursor-pointer w-full rounded-sm shadow-sm bg-telegram"
            >
                <div ref="telegram" />
            </button>
        </div>
    </div>
</template>

<script>
import Icon from '@/Components/Helpers/Icon';
export default {
    components: { Icon },
    props: {
        configs: { type: Object, default: () => {} }
    },
    created () {
        document.title = 'Login';
    },
    mounted() {
        this.$nextTick(function () {
            const pageLoadingIndicator = document.getElementById('page-loading-indicator');
            if (pageLoadingIndicator) {
                pageLoadingIndicator.remove();
            }
            const script = document.createElement('script');
            script.async = true;
            script.src = this.configs.telegram.widget_src;
            script.setAttribute('data-radius', '0');
            script.setAttribute('data-size', 'large');
            script.setAttribute('data-userpic', false);
            if (this.configs.telegram.request_access) {
                script.setAttribute('data-request-access', this.configs.telegram.request_access);
            }
            script.setAttribute('data-auth-url', this.configs.telegram.redirect);
            script.setAttribute('data-telegram-login', this.configs.telegram.client_id);
            this.$refs.telegram.appendChild(script);
        });
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