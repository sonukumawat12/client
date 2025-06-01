<template>
<div class="card">
    <div class="card-header p-2">
      <div class="w-100">
        <span v-if="method == 'create'">{{$t('add employee')}}</span>
        <span v-else>{{$t('Edit employee')}}</span>
        <button type="button" @click="$emit('close')" class="float-end">
          <i class="bi bi-x-lg"></i>
        </button>
      </div>
    </div>

    <div class="card-body" v-if="isLoading">
      <pre-loader />
    </div>

    <template v-else>
    <div class="card-body">
         <form v-on:keyup.enter="save">
            <div class="form-group">
              <label for="email">{{$t('Account email')}}</label>
              <input type="email" class="form-control" id="email" :placeholder="$t('Account email')" v-model="fields.email" />
            </div>
            <div class="form-group">
              <label for="name">{{$t('Name')}}</label>
              <input type="text" class="form-control" id="name" :placeholder="$t('Name')" v-model="fields.name" />
            </div>

            <div class="form-group">
              <label for="name">{{$t('Phone No')}}</label>
              <input type="text" class="form-control" id="phone_no" :placeholder="$t('Phone No')" v-model="fields.phone_no" />
            </div>

            <div class="form-group">
              <label for="name">{{$t('Account role')}}</label>
              <v-select :placeholder="$t('Choose a role')" v-model="fields.role" :options="roles"></v-select>
            </div>

            <div class="form-group">
              <label for="name">{{$t('Employee Groups')}}</label>
              <v-select :placeholder="$t('Choose a group')" v-model="fields.groups" :options="groups" multiple></v-select>
            </div>

            <div class="form-group">
              <label for="password">{{$t('Password')}}</label>
              <div class="input-group mb-3">
                <input type="password" class="form-control" ref="password" :placeholder="$t('Password')" v-model="fields.password" autocomplete="off">
                <div class="input-group-append">
                   <generate-password v-on:generated="pwdGenerated($event)"/>
                </div>
              </div>
              <small id="passwordHelpBlock" class="form-text text-muted">{{$t('Your password must be 8-20 characters long')}}</small>
            </div>

            <div class="form-group">
              <label for="password_confirmation">{{$t('Password confirmation')}}</label>
              <input type="password" id="password_confirmation" class="form-control" :placeholder="$t('Password confirmation')" v-model="fields.password_confirmation" autocomplete="off">
            </div>

          </form>
    </div>
    <div class="card-footer">
      <div class="w-100"><button type="button" class="btn btn-primary float-end" v-on:click="save()">{{$t('Save')}}</button></div>
    </div>
    </template>

</div>
</template>

<script>
import axios from 'axios';

import VueSelect from 'vue-select'
Vue.component('v-select', VueSelect)

export default {
    data(){
        return {
          fields: {
            user_id: null,
            email: null,
            name: null,
            phone: null,
            role: null,
            password: null,
            groups: null,
            password_confirmation: null
          },
          roles: [],
          groups: [],
          isLoading: false
        }
    },
    mounted () {
      Object.assign(this.$data.fields, formFields())
      this.loadRoles()
      this.loadEmployeeGroups()
      if(this.method == 'update') this.loadAccount()
    },
    props: {
      method: {
        type: String,
        default: 'create',
        validator: method => {
          return method === 'create' || method === 'update'
        }
      },
      userid: null,
      handlers: null
    },
    methods: {
      save(){
          axios
          .post(this.$myaccount_url+'employees/'+this.method,{
            fields: this.fields
          })
          .then(response => {
            if(response.data.status == "ok"){
                this.$showSuccess(response.data.message)
                if(this.method ) this.handlers.update(response)
                else this.handlers.create(response)
            }else{
                let messages = Object.values(response.data.messages);
                this.$showError(messages.join('<br />'))
            }
          })
      },
      loadRoles(){
        axios
        .post(this.$myaccount_url+'roles/list', {type: 'employee'})
        .then(response => {
            var respData = response.data.data;
            var list = this.roles;
            respData.forEach( function(i) {
                list.push(new role(i.id,i.name));
            });
        })
      },
      loadEmployeeGroups(){
        axios
        .get(this.$myaccount_url+'employee-groups/fetch')
        .then(response => {
            var respData = response.data.data;
            var list = this.groups;
            respData.forEach( function(i) {
                list.push(new group(i.id,i.name));
            });
        })
      },
      loadAccount(){
        if(this.method == "update" && this.userid != null){
          this.isLoading = true
          this.fields.user_id = this.userid
          axios
          .post(this.$myaccount_url+'employees/'+this.userid)
          .then(response => {
              var account = response.data.data;
              this.fields.name = account.name
              this.fields.phone_no = account.phone_no
              this.fields.email = account.email
              let accountRoles = Object.keys(account.roles)
              let accountGroups = Object.keys(account.groups)

              let roleToselect = null
              let groupToSelect = [];

              this.roles.forEach(function(role){
                  if(role.value == accountRoles[0]){
                      roleToselect = role
                  }
              })

              
              this.groups.forEach(function(group){                
                  if(accountGroups.includes((group.value).toString())){
                    groupToSelect.push(group)
                  }
              })


              this.fields.role = roleToselect
              this.fields.groups = groupToSelect
              this.isLoading = false
          })
        }
      },
      pwdGenerated: function(pwd){
        this.fields.password = pwd
        this.$refs.password.type = 'text'
      }
    }
}
class role {
  constructor(id,name) {
    this.label = name;
    this.value = id;
  }
}

class group {
  constructor(id,name) {
    this.label = name;
    this.value = id;
  }
}

function formFields(){
  return {
    user_id: null,
    email: null,
    name: null,
    phone: null,
    role: null,
    groups: null,
    password: null,
    password_confirmation: null
  }
}

</script>
