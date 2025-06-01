<template>
  <div class="card">
    <div class="card-header p-2">
      <div class="w-100">
        {{$t('Edit customer group')}}
        <button type="button" @click="$emit('close')" class="float-end">
          <i class="bi bi-x-lg"></i>
        </button>
      </div>
    </div>

    <div class="card-body" v-if="isLoading">
      <pre-loader />
    </div>

    <template v-else>
    <div class="card-body p-2 mx-2">
      <div class="form-group">
        <label for="category_name">{{$t('Group name')}}</label>
        <input
          type="category"
          class="form-control"
          v-model="categoryName"
          id="category_name"
          aria-describedby="categoryHelp"
          :placeholder="$t('Groups name')"
        />
      </div>
      
    </div>
    <div class="card-footer">
      <button type="submit" class="btn btn-outline-primary btn-sm float-end" v-on:click="save()">
        <i class="bi bi-check"></i> {{$t('save')}}
      </button>
    </div>
    </template>

  </div>
</template>
<script>
import axios from "axios";
import VueSelect from 'vue-select'
Vue.component('v-select', VueSelect)
export default {
  data() {
    return {
      categoryName: null,
      isLoading: true,
      listCategories: [],
    };
  },
  props: ['category','handlers'],
  async mounted() {
    await axios.post(this.$myaccount_url+"customer-groups/view", {id: this.category}).then((response) => {
      this.categoryName = response.data.data.name;
    });
    await axios
    .get(this.$myaccount_url+'customer-groups/fetch')
    .then(response => {
      this.listCategories = response.data.data;
    })

    this.isLoading = false
  },
  methods: {
      save(){

          let fields = {
              name: this.categoryName,
              id: this.category,
              _method: 'put'
          }

          
          axios
          .post(this.$myaccount_url+'customer-groups/'+this.category, fields)
          .then(response => {
            if(response.data.status == "ok"){
                this.$showSuccess(response.data.message)
                this.handlers.update(response);
            }else{
                let messages = Object.values(response.data.messages);
                this.$showError(messages.join('<br />'))
            }

          })
      },
      parentCandidate(){
        return  this.listCategories.filter(item => (item.parent == null && item.active));
      }
  },
};
</script>
