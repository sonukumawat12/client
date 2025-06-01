<template>
<router-view></router-view>
</template>

<script>
import VueRouter from 'vue-router'
const routes = [
  { name: 'articles_list', path: '/articles', component: () => import('./ArticlesList') },
  { name: 'articles_new', path: '/artilces/new', component: () => import('./CreateArticleForm') },
  { name: 'articles_edit', path: '/articles/edit/:article_id/:locale', component: () => import('./CreateArticleForm'), props: true },
]

const router = new VueRouter({
  routes,
  linkExactActiveClass: "active"
})

export default {
  router
}
</script>