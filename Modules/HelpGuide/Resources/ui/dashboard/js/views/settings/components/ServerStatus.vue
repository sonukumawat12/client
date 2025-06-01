<template>
<div class="card">
    <div
        class="card-header"
        data-toggle="collapse"
        data-target="#serverStatus"
        aria-expanded="true"
        aria-controls="serverStatus" >{{ $t('Server info') }}</div>
    <div class="card-body collaps show" id="serverStatus">
        <pre-loader v-if="isLoading" />

        <ul class="list-group" v-else >

          

            <li class="list-group-item d-flex justify-content-between align-items-center">
                PHP version
                <div class="text-end">
                    <span v-if="serverStatus.php_version >= '8.0'" class="badge bg-success rounded-pill float-end">
                        {{ serverStatus.php_version }}
                    </span>
                    <span v-else>
                        <span class="badge bg-danger rounded-pill">{{ serverStatus.php_version }}</span>
                        <div class="text-info small">PHP version >= 8.1 Recommended</div>
                    </span>
                </div>
            </li>


            <li class="list-group-item d-flex justify-content-between align-items-center">
                Max execution time
                <div class="text-end">
                    <span v-if="serverStatus.max_execution_time >= 300" class="badge bg-success rounded-pill float-end">
                        {{ serverStatus.max_execution_time }} seconds
                    </span>
                    <span v-else>
                        <span class="badge bg-danger rounded-pill">{{ serverStatus.max_execution_time }} seconds</span>
                        <div class="text-info small">+300 Seconds Recommended</div>
                    </span>
                </div>
            </li>

            <li class="list-group-item d-flex justify-content-between align-items-center">
                Max input time
                <div class="text-end">
                    <span v-if="serverStatus.max_input_time >= 120" class="badge bg-success rounded-pill float-end">
                        {{ serverStatus.max_input_time }} seconds
                    </span>
                    <span v-else>
                        <span class="badge bg-danger rounded-pill">{{ serverStatus.max_input_time }} seconds</span>
                        <div class="text-info small">+120 Seconds Recommended</div>
                    </span>
                </div>
            </li>

            <li class="list-group-item d-flex justify-content-between align-items-center">
                Max input vars
                <div class="text-end">
                    <span v-if="serverStatus.max_input_vars >= 2000" class="badge bg-success rounded-pill float-end">
                        {{ serverStatus.max_input_vars }} seconds
                    </span>
                    <span v-else>
                        <span class="badge bg-danger rounded-pill">{{ serverStatus.max_input_vars }} seconds</span>
                        <div class="text-info small">+2000 Recommended</div>
                    </span>
                </div>
            </li>

            <li class="list-group-item d-flex justify-content-between align-items-center">
                Memory limit
                <div class="text-end">
                    <span v-if="serverStatus.memory_limit.slice(0, -1) >= 512" class="badge bg-success rounded-pill float-end">
                        {{ serverStatus.memory_limit }} MB
                    </span>
                    <span v-else>
                        <span class="badge bg-danger rounded-pill">{{ serverStatus.memory_limit }} MB</span>
                        <div class="text-info small">+512MB Recommended</div>
                    </span>
                </div>
            </li>

            <li class="list-group-item d-flex justify-content-between align-items-center">
                Post max size
                <div class="text-end">
                    <span v-if="serverStatus.post_max_size.slice(0, -1) >= 64" class="badge bg-success rounded-pill float-end">
                        {{ serverStatus.post_max_size }} MB
                    </span>
                    <span v-else>
                        <span class="badge bg-danger rounded-pill">{{ serverStatus.post_max_size }} MB</span>
                        <div class="text-info small">+64MB Recommended</div>
                    </span>
                </div>
            </li>

            <li class="list-group-item d-flex justify-content-between align-items-center">
                Upload max filesize
                <div class="text-end">
                    <span v-if="serverStatus.upload_max_filesize.slice(0, -1) >= 64" class="badge bg-success rounded-pill float-end">
                        {{ serverStatus.upload_max_filesize }} MB
                    </span>
                    <span v-else>
                        <span class="badge bg-danger rounded-pill">{{ serverStatus.upload_max_filesize }} MB</span>
                        <div class="text-info small">+64MB Recommended</div>
                    </span>
                </div>
            </li>

            <li class="list-group-item d-flex justify-content-between align-items-center">
                Zip extension
                <div class="text-end">
                    <span v-if="serverStatus.zip" class="badge bg-success rounded-pill float-end">
                        {{ $t('Enabled') }}
                    </span>
                    <span v-else>
                        <span class="badge bg-danger rounded-pill">
                            {{ $t("Disabled / not installed") }}
                        </span>
                    </span>
                </div>
            </li>
        </ul>
    </div>
  </div>
</template>

<script>
import Axios from 'axios'
export default {
    data() {
        return {
            isLoading: false,
            serverStatus: []
        }
    },
    beforeMount(){
        this.loadServerStatus()
    },
    methods: {
        loadServerStatus(){
            this.isLoading = true
            Axios.post(this.$myaccount_url+'settings/server_status')
            .then((res) => {
                this.serverStatus = res.data
            })
            .catch((error) => {
                this.$showError(error)
            })
            .then(() => {
                this.isLoading = false
            })
        }
    }
}
</script>
