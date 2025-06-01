<template>
  <div
    class="searchBar w-100"
    v-bind:class="{ 'is-open': isOpen }"
    v-click-outside="closeSearchResults"
  >
    <div class="position-relative searchInput">
      <input
        class="form-control searchField pr-4"
        @focus="isOpen = true"
        v-model="query"
        type="text"
        ref="searchInput"
        :placeholder="placeholderText()"
      />
      <span class="searchIcon">
        <span v-if="query != '' || isOpen" v-on:click="closeSearchResults">
          <i class="bi bi-x-lg"></i>
        </span>
        <span v-else v-on:click="$refs.searchInput.focus()">
          <i class="bi bi-search"></i>
        </span>
      </span>
    </div>
    <div class="searchResultContainer" v-if="isOpen">
      <div class="searchResult">
        <div class="btn-group d-flex search-cat" role="group">
          <button
            type="button"
            class="btn btn-link"
            v-bind:class="{ 'text-white bg-info': type == 'all' }"
            v-on:click="type = 'all'"
          >
            {{ $t("All") }}
          </button>
          <button
            type="button"
            class="btn btn-link"
            v-bind:class="{ 'text-white bg-info': type == 'article' }"
            v-on:click="type = 'article'"
          >
            {{ $t("Articles") }}
          </button>
          <button
            type="button"
            class="btn btn-link"
            v-bind:class="{ 'text-white bg-info': type == 'ticket' }"
            v-on:click="type = 'ticket'"
          >
            {{ $t("Tickets") }}
          </button>

          <button
            type="button"
            class="btn btn-link pr-2"
            v-bind:class="{ 'text-white bg-info': type == 'customer' }"
            v-on:click="type = 'customer'"
          >
            {{ $t("Customers") }}
          </button>

          <!-- <button
            type="button"
            class="btn btn-link pr-2"
            v-bind:class="{'text-white bg-info': type == 'employees'}"
            v-on:click="type = 'employee'"
        >{{$t('Employees')}}</button> -->

          <button
            type="button"
            class="btn btn-link pr-2"
            v-bind:class="{ 'text-white bg-info': type == 'category' }"
            v-on:click="type = 'category'"
          >
            {{ $t("Categories") }}
          </button>
        </div>

        <div class="text-center p-2" v-if="isLoading">
          <pre-loader />
        </div>

        <template v-else>
          <div class="searchResultList" v-if="results.length != 0">
            <div
              class="list-group list-group-flush"
              v-if="!isLoading && results.length != 0"
            >
              <template v-if="type == 'article' || type == 'all'">
                <div class="list-group-item text-center bg-light p-1">
                  {{ $t("Articles") }}
                </div>
                <div v-if="results.articles.length">
                  <div
                    v-for="(item, index) in results.articles"
                    v-bind:key="'a-' + index"
                    class="list-group-item list-group-item-action"
                  >
                    <a
                      :href="$base_url + 'articles/' + item.id"
                      class="d-block w-100 p-2"
                      >{{ item.title }}</a
                    >
                  </div>
                </div>
              </template>

              <template v-if="type == 'ticket' || type == 'all'">
                <div class="list-group-item text-center bg-light p-1">
                  {{ $t("Tickets") }}
                </div>
                <div v-if="results.tickets.length">
                  <div
                    v-for="(item, index) in results.tickets"
                    v-bind:key="'a-' + index"
                    class="list-group-item list-group-item-action"
                  >
                    <a
                      :href="$myaccount_url + 'tickets/' + item.id"
                      class="d-block w-100 p-2"
                      >{{ item.title }}</a
                    >
                  </div>
                </div>
              </template>

              <template v-if="type == 'customer' || type == 'all'">
                <div class="list-group-item text-center bg-light p-1">
                  {{ $t("Customers") }}
                </div>
                <div v-if="results.customers.length">
                  <div
                    v-for="(item, index) in results.customers"
                    v-bind:key="'customer-' + index"
                    class="list-group-item list-group-item-action"
                  >
                    <a
                      :href="$myaccount_url + '#/customers/details/' + item.id"
                      class="d-block w-100 p-2"
                      >{{ item.name }}</a
                    >
                  </div>
                </div>
              </template>

              <template v-if="type == 'category' || type == 'all'">
                <div class="list-group-item text-center bg-light p-1">
                  {{ $t("Categories") }}
                </div>
                <div v-if="results.categories.length">
                  <div
                    v-for="(item, index) in results.categories"
                    v-bind:key="'category-' + index"
                    class="list-group-item list-group-item-action"
                  >
                    <a
                      :href="$myaccount_url + '#/categories:~:text=' + query"
                      class="d-block w-100 p-2"
                      >{{ item.name }}</a>
                  </div>
                </div>
              </template>
            </div>
          </div>

          <div v-if="!isLoading && results.length == 0">
            <div class="text-center notResult">
              {{ $t("no results") }}
            </div>
          </div>
        </template>
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
      type: "all",
    };
  },
  props: {
    placeholder: {
      type: String,
    },
    searchtype: {
      type: Array,
    },
  },
  methods: {
    doSearch() {
      let q = this.query.trim();
      if (!q) return;
      this.isLoading = true;
      if (cancel) cancel();
      searchHR = axios
        .get(
          this.$admin_api +
            "search?type=" +
            this.searchtype +
            "&q=" +
            this.query,
          {
            cancelToken: new CancelToken(function executor(c) {
              cancel = c;
            }),
          }
        )
        .then((response) => { this.results = response.data.results })
        .catch((e) => { this.$showError(e) })
        .finally((e) => { this.isLoading = false })
    },
    closeSearchResults() {
      this.isOpen = false;
      this.query = "";
    },
    placeholderText() {
      var types = this.searchtype.map((item) => this.$t(item));
      return this.placeholder + " " + types.join(", ") + "...";
    },
  },
  watch: {
    query: function (val, old) {
      if (!val.trim() || val.trim() == old.trim()) return;
      this.isTyping = true;
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
.notResult {
  height: 50vh;
  line-height: 25vh;
  text-transform: capitalize;
  font-size: 0.9rem;
  color: #444;
}

.searchResult .list-group-item {
  padding: 0;
}

.searchBar .search-cat button:last-child {
  border-right: none !important;
}

.searchBar .search-cat button {
  border-radius: 0;
}

.searchBar input {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.searchResultContainer {
  position: relative;
  margin-top: 10px;
}

.searchResult {
  position: fixed;
  background: white;
  margin-top: -0.5rem;
  padding-top: 0rem;
  border-radius: 0 0 5px 5px;
  border: 1px solid #d1d3e2;
  max-height: 60vh;
  min-height: 25vh;
  z-index: 9;
  width: 25rem;
  height: fit-content;
  overflow: auto;
  min-width: 25rem;
}

.searchResultContainer ul {
  margin: 0;
  padding: 0;
  list-style: none;
}

.searchResultContainer ul li {
  padding: 0.2rem 0.7rem;
  border-bottom: 1px solid #d1d3e2;
}

.searchResultContainer ul li:last-child {
  border: none;
}

.searchIcon {
  position: absolute;
  top: 47%;
  right: 0rem;
  margin-top: -10px;
  background: #fff;
  padding: 0 0 0 0.5rem;
}

.searchInput {
  padding: 0.5rem 1.5rem 0.4rem 1rem;
  margin: 0 0.4rem 0 0.4rem;
}

.searchBar {
  background: #ffffff;
  padding: 0;
  border-radius: 0.7rem;
  border: 1px solid #ddd;
  min-width: 15vw;
  max-width: 30vw;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.searchBar .searchField {
  margin: 0;
  padding: 0;
  box-shadow: none;
  border: none;
  border-radius: 0;
  border-bottom: 1px solid rgba(0, 0, 0, 0);
}

.searchIcon {
  font-size: 0.6rem;
  background: #0000000d;
  text-align: center;
  height: 1.5rem;
  width: 1.5rem;
  padding: 0;
  line-height: 1.5rem;
  border-radius: 50%;
  color: #000;
}

.searchIcon:hover {
  background: #00000007;
}

.searchBar.is-open .searchInput {
  border-bottom: 1px solid #ddd;
}

@media only screen and (max-width: 576px) {
  .searchResultList {
    border-radius: 0;
    overflow: auto;
    height: calc(100vh - 130px);
    border-top: 1px solid #ededed;
  }

  .searchBar.is-open .searchInput {
    margin-top: 0.74rem;
    border-radius: 0;
  }

  .searchBar.is-open {
    position: absolute;
    z-index: 9;
    width: 100%;
    left: 0;
    top: 0;
    height: 100vh;
    max-width: 100vw;
    border-radius: 0;
  }

  .searchResult {
    width: 100%;
    min-width: 100%;
    height: max-content;
    max-height: initial;
    border: none;
    border-radius: initial;
    margin: 0;
    position: initial;
    margin-bottom: 5rem;
  }

  .searchResultContainer {
    width: 100%;
    z-index: 99;
    background: #fff;
    height: 100vh;
    position: fixed;
    bottom: -3.5rem;
  }

  .search-cat {
    margin: 0rem;
    overflow: overlay;
    padding: 1em 0.5rem;
  }

  .searchBar .search-cat button {
    border: none !important;
    border-radius: 1em !important;
  }

  .searchIcon:hover {
    background: #00000007;
  }

  .searchResult .list-group-item {
    border: none;
  }
}

@media only screen and (min-width: 577px) {
  .searchInput {
    padding: 0.5rem 1.5rem 0.4rem 1rem;
    margin: 0 0.4rem 0 0.4rem;
  }

  .searchBar {
    position: fixed !important;
    top: 0.35rem;
  }

  .searchBar.is-open {
    height: max-content;
    max-height: 90vh;
  }

  .searchResult {
    position: initial;
    width: 100%;
    padding: 0.5em;
    min-width: 100%;
    border-radius: initial;
  }

  .searchResultContainer {
    background: #fff;
  }

  .searchResult {
    max-height: 25rem;
    min-height: 25vh;
    height: fit-content;
    overflow: hidden;
    margin-top: -0.2rem;
    left: 0;
    border: none;
  }

  .searchResultList {
    border-radius: 0;
    overflow: auto;
    height: calc(90vh - 140px);
    border-top: 1px solid #ededed;
    padding-bottom: 2em;
  }

  .search-cat {
    margin: 0rem;
    overflow: overlay;
    padding: 1em 0.5rem;
  }

  .searchBar .search-cat button {
    border: none !important;
    border-radius: 1em !important;
  }

  .searchResult .list-group-item {
    border: none;
  }
}
</style>