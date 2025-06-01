<template>
<div>
  <div class="row" v-if="ticket">
    <div class="col-md-4 sticky-section">
      <TicketWidget :ticket="ticket"></TicketWidget>
    </div>
    <div class="col-md-8 mb-2">
      <div class="row">
        <div class="col-md-12">
          <div class="my-3">
            <span class="h4">{{ ticket.title }}</span>
            <span :class="'badge badge-ticket-'+ticket.status">{{ ticket.status }}</span>
            <div class="float-end">
              <small>{{$t('Ticket ID')}} : #{{ticketid}}</small>
            </div>
          </div>
        </div>
        <div class="col-md-12">
          <TicketConversation :ticketStatus="ticket.status" :ticketid="ticketid" v-on:update="ticket.status = $event" />
        </div>
      </div>
    </div>
  </div>
  <pre-loader v-else />
</div>
</template>

<script>
import TicketWidget from "./TicketWidget";
import TicketConversation from "./TicketConversation";
import axios from "axios";

export default {
  data() {
    return {
      ticket: null,
      isLoading: true,
    };
  },
  mounted() {
    this.loadTicket();
  },
  components: {
    TicketWidget,
    TicketConversation,
  },
  props: {
    ticketid: {}
  },
  methods: {
    loadTicket: function () {
      axios
        .get(this.$api_url + "tickets/" + this.ticketid)
        .then((response) => {
          this.ticket = response.data.data;
          this.isLoading = false;
        });
    },
    ticketUpdated: function (status) {
      this.ticket.status = status;
    },
  },
};
</script>
