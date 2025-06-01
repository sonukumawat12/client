<template>
    <div class="">
        <a class="d-flex border-bottom align-items-center px-3 p-2" v-for="(notification, index) in notifications"
            v-bind:key="index" :href="`${notification.data.link}?notification_id=${notification.id}`">
            <div class="me-2">
                <span class="badge m-0 badge" v-bind:class="'badge-' + notification.data.color"><i
                        :class="'bi bi-' + notification.data.icon + ' text-white'"></i></span>
            </div>
            <div>
                <span>{{ notification.data.text }}</span>
                <div class="small text-muted text-gray-500">{{ notification.date }}</div>
            </div>
        </a>
        <div v-if="!notifications.length" class="text-center small p-5">{{ $t('No new notifications') }}</div>
        <div class="py-2 bg-light text-center"><a :href="$myaccount_url + 'notifications'" class="text-dark small">{{
            $t('See all notifications')}}</a></div>
    </div>
</template>

<script>
import axios from 'axios';
export default {
    data() {
        return {
            notifications: [],
            pageTitle: ''
        }
    },
    mounted() {
        this.pageTitle = document.title
        this.getNotifications()
    },
    methods: {
        getNotifications() {
            axios
                .post(this.$myaccount_url + 'notifications/unread')
                .then(response => {
                    this.notifications = response.data

                    const notificationElement = document.querySelector('.notification-count');
                    if (notificationElement) {
                        notificationElement.textContent = this.notifications.length;
                    }

                    //if( this.notifications.length ) document.title = '('+this.notifications.length+') '+this.pageTitle
                });
        },
        markAsRead() {
            if (!this.notifications.length) return
            axios
                .post(this.$myaccount_url + 'notifications/mark_as_read')
                .then(response => {
                    document.title = this.pageTitle
                });
        }
    }
}
</script>

<style scoped></style>