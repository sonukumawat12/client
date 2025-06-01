<template>
    <div class="card user-info-modal">
        <div class="card-body p-0 m-0 pt-3">
            <pre-loader v-if="isLoading" />
            <div v-else>
                <div class="d-flex align-items-center justify-content-center mb-2 ">
                    <img class="img-profile rounded-circle" width="90" :src="user.avatar">    
                </div>
                <div class="text-center list-group list-group-flush">
                    <div class="list-group-item">{{user.name}}</div>
                    <div class="list-group-item">
                        <span class="badge bg-primary" v-for="(role, index) in user.roles" v-bind:key="index">
                            {{ role }}
                        </span>
                    </div>
                    <div class="list-group-item">{{user.email}}</div>

                    <template v-if="user.custom_fields">
                    <div class="list-group-item" v-for="(cfield, index) in user.custom_fields" v-bind:key="index">
                        <span class="text-capitalize">{{ index.replaceAll('_', ' ') }}</span> : {{ cfield }}
                    </div>
                    </template>
                    
                    <div class="list-group-item">{{$t('Joined on')}} : {{user.created_at}}</div>
                    <div class="list-group-item">{{$t('Last login')}} : {{ user.last_login_at }}</div>
                    <div class="list-group-item">{{$t('Tickets')}} : {{ user.total_tickets }}</div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
export default {
    data() {
        return {
            isLoading: true,
            user: null
        }
    },
    props: { userid: null },
    async mounted(){
        this.loadUserDetails()
    },
    methods: {
        loadUserDetails(){
            axios
            .post(this.$myaccount_url+'customers/'+this.userid)
            .then(response => {
                    this.isLoading = false
                    this.user = response.data.data;
            })
            .catch((e) => {
                this.$showError(e)
                this.$emit('close')
            })
        }
    }
}
</script>