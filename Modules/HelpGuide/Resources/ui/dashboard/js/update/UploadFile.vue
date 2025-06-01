<template>
<div>
    <input type="file" id="updateFile" class="form-control p-1" accept="zip,application/zip,application/x-zip">
    <small class="d-block m-2">{{ $t('upload_update_notice') }}</small>
    <button type="button" @click="upload()" class="btn btn-primary d-block mt-2">{{$t('Update')}}</button>

    <div class="my-3 card" v-if="uploading">
        <div class="card-body">
            <div>
                <span v-if="uploadProgress == 100"><i class="bi bi-check text-success"></i> {{$t('Uploaded')}}</span>
                <span v-else><i class="bi bi-upload"></i> {{ uploadProgress }}%</span>
            </div>
            <div v-if="updating">
                {{$t('Updating')}}...
            </div>
        </div>
    </div>

    <div class="alert alert-danger my-3" v-if="updateFailed">
        {{ $t('Update failed') }} : {{ errorDetails }}
    </div>

    <div class="alert alert-success my-3" v-if="updateCompleted">
        {{ successMsg }}
    </div>

</div>
</template>

<script>
import axios from "axios";
export default {
    data(){
        return {
            uploadProgress: 0,
            uploading: false,
            updating: false,
            errorDetails: '',
            updateFailed: false,
            updateCompleted: false,
            successMsg: ''
        }
    },
    methods: {
        upload(){

            this.uploadProgress = 0
            this.uploading = false
            this.updating = false
            this.errorDetails = ''
            this.updateFailed = false
            this.updateCompleted = false

            var updatefile = document.querySelector('#updateFile');

            if( updatefile.files.length == 0 ){
                this.$showError(this.$t("Please select a .zip file"));
                return;
            }

            var formData = new FormData();

            formData.append("file", updatefile.files[0]);
            formData.append("type", 'update');

            const config = {
                headers: { 'Content-Type': 'multipart/form-data' },
                onUploadProgress: progressEvent => {
                    var percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total)
                    this.uploadProgress = percentCompleted
                }
            }

            this.uploading = true

            axios.post(this.$myaccount_url+'upload', formData, config)
            .then((response) => {

                if(  ! response ){
                    this.$showError(this.$t("An error occured please try again"));
                    return;
                }

                if(response.data.status == "ok"){
                    this.updating = true
                    this.update(response.data.data)
                } else {
                    this.updateFailed = true
                    if(typeof response.data.message == "string"){
                        this.errorDetails = response.data.message
                        this.$showError(response.data.message)
                    } else {
                        this.$showError(response.data.message.join(','))
                        this.errorDetails = response.data.message.join(',')
                    }
                }

            })
        },
        update(file){
            this.updating = true
            axios.post(this.$myaccount_url+'update', {file}).then( (response) => {
                if(response.data.status == "ok"){
                    this.updateCompleted = true
                    this.successMsg = response.data.message
                    this.$showSuccess(response.data.message)

                    this.uploadProgress = 0
                    this.uploading = false
                    this.updating = false
                    this.errorDetails = ''
                    this.updateFailed = false

                    this.refreshApp()

                } else {
                    this.updateFailed = true
                    if(typeof response.data.message == "string"){
                        this.errorDetails = response.data.message
                        this.$showError(response.data.message)
                    } else {
                        this.$showError(response.data.message.join(','))
                        this.errorDetails = response.data.message.join(',')
                    }
                }
            })
        },
        refreshApp(){
            axios.post(this.$myaccount_url+'update/refresh_app').then( (response) => {
                if(response.data.status == "ok"){
                    this.updateCompleted = true
                    this.successMsg = response.data.message
                    this.$showSuccess(response.data.message)

                    this.uploadProgress = 0
                    this.uploading = false
                    this.updating = false
                    this.errorDetails = ''
                    this.updateFailed = false

                } else {
                    if(typeof response.data.message == "string"){
                        this.errorDetails = response.data.message
                        this.$showError(response.data.message)
                    } else {
                        this.$showError(response.data.message.join(','))
                        this.errorDetails = response.data.message.join(',')
                    }
                }
            })
        }
    }
}
</script>
