<template>
  <router-view></router-view>
</template>

<script>
import VueRouter from "vue-router";
const routes = [
  { name: "home", path: "/", component: () => import("./Home") },
];

if( Vue.prototype.$allow('manage_categories') ){
  routes.push({
    name: "categories",
    path: "/categories",
    component: () => import("./categories/ManageCategories"),
    meta: { title: Vue.prototype.$t('Categories') }
  })
}

routes.push({
  name: "customer_groups",
  path: "/customer-groups",
  component: () => import("./customer_groups/Index"),
  meta: { title: Vue.prototype.$t('Customer Groups') }
})

routes.push({
  name: "employee_groups",
  path: "/employee-groups",
  component: () => import("./employee_groups/Index"),
  meta: { title: Vue.prototype.$t('Employee Groups') }
})

routes.push({
  name: "bulk_notifications",
  path: "/bulk-notifications",
  component: () => import("./bulk_notifications/Index"),
  meta: { title: Vue.prototype.$t('Bulk Notifications') }
})

if( Vue.prototype.$allow('create_saved_reply') ){
  routes.push({
    name: "saved_replies",
    path: "/saved_replies",
    component: () => import("./saved_replies/SavedReplies"),
    meta: { title: Vue.prototype.$t('Saved replies') }
  })
}

if( Vue.prototype.$allow('manage_articles') ){
  routes.push({
      name: "articles_list",
      path: "/articles",
      component: () => import("./articles/ArticlesList"),
      meta: { title: Vue.prototype.$t('Articles') }
  })

  routes.push({
    name: "articles_new",
    path: "/artilces/new",
    component: () => import("./articles/CreateArticleForm"),
    meta: { title: Vue.prototype.$t('New article') }
  })

  routes.push({
    name: "articles_edit",
    path: "/articles/edit/:article_id/:locale?",
    component: () => import("./articles/CreateArticleForm"),
    props: true,
    meta: { title: Vue.prototype.$t('Edit article') }
  })
}

if( Vue.prototype.$allow('manage_modules') ){
  routes.push({
      name: "modules",
      path: "/modules",
      component: () => import("./modules/Index"),
      meta: { title: Vue.prototype.$t('Modules') }
  })
}

if( Vue.prototype.$allow('manage_customers') ){
  routes.push({
      name: "customers",
      path: "/customers",
      component: () => import("./user/customers/Index"),
      meta: { title: Vue.prototype.$t('Customers') }
  })
  routes.push({
      name: "customer_details",
      props: true,
      path: "/customers/details/:userid",
      component: () => import("./user/View"),
      meta: { title: Vue.prototype.$t('Customers') }
  })
}

if( Vue.prototype.$allow('manage_employees') ){
  routes.push({
      name: "employees",
      path: "/employees",
      component: () => import("./user/employees/Index"),
      meta: { title: Vue.prototype.$t('Employees') }
  })
  routes.push({
      name: "employee_details",
      props: true,
      path: "/employees/details/:userid",
      component: () => import("./user/View"),
      meta: { title: Vue.prototype.$t('Employees') }
  })
}

if( Vue.prototype.$allow('view_statistics') ){
  routes.push({
      name: "statistics",
      path: "/statistics",
      component: () => import("./statistics/Index.vue"),
      meta: { title: Vue.prototype.$t('statistics') }
  })
}

routes.push({ path: "*", component: () => import("./../../../common/components/NotFound.vue")})

const router = new VueRouter({
  routes,
  linkExactActiveClass: "active",
});

router.afterEach((to, from) => {
  if (to.meta && to.meta.title) {
    document.title = to.meta.title;
  }
});

export default {
  router,
};
</script>
