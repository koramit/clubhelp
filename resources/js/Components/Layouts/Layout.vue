<template>
    <div>
        <!-- main contailner, flex makes its childs extend full h -->
        <div class="md:h-screen md:flex md:flex-col">
            <!-- this is navbar, with no shrink (fixed width) -->
            <div class="md:flex md:flex-shrink-0 sticky top-0 z-30">
                <!-- left navbar on desktop and full bar on mobile -->
                <div class="bg-dark-theme-light text-white md:flex-shrink-0 md:w-56 xl:w-64 px-4 py-2 flex items-center justify-between md:justify-center">
                    <!-- the logo -->
                    <inertia-link
                        class=" inline-block"
                        :href="`${$page.props.app.baseUrl}/home`"
                    >
                        <span class="font-fascinate-inline font-bold text-lg md:text-4xl">©lubHELP</span>
                    </inertia-link>
                    <!-- title display on mobile -->
                    <div class="text-soft-theme-light text-sm md:hidden">
                        {{ $page.props.flash.title }}
                    </div>
                    <!-- cookie menu on mobile -->
                    <button
                        class="md:hidden text-bitter-theme-light"
                        @click="mobileMenuVisible = !mobileMenuVisible"
                    >
                        <svg
                            class="w-6 h-6"
                            :class="{ 'animate-spin': typing }"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 512 512"
                        ><path
                            fill="currentColor"
                            d="M510.37 254.79l-12.08-76.26a132.493 132.493 0 0 0-37.16-72.95l-54.76-54.75c-19.73-19.72-45.18-32.7-72.71-37.05l-76.7-12.15c-27.51-4.36-55.69.11-80.52 12.76L107.32 49.6a132.25 132.25 0 0 0-57.79 57.8l-35.1 68.88a132.602 132.602 0 0 0-12.82 80.94l12.08 76.27a132.493 132.493 0 0 0 37.16 72.95l54.76 54.75a132.087 132.087 0 0 0 72.71 37.05l76.7 12.14c27.51 4.36 55.69-.11 80.52-12.75l69.12-35.21a132.302 132.302 0 0 0 57.79-57.8l35.1-68.87c12.71-24.96 17.2-53.3 12.82-80.96zM176 368c-17.67 0-32-14.33-32-32s14.33-32 32-32 32 14.33 32 32-14.33 32-32 32zm32-160c-17.67 0-32-14.33-32-32s14.33-32 32-32 32 14.33 32 32-14.33 32-32 32zm160 128c-17.67 0-32-14.33-32-32s14.33-32 32-32 32 14.33 32 32-14.33 32-32 32z"
                        /></svg>
                    </button>
                </div>
                <!-- right navbar on desktop -->
                <div class="hidden md:flex w-full font-semibold text-dark-theme-light bg-alt-theme-light border-b sticky top-0 z-30 p-4 md:py-0 md:px-12 justify-between items-center">
                    <!-- title display on desktop -->
                    <div class="mt-1 mr-4">
                        {{ $page.props.flash.title }}
                    </div>
                    <!-- username and menu -->
                    <dropdown>
                        <template #default>
                            <div class="flex items-center cursor-pointer select-none group">
                                <div class="group-hover:text-bitter-theme-light focus:text-bitter-theme-light mr-1 whitespace-no-wrap">
                                    <span>{{ $page.props.user.name }}</span>
                                </div>
                                <icon
                                    class="w-4 h-4 group-hover:text-bitter-theme-light focus:text-bitter-theme-light"
                                    name="double-down"
                                />
                            </div>
                        </template>
                        <template #dropdown>
                            <div class="mt-2 py-2 shadow-xl bg-thick-theme-light text-white cursor-pointer rounded text-sm">
                                <template v-if="hasRoles">
                                    <inertia-link
                                        class="block px-6 py-2 hover:bg-dark-theme-light hover:text-soft-theme-light"
                                        :href="`${$page.props.app.baseUrl}/supports`"
                                        v-if="! currentPage('supports')"
                                    >
                                        Consult IT
                                    </inertia-link>
                                    <inertia-link
                                        class="block px-6 py-2 hover:bg-dark-theme-light hover:text-soft-theme-light"
                                        :href="`${$page.props.app.baseUrl}/preferences`"
                                        v-if="! currentPage('preferences')"
                                    >
                                        Preferences
                                    </inertia-link>
                                </template>
                                <inertia-link
                                    class="w-full font-semibold text-left px-6 py-2 hover:bg-dark-theme-light hover:text-soft-theme-light"
                                    :href="`${$page.props.app.baseUrl}/logout`"
                                    method="post"
                                    as="button"
                                    type="button"
                                >
                                    Logout
                                </inertia-link>
                            </div>
                        </template>
                    </dropdown>
                </div>
                <!-- menu on mobile -->
                <div
                    class="h-4/5 mx-1 md:hidden block fixed top-0 inset-x-0 overflow-y-scroll text-soft-theme-light bg-dark-theme-light rounded-bl-xl rounded-br-xl transition-transform transform duration-300 ease-in-out"
                    :class="{ '-translate-y-full': !mobileMenuVisible }"
                >
                    <div class="p-4 relative min-h-full">
                        <!-- username and menu -->
                        <div
                            class="flex flex-col text-center"
                            @click="mobileMenuVisible = false"
                        >
                            <div class="flex justify-center mt-2">
                                <div
                                    class="w-12 h-12 rounded-full overflow-hidden border-bitter-theme-light border-2"
                                    v-if="!avatarSrcError"
                                >
                                    <img
                                        :src="`${$page.props.user.avatar}`"
                                        alt="C"
                                        @error="avatarSrcError = true"
                                    >
                                </div>
                            </div>
                            <span class="inline-block py-1 text-white">{{ $page.props.user.name }}</span>
                            <template v-if="hasRoles">
                                <inertia-link
                                    class="block py-1"
                                    :href="`${$page.props.app.baseUrl}/supports`"
                                    v-if="! currentPage('supports')"
                                >
                                    Consult IT
                                </inertia-link>
                                <inertia-link
                                    class="block py-1"
                                    :href="`${$page.props.app.baseUrl}/preferences`"
                                    v-if="! currentPage('preferences')"
                                >
                                    Preferences
                                </inertia-link>
                            </template>
                            <inertia-link
                                class="block py-1"
                                :href="`${$page.props.app.baseUrl}/logout`"
                                method="post"
                                as="button"
                                type="button"
                            >
                                Logout
                            </inertia-link>
                        </div>
                        <hr class="my-4">
                        <main-menu
                            @click="mobileMenuVisible = false"
                            :url="url()"
                        />
                        <action-menu @action-clicked="actionClicked" />
                    </div>
                    <div class="sticky bottom-0 px-4 py-2 flex justify-items-center bg-dark-theme-light">
                        <!-- cookie-bite menu on mobile -->
                        <button
                            class="block mx-auto text-bitter-theme-light"
                            @click="mobileMenuVisible = !mobileMenuVisible"
                        >
                            <svg
                                class="w-6 h-6"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 512 512"
                            ><path
                                fill="currentColor"
                                d="M510.52 255.82c-69.97-.85-126.47-57.69-126.47-127.86-70.17 0-127-56.49-127.86-126.45-27.26-4.14-55.13.3-79.72 12.82l-69.13 35.22a132.221 132.221 0 0 0-57.79 57.81l-35.1 68.88a132.645 132.645 0 0 0-12.82 80.95l12.08 76.27a132.521 132.521 0 0 0 37.16 72.96l54.77 54.76a132.036 132.036 0 0 0 72.71 37.06l76.71 12.15c27.51 4.36 55.7-.11 80.53-12.76l69.13-35.21a132.273 132.273 0 0 0 57.79-57.81l35.1-68.88c12.56-24.64 17.01-52.58 12.91-79.91zM176 368c-17.67 0-32-14.33-32-32s14.33-32 32-32 32 14.33 32 32-14.33 32-32 32zm32-160c-17.67 0-32-14.33-32-32s14.33-32 32-32 32 14.33 32 32-14.33 32-32 32zm160 128c-17.67 0-32-14.33-32-32s14.33-32 32-32 32 14.33 32 32-14.33 32-32 32z"
                            /></svg>
                        </button>
                    </div>
                </div>
            </div>
            <!-- this is content -->
            <div class="md:flex md:flex-grow md:overflow-hidden">
                <!-- this is sidebar menu on desktop -->
                <main-menu
                    :url="url()"
                    class="hidden md:block bg-thick-theme-light flex-shrink-0 w-56 xl:w-64 py-12 px-6 overflow-y-auto"
                />
                <!-- this is main page -->
                <div
                    class="w-full p-4 md:overflow-y-auto md:py-12 lg:px-8 xl:px-10"
                    scroll-region
                >
                    <!-- <flash-messages /> -->
                    <slot />
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Dropdown from '@/Components/Helpers/Dropdown';
import Icon from '@/Components/Helpers/Icon';
import MainMenu from '@/Components/Helpers/MainMenu';
import ActionMenu from '@/Components/Helpers/ActionMenu';
// import { onMounted } from 'vue';
import axios from 'axios';
export default {
    components: { Dropdown, Icon, MainMenu, ActionMenu },
    computed: {
        hasRoles() {
            return this.$page.props.user.abilities.length;
        }
    },
    watch: {
        '$page.props.flash': {
            immediate: true,
            deep: true,
            handler() { document.title = this.$page.props.flash.title; }
        },
    },
    data () {
        return {
            mobileMenuVisible: false,
            avatarSrcError: false,
            typing: false,
        };
    },
    created () {
        var lastTimeCheckSessionTimeout = Date.now();
        const endpoint = document.querySelector('meta[name=base-url]').content + '/session-timeout';
        const sessionLifetimeSeconds = parseInt(document.querySelector('meta[name=session-lifetime-seconds]').content);
        window.addEventListener('focus', () => {
            let timeDiff = Date.now() - lastTimeCheckSessionTimeout;
            if ( (timeDiff) > (sessionLifetimeSeconds) ) {
                axios.post(endpoint)
                    .then(() => lastTimeCheckSessionTimeout = Date.now())
                    .catch(() => location.reload());
            }
        });
        this.eventBus.on('typing', () => {
            if (! this.typing) {
                this.typing = true;
                console.log('roll the cookie');
            }
        });
        this.eventBus.on('typing-stopped', () => this.typing = false);
    },
    mounted () {
        this.$nextTick(() => {
            const pageLoadingIndicator = document.getElementById('page-loading-indicator');
            if (pageLoadingIndicator) {
                pageLoadingIndicator.remove();
            }
        });
        console.log(this.currentPage('preferences'));
    },
    // setup () {
    //     var lastTimeCheckSessionTimeout = Date.now();
    //     const endpoint = document.querySelector('meta[name=base-url]').content + '/session-timeout';
    //     const sessionLifetimeSeconds = parseInt(document.querySelector('meta[name=session-lifetime-seconds]').content);
    //     window.addEventListener('focus', () => {
    //         let timeDiff = Date.now() - lastTimeCheckSessionTimeout;
    //         if ( (timeDiff) > (sessionLifetimeSeconds) ) {
    //             axios.post(endpoint)
    //                 .then(() => lastTimeCheckSessionTimeout = Date.now())
    //                 .catch(() => location.reload());
    //         }
    //     });

    //     onMounted (() => {
    //         const pageLoadingIndicator = document.getElementById('page-loading-indicator');
    //         if (pageLoadingIndicator) {
    //             pageLoadingIndicator.remove();
    //         }
    //     });
    // },
    methods: {
        url() {
            return location.pathname.substr(1);
        },
        actionClicked (action) {
            this.mobileMenuVisible = false;

            setTimeout(() => {
                this.eventBus.emit('action-clicked', action);
            }, 300); // equal to animate duration
        },
        currentPage(route) {
            return location.pathname.substr(1) === route;
        }
    }
};
</script>