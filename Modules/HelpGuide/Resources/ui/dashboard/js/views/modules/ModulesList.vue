<template>
  <div class="modules-list">

    <pre-loader v-if="items.isLoading" />

    <div v-else-if="items.data.length">
      <div class="row">
        <div v-for="(module, index) in items.data" v-bind:key="index"
          class="col-sm-12 col-md-6 col-lg-4 col-xl-3 d-flex">
          <ModulesListItem :module="module" />
        </div>
      </div>
    </div>

    <empty-state v-else class="m-auto" :text="$t('No modules available')" />

  </div>
</template>

<script setup>
import ModulesListItem from './ModulesListItem.vue'
import { useModuleStore } from './../../stores/ModuleStore'
import { storeToRefs } from 'pinia'
import { onMounted } from 'vue'

const moduleStore = useModuleStore()
const { items } = storeToRefs(moduleStore);

onMounted(() => {
  moduleStore.loadItems()
})
</script>
