<template>
    <div>
        <ul class="nav nav-tabs mb-3 settings-nav" >
            <li class="nav-item">
                <router-link class="nav-link py-2 border-0" to="/">{{$t('General')}}</router-link>
            </li>

           <li class="nav-item">
                <router-link class="nav-link py-2 border-0" to="/advanced_settings">{{$t('Advanced settings')}}</router-link>
            </li>

            <!-- <li class="nav-item">
                <router-link class="nav-link py-2 border-0" to="/custom_fields">{{$t('Custom fields')}}</router-link>
            </li> -->

            <li class="nav-item">
                <router-link class="nav-link py-2 border-0" to="/ticket">
                  {{$t('Ticket')}}
                </router-link>
            </li>
            
            <li class="nav-item" v-if="$allow('manage_acl')">
              <router-link class="nav-link py-2 border-0" :to="{name: 'settings.roles_and_permissions'}">
              {{$t('Roles')}}
              </router-link>
            </li>

            <li class="nav-item" v-if="$allow('view_error_logs')">
                <a class="nav-link py-2 border-0" target="_blank" :href="$myaccount_url+'error_logs'">{{$t('Error logs')}} <i class="bi bi-box-arrow-up-right"></i></a>
            </li>
        </ul>
        
        <router-view></router-view>
    </div>
</template>

<script>

import VueRouter from 'vue-router'
const  General = () => import('./General')
const  AdvancedSettings = () => import('./AdvancedSettings')
const  CustomFields = () => import('./custom_fields/CustomFields')
import TicketSettings from './TicketSettings'

const  ACLMaster = () => import('./../acl/Master')
const  RolesAndPermissions = () => import('./../acl/RolesAndPermissions')
const  EditRole = () => import('./../acl/EditRole')

const routes = [
    { 
        name: "settings",
        path: '/',
        component: General
    },
    {
        name: "settings.advanced_settings",
        path: '/advanced_settings',
        component: AdvancedSettings
    },
    // {
    //     name: "settings.custom_fields",
    //     path: '/custom_fields',
    //     component: CustomFields
    // },
    {
        name: "settings.ticket",
        path: '/ticket',
        component: TicketSettings
    },
    {
        path: '/roles_and_permissions/:id',
        component: ACLMaster,
        children: [
            { 
                path: '/roles_and_permissions',
                component: RolesAndPermissions, 
                name: "settings.roles_and_permissions"
            },
            { 
                name: 'settings.roles_and_permissions.edit',
                path: 'edit',
                props: true,
                component: EditRole
            }
        ]
    },
]

const router = new VueRouter({
  routes,
  linkExactActiveClass: "active"
})

export default {
  router
}
</script>

<style lang="scss">
.container.page-settings.mb-3 {
    background: none;
    padding: 0;
}

.settings-nav.nav a {
    color: #444;
    padding: 15px !important;
    border-bottom: transparent !important;
}

.settings-nav.nav .active {
    background: white;
    color: #444;
    font-weight: bold;
    border-bottom: 2px solid #3F51B5 !important;
}

.settings-nav.nav a.active:hover { color: #000 !important; }

.settings-nav.nav-tabs { background: white; border-bottom: none; }

.settings-nav.nav li {
    padding: 2px;
}
</style>