<template>
  <div class="container">
    <div class="row">
      <div class="col-sm-12">
        <search-form
          :items="searchResults"
          :is-loading="isSearching"
          v-on:input="search($event)"
        ></search-form>
      </div>
      <div class="col-sm-6">
          <v-select
          :placeholder="$t('Filter by category')"
          class="bg-white"
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
            <i class="bi bi-folder" v-else></i>
            {{ option.name }}
          </template>
        </v-select>
      </div>
      <div class="col-sm-6">

        <div class="float-end">
          <router-link
            class="btn btn-sm btn-outline-secondary mt-2"
            :to="{ name: 'articles_new' }"
          >
            <i class="bi bi-plus"></i> {{ $t("new") }}
          </router-link>
        </div>
      </div>
    </div>

    <div
      class="card card-body my-3"
      v-if="isLoading && articlesList.length == 0"
    >
      <pre-loader />
    </div>

    <template v-else>
      <div v-if="articlesList.length">
        <ul class="list-group my-3 p-0">
          <li
            class="list-group-item"
            v-for="article in articlesList"
            :key="article.id"
          >
            <h5 class="card-title p-0 m-0 text-capitalize">
              <router-link
                :to="{
                  name: 'articles_edit',
                  params: { article_id: article.id },
                }"
                class="text-dark"
              >
                {{ strlimit(article.title) }}
              </router-link>
              <a
                class="small me-2 text-dark"
                target="_blank"
                :href="article.url"
                ><i class="bi bi-box-arrow-up-right"></i
              ></a>
            </h5>

            <div class="card-text row m-0">
              <div class="col-xs-12 col-sm-6 m-0 p-0">
                <span
                  v-if="article.category"
                  class="text-capitalize"
                  :style="{ color: '#' + stringToColor(article.category.name) }"
                  >{{ article.category.name }}</span
                >
                <span v-else>{{ $t("uncategorized") }}</span>
                -
                <small>{{
                  $t("out_of_found_this_helpful", {
                    helpful: article.rate_helpful,
                    total: article.rate_total,
                  })
                }}</small>
                -
                <small class="text-muted">{{ article.created_at }}</small>
              </div>
              <div class="col-xs-12 col-sm-6 m-0 p-0">
                <div class="float-end">
                  <span class="badge bg-warning" v-if="!article.published"
                    ><i class="bi bi-eye-slash"></i
                  ></span>
                  <div class="dropdown d-inline">
                    <button
                      class="btn btn-light btn-sm rounded-circle"
                      type="button"
                      id="dropdownMenuButton"
                      data-bs-toggle="dropdown"
                      aria-haspopup="true"
                      aria-expanded="false"
                    >
                      <i class="bi bi-three-dots-vertical"></i>
                    </button>
                    <div
                      class="dropdown-menu dropdown-menu-right"
                      aria-labelledby="dropdownMenuButton"
                    >
                      <button
                        class="dropdown-item"
                        type="button"
                        data-html="true"
                        data-bs-toggle="tooltip"
                        :title="$t('Toggle publish article')"
                        v-on:click="togglePublish(article.id)"
                      >
                        <span v-if="article.published">
                          <i class="bi bi-eye-slash"></i>
                          {{ $t("Unpublish") }}
                        </span>
                        <span v-else>
                          <i class="bi bi-eye"></i>
                          {{ $t("Publish") }}
                        </span>
                      </button>
                      <router-link
                        :to="{
                          name: 'articles_edit',
                          params: { article_id: article.id },
                        }"
                        class="dropdown-item"
                      >
                        <span>
                          <i class="bi bi-pencil"></i>
                          {{ $t("Edit") }}
                        </span>
                      </router-link>

                      <button
                        class="dropdown-item text-danger"
                        type="button"
                        data-bs-toggle="tooltip"
                        :title="$t('Delete')"
                        v-on:click="deleteArticle(article.id)"
                      >
                        <span>
                          <i class="bi bi-trash"></i>
                          {{ $t("Delete") }}
                        </span>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </li>
        </ul>
        <pre-loader v-if="isLoading" />

        <div class="loadmore text-center" v-if="!isLoading && nextpage">
            <button class="btn btn-outline-secondary" @click="fetchData()">{{ $t('load more') }}</button>
        </div>

      </div>
      <empty-state v-else text="No articles yet" />
    </template>
    <v-dialog />
  </div>
</template>
<script>
import axios from "axios";
export default {
  data() {
    return {
      listCategories: [],
      articlesList: [],
      columns: ["title", "category", "Published", "created at", ""],
      checkedArticles: [],
      checkAllState: false,
      indeterminateState: false,
      isLoading: true,
      nextpage: null,
      searchResults: {
        type: "article",
        target_url: this.$myaccount_url + "#/articles/edit/",
        data: [],
      },
      searchXHR: null,
      isSearching: false,
      selectedCategory: null,
    };
  },
  methods: {
    strlimit(str) {
      if (!str) return;
      var limit = 64;
      var strlen = str.length;
      str = str.substring(0, limit);
      if (strlen > limit) str = str + "...";
      return str;
    },
    loadCategories() {
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
    fetchData() {
      this.isLoading = true;
      console.log("acll");
      axios
        .post(this.nextpage, { category: this.selectedCategory })
        .then((resp) => {
          var newArticles = resp.data.data;
          this.articlesList = this.articlesList.concat(newArticles);
          this.nextpage = resp.data.links.next;
        })
        .catch((e) => {
          this.$showError(e);
        })
        .finally(() => {
          this.isLoading = false;
        });
    },
    deleteArticle: function (id) {
      this.$modal.show("dialog", {
        title: this.$t("Delete confirmation"),
        text:
          '<span class="text-danger font-weight-bold">' +
          this.$t("Are you sure you want to delete this record?") +
          "</span>",
        buttons: [
          { title: this.$t("Close") },
          {
            title:
              '<span class="text-danger font-weight">' +
              this.$t("Delete") +
              "</span>",
            handler: () => {
              let element = event.target;
              element.setAttribute("disabled", "disabled");
              this.$toastClear();
              axios
                .post(this.$myaccount_url + "manage_articles/" + id, {
                  _method: "delete",
                })
                .then((response) => {
                  if (response.data.status == "ok") {
                    this.$showSuccess(
                      this.$t("Article has been deleted.")
                    );
                    let index = this.articlesList.findIndex(
                      (item) => item.id === id
                    );
                    this.articlesList.splice(index, 1);
                  }
                  element.removeAttribute("disabled");
                })
                .catch((e) => {
                  this.$showError(e);
                });
              this.$modal.hide("dialog");
            },
          },
        ],
      });
    },
    togglePublish(article_id) {
      this.$toastClear();
      axios
        .post(this.$myaccount_url + "manage_articles/toggle_published", {
          article_id: article_id,
        })
        .then((response) => {
          if (response.data) {
            let article = response.data.data;
            let currentIndex = this.articlesList.findIndex(
              (u) => u.id === article.id
            );
            this.articlesList.splice(currentIndex, 1, article);
            this.$showSuccess(this.$t("Article has been updated."));
          }
        })
        .catch((e) => {
          this.$showError(e);
        });
    },
    scroll() {
      window.onscroll = () => {
        if (this.nextpage == null) return;
        if (window.innerHeight + window.scrollY >= document.body.offsetHeight) {
          this.fetchData();
        }
      };
    },
    search(term) {
      if (term == "") {
        this.searchResults = [];
        return;
      }

      if (this.searchXHR == null) {
        this.searchXHR = axios.CancelToken.source();
      } else {
        this.searchXHR.cancel();
        this.searchXHR = axios.CancelToken.source();
      }

      this.isSearching = true;
      axios
        .post(
          this.$base_url + "search",
          { q: term, type: "articles" },
          { cancelToken: this.searchXHR.token }
        )
        .then((response) => {
          this.searchResults.data = response.data.results.articles;
        })
        .catch((e) => {
          if (!axios.isCancel(e)) {
            this.$showError(e);
          }
        })
        .finally(() => {
          this.isSearching = false;
        });
    },
    reloadArticleByCategory(){
        this.articlesList = []
        this.nextpage = this.$myaccount_url + "manage_articles/fetch"
        this.fetchData()
    }
  },
  created() {
    this.nextpage = this.$myaccount_url + "manage_articles/fetch"
    return this.fetchData();
  },
  directives: {
    indeterminate: function (el, binding) {
      el.indeterminate = Boolean(binding.value);
    },
  },
  mounted() {
    this.scroll();
    this.loadCategories();
  },
  watch: {
    selectedCategory() {
      this.reloadArticleByCategory();
    },
  },
};
</script>

<style>
.search-result-list {
  background: white;
  padding: 5px;
  margin-top: -3px;
  border-radius: 5px;
  z-index: 1;
  border: 1px solid #ddd;
}
</style>
