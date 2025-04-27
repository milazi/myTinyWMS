<template>
    <vue-datepicker-local rangeSeparator="-" v-model="localValue" :local="local" :format="format" :type="type" input-class="form-input"/>
</template>

<script>
// https://vuejsexamples.com/a-beautiful-datepicker-component-for-vue2/
import VueDatepickerLocal from 'vue-datepicker-local'

export default {
    props: {
        value: {},
        format: { default: 'DD.MM.YYYY' },
        outputformat: { default: 'YYYY-MM-DD' },
        type: { default: 'normal' }
    },
    components: {
        VueDatepickerLocal
    },
    computed: {
        localValue: {
            get() {
                return this.value;
            },
            set(value) {
                if (typeof value == 'object' && !(value instanceof Date)) {
                    value[0] = moment(value[0]).format(this.outputformat);
                    value[1] = moment(value[1]).format(this.outputformat);
                    this.$emit('input', value);
                } else {
                    this.$emit('input', moment(value).format(this.outputformat));
                }
            }
        }
    },
    data() {
        return {
            local: {
                dow: 1, // Monday is the first day of the week
                // hourTip: 'Select Hour', 
                // minuteTip: 'Select Minute',
                // secondTip: 'Select Second',
                yearSuffix: '', // suffix for year
                monthsHead: [this.$t('January'), this.$t('February'), this.$t('March'), this.$t('April'), this.$t('May'), this.$t('June'), this.$t('July'), this.$t('August'), this.$t('September'), this.$t('October'), this.$t('November'), this.$t('December')],
                months: [this.$t('Jan'), this.$t('Feb'), this.$t('Mar'), this.$t('Apr'), this.$t('May'), this.$t('Jun'), this.$t('Jul'), this.$t('Aug'), this.$t('Sep'), this.$t('Oct'), this.$t('Nov'), this.$t('Dec')],
                weeks: [this.$t('Mon'), this.$t('Tue'), this.$t('Wed'), this.$t('Thu'), this.$t('Fri'), this.$t('Sat'), this.$t('Sun')],
                cancelTip: this.$t('Cancel'),
                submitTip: this.$t('Confirm')
            }
        }
    }
}
</script>
