<template>
    <employees-list><div class="preloader"></div></employees-list>
</template>

<script>
Vue.component('employees-list', () => import('./List'))
export default ({})
</script>
