<template>
    <div class="w-full">
        <label
            class="form-label"
            :for="name"
        >{{ label }} :</label>
        <input
            :id="name"
            :name="name"
            ref="input"
            type="date"
            :placeholder="placeholder"
            readonly
            :value="modelValue"
            class="appearance-none form-input"
        >
    </div>
</template>

<script>
import 'flatpickr/dist/themes/light.css';
import flatpickr from 'flatpickr';
export default {
    emits: ['autosave', 'update:modelValue'],
    props: {
        modelValue: { type: String, required: true },
        name: { type: String, required: true },
        label: { type: String, default: '' },
        mode: { type: String, default: 'date' },
        placeholder: { type: String, default: '' },
        format: { type: String, default: 'F j, Y' }, // format for display date
        options: { type: Object, default: () => {} },
    },
    created () {
        const onChange = (selectedDates, dateStr, fp) => {
            // update model
            this.$emit('update:modelValue', dateStr);
            // Emit autosave if field name available
            this.$emit('autosave');
        };

        this.flatpickrOptions = {
            date: { // init flatpickr instance
                altInput: true,
                altFormat: this.format,
                onChange: onChange
            },
            time: {
                enableTime: true,
                noCalendar: true,
                dateFormat: 'H:i',
                minTime: '07:00',
                maxTime: '21:00',
                time_24hr: true,
                minuteIncrement: 30,
                onChange: onChange,
            }
        };

        if (this.modelValue) {
            this.flatpickrOptions.date.defaultDate = this.modelValue;
        }

        if (this.options !== undefined) {
            this.flatpickrOptions[this.mode] = {... this.flatpickrOptions[this.mode], ...this.options};
        }
    },
    mounted () {
        this.fp = flatpickr(this.$refs.input, this.flatpickrOptions[this.mode]);
    },
};
</script>

<style>
    .calendar-event {
        position: absolute;
        width: 3px;
        height: 3px;
        border-radius: 150px;
        bottom: 3px;
        left: calc(50% - 1.5px);
        content: "???";
        display: block;
        background: #3d8eb9;
    }

    .calendar-event.busy {
        background: #f64747;
    }
</style>