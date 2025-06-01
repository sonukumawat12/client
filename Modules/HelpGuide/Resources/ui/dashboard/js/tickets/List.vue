<template>
  <div>
    <div class="p-0 mb-5 bg-white rounded shadow-sm tickets tickets-list">
      <div class="
              border-bottom border-gray
              p-2
              mb-0
              ticket-section-title
              overflow-auto
              ticket-list-option
            ">
        <input type="button" class="btn btn-link py-0 px-1 text-grey" @click="loadByStatus('all')" :value="$t('All')"
          v-bind:class="{ active: ticketStore.selectedStatus == 'all' }" />
        .
        <input type="button" class="btn btn-link py-0 px-1 text-danger" @click="loadByStatus('open')" :value="$t('Open')"
          v-bind:class="{ active: ticketStore.selectedStatus == 'open' }" />
        .
        <input type="button" class="btn btn-link py-0 px-1 text-secondary" @click="loadByStatus('closed')"
          :value="$t('Closed')" v-bind:class="{ active: ticketStore.selectedStatus == 'closed' }" />
        .
        <input type="button" class="btn btn-link py-0 px-1 text-success" @click="loadByStatus('resolved')"
          :value="$t('Resolved')" v-bind:class="{ active: ticketStore.selectedStatus == 'resolved' }" />

        <div class="float-end">
          <button class="btn btn-outline-secondary py-0 btn-sm" v-on:click="replyExcerptToggle = false">
            <i class="bi bi-list"></i>
          </button>
          <button class="btn btn-outline-secondary py-0 btn-sm" v-on:click="replyExcerptToggle = true">
            <i class="bi bi-body-text"></i>
          </button>
        </div>
      </div>

      <div class="overflow-hidden">
        <pre-loader v-if="ticketStore.isLoading" />
        <div v-else-if="ticketStore.tickets.length">
          <div class="media text-muted ticket border-bottom border-gray" v-for="(item) in ticketStore.tickets"
            :key="item.id">
            <ListItem :replyExcerptToggle="replyExcerptToggle" :ticket="item"></ListItem>
          </div>

          <scroll-loader :loader-method="ticketStore.fetchTickets"
            :loader-disable="ticketStore.nextPage == null"></scroll-loader>
        </div>

        <empty-state v-else text="No tickets yet" />
      </div>
    </div>
  </div>
</template>

<script setup>

import { useTicketStore } from "./../stores/TicketStore";
import ListItem from "./ListItem.vue";
import { onMounted } from 'vue';

const ticketStore = useTicketStore();
const replyExcerptToggle = false

function loadByStatus(status) {
  app.$setCookie("ticket_status", status, 365)
  ticketStore.loadByStatus(app.$admin_api + `tickets`, status)
}

onMounted(async () => {
  if (app.$getCookie("ticket_status")) ticketStore.selectedStatus = app.$getCookie("ticket_status")
  await ticketStore.fetchTickets(app.$admin_api + `tickets?status=${ticketStore.selectedStatus}`);
})

</script>