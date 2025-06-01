<template>
    <div
        class="row"
        v-bind:class="{hasReply: ( ticket.has_reply && ticket.status == 'open') }"
        >
        <div class="col-auto ms-2 mt-2">
            <img class="bd-placeholder-img rounded rounded-circle customer-picture" width="50" height="50" :src="ticket.user.avatar">
        </div>
        <div class="col d-flex flex-column position-static p-2 w-100">
            <a :href="$myaccount_url+'tickets/' + ticket.id ">
                <div class="media-body small lh-125">

                <span class="d-block text-gray-dark m-0 mb-1">
                    <a :href="$myaccount_url+'tickets/' + ticket.id " class="ticket-title">{{ ticket.title }}</a>
                    <span :class="'text-capitalize badge badge-ticket-'+ticket.status">{{ $t(ticket.status) }}</span>
                    <span :class="'text-capitalize badge badge-ticket-priority-'+ticket.priority">{{$t(ticket.priority)}}</span>
                    <span v-if="ticket.attachments.length" class="badge bg-secondary"><i class="bi bi-paperclip"></i></span>
                </span>

                <div>
                    {{ $t('Category')}} :
                    <span v-if="ticket.category">
                    <span :class="'badge badge-light border'" :style="'color: #'+stringToColor(ticket.category.name)">
                        {{ticket.category.name}}
                    </span>
                    </span>
                </div>

                <div v-if="replyExcerptToggle" v-text="lastReply()" class="my-2"></div>

                <div class="font-weight-light">
                    <div>{{$t('Submited by')}}
                        <a href="#" v-if="ticket.user" class="font-weight-bold">
                            {{ ticket.user.name}}
                        </a>
                        <span class="mx-1">.</span>
                        <span class="text-muted">
                        {{$t('Assigned to')}}
                        <a href="#" v-if="ticket.assigned_to" class="font-weight-bold">
                            {{ticket.assigned_to.name}}
                        </a>
                        </span>
                        <span class="mx-1">.</span>
                        <span> {{$t('on')}} {{ticket.submitted_on}}</span>
                        <template>
                            <span class="mx-1">.</span>
                            <span>{{$t('Last reply')}} {{ticket.last_reply_on}}</span>
                            {{$t('By')}}
                            <a href="#" v-if="ticket.assigned_to" class="font-weight-bold">
                                 {{ ticket.last_reply_by }}
                            </a>
                        </template>
                    </div>
                </div>

                </div>
            </a>
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
