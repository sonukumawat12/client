__webpack_public_path__ = BASE_URL + 'build/'

import Vue from "vue";

window.Vue = Vue;
require('./../../common/js/bootstrap');

window.missingLang = []
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

import Toast, { POSITION } from "vue-toastification";
Vue.use(Toast, {
    timeout: 10000,
    draggablePercent: 0.2,
    closeOnClick: false,
    position: POSITION.BOTTOM_LEFT
});
/* --- End Toast Options */

let toast = Vue.$toast

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

Vue.prototype.$base_url = BASE_URL
Vue.prototype.$mya_url = MYACCOUNT_URL

import searchBar from './components/SearchBar'
import articleRate from './components/ArticleRate'
import login from './components/auth/Login'
Vue.component('save-btn', () => import('./../../common/components/SaveButton'))
Vue.component('pre-loader', { template: '<div class="preloader"></div>' });

new Vue({
    el: '#app',
    components: {
        'search-bar': searchBar,
        'article-rate': articleRate,
        'app-login': login,
    }
})
