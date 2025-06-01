<template>
<div>
    <pre-loader v-if="isLoading" />
    <div v-else> 
        <ul v-if="listCategories.length" class="list-group list-group-flush ">
            <li class="list-group-item d-flex justify-content-between align-items-center text-capitalize p-2" v-for="category in listCategories" v-bind:key="category.id">
                <a :href="$myaccount_url+'tickets?category='+category.id" class="text-body">
                <categoryItemLabel :letter="category.name.substring(0,1)" :color="stringToColor(category.name)"></categoryItemLabel>
                <span>{{category.name}}</span>
                </a>
                <div>
                    <span class="badge badge-orange">{{category.tickets_count}}</span>
                </div>
            </li>
        </ul>
        <div class="text-center p-3" v-else>{{ $t('No categories') }}</div>
    </div>
    <v-dialog/>
</div>
</template>
<script>
import axios from 'axios';
import categoryItemLabel from './CategoryItemLabel'
export default {
    data() {
        return {
            isLoading: false,
            listCategories: []
        }
    },
    async mounted () {
        this.isLoading = true;
        await axios
        .post(this.$myaccount_url+'manage_categories/categories_tickets')
        .then(response => {
            var respData = response.data.data;
            var newCategories = response.data.data
            this.listCategories = this.listCategories.concat(newCategories);
            this.isLoading = false;
        })
  },
  components: {
      categoryItemLabel
  }
}
</script>
