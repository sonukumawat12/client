<template>
  <div class="card">
    <div class="card-body p-0">
      <div class="editor m-3 clearfix">
        <text-editor
          :placeholder="$t('Add a reply') + '...'"
          uploadtype="ticket"
          v-model="editorContent"
        />

        <div class="mt-1">
          <div class="listAttachments">
            <vueDropzone
              id="panelDropzone"
              ref="TCDropzone"
              v-on:vdropzone-max-files-exceeded="
                $toast.error($t('Too many attachments'))
              "
              :options="dropzoneOptions"
              v-on:vdropzone-error="failed"
              v-on:vdropzone-success="checkUploadedFile"
              v-on:vdropzone-removed-file="removeImage"
              :useCustomSlot="true"
            >
              <div class="dropzone-custom-content">
                <h3 class="dropzone-custom-title">
                  {{ $t("drop_files_upload") }}
                </h3>
                <div class="subtitle">{{ $t("click_select_file") }}</div>
                <small class="small">{{
                  $t("file_allowed_image_pdf__doc_zip")
                }}</small>
              </div>
            </vueDropzone>
          </div>
        </div>

        <div class="d-flex my-2">
          <div class="flex-grow-1 me-2">
            <v-select
              v-on:input="loadSelectedReply($event)"
              :placeholder="$t('Choose from saved replies')"
              :options="savedReplies"
            ></v-select>
          </div>
          <div>
            <div
              class="btn-group"
              role="group"
              aria-label="Button group with nested dropdown"
            >
              <button
                type="button"
                class="btn btn-outline-primary"
                :disabled="!editorContent"
                v-on:click="save('open')"
              >
                {{ $t("Submit as") }} <strong>{{ $t("Open") }}</strong>
              </button>
              <div class="btn-group" role="group">
                <button
                  id="btnGroupDrop1"
                  type="button"
                  class="btn btn-outline-primary dropdown-toggle"
                  data-bs-toggle="dropdown"
                  aria-expanded="false"
                ></button>
                <ul
                  class="dropdown-menu dropdown-menu-end"
                  aria-labelledby="btnGroupDrop1"
                >
                  <li>
                      <button class="dropdown-item" v-on:click="save('resolved')">{{ $t("Resolved") }}</button>
                    </li>
                    <li>
                      <button class="dropdown-item" v-on:click="save('closed')">{{ $t("Closed") }}</button>
                    </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>

      <transition-group name="fade" mode="out-in">
        <div
          v-for="(item, i) in conversation"
          v-bind:key="i + 1"
          v-bind:class="{ 'my-msg': item.is_owner }"
        >
          <hr class="m-0" />
          <div class="media p-2 ticket-reply-item">
            <div class="media-body">
              <div class="reply-header d-flex">
                <div class="flex-grow-1">
                  <img
                    class="
                      me-3
                      bd-placeholder-img
                      rounded rounded-circle
                      float-start
                    "
                    width="40"
                    height="40"
                    :src="item.user.avatar"
                    :alt="item.user.name"
                  />
                  <div>
                    <span class="h5 my-0 d-block text-capitalize">{{
                      item.user.name
                    }}</span>
                    <small class="text-muted mb-2 d-block">{{
                      item.created_at
                    }}</small>
                  </div>
                </div>
                <div class="reply-options ms-auto">
                  <button
                    class="btn btn-outline-danger rounded-circle btn-sm"
                    v-if="canDeleteReply"
                    v-on:click="deleteReply(item.id)"
                  >
                    <i class="bi bi-trash"></i>
                  </button>

                  <button
                    class="btn btn-outline-secondary mx-1 rounded-circle btn-sm"
                    v-if="$allow('update_ticket_reply') && editReply != item.id"
                    v-on:click="edit(item)"
                  >
                    <i class="bi bi-pencil"></i>
                  </button>

                  <button
                    class="btn btn-outline-secondary mx-1 rounded-circle btn-sm"
                    v-if="editReply && editReply == item.id"
                    v-on:click="editReply = null"
                  >
                    <i class="bi bi-x"></i>
                  </button>
                </div>
              </div>
              <div
                v-if="$allow('update_ticket_reply') && editReply == item.id"
                class="clearfix"
              >
                <text-editor
                  :placeholder="$t('Add a reply') + '...'"
                  uploadtype="ticket"
                  v-model="replyEditorContent"
                  class="rounded"
                  editor-height="200"
                />
                <save-btn
                  class="btn btn-outline-primary float-end btn-sm my-2"
                  @click.native="updateReply(item.id)"
                  :in-action="updatingReply"
                  :text="'Update'"
                />
              </div>
              <div v-else class="ticket-reply-content">
                <p v-html="item.content">{{ item.content }}</p>
                <div
                  class="reply-attachments mt-4 pt-2"
                  v-if="item.attachments && item.attachments.length"
                >
                  <div>{{ $t("Ticket attachments") }}</div>
                  <template v-for="(attachment, index) in item.attachments">
                    <fancy-box
                      v-bind:key="index"
                      v-if="
                        ['image', 'pdf'].includes(
                          fileType(attachment.file_type)
                        )
                      "
                      :href="attachment.url"
                      target="_blank"
                      data-fancybox="gallery"
                      class="btn btn-outline-secondary mb-1 py-0 px-1 fs-4 btn-attachment"
                    >
                      <i
                        v-bind:class="fileIcon(attachment.file_type)"
                        class="bi bi-2x"
                      ></i>
                    </fancy-box>

                    <a
                      class="btn btn-outline-secondary mb-1 py-0 px-1 fs-4 btn-attachment"
                      v-else
                      :href="attachment.url"
                      v-bind:key="index"
                    >
                      <i
                        v-bind:class="fileIcon(attachment.file_type)"
                        class="bi bi-2x"
                      ></i>
                    </a>
                  </template>
                </div>
              </div>
              <div class="user-signature" v-if="item.user.signature">
                {{ item.user.signature }}
              </div>
            </div>
          </div>
        </div>
      </transition-group>
    </div>
  </div>
</template>

<script>
import TicketListItem from "./ListItem";
import axios from "axios";
import deleteConfirmation from "./../../../common/components/DeleteConfirmation";

import VueSelect from 'vue-select'
Vue.component('v-select', VueSelect)

import vueDropzone from "vue2-dropzone";
import "vue2-dropzone/dist/vue2Dropzone.min.css";

export default {
  data() {
    return {
      conversation: [],
      savedReplies: [],
      currentPage: 1,
      editorContent: "",
      conversationDraft: "",
      replyEditorContent: "",
      editReply: null,
      
      updatingReply: false,
      replyAttachments: [],
      dropzoneOptions: {
        url: this.$myaccount_url + "upload?type=ticket_conversation",
        thumbnailWidth: 64,
        thumbnailHeight: 64,
        maxFilesize: 20,
        timeout: 0,
        acceptedFiles: ".jpg,.jpeg,.png,.pdf,.doc,.docx, .zip",
        maxFiles: 5,
        resizeWidth: 1080,
        resizeQuality: 0.6,
        addRemoveLinks: true,
        dictDefaultMessage: this.$t("drop_files_here_to_upload"),
        dictRemoveFile: "x",
        dictUploadCanceled: this.$t("upload_canceled"),
        dictCancelUploadConfirmation: this.$t(
          "are_you_sure_want_to_cancel_upload"
        ),
        dictRemoveFileConfirmation: this.$t(
          "are_you_sure_want_to_remove_this_file"
        ),
        headers: {
          "X-CSRF-TOKEN": document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute("content"),
        },
      },
    };
  },
  components: {
    TicketListItem,
    deleteConfirmation,
    vueDropzone,
  },
  props: ["ticketid", "type", "canDeleteReply"],
  updated() {
    this.addLinkToImage();
  },
  methods: {
    loadSavedReplies() {
      axios
        .get(this.$myaccount_url + "saved_replies/fetch")
        .then((response) => {
          var respData = response.data.data;
          var list = this.savedReplies;
          respData.forEach(function (i) {
            list.push(new savedReply(i.id, i.title));
          });
        });
    },
    loadConversation() {
      axios
        .get(
          this.$myaccount_url +
            "tickets/" +
            this.ticketid +
            "/conversation?page=" +
            this.currentPage
        )
        .then((response) => {
          var newConversation = response.data.data;
          this.conversation = this.conversation.concat(newConversation);

          if (response.data.meta.current_page == response.data.meta.last_page) {
            this.currentPage = 0;
          } else {
            this.currentPage++;
          }
        })
        .catch((error) => {
          this.$showError(error);
        });
    },
    addLinkToImage() {
      //   var img = jQuery(".ticket-reply-content img");
      //   img.each(function () {
      //     var element = jQuery(this);
      //     if (this.parentElement.tagName != "A") {
      //       var a = jQuery("<a />", {
      //         href: element.attr("src"),
      //         class: "js-fancybox",
      //         target: "_blank",
      //         "data-lightbox": "ticket-conversation-image",
      //       });
      //       element.wrap(a);
      //     }
      //   });
      //   jsFancyBox = document.querySelector('.js-fancybox')
      //   jsFancyBox.fancybox({
      //     infobar: false,
      //     buttons: ["zoom", "fullScreen", "close"],
      //   });
    },
    checkUploadedFile: function (file, response) {
      if (response.status && response.status == "ok") {
        let fileid = file.upload.uuid;
        let uimg = response.data.path;
        this.replyAttachments.push({ fid: fileid, fpath: uimg });
      } else if (response.status && response.status == "fail") {
        let messages = Object.values(response.messages);
        this.$showError(messages.join("<br />"));
      } else {
        this.$showError(
          this.$t("Failed to upload your attachment please try again")
        );
      }
    },
    removeImage: function (file) {
      const idx = this.replyAttachments.findIndex(
        (u) => u.fid == file.upload.uuid
      );
      if (idx != -1) this.replyAttachments.splice(idx, 1);
    },
    failed: function (file, response, xhr) {
      this.$showError(response);
      file.previewElement.querySelectorAll(
        ".dz-error-message span"
      )[0].textContent = this.$t("File upload failed");
    },
    loadMore() {
      this.loadConversation();
    },
    save(status) {
      this.$toastClear();

      if (!this.editorContent && status != "resolved" && status != "closed") {
        this.$showError(this.$t("Please type a reply"));
        return;
      }

      var attachs = this.replyAttachments.map((item) => {
        return item.fpath;
      });

      axios
        .post(this.$myaccount_url + "tickets/conversation/add", {
          ticket_id: this.ticketid,
          content: this.editorContent,
          attachments: JSON.stringify(attachs),
          status: status,
        })
        .then((response) => {
          if (response.data.status == "ok") {
            this.editorContent = "";
            this.$refs.TCDropzone.removeAllFiles();
            this.replyAttachments = [];
            this.$showSuccess(response.data.message);
            if (response.data.data) {
              var newConversation = response.data.data;
              var oldConversation = this.conversation;
              var emptyArray = [];
              this.conversation = emptyArray.concat(
                newConversation,
                oldConversation
              );
            }

            this.$emit("update", status);
          } else if (response.data.status && response.data.status == "fail") {
            if (Array.isArray(response.data.message)) {
              this.$showError(
                Object.values(response.data.message).join("<br />")
              );
            } else {
              this.$showError(response.data.message);
            }
          } else {
            this.$showError(
              this.$t("Failed to submit your reply, Please try again.")
            );
          }
        });
    },
    fileIcon($ftype) {
      if (this.fileType($ftype) == "pdf") return "bi-file-pdf";
      if (this.fileType($ftype) == "image") return "bi-file-image";
      if (this.fileType($ftype) == "doc") return "bi-file-word";
      if (this.fileType($ftype) == "zip") return "bi-file-zip";

      translate
      
      return "bi-file";
    },
    fileType($filetype) {
      if ($filetype.includes("application/pdf")) return "pdf";
      if ($filetype.includes("image")) return "image";
      if ($filetype.includes("officedocument")) return "doc";
      if ($filetype.includes("zip")) return "zip";
      return null;
    },
    deleteReply: function (replyId) {
      this.$modal.show(deleteConfirmation, {
        handlers: {
          confirm: () => {
            axios
              .post(this.$myaccount_url + "tickets/conversation/delete", {
                reply_id: replyId,
              })
              .then((response) => {
                if (response.data.status == "ok") {
                  let index = this.conversation.findIndex(
                    (item) => item.id === replyId
                  );
                  this.conversation.splice(index, 1);
                  this.$showSuccess(response.data.message);
                } else {
                  this.$showError(response.data.message);
                }
              })
              .catch((e) => {
                this.$showError(e);
              });
          },
        },
      });
    },
    loadSelectedReply: function (selected) {
      axios
        .get(this.$myaccount_url + "saved_replies/" + selected.value)
        .then((response) => {
          this.editorContent += response.data.data.content;
        })
        .catch((e) => {
          this.$showError(e);
        });
    },
    restoreDraftReply() {
      if (localStorage.getItem(this.ticketid + "_conversation_draft")) {
        try {
          let LocalDraftReply = JSON.parse(
            localStorage.getItem(this.ticketid + "_conversation_draft")
          );
          if (LocalDraftReply && LocalDraftReply.content != "") {
            this.editorContent = LocalDraftReply.content;
            this.$showInfo(
              this.$t("A saved reply from your browser has been restored!")
            );
          }
        } catch (e) {
          localStorage.removeItem(this.ticketid + "_conversation_draft");
        }
      }
    },
    edit(item) {
      this.replyEditorContent = item.content;
      this.editReply = item.id;
    },
    updateReply(replyId) {
      this.updatingReply = true;
      axios
        .post(this.$myaccount_url + "tickets/conversation/update", {
          reply_id: replyId,
          content: this.replyEditorContent,
        })
        .then((r) => {
          this.$showSuccess(r.data.message);
          const currentIndex = this.conversation.findIndex(
            (u) => u.id === replyId
          );
          let c = this.conversation[currentIndex];
          c.content = this.replyEditorContent;
          this.conversation.splice(currentIndex, 1, c);

          this.replyEditorContent = "";
          this.editReply = null;
          this.updatingReply = false;
        })
        .catch((e) => {
          this.$showError(e);
        })
        .then((r) => {
          this.updatingReply = false;
        });
    },
  },
  mounted() {
    this.loadConversation();
    this.loadSavedReplies();
    this.restoreDraftReply();
  },
  watch: {
    editorContent() {
      if (this.editorContent != "") {
        const parsed = JSON.stringify({ content: this.editorContent });
        localStorage.setItem(this.ticketid + "_conversation_draft", parsed);
      } else {
        localStorage.removeItem(this.ticketid + "_conversation_draft");
      }
    },
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

.dropzone .dz-message {
  margin: 1em 0;
}

.vue-dropzone {
  border: 2px dashed #dddddd;
  min-height: 64px;
  padding: 5px 5px;
}

.dropzone-custom-title {
  font-size: 1em;
}

.dropzone .subtitle {
  font-size: 0.8em;
}

.dropzone .small {
  font-size: 0.7em;
}

.dropzone .dz-preview {
  margin: 5px;
  min-height: 64px;
}

.dropzone .dz-preview .dz-image {
  width: 64px;
  height: 64px;
}

.vue-dropzone > .dz-preview .dz-remove {
  top: 0;
  border: none;
  text-decoration: none;
  font-weight: normal;
  right: 0;
  background: white;
  padding: 0px 6px;
  font-size: 0.8rem;
  border-radius: 0 0 0 5px;
  color: #3e3e3e;
  bottom: inherit;
  text-transform: lowercase;
}

.dropzone .dz-preview .dz-details .dz-size {
  margin-bottom: 1em;
  font-size: 1em;
}

.dropzone .dz-preview .dz-details {
  font-size: 10px;
  padding: 0em 0em;
  line-height: 100%;
}

.dropzone .dz-preview .dz-progress {
  height: 6px;
  left: 0;
  top: 50%;
  margin-top: 0;
  width: 100%;
  margin-left: 0;
}

.dropzone .dz-preview .dz-success-mark svg,
.dropzone .dz-preview .dz-error-mark svg {
  width: 40px;
  height: 40px;
}

.btn.attachment {
  padding: 0.5em 0.5em;
  font-size: 0.7em;
  border: none;
  margin: 0 3px;
  color: #7d7d7d;
}

.btn.attachment:hover {
  background: white;
}

.reply-attachments {
  border-top: 1px dashed #0000002b;
}

.dropzone .dz-error-message {
  top: 100%;
  padding: 0;
  border-radius: 0;
}
</style>
