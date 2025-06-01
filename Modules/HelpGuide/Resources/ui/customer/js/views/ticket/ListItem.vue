<template>
    <div class="row">
    <div class="col-auto ms-2 mt-2">
        <a href="#">
            <img class="bd-placeholder-img rounded rounded-circle customer-picture" width="50" height="50" :src="ticket.user.avatar">
        </a>
    </div>
    <div class="col d-flex flex-column position-static p-2 w-100">
        <router-link :to="{name: 'tickets.view', params: { id: ticket.id }}">
            <div class="media-body small lh-125">

            <span class="d-block text-gray-dark m-0 mb-1">
                <router-link :to="{name: 'tickets.view', params: { id: ticket.id }}" class="ticket-title">{{ ticket.title }}</router-link>
                <span :class="'badge badge-ticket-'+ticket.status">{{$t(ticket.status)}}</span>
                <span :class="'badge badge-ticket-priority-'+ticket.priority">{{$t(ticket.priority)}}</span>
                 <span v-if="ticket.attachments.length" class="badge bg-secondary"><i class="bi bi-paperclip"></i></span>
            </span>

            <div v-if="ticket.category">
                {{ $t('Category')}} : <span :class="'badge badge-light border'" :style="'color: #'+stringToColor(ticket.category.name)">{{$t(ticket.category.name)}}</span>
            </div>

            <div v-if="replyExcerptToggle" v-text="lastReply()" class="my-2"></div>

            <div class="font-weight-light d-flex justify-content-between">
                <div>{{$t('Submited by')}}
                    <a href="#" class="font-weight-bold">
                        {{ ticket.user.name }}
                    </a>
                    <span class="mx-1">.</span>
                    <span class="text-muted">
                    {{$t('Assigned to')}}
                    <a href="#" class="font-weight-bold">
                        {{ticket.assigned_to.name}}
                    </a>
                    </span>
                    <span class="mx-1">.</span>
                    <span> on {{ticket.submitted_on}}</span>
                    <template v-if="ticket.last_reply_on">
                    <span class="mx-1">.</span>
                    <span> {{$t('Last reply')}} {{ticket.last_reply_on}}</span>
                    </template>
                </div>
            </div>

            </div>
        </router-link>
    </div>
    </div>
</template>
<script>
export default {
    name: "TicketListItem",
    props: ["ticket","replyExcerptToggle"],
    methods: {
        lastReply() {

            if(!this.ticket.hasOwnProperty('last_reply')){
                return null;
            }

            let regex = /(<([^>]+)>)/ig;
            let content = this.ticket.last_reply
            content = content.replace(regex, "");

            if(content.length > 50){
                content = content.substring(0, 100)+'...'
            }
            return content;
        }
    }
}
</script>
