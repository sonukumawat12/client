<template>
    <customers-list><div class="preloader"></div></customers-list>
</template>

<script>
Vue.component('customers-list', () => import('./List'))
export default ({})
</script>
