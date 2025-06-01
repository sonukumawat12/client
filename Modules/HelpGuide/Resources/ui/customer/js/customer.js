
__webpack_public_path__ = BASE_URL + 'build/'

require('./../../common/js/bootstrap');

import Vue from "vue";
window.Vue = Vue;
window.events = new Vue({ name: 'events' });
import NProgress from 'nprogress'
/* ------------ Routers --------------------*/
import VueRouter from "vue-router";
Vue.use(VueRouter);
import routes from "./routers";
const router = new VueRouter({
    routes,
    linkExactActiveClass: "active",
});

router.afterEach((to) => {
    if (to.meta && to.meta.title) {
        document.title = Vue.prototype.$t(to.meta.title);
    }
});

Vue.config.ignoredElements = [/appm:*/]

Vue.component('v-notifications', () => import('./../../common/components/NotificationItem'))
Vue.component('text-editor', () => import('./../../common/components/TextEditor'))
Vue.component('pre-loader', () => import('./../../common/components/PreLoader.vue'))
Vue.component('fancy-box', () => import('./../../common/components/FancyBox'))
Vue.component('empty-state', () => import('./../../common/components/EmptyState'))
Vue.component('scroll-loader', () => import('./../../common/components/ScrollLoader'))
Vue.component('generate-password', () => import('./../../common/components/GeneratePassword'))
Vue.component('save-btn', () => import('./../../common/components/SaveButton'))

import vmodal from 'vue-js-modal'

Vue.use(vmodal, { dialog: true, dynamic: true, injectModalsContainer: true, dynamicDefaults: { height: 'auto', scrollable: true, clickToClose: true } })

/*---- Toast ----*/
import Toast, { POSITION } from "vue-toastification";
Vue.use(Toast, {
    timeout: 10000,
    draggablePercent: 0.2,
    closeOnClick: false,
    position: POSITION.BOTTOM_LEFT
});
/* --- End Toast Options */

window.missingLang = []
var _ = require('lodash');
Vue.prototype.$t = (string, args) => {
    let value = _.get(window.i18n, string.toLowerCase());

    _.eachRight(args, (paramVal, paramKey) => {
        value = _.replace(value, `:${paramKey}`, paramVal);
    });

    if (!value && window.missingLang.indexOf(string.toLowerCase()) === -1) {
        window.missingLang.push(string.toLowerCase())
    }

    if (value) return value

    return string
};

Vue.prototype.$allow = (string) => {
    if (USER.user_permissions.includes(string.toLowerCase())) {
        return true
    }
    return false
};

Vue.prototype.$showError = (e) => {

    if (e.response && e.response.data && e.response.data.errors) {
        let errors = Object.values(e.response.data.errors);
        Vue.$toast.error(errors.join('\n'))
        return;
    }

    if (e.response && e.response.data && e.response.data.messages) {
        let messages = Object.values(e.response.data.messages);
        Vue.$toast.error(messages.join('\n'))
        return;
    }

    if (e.response && e.response.data && e.response.data.message) {
        Vue.$toast.error(e.response.data.message);
        return;
    }

    if (e.message) {
        Vue.$toast.error(e.message);
        return;
    }

    if(typeof e === 'string' ) {
      Vue.$toast.error(e);
      return;
    }

    Vue.$toast.error(Vue.prototype.$t('Something went wrong. Please try again later.'));
};

Vue.prototype.$showSuccess = (m) => {
    Vue.$toast.success(m);
}

Vue.prototype.$showInfo = (m) => {
    Vue.$toast.info(m);
}

Vue.prototype.$showInfo = (m) => {
    Vue.$toast.info(m);
}

Vue.prototype.$showWarning = (m) => {
    Vue.$toast.warning(m);
}

Vue.prototype.$toastClear = () => {
    Vue.$toast.clear();
}

Vue.mixin({
    methods: {
        hashCode: function (str) { // java String#hashCode
            var hash = 0;
            for (var i = 0; i < str.length; i++) {
                hash = str.charCodeAt(i) + ((hash << 5) - hash);
            }
            return hash;
        },
        intToRGB: function (i) {
            var c = (i & 0x00FFFFFF)
                .toString(16)
                .toUpperCase();
            return "00000".substring(0, 6 - c.length) + c;
        },
        stringToColor: function (str) {
            return this.intToRGB(this.hashCode(str));
        }

    }
})

/* ------ Axios -------*/
import axios from 'axios';
axios.defaults.baseURL = BASE_URL
axios.interceptors.request.use(config => {
    NProgress.start(); return config;
}, (error) => {
    NProgress.done(); return Promise.reject(error);
});

axios.interceptors.response.use(response => {
    NProgress.done(); return response
})
NProgress.configure({ showSpinner: false });
axios.interceptors.response.use(
    response => response,
    errorHandle
);

function errorHandle(error) {
    if (error.response && error.response.status && (error.response.status == 419 || error.response.status == 401)) {
        Vue.$toast.clear()
        Vue.prototype.$showError(Vue.prototype.$t("session_expired_login_again_refresh_page"));
    }

    NProgress.done();
    return Promise.reject(error);
}

Vue.prototype.$base_url = BASE_URL
Vue.prototype.$api_url = API_URL
Vue.prototype.$myaccount_url = MYACCOUNT_URL

Vue.directive('click-outside', {
    bind: function (el, binding, vNode) {
        // Provided expression must evaluate to a function.
        if (typeof binding.value !== 'function') {
            const compName = vNode.context.name
            let warn = `[Vue-click-outside:] provided expression '${binding.expression}' is not a function, but has to be`
            if (compName) { warn += `Found in component '${compName}'` }

            console.warn(warn)
        }
        // Define Handler and cache it on the element
        const bubble = binding.modifiers.bubble
        const handler = (e) => {
            if (bubble || (!el.contains(e.target) && el !== e.target)) {
                binding.value(e)
            }
        }
        el.__vueClickOutside__ = handler


        document.addEventListener('click', handler)
    },

    unbind: function (el, binding) {
        document.removeEventListener('click', el.__vueClickOutside__)
        el.__vueClickOutside__ = null

    }
})

Vue.filter('capitalize', function (value) {
    if (!value) return ''
    value = value.toString()
    return value.charAt(0).toUpperCase() + value.slice(1)
})

const app = new Vue({
    el: '#app',
    router,
    data() {
        return {
            sidebarToggled: false
        }
    }
})

events.$on('loaded', function () {
    app.$forceUpdate();
})
