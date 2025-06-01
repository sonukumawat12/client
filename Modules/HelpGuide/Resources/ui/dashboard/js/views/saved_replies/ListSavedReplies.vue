<template>
<div class="card-body p-0">
    <div class="m-2">
        <input class="form-control searchField" v-model="search" type="text" :placeholder="$t('Search saved replies')">
        <button type="button" class="close" v-if="search" v-on:click="search = ''" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
    </div> 
    <vue-custom-scrollbar v-if="savedRepliesList.length" class="scroll-area"  :settings="settings" @ps-scroll-y="scrollHanle">
    <ul class="list-group list-group-flush p-2 savedRepliesList">
        <li class="list-group-item p-2" v-for="reply in filteredList" :id='"sreply-"+reply.id' v-bind:key="reply.id">
          <div v-on:click='loadReply(reply.id)'>{{ reply.title }}</div><span v-on:click='deleteReply(reply.id)' class="float-end deleteReplyButton"><i class="bi bi-trash-alt"></i></span>
        </li>
    </ul>
    </vue-custom-scrollbar>
    <empty-state v-else text="No saved replies" />
</div>
</template>

<script>
import vueCustomScrollbar from 'vue-custom-scrollbar'
export default {
  components: {
    vueCustomScrollbar
  },
  data() {
    return {
        settings: {
            maxScrollbarLength: 60
        },
        search: '',
    }
  },
  props: [
    "savedRepliesList"
  ],
  methods: {
    scrollHanle(evt) {
      console.log(evt)
    },
    deleteReply: function(replyId){
      this.$emit('deleteReply',replyId)
    },
    loadReply: function(id){
      this.$emit('loadReply',id)
    }
  },
  computed: {
    filteredList() {
      return this.savedRepliesList.filter(item => {
        return item.title.toLowerCase().includes(this.search.toLowerCase())
      })
    }
  },
  mounted () {
   
  }
}

</script>