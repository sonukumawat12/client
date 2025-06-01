<template>
  <main role="main" class="container">
    <div class="row">
      <div class="col-sm-12 col-md-3 sticky-section">
        <div class="card sticky-section-item">
          <div class="card-body p-3">
            <h4 class="mb-3 h4 header-title">{{ $t("New") }}</h4>
            <div class="form-floating mb-3">
              <input
                type="text"
                v-model="categoryName"
                v-on:keyup.enter="addCategory()"
                class="form-control"
                id="category_name"
                :placeholder="$t('new category')"
              />
              <label for="category_name">{{ $t("Category name") }}</label>
            </div>
            <div class="form-group">
              <v-select
                :placeholder="$t('Parent category')"
                v-model="parent"
                label="name"
                :options="parentCandidate()"
              />
            </div>

            <div class="form-check">
              <input
                type="checkbox"
                class="form-check-input"
                id="has-ticket"
                v-model="hasTicket"
              />
              <label
                class="form-check-label"
                for="has-ticket"
                data-toggle="tooltip"
                data-placement="top"
                :title="$t('Use this category for tickets')"
                >{{ $t("Use for tickets") }}</label
              >
            </div>

            <div class="form-check">
              <input
                type="checkbox"
                class="form-check-input"
                v-model="isFeatured"
                id="show-frontpage"
              />
              <label
                class="form-check-label"
                for="show-frontpage"
                data-toggle="tooltip"
                data-placement="top"
                :title="$t('Display this category on front page')"
                >{{ $t("Show on front page") }}</label
              >
            </div>

            <div class="form-check">
              <input
                type="checkbox"
                class="form-check-input"
                v-model="isActive"
                id="isEnabled"
              />
              <label
                class="form-check-label"
                for="isEnabled"
                data-toggle="tooltip"
                data-placement="top"
                :title="$t('Enable this category')"
                >{{ $t("Enable") }}</label
              >
            </div>

            <save-btn
              class="btn btn-outline-primary btn-sm float-end"
              :in-action="isSaving"
              v-on:click.native="addCategory()"
            />
          </div>
        </div>
      </div>
      <div class="col-sm-12 col-md-9">
        <div class="card card-body" v-if="isLoading"><pre-loader /></div>
        <div v-else class="p-3 bg-white rounded shadow-sm">
          <div v-if="parentCategories().length">
            <h4 class="h4 pb-2 mb-2">{{ $t("Categories") }}</h4>
            <ul class="list-group">
              <li
                v-for="category in parentCategories()"
                v-bind:key="category.id"
                class="list-group-item"
              >
                <transition-group name="fade" mode="out-in">
                  <manageCategoriesItem
                    :category="category"
                    v-bind:key="category.id"
                    v-on:edit="edit($event)"
                    v-on:delete="deleteRecord($event)"
                    v-on:toggle-active="toggleActive($event)"
                  />

                  <ul
                    class="mt-3 list-group"
                    v-if="childCategories(category.id).length"
                    v-bind:key="'childs-' + category.id"
                  >
                    <li
                      v-for="childCategory in childCategories(category.id)"
                      v-bind:key="childCategory.id"
                      class="list-group-item p-2"
                    >
                      <manageCategoriesItem
                        :category="childCategory"
                        v-on:edit="edit($event)"
                        v-on:delete="deleteRecord($event)"
                        v-on:toggle-active="toggleActive($event)"
                      />
                    </li>
                  </ul>
                </transition-group>
              </li>
            </ul>
          </div>
          <empty-state v-else text="No categories yet" />
        </div>
      </div>
    </div>
    <v-dialog />
  </main>
</template>
<script>
import axios from "axios";
import categoryItemLabel from "./CategoryItemLabel";
import manageCategoriesItem from "./ManageCategoriesItem";
import edit from "./Edit";

import VueSelect from 'vue-select'
Vue.component('v-select', VueSelect)

export default {
  data() {
    return {
      listCategories: [],
      categoryName: null,
      parent: null,
      isFeatured: 0,
      hasTicket: 0,
      isActive: 0,
      isSaving: false,
      isLoading: false,
    };
  },
  methods: {
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
        .then(() => {
          this.isLoading = false;
        });
    },
    addCategory: function () {
      this.$toastClear();

      let fields = {
        name: this.categoryName,
        is_featured: this.isFeatured ? 1 : 0,
        has_ticket: this.hasTicket ? 1 : 0,
        active: this.isActive ? 1 : 0,
      };

      if (this.parent) fields["parent_id"] = this.parent.id;

      this.isSaving = true;

      axios
        .post(this.$myaccount_url + "manage_categories", fields)
        .then((response) => {
          if (response.data.data) {
            this.$showSuccess(this.$t("Category has been created."));
            var newCategories = response.data.data;
            var oldCategories = this.listCategories;
            var emptyArray = [];
            this.listCategories = emptyArray.concat(
              newCategories,
              oldCategories
            );
          }
        })
        .catch((e) => {
          this.$showError(e);
        })
        .then(() => {
          this.isSaving = false;
        });
    },
    updateCategory: function () {},
    deleteRecord: function (category_id) {
      this.$modal.show("dialog", {
        title: this.$t("Delete confirmation"),
        text:
          '<span class="text-danger font-weight-bold">' +
          this.$t("Are you sure you want to delete this record?") +
          "</span>",
        buttons: [
          {
            title:
              '<span class="text-danger font-weight">' +
              this.$t("Delete") +
              "</span>",
            handler: () => {
              this.doDelete(category_id);
              this.$modal.hide("dialog");
            },
          },
          {
            title: this.$t("Close"),
          },
        ],
      });
    },
    doDelete: function (category_id) {
      event.target.setAttribute("disabled", "disabled");
      this.$toastClear();
      axios
        .post(this.$myaccount_url + "manage_categories/" + category_id, {
          _method: "delete",
        })
        .then((response) => {
          if (response.data.data) {
            this.$showSuccess(this.$t("Category has been deleted."));
            let index = this.listCategories.findIndex(
              (item) => item.id === category_id
            );
            this.listCategories.splice(index, 1);
          }
        });
    },
    toggleActive(category_id) {
      axios
        .post(
          this.$myaccount_url + "manage_categories/toggle_active/" + category_id
        )
        .then((response) => {
          if (response.data.data) {
            this.$showSuccess(this.$t("Category has been updated."));
            let cat = res.data.data;
            const currentIndex = this.listCategories.findIndex(
              (u) => u.id === cat.id
            );
            this.listCategories.splice(currentIndex, 1, cat);
          }
        });
    },
    edit(id) {
      this.$modal.show(edit, {
        category: id,
        handlers: {
          update: (res) => {
            this.loadCategories();
          },
        },
      });
    },
    parentCandidate() {
      return this.listCategories.filter(
        (item) => item.parent == null && item.active
      );
    },
    parentCategories() {
      return this.listCategories.filter((item) => item.parent == null);
    },
    childCategories(id) {
      return this.listCategories.filter(
        (item) => item.parent && item.parent.id == id
      );
    },
  },
  mounted() {
    this.loadCategories();
  },
  components: {
    categoryItemLabel,
    manageCategoriesItem,
  },
};
</script>
