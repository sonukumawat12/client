<template>
<div class="container page-saved-replies">
<div class="row">
  <div class="col-md-12 col-lg-4 sticky-section">
    <div class="nav card sticky-section-item flex-column">
      <SavedRepliesList  v-on:deleteReply="deleteReply($event)" v-on:loadReply="loadReply($event)" :savedRepliesList='savedRepliesList'><div class="spinner-border mt-5 mb-5"></div></SavedRepliesList>
    </div>
  </div>
  <div class="col-md-12 col-lg-8">
      <SavedRepliesForm v-on:updateTitle='title = $event' v-on:newReply="newReply()" v-on:replyChanged="updateList($event)" :currentReply='currentReply' v-model='content' :title='title'></SavedRepliesForm>
  </div>
</div>
<v-dialog/>
</div>
</template>

<script>
import SavedRepliesList from './ListSavedReplies'
import SavedRepliesForm from './SavedRepliesForm'
import axios from 'axios'
import deleteConfirmation from "./../../../../common/components/DeleteConfirmation.vue";

export default {
  components: {
    SavedRepliesList, SavedRepliesForm, deleteConfirmation
  },
  data() {
    return {
      savedRepliesList: [],
      currentReply: null,
      title: null,
      content: null
    }
  },
  methods: {
      updateList: function(data){
        let index = this.savedRepliesList.findIndex(item => item.id === data.id);
        if(index === -1){
          this.savedRepliesList.push(new savedReply(data.id,data.title));
        }else{
          this.savedRepliesList.splice(index, 1, new savedReply(data.id,data.title));
        }
        this.currentReply = data.id;
        this.title = data.title;
        this.content = data.content;
      },
      loadReply: function(replyId){
        axios
        .get(this.$myaccount_url+'saved_replies/'+replyId)
        .then(response => {
          this.currentReply = response.data.data.id;
          this.title = response.data.data.title;
          this.content = response.data.data.content;
        })
      },
      newReply: function(){
        this.title = "";
        this.content = "";
        this.currentReply = null;
      },
      deleteReply: function(replyId){

        this.$modal.show(deleteConfirmation, {
        record_name: this.$t("ticket"),
        handlers: {
          confirm: () => {
            axios
              .post(this.$myaccount_url + "saved_replies/" + ticketId, {
                _method: "delete",
                SavedReply_id: replyId
              })
              .then((response) => {
                  let index = this.savedRepliesList.findIndex(item => item.id === replyId);
                  this.savedRepliesList.splice(index, 1);
                  if(replyId == this.currentReply){
                    this.currentReply = null
                    this.newReply()
                  }
              });
          },
        },
      });


        // this.$modal.show('dialog', {
        //     title: this.$t('Delete confirmation'),
        //     text: '<span class="text-danger font-weight-bold">'+this.$t('Are you sure you want to delete this record?')+'</span>',
        //     buttons: [{ title: this.$t('Close') },{
        //             title: '<span class="text-danger font-weight">'+this.$t('Delete')+'</span>',
        //             handler: () => {
        //                 axios
        //                 .post(this.$myaccount_url+'saved_replies', {_method: 'delete', SavedReply_id: replyId})
        //                 .then(response => {
        //                   let index = this.savedRepliesList.findIndex(item => item.id === replyId);
        //                   this.savedRepliesList.splice(index, 1);
        //                   if(replyId == this.currentReply){
        //                     this.currentReply = null
        //                     this.newReply()
        //                   }
        //                 })
        //                 this.$modal.hide('dialog')
        //             }
        //         }
        //     ]
        // })

    },
  },
  mounted () {
      axios
      .get(this.$myaccount_url+'saved_replies/fetch')
      .then(response => {
        var respData = response.data.data;
        var list = this.savedRepliesList;
        respData.forEach( function(i) { 
          list.push(new savedReply(i.id,i.title));
        });
      })
  }
}
class savedReply {
  constructor(id,title) {
    this.id = id;
    this.title = title;
  }
}
</script>