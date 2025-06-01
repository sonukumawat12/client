<template>
  <div class="card ticket-form">
    <div class="card-header px-5">
      <h5 class="mx-3">{{ $t("New ticket") }}</h5>
    </div>
    <div class="card-body">
      <div class="form-group">
        <input
          type="text"
          v-model="title"
          id="ticket-title"
          aria-describedby="ticket-titleHelp"
          :placeholder="$t('ticket_subject')"
          class="form-control"
        />
        <small id="ticket-titleHelp" class="form-text text-muted"></small>
      </div>

      <div class="form-group text-capitalize">
        <v-select
          :placeholder="$t('Choose a category')"
          label="name"
          v-model="selectedCategory"
          :options="parentCategories"
        >
          <template slot="option" slot-scope="option">
            <img
              :src="option.thumbnail"
              width="20"
              height="20"
              v-if="option.thumbnail"
            />
            <i class="bi bi-folder2-open" v-else></i>
            {{ option.name }}
          </template>
        </v-select>
      </div>

      <div class="form-group text-capitalize" v-if="childCategories.length">
        <v-select
          :placeholder="$t('Choose a sub category')"
          label="name"
          v-model="selectedSubCategory"
          :options="childCategories"
        >
          <template slot="option" slot-scope="option">
            <img
              :src="option.thumbnail"
              width="20"
              height="20"
              v-if="option.thumbnail"
            />
            <i class="bi bi-folder2-open" v-else></i>
            {{ option.name }}
          </template>
        </v-select>
      </div>

      <div class="form-group">
        <v-select
          :placeholder="$t('Priority')"
          label="name"
          v-model="priority"
          class="text-capitalize"
          :options="['low', 'medium', 'high', 'urgent']"
        ></v-select>
      </div>

      <div class="form-group">
        <text-editor
          uploadtype="ticket"
          v-model="ticketContent"
          :placeholder="$t('How can we help you today?') + '...'"
        ></text-editor>
      </div>

      <div class="form-group">
        <label id="panelDropzone" class="text-capitalize">{{
          $t("Attachments")
        }}</label>
        <vueDropzone
          id="panelDropzone"
          ref="panelDropzone"
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
            <h3 class="dropzone-custom-title">{{ $t("drop_files_upload") }}</h3>
            <div class="subtitle">{{ $t("click_select_file") }}</div>
            <small class="small">{{ $t("file_allowed_image_pdf__doc") }}</small>
          </div>
        </vueDropzone>
      </div>

      <div class="form-group">
        <save-btn
          :text="'Submit ticket'"
          class="btn btn-outline-primary float-end mt-3"
          v-on:click.native="submit()"
          :inAction="isSaving"
        />
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";

import vSelect from "vue-select";
import "vue-select/dist/vue-select.css";

import vueDropzone from "vue2-dropzone";
import "vue2-dropzone/dist/vue2Dropzone.min.css";

export default {
  data() {
    return {
      listCategories: [],

      isSaving: false,

      selectedCategory: null,
      selectedSubCategory: null,

      title: null,
      priority: null,
      ticketContent: null,
      attachments: [],

      custom_fields: {},

      dropzoneOptions: {
        url: this.$myaccount_url + "upload?type=ticket_attachment",
        thumbnailWidth: 128,
        thumbnailHeight: 128,
        maxFilesize: 4,
        acceptedFiles: ".jpg,.jpeg,.png,.pdf,.doc, .docx",
        maxFiles: 5,
        resizeWidth: 1080,
        resizeQuality: 0.6,
        addRemoveLinks: true,
        dictDefaultMessage: this.$t("drop_files_here_to_upload"),
        dictRemoveFile: this.$t("X"),
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
  components: { vueDropzone, vSelect },
  methods: {
    submit() {
      this.$toastClear();

      if (this.title == null) {
        this.$showError(
          this.$t("Title can not be empty, Please type your ticket title")
        );
        return false;
      }

      if (this.selectedCategory == null && this.selectedSubCategory == null) {
        this.$showError(this.$t("Please select a category"));
        return false;
      }

      if (this.ticketContent == null) {
        this.$showError(
          this.$t(
            "Ticket content can not be empty, Please type your ticket content"
          )
        );
        return false;
      }

      var attachs = this.attachments.map((item) => {
        return item.fpath;
      });

      this.isSaving = true;

      axios
        .post(this.$api_url + "tickets/save", {
          title: this.title,
          content: this.ticketContent,
          priority: this.priority,
          custom_fields: this.custom_fields,
          attachments: JSON.stringify(attachs),
          category: this.selectedSubCategory
            ? this.selectedSubCategory.id
            : this.selectedCategory.id,
        })
        .then((response) => {
          if (response.data.status == "ok") {
            this.$showSuccess(response.data.message);

            this.$router.push({
              name: "tickets.view",
              params: { id: response.data.id },
            });
            
          } else if (response.data.status && response.data.status == "fail") {
            if (
              Array.isArray(response.data.message) ||
              typeof response.data.message === "object"
            ) {
              this.$showError(
                Object.values(response.data.message).join("<br />")
              );
            } else {
              this.$showError(response.data.message);
            }
          } else {
            this.$showError("Failed to submit your ticket, Please try again.");
          }
        })
        .catch((e) => {
          this.$showError(e);
        })
        .finally(() => {
          this.isSaving = false;
        });
    },
    checkUploadedFile: function (file, response) {
      if (response.status && response.status == "ok") {
        let fileid = file.upload.uuid;
        let uimg = response.data.path;
        this.attachments.push({ fid: fileid, fpath: uimg });
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
      const idx = this.attachments.findIndex((u) => u.fid == file.upload.uuid);
      if (idx != -1) this.attachments.splice(idx, 1);
    },
    failed: function (file, response, xhr) {
      this.$showError(response);
      file.previewElement.querySelectorAll(
        ".dz-error-message span"
      )[0].textContent = this.$t("File upload failed");
    },
  },
  mounted() {
    axios.get(this.$api_url + "tickets/categories").then((response) => {
      this.listCategories = response.data;
    });
  },
  computed: {
    parentCategories: function () {
      var vm = this;
      return vm.listCategories.filter(function (item) {
        return !item.parent_id;
      });
    },
    childCategories: function () {
      var vm = this;
      if (!vm.selectedCategory) return [];
      return vm.listCategories.filter(function (item) {
        return item.parent_id === vm.selectedCategory.id;
      });
    },
  },
  watch: {
    selectedCategory() {
      this.selectedSubCategory = null;
    },
  },
};
</script>

<style lang="scss">
.ticket-form {
  max-width: 960px;
  margin: auto;
}
</style>