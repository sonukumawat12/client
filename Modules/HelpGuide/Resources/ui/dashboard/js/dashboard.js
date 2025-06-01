
__webpack_public_path__ = BASE_URL + 'build/'
require('./../../common/js/bootstrap');
require('./custom');

import Vue from "vue";
import { createPinia, PiniaVuePlugin } from 'pinia'
import NProgress from 'nprogress'
import vmodal from 'vue-js-modal'

var _ = require('lodash');
window.simplebar = require('simplebar-vue');
window.Vue = Vue;
Vue.config.ignoredElements = [/appm:*/]
Vue.prototype.$base_url = BASE_URL
Vue.prototype.$admin_api = ADMIN_API_URL
Vue.prototype.$api_url = BASE_URL + "api/"
Vue.prototype.$myaccount_url = MYACCOUNT_URL

Vue.component('ticket-list', () => import('./tickets/List'))
Vue.component('my-ticket', () => import('./tickets/Ticket'))
Vue.component('search-form', () => import('./searchForm'))
Vue.component('v-notifications', () => import('./../../common/components/NotificationItem'))
Vue.component('insights-overview', () => import('./insights/ticket/Overview'))
Vue.component('upload-update', () => import('./update/UploadFile'))
Vue.component('app-articles', () => import('./views/articles/Page.vue'))
Vue.component('app-settings', () => import('./views/settings/Settings'))
Vue.component('app-dashboard', () => import('./views/Dashboard'))
Vue.component('text-editor', () => import('./../../common/components/TextEditor'))
Vue.component('search-bar', () => import('./components/Searchbar'))
Vue.component('pre-loader', () => import('./../../common/components/PreLoader.vue'))
Vue.component('fancy-box', () => import('./../../common/components/FancyBox'))
Vue.component('empty-state', () => import('./../../common/components/EmptyState'))
Vue.component('scroll-loader', () => import('./../../common/components/ScrollLoader'))
Vue.component('save-btn', () => import('./../../common/components/SaveButton'))
Vue.component('generate-password', () => import('./../../common/components/GeneratePassword'))

Vue.use(vmodal, { dialog: true, dynamic: true, injectModalsContainer: true, dynamicDefaults: { height: 'auto', scrollable: true, clickToClose: true } })

Vue.use(PiniaVuePlugin)
const pinia = createPinia()
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

Vue.prototype.$t = (string, args) => {

  let value = _.get(window.i18n, string.toLowerCase());

  if (!value) {
      if( window.missingLang.indexOf(string.toLowerCase()) === -1 ){
          window.missingLang.push(string.toLowerCase())
      }
      value = string
  }

  _.eachRight(args, (paramVal, paramKey) => {
      value = _.replace(value, `:${paramKey}`, paramVal);
  });

  return value
};

Vue.prototype.$setCookie = (cname, cvalue, exdays) => {
  const d = new Date();
  d.setTime(d.getTime() + (exdays*24*60*60*1000));
  let expires = "expires="+ d.toUTCString();
  document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

Vue.prototype.$getCookie = (cname) => {
    let name = cname + "=";
    let decodedCookie = decodeURIComponent(document.cookie);
    let ca = decodedCookie.split(';');
    for(let i = 0; i <ca.length; i++) {
      let c = ca[i];
      while (c.charAt(0) == ' ') {
        c = c.substring(1);
      }
      if (c.indexOf(name) == 0) {
        return c.substring(name.length, c.length);
      }
    }
    return null;
}

Vue.prototype.$allow = (string) => {
    if (USER.user_permissions.includes(string.toLowerCase())) {
        return true
    }
    return false
};

let toast = Vue.$toast

function translate(s){
  return Vue.$t(s)
}

Vue.prototype.$showError = (e) => {

  if (e.response && e.response.data && e.response.data.errors) {
      let errors = Object.values(e.response.data.errors);
      toast.error(errors.join('\n'))
      return;
  }

  if (e.response && e.response.data && e.response.data.messages) {
      let messages = Object.values(e.response.data.messages);
      toast.error(messages.join('\n'))
      return;
  }

  if (e.response && e.response.data && e.response.data.message) {
      toast.error(e.response.data.message);
      return;
  }

  if (e.response && e.response.data && e.response.data.error) {
      toast.error(e.response.data.error);
      return;
  }

  if (e.message) {
      toast.error(e.message);
      return;
  }


  if(typeof e === 'string' ) {
      toast.error(e);
      return;
  }

  toast.error(translate('Something went wrong. Please try again later.'));
};

Vue.prototype.$showSuccess = (m) => {
    Vue.$toast.success(m);
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
    pinia,
    data() {
        return {
            sidebarToggled: false
        }
    }
})

Vue.prototype.$appModal = (modal, options) => {
  Vue.prototype.$modal.show(modal, options)
}

window.app = app
window.events = app

events.$on('loaded', function () {
    app.$forceUpdate();
})
