<template>
    <div ref="plugin">
        <div
            class="line-it-button"
            style="display: none;"
            data-lang="en"
            data-type="friend"
            :data-lineid="botId"
        />
    </div>
</template>

<script>
export default {
    props: {
        botId: { type: String, required: true }
    },
    mounted () {
        setTimeout(() => {
            const script = document.createElement('script');
            script.async = true;
            script.defer = true;
            script.src = 'https://d.line-scdn.net/r/web/social-plugin/js/thirdparty/loader.min.js';
            this.$refs.plugin.appendChild(script);
        }, 1500);
    }
};
</script>
