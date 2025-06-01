<template>
<div class="p-1">

    <div class="input-group">
        <input class="form-control" id="modulefile" @change="fileSelected" type="file" :placeholder="$t('Upload module zip file')">
        <button class="btn btn-outline-secondary" type="button"  @click="upload()">{{$t('Upload module zip file')}}</button>
    </div>

    <div class="p-2" v-if="uploading">
        <span v-if="uploadProgress == 100"><i class="bi bi-check text-success"></i> {{$t('Uploaded')}}</span>
        <div v-else>
            <div class="progress">
                <div class="progress-bar" role="progressbar" :style="'width: '+uploadProgress+'%'" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">
                    {{ uploadProgress }}%
                </div>
            </div>
        </div>
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
            errorDetails: '',
            successMsg: '',
            fileName: ''
        }
    },
    methods: {
        upload(){

            this.uploadProgress = 0
            this.uploading = false
            this.errorDetails = ''

            var modulefile = document.querySelector('#modulefile');

            if( modulefile.files.length == 0 ){
                this.$showError(this.$t("Please select a .zip file"));
                return;
            }

            var formData = new FormData();

            formData.append("file", modulefile.files[0]);
            formData.append("type", 'module');

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
                    this.$showSuccess(response.data.message);
                    location.reload();
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
            .catch((e) => {
                this.$showError(e);
            })

        },
        fileSelected(event){
            var fileData =  event.target.files[0];
            this.fileName = fileData.name;
        }
    }
}
</script>

<style scoped>
.form-control {
    font-size: 0.95rem;
}
</style>