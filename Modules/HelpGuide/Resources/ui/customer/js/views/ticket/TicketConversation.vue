<template>
  <div class="card">
    <div class="card-body p-0">
      <div class="ticket-closed alert alert-secondary m-4" v-if="ticketStatus == 'closed'">
        {{ $t("This ticket is closed and can not reopened or updated.") }}
      </div>

      <div class="editor m-3 clearfix" v-else>
        <text-editor ref="editor" uploadtype="ticket" :placeholder="$t('Add a reply') + '...'"
          v-model="editorContent"></text-editor>

        <div class="row">
          <div class="col-md-12 py-2">
            <div class="btn-group float-end" role="group" aria-label="Button group with nested dropdown">
              <button type="button" class="btn btn-outline-primary" :disabled="!editorContent"
                v-on:click="save('open')">
                {{ $t("Submit as") }} <strong>{{ $t("Open") }}</strong>
              </button>
              <div class="btn-group" role="group">
                <button id="btnGroupDrop1" type="button" class="btn btn-outline-primary dropdown-toggle"
                  data-bs-toggle="dropdown" aria-expanded="false"></button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="btnGroupDrop1">
                  <li>
                    <button class="dropdown-item" v-on:click="save('resolved')">
                      {{ $t("Resolved") }}
                    </button>
                  </li>
                  <li>
                    <button class="dropdown-item" v-on:click="save('closed')">
                      {{ $t("Closed") }}
                    </button>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div>
        <transition-group name="fade" mode="out-in">
          <div v-for="item in conversation" v-bind:key="item.id" class="ticket-c"
            v-bind:class="{ 'my-msg': item.is_owner }">
            <hr class="m-0" />
            <div class="media p-2 c-item ticket-reply-item">
              <div class="media-body">
                <div>
                  <img class="
                      me-3
                      bd-placeholder-img
                      rounded rounded-circle
                      float-start
                    " width="40" height="40" :src="item.user.avatar" :alt="item.user.name" />
                  <div>
                    <span class="h5 my-0 d-block text-capitalize">{{
                      item.user.name
                      }}</span>
                    <small class="text-muted mb-2 d-block">{{
                      item.created_at
                      }}</small>
                  </div>
                </div>

                <div class="ticket-reply-content">
                  <p v-html="item.content" class="c-msg">{{ item.content }}</p>
                </div>

                <div class="reply-attachments mt-4 pt-2" v-if="item.attachments && item.attachments.length">
                  <div>{{ $t("Ticket attachments") }}</div>
                  <template v-for="(attachment, index) in item.attachments">
                    <fancy-box v-bind:key="index" v-if="
                      ['image', 'pdf'].includes(
                        fileType(attachment.file_type)
                      )
                    " :href="attachment.url" target="_blank" data-fancybox="gallery"
                      class="btn btn-outline-secondary mb-1 btn-sm attachment">
                      <i v-bind:class="fileIcon(attachment.file_type)" class="bi bi-2x"></i>
                    </fancy-box>

                    <a class="btn btn-outline-secondary mb-1 btn-sm attachment" v-else :href="attachment.url"
                      v-bind:key="index">
                      <i v-bind:class="fileIcon(attachment.file_type)" class="bi bi-2x"></i>
                    </a>
                  </template>
                </div>

                <div class="user-signature" v-if="item.user.signature">
                  {{ item.user.signature }}
                </div>
              </div>
            </div>
          </div>
        </transition-group>
        <pre-loader v-if="isLoading" />
        <div class="text-center p-2" v-if="next_page && !isLoading">
          <button @click="loadConversation(next_page)" class="btn btn-outline-secondary">
            {{ $t("Load more") }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
export default {
  data() {
    return {
      conversation: [],
      next_page: 1,
      editorContent: null,
      editorValue: null,
      isLoading: true,
    };
  },
  props: ["ticketid", "type", "ticketStatus"],
  methods: {
    loadConversation(url) {
      const hash = window.location.hash;
      const queryParamsStart = hash.indexOf('?');
      if (queryParamsStart !== -1) {
        const queryParamsString = hash.substring(queryParamsStart + 1);
        const urlParams = new URLSearchParams(queryParamsString);
        let notificationId = urlParams.get('notification_id');
        if (notificationId) {
          url = url + '?notification_id='+ notificationId;
          console.log(url);
        }
      }
      this.isLoading = true;
      axios
        .get(url)
        .then((response) => {
          var newConversation = response.data.data;
          this.conversation = this.conversation.concat(newConversation);
          this.next_page = response.data.links.next;
          this.isLoading = false;
          this.addLinkToImage();
        })
        .catch((error) => {
          console.log(error);
        });
    },
    addLinkToImage() {
      // var img = document.querySelector(".ticket-reply-content img");
      // img.each(function () {
      //   var element = jQuery(this);
      //   if (this.parentElement.tagName != "A") {
      //     var a = jQuery("<a />", {
      //       href: element.attr("src"),
      //       "data-lightbox": "Article image",
      //     });
      //     element.wrap(a);
      //   }
      // });
    },
    save(status) {
      this.$toastClear();
      if (!this.editorContent && status != "resolved" && status != "closed") {
        this.$showError(this.$t("Please type a reply"));
        return;
      }
      axios
        .post(this.$api_url + `tickets/${this.ticketid}/conversation/add`, {
          content: this.editorContent,
          status: status,
        })
        .then((r) => {
          this.$refs.editor.econtent = null;
          this.editorContent = "";
          this.$showSuccess(r.data.message);
          if (r.data.data) {
            var newConversation = r.data.data;
            var oldConversation = this.conversation;
            var emptyArray = [];
            this.conversation = emptyArray.concat(
              newConversation,
              oldConversation
            );
          }
          this.$emit("update", status);
        })
        .catch((e) => {
          this.$showError(e);
        });
    },
    fileIcon($ftype) {
      if (this.fileType($ftype) == "pdf") return "bi-file-pdf";
      if (this.fileType($ftype) == "image") return "bi-file-image";
      if (this.fileType($ftype) == "doc") return "bi-file-word";
      if (this.fileType($ftype) == "zip") return "bi-file-archive";
      return "bi-file";
    },
    fileType($filetype) {
      if ($filetype.includes("application/pdf")) return "pdf";
      if ($filetype.includes("image")) return "image";
      if ($filetype.includes("officedocument")) return "doc";
      if ($filetype.includes("zip")) return "zip";
      return null;
    },
  },
  mounted() {
    this.loadConversation(
      this.$api_url + "tickets/" + this.ticketid + "/conversation"
    );
  },
};

class savedReply {
  constructor(id, title) {
    this.label = title;
    this.value = id;
  }
}
</script>


<style>
.ticket-reply-item img,
.ticket-reply-item video {
  max-width: 100%;
  height: auto;
}

.my-msg {
  background: #2196f30f;
}

.reply-attachments .btn.attachment {
  padding: 0.5em 0.5em;
  font-size: 0.7em;
  border: none;
  margin: 0 3px;
  color: #7d7d7d;
}

.reply-attachments .btn.attachment:hover {
  background: white;
}

.reply-attachments {
  border-top: 1px dashed #0000002b;
}
</style>
