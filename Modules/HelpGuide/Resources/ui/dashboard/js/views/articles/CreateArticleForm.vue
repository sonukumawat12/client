<template>
  <div>

    <div class="card" v-if="isLoading">
      <div class="card-body">
        <pre-loader />
      </div>
    </div>

    <div v-else>
      <div class="clearfix">
        <div class="my-2 float-start">
          <router-link
            :to="{ name: 'articles_list' }"
            class="btn btn-sm btn-outline-secondary me-2"
          >
            <i class="bi bi-angle-left"></i> {{ $t("manage_articles") }}
          </router-link>
        </div>

        <div class="my-2 float-end" v-if="article_id">
          <a :href="url" class="btn btn-sm btn-outline-secondary me-2">
            <i class="bi bi-eye"></i> {{ $t("view") }}
          </a>
        </div>
      </div>

      <div class="row">
        <div class="col-sm-12 col-md-4 col-lg-3">
          <div class="card">
            <div class="card-body p-3">
              <div class="form-group">
                {{ $t("Category") }}
                <v-select
                  :placeholder="$t('Attach to a category')"
                  @blur="autosave()"
                  label="name"
                  v-model="selectedCategory"
                  :options="listCategories"
                >
                  <template slot="option" slot-scope="option">
                    <img
                      :src="option.thumbnail"
                      width="20"
                      height="20"
                      v-if="option.thumbnail"
                    />
                    <i class="bi bi-folder " v-else></i>
                    {{ option.name }}
                  </template>
                </v-select>
              </div>

              <div class="form-group">
                {{ $t("tags") }}
                <v-select
                  :placeholder="$t('Add tags')"
                  @blur="autosave()"
                  ref="tags"
                  @input="autoFocus()"
                  v-model="selectedTags"
                  :options="tags"
                  multiple
                  taggable
                ></v-select>
              </div>

              <div class="form-group">
                <div class="form-check">
                  <input
                    type="checkbox"
                    class="form-check-input"
                    @blur="autosave()"
                    id="is_featured"
                    :checked="featured == 1"
                    v-model="featured"
                  />
                  <label class="form-check-label" for="is_featured">{{
                    $t("Featured")
                  }}</label>
                </div>
                <span class="help-block"
                  ><small>{{
                    $t("Featured articles are displayed on the home page")
                  }}</small></span
                >
              </div>

              <div>
                <div class="form-group d-inline my-1">
                  <button
                    type="submit"
                    class="btn btn-outline-primary my-1"
                    v-on:click="save(1)"
                  >
                    <i class="bi bi-save"></i> {{ $t("Save & publish") }}
                  </button>
                </div>

                <div class="form-group d-inline my-1">
                  <button
                    type="submit"
                    class="btn btn-outline-primary my-1"
                    v-on:click="save(0)"
                  >
                    <i class="bi bi-save"></i> {{ $t("Draft") }}
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-sm-12 col-md-8 col-lg-9">
          <div class="card">
            <div class="card-body p-3">
              <div class="row">
                <div class="col-12">
                  <div class="form-group">
                    <v-select
                      :placeholder="$t('Article language')"
                      label="name"
                      v-model="selectedLanguage"
                      :options="listLanguages"
                    />
                  </div>
                </div>
                <div class="col">
                  <div class="form-group">
                    <input
                      type="text"
                      class="form-control"
                      @blur="autosave()"
                      v-model="title"
                      id="article-title"
                      :placeholder="$t('Article Title')"
                    />
                  </div>
                </div>
              </div>

              <div class="form-groups">
                <div class="card shadow-none">
                  <div class="card-body p-0">
                    <text-editor
                      options="full"
                      @blur="autosave()"
                      uploadtype="article"
                      :placeholder="$t('Article content') + '...'"
                      v-model="editorContent"
                    />
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>

import axios from "axios";

import VueSelect from 'vue-select'
Vue.component('v-select', VueSelect)

// Auto save after 5 seconds reset on change
let autosavetimer = null;

export default {
  data() {
    return {
      title: "",
      url: null,
      listLanguages: [],
      editorContent: "",
      tags: [],
      featured: 0,
      published: 0,
      listCategories: [
        {
          title: "",
        },
      ],
      isLoading: false,
      selectedLanguage: null,
      currentLanguage: null,
      selectedCategory: null,
      selectedTags: [],
      confirmReload: false
    };
  },
  methods: {
    save(published) {
      this.$toastClear();
      if (!this.title) {
        this.$showError(this.$t("title_can_not_be_empty"));
        return;
      }

      if (!this.selectedCategory) {
        this.$showError(this.$t("category_must_be_selected"));
        return;
      }

      if (!this.selectedLanguage) {
        this.$showError(this.$t("lang_must_be_selected"));
        return;
      }

      axios
        .post(this.$myaccount_url + "manage_articles", {
          title: this.title,
          content: this.editorContent,
          tags: this.selectedTags,
          language: this.selectedLanguage.code,
          _method: this.article_id == null ? "post" : "put",
          article_id: this.article_id,
          category:
            this.selectedCategory == null ? 0 : this.selectedCategory.id,
          published: published,
          featured: this.featured == true ? 1 : 0,
        })
        .then((response) => {
          if (response.data.data != "undefined") {
            this.$showSuccess(this.$t("article_saved"));
            this.listTags();
            if (!this.article_id) {
              window.location.href =
                this.$myaccount_url +
                "#/articles/edit/" +
                response.data.data.id;
            }
          }

          this.confirmReload = false
        })
        .catch((e) => {
          this.$showError(e);
        });
    },
    loadRecord: function () {

      if ( !this.article_id ) return

      this.isLoading = true;

      axios
        .post(this.$myaccount_url + "manage_articles/show/" + this.article_id, {
          language: this.selectedLanguage ? this.selectedLanguage.code : null,
        })
        .then((response) => {
          this.title = response.data.data.title;
          this.url = response.data.data.url;
          this.featured = response.data.data.featured;
          this.published = response.data.data.published;
          this.selectedTags = response.data.data.tags;
          this.language = response.data.data.language;
          this.selectedCategory = response.data.data.category;

          this.editorContent = response.data.data.content
            ? response.data.data.content
            : "";

          let i = this.listLanguages.findIndex(
            (item) => item.code === this.language
          );
          this.selectedLanguage = this.listLanguages[i];
          this.confirmReload = false

        })
        .catch((e) => {
          this.$showError(e);
        })
        .finally(() => {
          this.isLoading = false;
        })
    },
    listTags: function () {
      this.isLoading = true;
      axios.post(this.$myaccount_url + "tags")
      .then((response) => {
        this.tags = response.data.data;
      })
      .catch((e) => {
        this.$showError(e);
      })
      .finally(() => {
        this.isLoading = false;
      })
    },
    loadCategories: function () {
      this.isLoading = true;
      axios
        .get(this.$myaccount_url + "manage_categories/fetch")
        .then((response) => {
          this.listCategories = response.data.data;
        })
        .catch((e) => {
          this.$showError(e);
        })
        .finally(() => {
          this.isLoading = false;
        })
    },
    autoFocus() {
      this.$nextTick(() => {
        this.$refs.tags.$refs.search.focus();
      });
    },
    autosave() {
      if (!this.article_id) return;
      // clearTimeout(autosavetimer);
      // autosavetimer = setTimeout(() => {
      //   this.save(this.published);
      // }, 5000);
    },
    languages() {
      this.isLoading = true;
      axios
        .post(this.$myaccount_url + "available_languages")
        .then((res) => {
          this.listLanguages = Object.entries(res.data).map((v) => {
            return { code: v[0], name: v[1] };
          });

          let i = this.listLanguages.findIndex(
            (item) => item.code === this.locale
          );
          this.selectedLanguage = this.listLanguages[i];

        })
        .catch((e) => {
          this.$showError(e);
        })
        .finally(() => {
          this.isLoading = false;
        })
    },
  },
  mounted(){
    this.selectedLanguage = {'code': this.locale};

    this.languages();
    this.listTags();
    this.loadCategories();

    if (this.article_id) {
      this.loadRecord();
    }

    this.confirmReload = false
  },
  props: ["article_id", 'locale'],
  watch: {
    editorContent: function (newdata, olddata){
      if( !this.isLoading && newdata != olddata && newdata != "") this.confirmReload = true
    },
    title: function (newdata, olddata){
      if( !this.isLoading && newdata != olddata && newdata != "") this.confirmReload = true
    },
    selectedCategory: function (newdata, olddata){
      if( !this.isLoading && newdata != olddata && newdata) this.confirmReload = true
    },
    featured: function (newdata, olddata){
      if( !this.isLoading && newdata != olddata) this.confirmReload = true
    },
    selectedLanguage: function(newdata, olddata){
      if( this.currentLanguage != this.selectedLanguage && this.currentLanguage != null && newdata != this.currentLanguage && this.confirmReload ){
        if( ! confirm( this.$t("Reload article confirmation") ) ){
          this.selectedLanguage = olddata
          return false
        }
      }

      this.currentLanguage = newdata
      this.loadRecord()
    },
  }
};
</script>
