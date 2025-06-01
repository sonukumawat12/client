<template>
<div class="card widget-flat">
    <div class="card-body">
        <h6 class="text-capitalize mt-0" title="Number of Customers">{{ $t(type) }}</h6>
        <span class="badge" v-bind:class="badgeClass()">{{ value }}</span>
    </div>
</div>
</template>

<script>
export default {
    props: {
        value: {
            default: '0',
        },
        type: {
            default: '0',
        }
    },
    methods: {
        badgeClass(){
            switch(this.type){
                case 'open':  return 'bg-danger'
                case 'resolved':  return 'bg-success'
                case 'closed':  return 'bg-secondary'
                case 'total':  return 'bg-info'
            }
        }
    }
}
</script>