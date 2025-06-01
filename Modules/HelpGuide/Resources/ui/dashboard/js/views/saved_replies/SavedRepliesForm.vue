<template>
<div class="card">
  <div class="card-body">
    <button type='button' v-if="currentReply" class="btn btn-outline-primary float-end mb-1" v-on:click="newReply()">{{$t('New reply')}}</button>
      <div class="form-group">
        <label for="reply-title">{{$t('Reply title')}}</label>
        <input type="reply-title" v-model="replyTitle" class="form-control" id="reply-title" aria-describedby="reply-titleHelp" :placeholder="$t('Enter reply title')">
        <small id="reply-titleHelp" class="form-text text-muted"></small>
      </div>
      <text-editor :placeholder="$t('Type a reply')+'...'" uploadtype="ticket" v-model="editorContent"></text-editor>
      <button type="button" v-on:click="save()" class="btn btn-outline-primary float-end mt-3">{{$t('Save reply')}}</button>
  </div>
</div>
</template>

<script>
import axios from "axios"
export default {
  data() {
    return {}
  },
  props: [
    "title",
    "value",
    "currentReply"
  ],
  methods: {
    save: function(e){

      this.$toastClear()
      if(!this.title){
        this.$showError(this.$t("title can not be empty"))
        return;
      }
      if(!this.editorContent){
        this.$showError(this.$t("reply content can not be empty"));
        return;
      }

      axios
      .post(this.$myaccount_url+'saved_replies',{
        title: this.title,
        content: this.editorContent,
        _method: this.currentReply == null? 'post' : 'put',
        SavedReply_id: this.currentReply
      })
      .then(response => {
        this.$showSuccess("Saved");
        this.$emit('replyChanged', response.data.data)
      })
    },
    newReply: function(){
      this.$emit("newReply")
    }
  },
  computed: {
    editorContent: {
      get: function () {return this.value},
      set: function (value) {
        this.$emit('input', value)
      }
    },
    replyTitle: {
      get: function () { return this.title },
      set: function (newValue) { this.$emit('updateTitle',newValue) }
    }

  }
}
</script>
