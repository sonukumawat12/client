import { defineStore } from 'pinia'

export const useTicketStore = defineStore('TicketStore', {
  state: () => ({
    tickets: [],
    ticket: [],
    nextPage: null,
    selectedStatus: 'all',
    isLoading: true
  }),
  actions: {
    async fetchTickets(url) {
      await axios
        .get(url ? url : this.nextPage)
        .then((r) => {
          var newTickets = r.data.data;
          this.tickets = this.tickets.concat(newTickets);
          this.nextPage = r.data.links.next;
        })
        .catch((e) => {
          app.$showError(e)
        })
        .finally(() => {
          this.isLoading = false
        });
    },
    loadByStatus(url, status){
      this.tickets = []
      this.selectedStatus = status
      this.isLoading = true
      this.fetchTickets(url+`?status=${this.selectedStatus}`)
    }
  }
})