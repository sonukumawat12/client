<template>
<div class="card">
    <div class="card-header p-2">
      <div class="w-100">
        <span v-if="method == 'create'">{{$t('Add customer')}}</span>
        <span v-else>{{$t('Edit customer')}}</span>
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
              <label for="name">{{$t('Employee Groups')}}</label>
              <v-select :placeholder="$t('Choose a group')" v-model="fields.groups" :options="groups" multiple></v-select>
            </div>

            <div class="form-group">
              <label for="password">{{$t('Password')}}</label>
              <div class="input-group">
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
      <div class="w-100">
        <save-btn class="btn btn-primary float-end" :in-action="isSaving" v-on:click.native="save()" />
        </div>
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
            phone_no: null,
            password: null,
            password_confirmation: null,
            groups: null
          },
          groups: [],
          isLoading: false,
          isSaving: false
        }
    },
    mounted () {
      Object.assign(this.$data.fields, formFields())
      this.loadCustomerGroups()

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
      async save(){
          this.isSaving = true
          await axios
          .post(this.$myaccount_url+'customers/'+this.method,{
            user_id: this.fields.user_id,
            email: this.fields.email,
            name: this.fields.name,
            phone_no: this.fields.phone_no,
            groups: this.fields.groups,
            password: this.fields.password,
            password_confirmation: this.fields.password_confirmation,
          })
          .then(response => {
            if(response.data.status == "ok"){
                this.$showSuccess(response.data.message)
                this.handlers.update(response)
            }else{
                let messages = Object.values(response.data.messages);
                this.$showError(messages.join('<br />'))
            }
          })

          this.isSaving = false
      },
      loadAccount(){
        if(this.method == "update" && this.userid != null){
          this.isLoading = true
          this.fields.user_id = this.userid
          axios
          .post(this.$myaccount_url+'customers/'+this.userid)
          .then(response => {
              var account = response.data.data;
              this.fields.name = account.name
              this.fields.email = account.email
              this.fields.phone_no = account.phone_no

              let accountGroups = Object.keys(account.groups)

              let groupToSelect = [];

              this.groups.forEach(function(group){                               
                  if(accountGroups.includes((group.value).toString())){
                    groupToSelect.push(group)
                  }
              })


              this.fields.groups = groupToSelect

              this.isLoading = false
          })
        }
      },
      loadCustomerGroups(){
        axios
        .get(this.$myaccount_url+'customer-groups/fetch')
        .then(response => {
            var respData = response.data.data;
            var list = this.groups;
            respData.forEach( function(i) {
                list.push(new group(i.id,i.name));
            });
        })
      },
      pwdGenerated: function(pwd){
        this.fields.password = pwd
        this.$refs.password.type = 'text'
      }
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
    password: null,
    phone_no: null,
    groups: null,
    password_confirmation: null
  }
}

</script>
