<template>
<div class="mb-2">
    <pre-loader v-if="isLoading" />
    <div class="row" v-else>
        <div class="col-md-3 col-sm-6">
            <CardItem type='open' :value="tickets.open" />
        </div>
        <div class="col-md-3 col-sm-6">
            <CardItem type='resolved' :value="tickets.resolved" />
        </div>
        <div class="col-md-3 col-sm-6">
            <CardItem type='closed' :value="tickets.closed" />
        </div>
        <div class="col-md-3 col-sm-6">
            <CardItem type='total' :value="tickets.total" />
        </div>
    </div>
</div>
</template>

<script>
import axios from 'axios';
import CardItem from './CardItem'
export default {
    data(){
        return {
            isLoading: false,
            tickets: {
                open: 0,
                resolved: 0,
                closed: 0,
                total: 0,
            } 
        }
    },
    components: { CardItem },
    beforeMount(){
        this.load()
    },
    methods: {
        async load(){
            this.isLoading = true
            await axios
            .post(this.$admin_api+'tickets/statistics/overview')
            .then(response => {
                this.tickets = response.data
            })
            .catch((e) => { this.$showError(e) })
            .finally(() => { this.isLoading = false })
            
        }
    }

    
}
</script>