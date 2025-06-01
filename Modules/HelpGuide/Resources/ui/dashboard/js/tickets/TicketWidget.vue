<template>
  <div class="card position-sticky">
    <h6 class="card-header bg-white border-0 fs-5">{{ $t("Ticket details") }}</h6>
    <div class="card-body p-1 pb-3 px-2">
      <pre-loader v-if="isLoading" />

      <template v-else>
        <ul class="list-group list-group-flush">
          <li class="list-group-item p-1">
            <div class="clearfix">
              <v-select
                v-model="ticketCategory"
                :placeholder="$t('Choose a category')"
                :options="parentCategories"
                label="name"
                value="id"
                class="mb-2 text-capitalize"
              >
                <pre-loader />
              </v-select>
              <div
                class="form-group text-capitalize"
                v-if="childCategories.length"
              >
                <v-select
                  :placeholder="$t('Choose a sub category')"
                  label="name"
                  v-model="ticketSubCategory"
                  :options="childCategories"
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
              <save-btn
                class="btn btn-outline-primary btn-sm float-end m-0"
                :inAction="isSavingCategory"
                v-if="
                  newCategory &&
                  ticket.category &&
                  ticket.category.id != newCategory.id
                "
                v-on:click.native="changeCategory()"
              />
            </div>
          </li>

          <li class="list-group-item p-2" @click="showUserDetails()">
            <div class="d-inline-block align-top pt-2">
              <img
                class="me-2 bd-placeholder-img rounded rounded-circle"
                width="32"
                height="32"
                :src="customer.gravatar"
              />
            </div>
            <div class="d-inline-block">
              <strong class="text-capitalize">{{ customer.name }}</strong>
              <div><small>{{ customer.email }}</small></div>
            </div>
          </li>

          <li class="list-group-item p-2">
            <i class="bi bi-user"></i>
            {{ $t("Assigned to") }}
            <strong>{{ assigned }}</strong>
            <button v-if="$allow('reassign_ticket') " class="btn btn-sm px-1 py-0 bi-1x" @click="reAssigneTicket()"><i class="bi bi-arrow-repeat"></i></button>
          </li>
          <li class="list-group-item p-2">
            <i class="bi bi-stack"></i>
            {{ $t("Priority") }} :
            <strong :class="'ticket-priority level-' + ticket.priority">
              {{ $t(ticket.priority) }}</strong
            >
          </li>
          <li class="list-group-item p-2" v-if="customer.envato_customer">
            <a
              :href="
                'https://codecanyon.net/user/' +
                customer.envato_customer.provider_username
              "
              class="btn btn-outline-primary btn-sm"
              target="_blank"
            >
              <i class="bi bi-envato"></i>
              {{ customer.envato_customer.provider_username }}
              <i class="bi bi-external-link-alt"></i>
            </a>
            <button
              type="button"
              class="btn btn-outline-success btn-sm"
              @click="showUserPurchaseList()"
            >
              <span
                >{{ customer.purchase_count }}
                <i class="bi bi-shopping-bag"></i
              ></span>
            </button>
          </li>
          <li class="list-group-item p-2">
            <i class="bi bi-clock"></i>
            {{ $t("Submitted on") }} {{ created_at }}
          </li>
        </ul>

        <template v-if="ticket.custom_fields">
          <div class="list-group m-2 mb-3">
          <div class="list-group-item px-2 py-1" v-for="(cfield, index) in ticket.custom_fields" v-bind:key="index">
              <span class="text-capitalize font-weight-bold">{{ index.replaceAll('_', ' ') }}</span> : {{ cfield }}
          </div>
          </div>
        </template>


        <div v-if="ticket.attachments.length">

          <div>{{ $t("Ticket attachments") }}</div>

          <template v-for="(attachment, index) in ticket.attachments" >
          <fancy-box
              v-bind:key="index"
              v-if="['image', 'pdf'].includes(fileType(attachment.file_type))"
              :href="attachment.url"
              target="_blank"
              data-fancybox="gallery"
              class="btn btn-outline-secondary mb-1 btn-sm attachment"
            >
              <i
                v-bind:class="fileIcon(attachment.file_type)"
                class="bi bi-2x"
              ></i>
            </fancy-box>

            <a class="btn btn-outline-secondary mb-1 btn-sm attachment" v-else :href="attachment.url" :key="index">
              <i
                v-bind:class="fileIcon(attachment.file_type)"
                class="bi bi-2x"
              ></i>
            </a>
          </template>


        </div>

        <div class="mt-2" v-if="canDeleteTicket">
          <button
            type="button"
            class="btn btn-danger btn-sm"
            @click="deleteTicket(ticket.id)">
            {{ $t("Delete") }}
          </button>
        </div>
      </template>
    </div>
  </div>
</template>

<script>
Vue.component("v-select", VueSelect);
import customerDetailsModal from "./../views/user/customers/Details";
import deleteConfirmation from "./../../../common/components/DeleteConfirmation";
import UserPurchaseList from "./../components/PurchaseList"
import axios from "axios";
import AssignTicket from './AssignTicket'

export default {
  data() {
    return {
      listCategories: [],
      customer: {
        id: null,
        name: null,
        gravatar: null,
        email: null,
      },
      ticketCategory: null,
      ticketSubCategory: null,
      newCategory: null,
      created_at: null,
      updated_at: null,
      assigned: null,
      status: null,
      isLoading: true,
      isSavingCategory: false,
    };
  },
  components: {
    customerDetailsModal,
    deleteConfirmation,
  },
  methods: {
    changeCategory: function () {
      if (this.ticketCategory == null && this.ticketSubCategory == null) {
        this.$showError(this.$t("Please select a category"));
        return false;
      }

      axios
        .post(this.$myaccount_url + "tickets/update/" + this.ticket.id, {
          category_id: this.ticketSubCategory
            ? this.ticketSubCategory.id
            : this.ticketCategory.id,
        })
        .then((response) => {
          this.$showSuccess(this.$t("Ticket has been updated."));
        });
    },
    loadTicket: function () {
      if (this.ticket.category) this.ticketCategory = this.ticket.category;

      if (this.ticket.user) {
        this.customer.id = this.ticket.user.id;
        this.customer.name = this.ticket.user.name;
        this.customer.email = this.ticket.user.email;
        this.customer.gravatar = this.ticket.user.avatar;
        this.customer.envato_customer = this.ticket.user.envato_customer;
        this.customer.purchase_count = this.ticket.user.purchase_count;
      }

      this.status = this.ticket.status;
      this.assigned = this.ticket.assigned_to
        ? this.ticket.assigned_to.name
        : null;
      this.created_at = this.ticket.submitted_on;
      this.updated_at = this.ticket.updated_at;
      this.ticketloaded = true;
    },
    async loadCategories() {
      await axios
        .post(this.$myaccount_url + "manage_categories/categories_tickets")
        .then((response) => {
          var respData = response.data.data;
          var list = this.listCategories;
          respData.forEach(function (i) {
            list.push(new category(i.id, i.name, i.parent));
          });
        });
    },
    fileIcon($ftype) {
      if (this.fileType($ftype) == "pdf") return "bi-file-pdf";
      if (this.fileType($ftype) == "image") return "bi-file-image";
      if (this.fileType($ftype) == "doc") return "bi-file-word";
      if (this.fileType($ftype) == "zip") return "bi-file-archive";
      return "bi-file";
    },
    fileType($filetype){
      if ($filetype.includes("application/pdf")) return "pdf";
      if ($filetype.includes("image")) return "image";
      if ($filetype.includes("officedocument")) return "doc";
      if ($filetype.includes("zip")) return "zip";
      return null;
    },
    showUserDetails() {
      this.$modal.show(
        customerDetailsModal,
        { userid: this.customer.id },
        { width: 300 }
      );
    },
    deleteTicket: function (ticketId) {
      this.$modal.show(deleteConfirmation, {
        record_name: this.$t("ticket"),
        handlers: {
          confirm: () => {
            axios
              .post(this.$myaccount_url + "tickets/" + ticketId, {
                _method: "delete",
              })
              .then((response) => {
                if (response.data.status == "ok") {
                  this.$showSuccess(response.data.message);
                  window.location.href = this.$myaccount_url + "tickets/";
                } else {
                  this.$showError(response.data.message);
                }
              });
          },
        },
      });
    },
    showUserPurchaseList: function () {
      this.$modal.show(UserPurchaseList, { userid: this.customer.id });
    },
    reAssigneTicket(){
      this.$modal.show(AssignTicket, {
          ticket: this.ticket.id,
          currentAgent: this.ticket.assigned_to.id,
          handlers: {
              update: (res) => {
                this.assigned = res.assigned_to? res.assigned_to.name : null;
              }
          }
      })
    }
  },
  props: ["ticket", "can-delete-ticket"],
  async mounted() {
    this.isLoading = true;
    await this.loadCategories();
    await this.loadTicket();
    this.isLoading = false;
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
      if (!vm.ticketCategory) return [];
      return vm.listCategories.filter(function (item) {
        return item.parent_id === vm.ticketCategory.id;
      });
    },
  },
  watch: {
    ticketCategory() {
      if (this.ticketCategory) this.newCategory = this.ticketCategory;
      this.ticketSubCategory = null;
    },
    ticketSubCategory() {
      if (this.ticketSubCategory) this.newCategory = this.ticketSubCategory;
    },
  },
};
class category {
  constructor(id, name, parent) {
    this.name = name;
    this.id = id;
    this.parent_id = parent ? parent.id : null;
  }
}

</script>
