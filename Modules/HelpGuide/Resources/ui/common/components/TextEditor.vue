<template>
   <div class="text-editor-container">
     <editor
       v-model="econtent"
       :init="editorOptions"
       :placeholder="placeholder"
       :tinymce-script-src="$base_url+'assets/libs/tinymce/tinymce.min.js'"
     >loading...</editor>
   </div>
</template>

<script>
import axios from "axios";
import Editor from '@tinymce/tinymce-vue'
var plugins = [
           'autolink lists link image preview anchor',
           'searchreplace code',
           'media table paste code',
           'codesample'
         ]

var basicPlugins = [
           'autolink lists link image anchor',
           'searchreplace code',
           'table paste'
         ]

var toolbar = 'undo redo | formatselect | bold italic forecolor backcolor | \
           alignleft aligncenter alignright alignjustify | \
           bullist numlist outdent indent | link removeformat image media | code preview searchreplace anchor codesample'

var basicToolbar = 'undo redo | formatselect | bold italic forecolor backcolor | \
           bullist numlist outdent indent removeformat image | \
           alignleft aligncenter alignright alignjustify'

export default {
   data() {
    return {
      editorOptions: {
         height: 300,
         menubar: false,
         images_upload_handler: false,
         relative_urls : false,
         remove_script_host : false,
         plugins: basicPlugins,
         toolbar: basicToolbar
       }
    };
  },
   components: {
     'editor': Editor
   },
   beforeMount(){

     if( this.editorHeight ) {
       this.editorOptions.height = this.editorHeight
     }

     if(this.uploadtype){
       this.editorOptions.images_upload_handler = this.handleImageAdded
     }
     if(this.options == 'full'){
       this.editorOptions.toolbar = toolbar
       this.editorOptions.plugins = plugins
     }
   },
   props: ["placeholder", "editorHeight", "value", 'options', 'uploadtype'],
   mounted(){

   },
   computed: {
      econtent: {
          get: function () {return this.value},
          set: function (value) { this.$emit('input', value); }
      }
  },
  methods: {
    handleImageAdded: function(blobInfo, success, failure, progress) {
      var formData = new FormData();
      formData.append('file', blobInfo.blob(), blobInfo.filename());

      axios({
        url: this.$myaccount_url+'upload?type='+this.uploadtype,
        method: "POST",
        data: formData
      })
        .then(result => {
          if(result.data.status == 'ok'){
            let url = result.data.data; // Get url from response
            success(url);
          } else {
            failure('Image upload failed');
          }
        })
        .catch(err => {
          if(err.response.data.hasOwnProperty('message'))
            failure(err.response.data.message);
          else
            failure(err);
        });
    }
  }
 }
 </script>

<style>
.tox-statusbar__branding {
  display: none;
}

.text-editor-container textarea {
  display: none;
}
</style>
