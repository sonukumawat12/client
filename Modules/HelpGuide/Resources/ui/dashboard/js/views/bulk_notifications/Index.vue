<template>
  <main role="main" class="container">
    <div class="row">
      <div class="col-sm-12 col-md-4 sticky-section">
        <div class="card sticky-section-item">
          <div class="card-body p-3">
            <h4 class="mb-3 h4 header-title">{{ $t("Send a Notification") }}</h4>

            <div class="form-floating mb-3">
              <v-select :placeholder="$t('Choose a channel')" v-model="channel" :options="list_channels"></v-select>
              <span v-if="errors.subject" class="text-danger">{{ errors.channel && errors.channel.length ? errors.channel[0] : "" }}</span>
            </div>

            <div class="form-floating mb-3">
              <v-select :placeholder="$t('Notify By')" v-model="notify_by" :options="list_notify_by"></v-select>
              <span v-if="errors.subject" class="text-danger">{{ errors.notify_by && errors.notify_by.length ? errors.notify_by[0] : "" }}</span>
            </div>

            <div class="form-floating mb-3" v-if="showCustomerGroups">
              <v-select :placeholder="$t('Customer Groups')" v-model="customer_groups" :options="list_customer_groups"
                multiple></v-select>
                <span v-if="errors.subject" class="text-danger">{{ errors.customer_groups && errors.customer_groups.length ? errors.customer_groups[0] : "" }}</span>
            </div>

            <div class="form-floating mb-3" v-if="showCustomers">
              <v-select :placeholder="$t('Individual customers')" v-model="customers" :options="list_customers"
                multiple></v-select>
                <span v-if="errors.subject" class="text-danger">{{ errors.customers && errors.customers.length ? errors.customers[0] : "" }}</span>
            </div>

            <div class="form-floating mb-3">
              <input type="text" v-model="subject" class="form-control" id="subject" :placeholder="$t('Subject')" />
              <label for="subject">{{ $t("Subject") }}</label>
              <span v-if="errors.subject" class="text-danger">{{ errors.subject && errors.subject.length ? errors.subject[0] : "" }}</span>
            </div>

            <div class="form-floating mb-3">
              <textarea v-model="message" class="form-control" id="message" :placeholder="$t('Message')"
                style="height: 150px;"></textarea>
              <label for="message">{{ $t("Message") }}</label>
              <span v-if="errors.subject" class="text-danger">{{ errors.message && errors.message.length ? errors.message[0] : "" }}</span>
            </div>


            <save-btn class="btn btn-outline-primary btn-sm float-end" :in-action="isSaving"
              v-on:click.native="addCategory()" />
          </div>
        </div>
      </div>
      <div class="col-sm-12 col-md-8">
        <div class="card card-body" v-if="isLoading"><pre-loader /></div>
        <div v-else class="p-3 bg-white rounded shadow-sm">
          <div v-if="categories().length">
            <h4 class="h4 pb-2 mb-2">{{ $t("Outbox") }}</h4>
            <ul class="list-group">
              <li v-for="category in categories()" v-bind:key="category.id" class="list-group-item">
                <transition-group name="fade" mode="out-in">
                  <manageCategoriesItem :category="category" v-bind:key="category.id" v-on:delete="deleteRecord($event)" />
                </transition-group>
              </li>
            </ul>
          </div>
          <empty-state v-else text="No notifications yet" />
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

import VueSelect from 'vue-select'
Vue.component('v-select', VueSelect)

export default {
  data() {
    return {
      listCategories: [],
      subject: null,
      message: null,
      channel: null,
      list_channels: [],

      notify_by: null,
      list_notify_by: [],

      customer_groups: null,
      list_customer_groups: [],

      customers: null,
      list_customers: [],

      showCustomerGroups: true, // Default visibility
      showCustomers: false,    // Default visibility

      isSaving: false,
      isLoading: false,
      errors: {},
    };
  },
  methods: {
    loadCategories() {
      this.isLoading = true;
      axios
        .get(this.$myaccount_url + "bulk-notifications/fetch")
        .then((response) => {
          this.listCategories = response.data.data;

          console.log(this.listCategories);

        })
        .catch((e) => {
          this.$showError(e);
        })
        .then(() => {
          this.isLoading = false;
        });
    },
    loadDropdownData() {
      axios
        .get(this.$myaccount_url + 'bulk-notifications/get-channels')
        .then(response => {
          var respData = response.data;
          var channels = respData.channels
          var mode = respData.mode
          var customers = respData.customers
          var customer_grps = respData.customer_groups

          var __channels = this.list_channels;
          channels.forEach(function (i) {
            __channels.push(new group(i.id, i.name));
          });

          var __notify_by = this.list_notify_by;
          mode.forEach(function (i) {
            __notify_by.push(new group(i.id, i.name));
          });

          var __customers = this.list_customers;
          customers.forEach(function (i) {
            __customers.push(new group(i.id, i.name));
          });

          var __customer_grps = this.list_customer_groups;
          customer_grps.forEach(function (i) {
            __customer_grps.push(new group(i.id, i.name));
          });
        })
    },
    addCategory: function () {
      this.$toastClear();

      let fields = {
        subject: this.subject,
        message: this.message,
        channel: this.channel,
        notify_by: this.notify_by,
        customer_groups : this.customer_groups,
        customers: this.customers
      };


      this.isSaving = true;

      
      axios
        .post(this.$myaccount_url + "bulk-notifications", fields)
        .then((response) => {

          if(response.data.errors){
            this.errors = response.data.errors;
          }

          if (response.data.data) {
            this.$showSuccess(this.$t("Notification has been created."));
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
        .post(this.$myaccount_url + "bulk-notifications/" + category_id, {
          _method: "delete",
        })
        .then((response) => {
          if (response.data.data) {
            this.$showSuccess(this.$t("Notification has been deleted."));
            let index = this.listCategories.findIndex(
              (item) => item.id === category_id
            );
            this.listCategories.splice(index, 1);
          }
        });
    },
    categories() {
      return this.listCategories;
    },

  },
  mounted() {
    this.loadCategories();
    this.loadDropdownData();
  },
  components: {
    categoryItemLabel,
    manageCategoriesItem,
  },

  watch: {
    notify_by(newVal) {
      if (newVal.value === 'customer_groups') {
        this.showCustomerGroups = true;
        this.showCustomers = false;
      } else if (newVal.value === 'customers') {
        this.showCustomerGroups = false;
        this.showCustomers = true;
      }
    }
  },

};

class group {
  constructor(id, name) {
    this.label = name;
    this.value = id;
  }
}
</script>

<style scoped>
.text-danger {
  color: red;
}
</style>
