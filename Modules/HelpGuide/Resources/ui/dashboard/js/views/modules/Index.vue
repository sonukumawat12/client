<template>
  <div class="container-fluid content-row" id="modulesManager">
    <div class="card">
      <div class="card-body p-2">
        <div class="upload-module">
          <upload-module><div class="preloader"></div></upload-module>
        </div>
      </div>
    </div>

    <modules-list><div class="preloader"></div></modules-list>
  </div>
</template>

<script>

Vue.component('upload-module', () => import('./UploadModule'))
Vue.component('modules-list', () => import('./ModulesList'))

export default ({
    data() {
        return {}
    },
})
</script>
