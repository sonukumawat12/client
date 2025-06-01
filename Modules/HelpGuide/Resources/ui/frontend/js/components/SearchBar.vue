<template>
    <div>
        <div class="input-group mb-2 searchbar">
            <input type="text" class="form-control border-0" v-model="query" :placeholder="$t('type_to_search')">
            <div class="input-group-append">
                <button type="button" @click="goSearch()" class="btn btn-secondary color-grey"><i class="bi bi-search"></i></button>
            </div>
        </div>
        <div class="searchResultContainer" v-if="isOpen || results.length!=0 || isLoading">
            <div class="searchResult">
                <pre-loader v-if="isLoading" />
                <div v-else-if="results.length!=0">
                    <ul class="list-group">
                        <li v-for="(article, index) in results" v-bind:key="index" class="list-group-item list-group-item-action">
                            <a :href="article.url" class="d-block text-left">{{ article.title }}</a>
                        </li>
                    </ul>
                </div>
                <div v-else class="text-dark text-center p-2">
                    {{ $t('no results') }}
                </div>
            </div>
        </div>
    </div>
</template>
<script>
let isTypingTimeOut = null;
let searchHR = null;
const CancelToken = axios.CancelToken;
let cancel;
import axios from "axios";
export default {
  data() {
    return {
      isOpen: false,
      results: [],
      query: "",
      isLoading: false,
      arrowCounter: 0,
      isTyping: false,
      noResult: false
    };
  },
  props: {
    placeholder: {
      type: String,
    },
    searchtype: {
      type: Array
    }
  },
  methods: {
    doSearch() {
      let q = this.query.trim();
      if (!q) return;
      this.isLoading = true;
      if (cancel) cancel();
      searchHR = axios
        .post(this.$base_url + "search?type=articles&q=" + this.query, {
          cancelToken: new CancelToken(function executor(c) {
            cancel = c;
          }),
        })
        .then((response) => {
          this.results = response.data.results.articles;
          this.isLoading = false;
        })
        .catch((error) => {
          this.isLoading = false;
        });
    },
    closeSearchResults() {
      this.isOpen = false;
      this.query = "";
    },
    goSearch(){
      window.location.href = this.$base_url+'search?q='+this.query
    }
  },
  watch: {
    query: function (val, old) {
        if (!val.trim()) this.isOpen = false
        if (!val.trim() || val.trim() == old.trim()) return;
        this.isTyping = true;
        this.isOpen = true
        this.isLoading = true;
        clearTimeout(isTypingTimeOut);
        isTypingTimeOut = setTimeout(() => {
            this.isTyping = false;
            this.doSearch();
      }, 500);
    },
  },
};
</script>

<style>

.searchbar .form-control {
  height: calc(2.5em + 0.75rem + 10px);
  border-radius: 5px 0 0 5px;
  padding: 0px 20px;
}

.searchbar .btn {
    border-radius: 0 5px 5px 0;
    background: white;
    color: #6c757d;
    border: none;
    border-left: none;
    padding: 15.85px;
}

.searchResultContainer {
    width: 100%;
    position: relative;
}

.searchResult {
    position: absolute;
    background: white;
    margin-top: -0.5rem;
    padding-top: 0rem;
    border-radius: 5px;
    border: 1px solid #d1d3e2;
    max-height: 60vh;
    z-index: 99;
    height: -webkit-fit-content;
    height: -moz-fit-content;
    height: fit-content;
    overflow: auto;
    width: 100%;
}
</style>