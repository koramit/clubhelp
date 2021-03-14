<template>
    <div class="flex flex-col justify-center items-center w-full min-h-screen p-4">
        <div class="mt-4 px-4 py-8 w-80 bg-white rounded shadow">
            <div class="font-semibold text-xl text-center">
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
        </div>
    </div>
</template>

<script>
import Icon from '@/Components/Helpers/Icon.vue';
export default {
    components: { Icon },
    props: {
        mode: { type: String, required: true },
        socialProvider: { type: String, required: true },
        botLink: { type: String, default: '' },
    },
    computed: {
        setupNotificationLabel () {
            return this.socialProvider === 'line' ?
                'Please add LINE bot' :
                'Please add Telegram bot';
        }
    },
    mounted() {
        this.$nextTick(() => {
            const pageLoadingIndicator = document.getElementById('page-loading-indicator');
            if (pageLoadingIndicator) {
                pageLoadingIndicator.remove();
            }
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