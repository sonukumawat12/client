<template>
<div>
<div class="d-flex justify-content-end options mb-3">
    <button class="btn btn-sm btn-primary shadow-sm float-end mx-1" @click="addNew">
        <i class="bi bi-plus-lg text-white-50"></i> {{ $t('Add user') }}
    </button>

</div>

<pre-loader v-if="isLoading" />
<div v-else>
<div class="table-responsive" v-if="usersList.length">
<table class="table table-striped table-sm table-centered mb-2 border bg-white">
    <thead>
        <tr>
            <th>{{ $t("Name") }}</th>
            <th>{{ $t("E-mail") }}</th>
            <th>{{ $t("Phone No") }}</th>
            <th>{{ $t("Role") }}</th>
            <th>{{ $t("Groups") }}</th>
            <th>{{ $t("Last login") }}</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <tr v-for="user in usersList" v-bind:key="user.id">
            <td class="table-user">
                <img :src="user.avatar" :alt="user.name" class="me-2 rounded-circle" width="32"/>
                {{ user.name }}
            </td>
            <td>{{ user.email }}</td>
            <td>{{ user.phone_no }}</td>
            <td>
                <span v-for="role in user.roles" v-bind:key="role" class="badge bg-secondary p-2 me-1">
                    {{ role.replace('_',' ') | capitalize }}
                </span>
            </td>
            <td>
                <span v-for="group in user.groups" v-bind:key="group" class="badge bg-secondary p-2 me-1">
                    {{ group.replace('_',' ') | capitalize }}
                </span>
            </td>
            <td>{{ user.last_login_at }}</td>
            <td class="table-action">
                <button class="btn btn-sm btn-danger btn-icon-split" v-on:click="deleteUser(user)">
                    <span class="icon text-white-50" data-toggle="tooltip" data-placement="top" :title="$t('Delete')"><i class="bi bi-trash"></i></span>
                </button>
                <button class="btn btn-sm btn-primary btn-icon-split" data-toggle="modal" data-target="#userModal" v-on:click="edit(user.id)">
                    <span class="icon text-white-50" data-toggle="tooltip" data-placement="top" :title="$t('Edit')"><i class="bi bi-pencil"></i></span>
                </button>
                <router-link :to="{ name: 'employee_details', params: { userid: user.id }}" class="btn btn-sm btn-secondary btn-icon-split">
                    <span class="icon text-white-50" data-toggle="tooltip" data-placement="top" :title="$t('View')"><i class="bi bi-eye"></i></span>
                </router-link>
            </td>
        </tr>
    </tbody>
</table>
</div>
<empty-state v-else text="No employees yet" />
</div>

<v-dialog/>
</div>

</template>

<script>
import axios from 'axios';
import userModal from './UserModal';

export default {
    data(){
        return {
            usersList: [],
            isACModalVisible: false,
            userToDelete: null,
            userToUpdate: null,
            userModal: "create",
            isLoading: false
        };
    },
    components: {
        userModal
    },  
    mounted () {
        this.isLoading = true
        axios
        .post(this.$myaccount_url+'employees/list')
        .then(response => {
            this.isLoading = false
            var respData = response.data.data;
            var newUsers = response.data.data
            this.usersList = this.usersList.concat(newUsers);
        })
    },
    methods: {
        recordUpdated: function(response) {
            let user = response.data.data
            const currentIndex = this.usersList.findIndex(u => u.id === user.id);
            this.usersList.splice(currentIndex, 1, user)
        },
        recordAdded: function(response) {
            this.usersList.unshift(response.data.data);
        },
        deleteUser: function(id){
            this.userToDelete = id
            this.$modal.show('dialog', {
                title: this.$t('Delete confirm'),
                text: '<span class="text-danger font-weight-bold">Are you sure you want to delete this user account? <br />this action will permanently delete the account and all data associated with it.</span>',
                buttons: [{
                        title: '<span class="text-danger font-weight">Permanently delete</span>',
                        handler: () => {
                            this.doDeleteUser()
                            this.$modal.hide('dialog')
                        }
                    },{
                        title: 'Close'
                    }
                ]
                })

        },
        doDeleteUser: function(){
            this.isACModalVisible = false;
            axios
            .post(this.$myaccount_url+'employees/user/delete', {
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
            this.$modal.show(userModal, { userid: id, method: "update", handlers: {
                update: (res) => {
                    this.recordUpdated(res)
                }
            } })
        },
        addNew(){
            this.$modal.show(userModal, {  method: "create", handlers: {
                update: (res) => {
                    this.recordAdded(res)
                }
            } })
        }
    }
}
</script>