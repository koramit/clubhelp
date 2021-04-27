<template>
    <div class="p-4">
        <h1 class="font-bold text-lg underline">
            แบบบันทึกการส่งต่อผู้ป่วยไป Hospitel
        </h1>

        <div class="bg-white rounded shadow-sm p-4 mt-4">
            <h2 class="font-bold">
                ข้อมูลเบื้องต้น
            </h2>
            <form-input
                class="mt-2"
                name="name"
                label="ชื่อผู้ป่วย"
                v-model="patient.name"
            />
            <form-input
                class="mt-2"
                name="hn"
                label="HN"
                v-model="patient.hn"
            />
            <form-input
                class="mt-2"
                name="an"
                label="AN"
                v-model="patient.an"
            />
            <form-select
                class="mt-2"
                label="สิทธิ์การรักษา"
                v-model="patient.insurrance"
                name="insurrance"
                :options="['กรมบัญชีกลาง', 'ประกันสังคม', '30 บาท', 'ชำระเงินเอง', 'อื่นๆ']"
            />
            <form-datetime
                class="mt-2"
                label="วันที่รับไว้ในโรงพยาบาล"
                v-model="patient.date_admit"
                name="date_admit"
            />
            <form-datetime
                class="mt-2"
                label="วันที่ส่งผู้ป่วยไป Hospitel"
                v-model="patient.date_transfer"
                name="date_transfer"
            />
            <form-datetime
                class="mt-2"
                label="วันที่ตรวจพบเชื้อ"
                v-model="patient.date_dx_covid"
                name="date_dx_covid"
            />
            <form-datetime
                class="mt-2"
                label="วันที่ครบกำหนดกักตัว"
                v-model="patient.date_quarantine_completed"
                name="date_quarantine_completed"
            />
        </div>

        <div class="bg-white rounded shadow-sm p-4 mt-4">
            <h2 class="font-bold">
                Vital Signs ล่าสุด
            </h2>

            <form-input
                class="mt-2"
                label="Temp (℃)"
                type="number"
                name="temperature_celsius"
                v-model="vital_signs.temperature_celsius"
            />

            <form-input
                class="mt-2"
                label="Pulse (min)"
                type="tel"
                name="pulse_per_minute"
                v-model="vital_signs.pulse_per_minute"
            />

            <form-input
                class="mt-2"
                label="RR (min)"
                type="tel"
                name="respiration_rate_per_minute"
                v-model="vital_signs.respiration_rate_per_minute"
            />

            <form-input
                class="mt-2"
                label="BP (mmHg)"
                name="bp"
                v-model="vital_signs.bp"
            />

            <form-input
                class="mt-2"
                label="O₂ sat (% RA)"
                type="tel"
                name="o2_sat"
                v-model="vital_signs.o2_sat"
            />
        </div>

        <div class="bg-white rounded shadow-sm p-4 mt-4">
            <h2 class="font-bold">
                บันทึกอาการแสดง
            </h2>

            <form-checkbox
                class="mt-2"
                v-model="symptoms.asymptomatic"
                label="Asymptomatic"
                :toggler="true"
            />

            <div v-if="!symptoms.asymptomatic">
                <form-checkbox
                    class="mt-2"
                    v-model="symptoms.uri"
                    label="URI"
                />

                <form-checkbox
                    class="mt-2"
                    v-model="symptoms.pneumonia"
                    label="Pneumonia"
                />

                <form-checkbox
                    v-for="(symptom, key) in symptomSet"
                    :key="key"
                    class="mt-2"
                    v-model="symptoms[symptom.name]"
                    :label="symptom.label"
                />

                <form-input
                    class="mt-2"
                    placeholder="อาการอื่นๆ คือ"
                    name="symptoms_others"
                    v-model="symptoms.others"
                />
            </div>

            <hr class="my-2">

            <form-checkbox
                class="mt-2"
                v-model="symptoms.adr"
                label="แพ้ยา/แพ้อาหาร"
                :toggler="true"
            />

            <form-input
                v-if="symptoms.adr"
                class="mt-2"
                label="ระบุชนิดของยา/อาหารที่แพ้"
                name="others"
                v-model="symptoms.adr_detail"
            />

            <hr class="my-2">

            <h3 class="form-label">
                โรคประจำตัว
            </h3>

            <form-checkbox
                class="mt-2"
                v-model="comorbid.dm"
                label="เบาหวาน"
            />

            <form-checkbox
                class="mt-2"
                v-model="comorbid.ht"
                label="ความดัน"
            />

            <form-input
                class="mt-2"
                placeholder="ระบุโรคประจำตัวอื่นๆ"
                name="comorbid_others"
                v-model="comorbid.others"
            />

            <hr class="my-2">

            <form-select
                label="อาหารที่ต้องการทาน"
                name="meal"
                v-model="patient.meal"
                :options="['ปกติ', 'อิสลาม', 'มังสวิรัติ', 'อื่นๆ']"
            />
        </div>

        <div class="bg-white rounded shadow-sm p-4 mt-4">
            <h2 class="font-bold">
                คำสั่งการรักษา
            </h2>

            <form-select
                class="mt-2"
                label="Temperature"
                name="temperature_per_day"
                v-model="treatments.temperature_per_day"
                :options="['วันละครั้ง', 'วันละสองครั้งเช้าเย็น']"
            />

            <form-select
                class="mt-2"
                label="Oxygen sat RA"
                name="oxygen_sat_RA"
                v-model="treatments.oxygen_sat_RA"
                :options="['วันละครั้ง', 'วันละสองครั้งเช้าเย็น']"
            />

            <form-datetime
                class="mt-2"
                label="Favipiravir (วันที่เริ่มยา)"
                v-model="treatments.date_start_favipiravir"
                name="date_start_favipiravir"
            />

            <form-datetime
                class="mt-2"
                label="Favipiravir (กำหนดครบวันที่)"
                v-model="treatments.date_stop_favipiravir"
                name="date_stop_favipiravir"
            />

            <form-datetime
                class="mt-2"
                label="นัดมาทำ NP swab ซ้ำ วันที่"
                v-model="treatments.date_repeat_NP_swap"
                name="date_repeat_NP_swap"
            />
        </div>

        <button class="btn btn-dark w-full my-4">
            SUBMIT
        </button>
    </div>
</template>

<script>
import FormCheckbox from '@/Components/Controls/FormCheckbox';
import FormDatetime from '@/Components/Controls/FormDatetime';
import FormInput from '@/Components/Controls/FormInput';
import FormSelect from '@/Components/Controls/FormSelect';
export default {
    components: {
        FormCheckbox,
        FormDatetime,
        FormInput,
        FormSelect,
    },
    data () {
        return {
            patient: {
                name: '',
                hn: '',
                an: '',
                insurrance: '',
                date_admit: '',
                date_transfer: '2021-04-27',
                date_dx_covid: '',
                date_quarantine_completed: '',
                meal: '',
            },
            vital_signs: {
                temperature_celsius: '',
                pulse_per_minute: '',
                respiration_rate_per_minute: '',
                bp: '',
                o2_sat: '',
            },
            symptoms: {
                asymptomatic: false,
                uri: false,
                pneumonia: false,
                anosmia: false,
                fever: false,
                cough: false,
                fatigue: false,
                sore_throat: false,
                rhinorrhoea: false,
                others: '',
                adr: false,
                adr_detail: ''
            },
            symptomSet: [
                { label: 'จมูกไม่ได้กลิ่น', name: 'anosmia'},
                { label: 'ไข้', name: 'fever'},
                { label: 'ไอ', name: 'cough'},
                { label: 'เหนื่อย', name: 'fatigue'},
                { label: 'เจ็บคอ', name: 'sore_throat'},
                { label: 'มีน้ำมูก', name: 'rhinorrhoea'},
            ],
            comorbid: {
                dm: false,
                ht: false,
                others: '',
            },
            treatments: {
                temperature_per_day: '',
                oxygen_sat_RA: '',
                date_start_favipiravir: '',
                date_stop_favipiravir: '',
                date_repeat_NP_swap: '',
            }
        };
    },
    created () {
        document.title = 'Hospitel Transfer Form';
    },
    mounted() {
        this.$nextTick(function () {
            const pageLoadingIndicator = document.getElementById('page-loading-indicator');
            if (pageLoadingIndicator) {
                pageLoadingIndicator.remove();
            }
        });
    },
};
</script>