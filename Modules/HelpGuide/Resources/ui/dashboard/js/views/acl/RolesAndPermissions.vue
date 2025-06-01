<template>
<div>
    <pre-loader v-if="isLoading" />
    <div v-else class="row">
    <div class="col" v-for="(role, index) in roles" v-bind:key="index">
        <div class="card">
            <div class="card-body p-3">
                <router-link class="btn btn-sm btn-link rounded-circle float-end" :to="{ name: 'settings.roles_and_permissions.edit', params: { id: role.id }}">
                    <i class="bi bi-pen"></i>
                </router-link>
                <div class="flex-fill">
                <div class="text-capitalize font-weight-bold">{{ role.name.replaceAll("_", " ") }}</div>
                <div><button class="btn btn-link py-0">{{ role.permissions_count }} {{ $t('permissions') }}</button></div>
                <div><button class="btn btn-link py-0">{{ role.users_count }} {{ $t('Users') }}</button></div>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>
</template>

<script>
import Axios from 'axios'
export default {
    data(){
        return {
            isLoading: true,
            roles: []
        }
    },
    mounted(){
        this.loadRoles()
    },
    methods: {
        loadRoles(){
            this.isLoading = true
            Axios.post(this.$myaccount_url+'acl/roles')
            .then((res) => {
                this.roles = res.data.data
            })
            .catch((e) => { this.$showError(e) })
            .then( () => { this.isLoading = false })
        }
    }
}
</script>
