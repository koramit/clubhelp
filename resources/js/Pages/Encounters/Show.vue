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
        <div
            v-for="note in encounter.notes"
            :key="note.slug"
            class="p-2 trix-text bg-white rounded shadow mt-2"
            :class="{'ml-2 rounded-br-2xl': note.user_id == $page.props.user.id}"
        >
            <div v-html="note.content" />
            <p class=" text-xs text-gray-400 italic text-right px-2">
                {{ note.created_at }}
            </p>
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
                :class="{ 'bg-thick-theme-light': tags.indexOf(division) > -1 }"
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
            >
                CONSULT
            </button>
            <button
                class="btn btn-dark w-full"
                @click="addNote"
                :disabled="!content"
            >
                ADD NOTE
            </button>
        </div>
    </div>
</template>
<script>
import Layout from '@/Components/Layouts/Layout';
import TextEditor from '@/Components/Controls/TextEditor.vue';
export default {
    components: { TextEditor },
    layout: Layout,
    props: {
        encounter: { type: Object, required: true }
    },
    data () {
        return {
            content: '',
            showDivisions: false,
            divisions: ['Allergy','Ambulatory','Cardiology','Chest','Critical Care','Endocrinology','Gastroenterology','Genetics','Geriatric','Hematology','Hypertension','Infectious','Nephrology','Neurology','Nutrition','Oncology','Rheumatology'],
            tags: []
        };
    },
    methods: {
        autosave () {
            console.log('autosave');
            this.eventBus.emit('typing-stopped');
        },
        addNote () {
            this.$inertia.post(
                `${this.$page.props.app.baseUrl}/encounters/${this.encounter.id}/notes`,
                { content: this.content },
                {
                    onSuccess: () => this.$refs.textEditor.clear(),
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
        }
    }
};
</script>