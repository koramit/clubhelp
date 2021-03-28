<template>
    <div>
        <h1 class="font-fascinate-inline text-xl mb-4">
            <inertia-link
                :href="`${$page.props.app.baseUrl}/cases`"
                class=" text-dark-theme-light"
            >
                My Cases
            </inertia-link>
            <span class=" text-bitter-theme-light">/ {{ capitalize(encounter.meta.type) }}@{{ encounter.encountered_at }}</span>
        </h1>

        <hr class="my-4 border-b-2 border-bitter-theme-light">

        <div>
            <div class="flex">
                <form-input
                    class=" w-8/12"
                    placeholder="todo..."
                    v-model="task"
                />
                <button
                    class="block font-fascinate-inline bg-alt-theme-light text-white ml-1 rounded w-4/12"
                    @click="addTask"
                >
                    ADD
                </button>
            </div>
            <div
                class="mt-2 flex"
                v-for="(todo, key) in todos"
                :key="key"
            >
                <form-checkbox
                    class="w-10/12"
                    v-model="todo.check"
                    :label="todo.label"
                />
                <button
                    class="w-2/12"
                    @click="todos.splice(key, 1)"
                >
                    <icon
                        name="times-circle"
                        class="w-5 h-5"
                    />
                </button>
            </div>

            <hr class="my-4 border-b-2 border-bitter-theme-light">
        </div>

        <div
            v-for="note in encounter.notes"
            :key="note.slug"
            class="p-2 trix-text bg-white rounded shadow mt-2"
            :class="{
                'ml-4 rounded-br-2xl': note.user_id == $page.props.user.id,
                'mr-4 rounded-bl-2xl': note.user_id != $page.props.user.id,
                'border-b-4 border-bitter-theme-light': note.type === 'consult'
            }"
        >
            <div v-html="note.content" />
            <div
                class="mt-1"
                :class="{'flex justify-between': note.user_id != $page.props.user.id}"
            >
                <p
                    class=" text-xs text-gray-400 italic text-left px-2"
                    v-if="note.user_id != $page.props.user.id"
                >
                    {{ note.author }}
                </p>
                <p class=" text-xs text-gray-400 italic text-right px-2">
                    {{ note.created_at }}
                </p>
            </div>
        </div>
        <text-editor
            class="mt-8"
            v-model="content"
            name="content"
            @autosave="autosave"
            ref="textEditor"
        />
        <div
            class="my-4 flex flex-wrap max-h-0 transition-all duration-300 ease-linear overflow-hidden"
            :class="{'max-h-96': showDivisions}"
        >
            <button
                class="m-1 p-2 bg-alt-theme-light text-white font-semibold shadow-sm rounded-2xl transition-colors duration-300 ease-in-out"
                :class="{
                    'bg-thick-theme-light': tags.indexOf(division) > -1,
                    'bg-bitter-theme-light': division.toLowerCase() === 'urgent'
                }"
                v-for="division in divisions"
                :key="division"
                @click="toggleTag(division)"
            >
                <span v-if="tags.indexOf(division) > -1">✔︎ </span>{{ division }}
            </button>
        </div>
        <div class="grid grid-cols-2 gap-2">
            <button
                class="btn btn-bitter w-full"
                @click="toggleConsult"
                :disabled="busy"
            >
                CONSULT
            </button>
            <spinner-button
                :spin="busy"
                class="btn btn-dark w-full"
                @click="addNote"
                :disabled="!content"
            >
                ADD NOTE
            </spinner-button>
        </div>
    </div>
</template>
<script>
import Layout from '@/Components/Layouts/Layout';
import TextEditor from '@/Components/Controls/TextEditor';
import Icon from '@/Components/Helpers/Icon';
import FormInput from '@/Components/Controls/FormInput';
import FormCheckbox from '@/Components/Controls/FormCheckbox';
import SpinnerButton from '@/Components/Controls/SpinnerButton';
export default {
    components: { TextEditor, FormInput, SpinnerButton, FormCheckbox, Icon },
    layout: Layout,
    props: {
        encounter: { type: Object, required: true },
        subscription: { type: Object, required: true },
    },
    data () {
        return {
            busy: false,
            content: '',
            showDivisions: false,
            divisions: ['Urgent','Allergy','Ambulatory','Cardiology','Chest','Critical Care','Endocrinology','Gastroenterology','Genetics','Geriatric','Hematology','Hypertension','Infectious','Nephrology','Neurology','Nutrition','Oncology','Rheumatology'],
            tags: [],
            todos: [],
            task: '',
        };
    },
    methods: {
        autosave () {
            console.log('autosave');
            this.eventBus.emit('typing-stopped');
        },
        addNote () {
            this.busy = true;
            const data = { content: this.content };
            if (this.tags.length) {
                data.type = 'consult';
                data.tags = this.tags;
            } else {
                data.type = this.subscription.as === 'consultant' ? 'service' : 'note';
                data.tags = null;
            }
            this.$inertia.post(
                `${this.$page.props.app.baseUrl}/cases/${this.encounter.id}/notes`, data,
                // { content: this.content, type: this.tags.length > 0 ? 'consult':'note', tags: this.tags.length > 0 ? this.tags : null },
                {
                    onSuccess: () => {
                        this.$refs.textEditor.clear();
                        this.tags = [];
                        this.showDivisions = false;
                    },
                    onFinish: () => {
                        this.busy = false;
                    },
                }
            );
        },
        toggleTag (division) {
            if (this.tags.indexOf(division) !== -1) { // remove
                this.tags = [...this.tags.filter(tag => tag != division)];
            } else { // add
                this.tags.push(division);
            }
        },
        toggleConsult () {
            if (this.showDivisions) {
                this.tags = [];
                this.showDivisions = false;
            } else {
                this.showDivisions = true;
                setTimeout(() => window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' }), 300);
            }
        },
        capitalize (word) {
            if (typeof word !== 'string') return '';
            return word.charAt(0).toUpperCase() + word.slice(1);
        },
        addTask () {
            this.todos.push({label: this.task, check: false});
            this.task = '';
        },
        // removeTask (label) {
        //     const point =  this.todos.splice(key);
        // }
    }
};
</script>