import { defineStore } from 'pinia'

export const useModuleStore = defineStore('ModuleStore', {
  state: () => ({
    items: {
      isLoading: false,
      isLoadingMore: false,
      nextPage: null,
      data: []
    },
    item: {
      isLoading: false,
      data: []
    },
  }),
  actions: {
    loadItems() {
      this.items.nextPage = `${app.$admin_api}modules`
      this.items.isLoading = true
      this.items.data = []
      this.loadMoreItems().finally(() => this.items.isLoading = false)
    },
    loadMoreItems() {
      return new Promise((resolve, reject) => {
        this.items.isLoadingMore = true;
        axios
          .get(this.items.nextPage)
          .then((response) => {

            this.items.data = this.items.data.concat(response.data);

            this.items.nextPage = null
            resolve(response)

          })
          .catch((e) => { app.$showError(e), reject(e) })
          .finally(() => this.items.isLoadingMore = false);
      });
    },
    install(module) {

      module.is_installing = true

      axios
        .post(app.$admin_api + "modules/install", {
          module: module.id
        })
        .then((response) => {
          app.$showSuccess(response.data.message);
          location.reload();
        })
        .catch((e) => app.$showError(e))
        .finally(() => module.is_installing = false);
    },
    toggleModuleStatus(moduleItem) {
      moduleItem.is_changing = true;
      axios
        .post(`${app.$admin_api}modules/${moduleItem.id}/toggle_module_status`)
        .then((response) => app.$showSuccess(response.data.message))
        .catch((e) => app.$showError(e))
        .finally(() => moduleItem.is_changing = false)
    },
  }
})