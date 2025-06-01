<template>
    <div class="card delete-confirmation-modal">
        <div class="card-body">
            <span class="text-danger font-weight-bold">
                {{ $t('delete_record_confirmation', { record: record_name }) }}
            </span>
            <div v-if="details" class="my-1">{{ $t(details) }}</div>
        </div>
        <div class="row border-top p-0 m-0">
            <div class="col modal-button p-0 m-0 border-right">
                <button class="btn b-block w-100" @click="$emit('close')">{{ $t('cancel') }}</button>
            </div>
            <div class="col modal-button m-0 p-0">
                <button class="btn b-block w-100 text-danger" :disabled='disabled' @click="confirm()">
                   <span v-if="countDown">({{countDown}})</span>  {{ $t('confirm')}}
                </button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data(){
        return {
            disabled: true,
            countDown: 0
        }
    },
    props: { 
        handlers: null,
        wait: {
            type: Number,
            default: 0
        },
        record_name: {
            type: String,
            default: 'record'
        },
        details: {
            type: String,
            default: ""
        },
    },
    mounted(){
        this.countDown = this.wait
        this.countDownTimer()
    },
    methods: {
        confirm(){
            this.handlers.confirm()
            this.$emit('close')
        },
        countDownTimer() {
            if(this.countDown > 0) {
                setTimeout(() => {
                    this.countDown -= 1
                    this.countDownTimer()
                }, 1000)
            } else {
                this.disabled = false
            }
        }
    }
}
</script>

<style>
    .delete-confirmation-modal .modal-button {
        text-align: center;
    }
</style>