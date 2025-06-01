<template>
    <div>
        <button type="button" class="btn btn-sm btn-danger" @click="clearCache()">
            <span v-if="isLoading">
                <i class="bi bi-terminal"></i> {{$t('Clearing cache')}}...
            </span>
            <span v-else>{{$t('Clear application cache')}}</span>
        </button>
        <div class="form-text small text-muted">{{$t('app_clear_and_recache')}}</div>
    </div>
</template>

<script>
import axios from 'axios';
export default {
    data(){
        return {
            isLoading: false
        }
    },
    methods: {
        clearCache(){
            this.isLoading = true
            axios
            .post(this.$myaccount_url + "settings/clear_cache")
            .then((response) => {
                if(response.data.status == "ok") this.$showSuccess(response.data.message)
                this.isLoading = false;
            });
        }
    }
}
</script>
