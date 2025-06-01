<template>
<div class="container page-ticket">
    <Ticket :ticketid="ticketId" v-if="ticketId"/>
</div>
</template>

<script>
import Ticket from './Ticket.vue'
export default {
    data(){
        return {
            ticketId: null
        }
    },
    beforeMount(){
        this.ticketId = this.$route.params.id;
    },
    components: {
        Ticket
    }
}
</script>

<style>

</style>
