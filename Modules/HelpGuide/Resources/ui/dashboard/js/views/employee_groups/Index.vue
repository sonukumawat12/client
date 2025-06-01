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
                :placeholder="$t('new group')"
              />
              <label for="category_name">{{ $t("employee Group name") }}</label>
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
          <div v-if="categories().length">
            <h4 class="h4 pb-2 mb-2">{{ $t("employee Groups") }}</h4>
            <ul class="list-group">
              <li
                v-for="category in categories()"
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
                </transition-group>
              </li>
            </ul>
          </div>
          <empty-state v-else text="No employee groups yet" />
        </div>
      </div>
    </div>
    <v-dialog />
  </main>
</template>
<script>
import axios from "axios";
import categoryItemLabel from "./ItemLabel";
import manageCategoriesItem from "./ManageItem";
import edit from "./Edit";

import VueSelect from 'vue-select'
Vue.component('v-select', VueSelect)

export default {
  data() {
    return {
      listCategories: [],
      categoryName: null,
      isSaving: false,
      isLoading: false,
    };
  },
  methods: {
    loadCategories() {
      this.isLoading = true;
      axios
        .get(this.$myaccount_url + "employee-groups/fetch")
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
      };
    

      this.isSaving = true;

      axios
        .post(this.$myaccount_url + "employee-groups", fields)
        .then((response) => {
          if (response.data.data) {
            this.$showSuccess(this.$t("Group has been created."));
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
        .post(this.$myaccount_url + "employee-groups/" + category_id, {
          _method: "delete",
        })
        .then((response) => {
          if (response.data.data) {
            this.$showSuccess(this.$t("Group has been deleted."));
            let index = this.listCategories.findIndex(
              (item) => item.id === category_id
            );
            this.listCategories.splice(index, 1);
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
    categories() {
      return this.listCategories;
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
