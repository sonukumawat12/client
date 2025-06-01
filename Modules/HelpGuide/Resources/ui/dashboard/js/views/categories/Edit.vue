<template>
  <div class="card">
    <div class="card-header p-2">
      <div class="w-100">
        {{$t('Edit category')}}
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
        <label for="category_name">{{$t('Category name')}}</label>
        <input
          type="category"
          class="form-control"
          v-model="categoryName"
          id="category_name"
          aria-describedby="categoryHelp"
          :placeholder="$t('Category name')"
        />
      </div>
      <div class="form-group">
          <v-select :placeholder="$t('Parent category')" v-model="parent" label="name" :options="parentCandidate()" />
      </div>
      <div class="form-group">
          <div class="form-check">
              <input type="checkbox" class="form-check-input" id="edit-has-ticket" v-model="hasTicket">
              <label class="form-check-label" for="edit-has-ticket" data-toggle="tooltip" data-placement="top" :title="$t('Use this category for tickets')" >{{$t('Use for tickets')}}</label>
          </div>
      </div>
      <div class="form-group">
          <div class="form-check">
              <input type="checkbox" class="form-check-input" v-model="isFeatured"  id="edit-show-frontpage">
              <label class="form-check-label" for="edit-show-frontpage" data-toggle="tooltip" data-placement="top" :title="$t('Display this category on front page')">{{$t('Show on front page')}}</label>
          </div>
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
      parent: null,
      listCategories: [],
      isFeatured: 1,
      hasTicket: 1,
    };
  },
  props: ['category','handlers'],
  async mounted() {
    await axios.post(this.$myaccount_url+"manage_categories/view", {id: this.category}).then((response) => {
      this.categoryName = response.data.data.name;
      this.parent = response.data.data.parent;
      this.isFeatured = response.data.data.is_featured;
      this.hasTicket = response.data.data.has_ticket;
    });
    await axios
    .get(this.$myaccount_url+'manage_categories/fetch')
    .then(response => {
      this.listCategories = response.data.data;
    })

    this.isLoading = false
  },
  methods: {
      save(){

          let fields = {
              name: this.categoryName,
              is_featured: this.isFeatured ? 1 : 0,
              has_ticket: this.hasTicket ? 1 : 0,
              active: this.isActive ? 1 : 0,
              id: this.category,
              _method: 'put'
          }

          if(this.parent) fields['parent_id'] = this.parent.id

          axios
          .post(this.$myaccount_url+'manage_categories/'+this.category, fields)
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
