<template>
    <div class="search-form" v-click-outside="closeSearchList">
        <div class="form-group m-0">
            <input type="text" @input="onChange" @focus="openSearchList" v-model="query" class="form-control" :placeholder="$t('Search for articles')+'...'">
        </div>
        <div class="search-autocomplete"> 
            <div class="search-result-list" v-show="isOpen || isLoading" >
                <div v-if="isLoading" >
                    <pre-loader />
                </div>
                <ul v-else class="list-group">
                    <li
                        v-for="(result, i) in results.data"
                        :key="i"
                        class="search-result-item list-group-item">
                        <a :href="results.target_url+result.id">{{ result.title }}</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            isOpen: false,
            query: '',
        }
    },
    props: [
        "items",
        "isLoading"
    ],
    methods: {
      onChange(event) {
        this.$emit('input', this.query)
      },
      closeSearchList(){
        this.isOpen = false;
      },
      openSearchList(event){
        if(this.items.data.length > 0) this.isOpen = true;
      }
    },
    computed: {
        results: function(){
            if(this.items.data.length > 0)  this.isOpen = true;
            return this.items
        }
    }
}
</script>
