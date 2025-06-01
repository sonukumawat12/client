<template>
<div class="container">
    <pre-loader v-if="isLoading" />
    <div v-else class="card-shadow-primary card-border mb-3 card">
        <div class="row">
            <div class="col-md-3 bg-light">
                <div class="p-3">
                    <div class="d-flex align-items-center justify-content-center mb-2 ">
                        <img class="img-profile rounded-circle" width="90" :src="user.avatar">    
                    </div>
                    <div class="d-flex align-items-center justify-content-center">
                        <button class="btn btn-outline-primary" @click="edit(user.id)"><i class="bi bi-pencil"></i> {{$t('Edit')}}</button>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="text-center list-group list-group-flush">
                    <div class="list-group-item">{{user.name}}</div>
                    <div class="list-group-item">
                        <span class="badge bg-primary" v-for="(role, index) in user.roles" v-bind:key="index">
                            {{ role.replaceAll('_', ' ') }}
                        </span>
                    </div>
                    <div class="list-group-item">{{user.email}}</div>

                    <template v-if="user.custom_fields">
                    <div class="list-group-item" v-for="(cfield, index) in user.custom_fields" v-bind:key="index">
                        <span class="text-capitalize">{{ index.replaceAll('_', ' ') }}</span> : {{ cfield }}
                    </div>
                    </template>

                    <div class="list-group-item">{{$t('Joined on')}} : {{user.created_at}}</div>
                    <div class="list-group-item">{{$t('Tickets')}} : {{user.total_tickets}}</div>
                    <div class="list-group-item">{{$t('Last login')}} : {{ user.last_login_at }}</div>
                </div>
            </div>
        </div>
    </div>
    <v-dialog/>
</div>
</template>

<script>
import axios from 'axios';
import EModal from './employees/UserModal';
import CModal from './customers/UserModal';

export default {
    data(){
        return {
            isLoading: true,
            user: null
        };
    },
    props: [ 'userid' ],
    components: {
        EModal, CModal
    },  
    mounted () {
      axios
      .post(this.$myaccount_url+'users/'+this.userid)
      .then(response => {
            this.isLoading = false
            this.user = response.data.data;
      })
    },
    methods: {
        recordUpdated: function(response) {
            let user = response.data.data
            const currentIndex = this.usersList.findIndex(u => u.id === user.id);
            this.usersList.splice(currentIndex, 1, user)
        },
        recordAdded: function(response) {
            var respData = response.data.data;
            var user = response.data.data
            this.usersList = this.usersList.concat(user);
        },
        deleteUser: function(id){
            this.userToDelete = id
            this.$modal.show('dialog', {
                title: this.$t('Delete confirmation'),
                text: '<span class="text-danger font-weight-bold">'+this.$t('Are you sure you want to delete this record?')+'</span>',
                buttons: [{
                        title: '<span class="text-danger font-weight">'+this.$t('Delete')+'</span>',
                        handler: () => {
                            this.doDeleteUser()
                            this.$modal.hide('dialog')
                        }
                    },{
                        title: this.$t('Close')
                    }
                ]
                })

        },
        doDeleteUser: function(){
            this.isACModalVisible = false;
            axios
            .post(this.$myaccount_url+this.type+'/user/delete', {
                _method: 'delete',
                user_id: this.userToDelete.id
            })
            .then(response => {
                if(response){
                    let index = this.usersList.findIndex(item => item === this.userToDelete);
                    this.$delete(this.usersList, index)
                }
                this.userToDelete = null
            }).catch(function (thrown) {
                console.log('Error: ', thrown.message);
            });
        },
        closeModal() {
            this.isACModalVisible = false;
        },
        edit(id){
            this.$modal.show(this.type == 'customers' ? CModal : EModal , { userid: id, method: "update", handlers: {
                update: (res) => {
                    this.recordUpdated(res)
                }
            } })
        },
    }
}
</script>