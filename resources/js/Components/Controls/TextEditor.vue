<template>
    <div class="trix-text">
        <input
            :id="name"
            type="hidden"
            name="content"
            :value="content"
        >
        <trix-editor
            :input="name"
            @trix-change="contentChange"
            ref="trix"
        />
    </div>
</template>

<script>
import Trix from 'trix';
import 'trix/dist/trix.css';
import throttle from 'lodash/throttle';
import debounce from 'lodash/debounce';
export default {
    emits: ['autosave', 'typing', 'update:modelValue'],
    props: {
        modelValue: { type: String, required: true },
        name: { type: String, required: true },
    },
    data () {
        return {
            content: this.modelValue,
        };
    },
    created () {
        this.typing = throttle(this.throttleTyping, 3000);
        this.autosave = debounce(this.debounceSave, 3000);
    },
    mounted () {
        document.querySelector('button.trix-button.trix-button--icon.trix-button--icon-code').style.display = 'none';
        document.querySelector('span.trix-button-group.trix-button-group--file-tools').style.display = 'none';
        // document.querySelector('button.trix-button.trix-button--icon.trix-button--icon-attach').style.display = 'none';
        document.querySelector('button.trix-button.trix-button--icon.trix-button--icon-decrease-nesting-level').style.display = 'none';
        document.querySelector('button.trix-button.trix-button--icon.trix-button--icon-increase-nesting-level').style.display = 'none';
    },
    methods: {
        contentChange (event) {
            this.typing();
            this.content = event.srcElement ? event.srcElement.value : event.target.value;
            this.$emit('update:modelValue', this.content);
            this.autosave();
        },
        throttleTyping () {
            this.eventBus.emit('typing');
        },
        debounceSave () {
            this.$emit('autosave');
        },
        clear () {
            this.$refs.trix.value = '';
        }
    }
};
</script>