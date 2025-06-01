<template>
<div>

  <div class="d-flex justify-content-end options mb-3">
    <router-link class="btn btn-sm btn-primary shadow-sm float-end mx-1" to="/tickets/new">
      <i class="bi bi-plus text-white-50"></i>
      {{ $t('New ticket') }}
    </router-link>
  </div>

  <pre-loader v-if="isLoading" />
  <div class="p-0 mb-5 bg-white rounded shadow-sm tickets tickets-list" v-else>
    <div class="border-bottom border-gray p-3 mb-0 ticket-section-title overflow-auto ticket-list-option">
      <input
        type="button"
        class="btn btn-link p-1 text-grey"
        v-bind:class="{active: selectedStatus == 'all'}"
        @click="selectedStatus = 'all'"
        :value="$t('All') + ' (' + this.total_count + ')'" />
      .
      <input
        type="button"
        class="btn btn-link p-1 text-danger"
        v-bind:class="{active: selectedStatus == 'open'}"
        @click="selectedStatus = 'open'"
        :value="$t('Open') + ' (' + this.open_count + ')'" />
      .
      <input
        type="button"
        class="btn btn-link p-1 text-secondary"
        v-bind:class="{active: selectedStatus == 'closed' }"
        @click="selectedStatus = 'closed'"
        :value="$t('Closed') + ' (' + this.closed_count + ')'" />
      .
      <input
        type="button"
        class="btn btn-link p-1 text-success"
        v-bind:class="{active: selectedStatus == 'resolved' }"
        @click="selectedStatus = 'resolved'"
        :value="$t('resolved') + ' (' + this.resolved_count + ')'" />

      <div class="float-end">
        <button class="btn btn-outline-secondary btn-sm" v-on:click="replyExcerptToggle = false">
          <i class="bi bi-list"></i>
        </button>
        <button class="btn btn-outline-secondary btn-sm" v-on:click="replyExcerptToggle = true">
          <i class="bi bi-body-text"></i>
        </button>
      </div>
    </div>

    <empty-state  v-if="filteredList.length == 0" text="No tickets yet" />
    <div v-else>
      <div
        class="media text-muted ticket border-bottom border-gray"
        v-for="ticket in filteredList"
        :key="ticket.id"
        v-bind:class="{'has-reply': ticket.has_reply}"
      >
        <ListItem :replyExcerptToggle="replyExcerptToggle" :ticket="ticket" />
      </div>

      <scroll-loader
        :loader-method="loadList"
        :loader-disable="nextPage == null"
      ></scroll-loader>

    </div>
  </div>

</div>
</template>

<script>
import ListItem from "./ListItem";
import axios from "axios";
export default {
  data() {
    return {
      isLoading: true,
      tickets: [],
      currentPage: 1,
      metaData: {
        to: null,
        total: 0,
      },
      selectedStatus: "open",
      replyExcerptToggle: false,
      nextPage: null,
      statsUrl: this.$api_url+"tickets/status-counts",
      total_count: 0,
      resolved_count: 0,
      open_count: 0,
      closed_count: 0,
    };
  },
  components: {
    ListItem,
  },
  mounted() {
    this.nextPage = this.$api_url+"tickets"
    this.loadStats(this.statsUrl)
    this.loadList("open")
  },
  props: ["title", "category", "type"],
  methods: {
    loadList(status) {
      this.isLoading = true
      axios
        .get(this.nextPage)
        .then((response) => {
          this.metaData = response.data.meta;
          this.nextPage = response.data.links.next;
          var newTickets = response.data.data;
          this.tickets = this.tickets.concat(newTickets);
        })
        .catch((e) => {
          this.$showError(e);
        })
        .finally(() => {
            this.isLoading = false
        })
    },
    loadStats(url) {
      this.isLoading = true
      axios
        .get(url)
        .then((response) => {
          this.total_count = response.data[0].total
          this.open_count = response.data[0].open
          this.closed_count = response.data[0].closed
          this.resolved_count = response.data[0].resolved
        })
        .catch((e) => {
          this.$showError(e);
        })
        .finally(() => {
            this.isLoading = false
        })
    },
    loadMore() {
      this.loadList("open");
    },
  },
  computed: {
    filteredList: function () {
      var vm = this;
      var status = vm.selectedStatus;

      if (status === "all") {
        return vm.tickets;
      } else {
        return vm.tickets.filter(function (item) {
          return item.status === status;
        });
      }
    },
  },
};
</script>
