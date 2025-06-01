<template>
  <div>
    <div class="card mt-3">
      <div class="card-header" data-toggle="collapse" data-target="#frontEndSettings" aria-expanded="true"
        aria-controls="frontEndSettings">
        {{ $t("Frontend settings") }}
      </div>
      <pre-loader v-if="isLoading" />
      <div class="card-body p-3 collapse show" v-else id="frontEndSettings">

        <div class="form-check mb-3">
          <input class="form-check-input" v-model="settings.frontend_enabled" type="checkbox" v-bind:true-value="1"
            v-bind:false-value="0" @change="save('frontend_enabled', settings.frontend_enabled)"
            id="frontend_enabled" />
          <label class="form-check-label" for="frontend_enabled">
            {{ $t("Enable / disabled frontend") }}
          </label>
        </div>

        <div class="form-floating mb-3">
          <input v-model="settings.site_title" @blur="save('site_title', settings.site_title)" type="text"
            class="form-control" id="site_title" :placeholder="$t('Frontend title')" />
          <label for="site_title">{{ $t("Frontend title") }}</label>
        </div>

        <div class="form-floating mb-3">
          <input v-model="settings.site_description" @blur="save('site_description', settings.site_description)"
            type="text" class="form-control" id="site_description" :placeholder="$t('Frontend description')" />
          <label for="site_description">{{ $t("Frontend description") }}</label>
        </div>

        <div class="form-floating mb-3">
          <input v-model="settings.site_keywords" @blur="save('site_keywords', settings.site_keywords)" type="text"
            class="form-control" id="site_keywords" :placeholder="$t('Frontend keywords')" />
          <label for="site_keywords">{{ $t("Frontend keywords") }}</label>
        </div>

        <div class="form-floating mb-3">
          <textarea v-model="settings.custom_js" @blur="save('custom_js', settings.custom_js)" type="text"
            class="form-control" id="custom_js" :placeholder="$t('custom_js_example')"></textarea>
          <label for="custom_js">{{ $t("Custom javascript code") }}</label>
          <small class="form-text text-muted">{{ $t("custom js code") }}</small>
        </div>

        <div class="form-floating mb-3">
          <textarea v-model="settings.custom_dashboard_js"
            @blur="save('custom_dashboard_js', settings.custom_dashboard_js)" type="text" class="form-control"
            id="custom_dashboard_js" :placeholder="$t('custom_dashboard_js_example')"></textarea>
          <label for="custom_dashboard_js">{{
              $t("Custom dashboard javascript code")
          }}</label>
          <small class="form-text text-muted">{{
              $t("custom dashboard javascript code")
          }}</small>
        </div>

        <div class="form-floating mb-3">
          <textarea v-model="settings.custom_customer_area_js" @blur="
            save('custom_customer_area_js', settings.custom_customer_area_js)
          " type="text" class="form-control" id="custom_customer_area_js"
            :placeholder="$t('custom_customer_area_js_example')"></textarea>
          <label for="custom_customer_area_js">{{
              $t("Custom customer area javascript code")
          }}</label>
          <small class="form-text text-muted">{{
              $t("custom customer area javascript code")
          }}</small>
        </div>
      </div>
    </div>

    <div class="card mt-3">
      <div class="card-header" data-toggle="collapse" data-target="#systemUpdate" aria-expanded="true"
        aria-controls="systemUpdate">
        {{ $t("System update") }}
      </div>
      <div class="card-body collapse show" id="systemUpdate">
        <upload-update />
      </div>
    </div>

    <div class="row">
      <div class="col">
        <div class="card mb-3">
          <div class="card-header" data-toggle="collapse" data-target="#developerOptions" aria-expanded="true"
            aria-controls="developerOptions">
            {{ $t("Developer Options") }}
          </div>
          <div class="card-body p-3 collapse show" id="developerOptions">
            <div class="form-check form-switch my-2">
              <input class="form-check-input" type="checkbox" @change="toggleDownForMaintenance()"
                id="isDownForMaintenance" v-model="isDownForMaintenance" />
              <label class="form-check-label" for="isDownForMaintenance">
                {{ $t("Enable maintenance mode") }}
              </label>
              <small class="d-block text-muted">{{ $t("maintenance mode") }}</small>
            </div>

            <div class="form-check form-switch my-2">
              <input class="form-check-input" type="checkbox" v-bind:true-value="1" v-bind:false-value="0"
                id="debugMode" v-model="settings.app_debug" @change="save('app_debug', settings.app_debug)" />
              <label class="form-check-label" for="debugMode">
                {{ $t("Enable debug mode") }}
              </label>
              <small class="d-block text-muted">{{ $t("enabled errors") }}</small>
            </div>

            <div class="form-check form-switch my-2">
              <input class="form-check-input" type="checkbox" v-bind:true-value="1" v-bind:false-value="0"
                id="enable_cache" v-model="settings.enable_cache" @change="toggleEnableCache()" />
              <label class="form-check-label" for="enable_cache">
                {{ $t("Enable application cache") }}
              </label>
              <small class="d-block text-muted">{{
                  $t("enable application cache")
              }}</small>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-header" data-toggle="collapse" data-target="#refreshApp" aria-expanded="true"
            aria-controls="refreshApp">
            {{ $t("Application Refresh") }}
          </div>
          <div class="card-body collapse show" id="refreshApp">
            <button class="btn btn-primary" @click="refreshApp()">
              {{ $t("Application Refresh") }}
            </button>
            <div>
              <small class="small">{{
                  $t("application refresh services")
              }}</small>
            </div>
          </div>
        </div>
      </div>
      <div class="col">
        <serverStatus>
          <preloader />
        </serverStatus>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import serverStatus from "./components/ServerStatus";

export default {
  data() {
    return {
      isLoading: false,
      settings: {
        frontend_enabled: 1,
        site_title: "",
        site_description: "",
        site_keywords: "",
        custom_js: "",
        app_debug: "",
        enable_cache: 0,
      },
      isDownForMaintenance: false,
    };
  },
  components: {
    serverStatus,
  },
  mounted() {
    this.loadSettings();
  },
  methods: {
    loadSettings() {
      this.isLoading = true;
      axios.post(this.$myaccount_url + "settings/fetch").then((response) => {
        this.settings = response.data.settings;
        this.isDownForMaintenance = response.data.isDownForMaintenance;
        this.isLoading = false;
      });
    },
    refreshApp() {
      axios
        .post(this.$myaccount_url + "update/refresh_app")
        .then((response) => {
          if (response.data.status == "ok") {
            this.updateCompleted = true;
            this.successMsg = response.data.message;
            this.$showSuccess(response.data.message);

            this.uploadProgress = 0;
            this.uploading = false;
            this.updating = false;
            this.errorDetails = "";
            this.updateFailed = false;
          } else {
            if (typeof response.data.message == "string") {
              this.errorDetails = response.data.message;
              this.$showError(response.data.message);
            } else {
              this.$showError(response.data.message.join(","));
              this.errorDetails = response.data.message.join(",");
            }
          }
        })
        .catch((e) => {
          this.$showError(e);
        });
    },
    save(key, val) {
      this.$toastClear();
      axios
        .post(this.$myaccount_url + "settings/save", { key, val })
        .then((response) => {
          if (response.data.status == "ok") {
            this.$showSuccess(response.data.message);
          } else {
            if (typeof response.data.message == "string")
              this.$showError(response.data.message);
            else this.$showError(response.data.message.join(","));
          }
        })
        .catch((e) => {
          this.$showError(e);
        });
    },
    toggleDownForMaintenance() {
      this.$toastClear();
      axios
        .post(this.$myaccount_url + "settings/toggle_maintenance_mode", {
          maintenance_mode: this.isDownForMaintenance,
        })
        .then((response) => {
          if (response.data.status == "ok") {
            this.$showSuccess(response.data.message);
          } else {
            if (typeof response.data.message == "string")
              this.$showError(response.data.message);
            else this.$showError(response.data.message.join(","));
          }
        });
    },
    toggleEnableCache() {
      this.$toastClear();
      axios
        .post(this.$myaccount_url + "settings/toggle_enable_cache", {
          enable_cache: this.settings.enable_cache,
        })
        .then((response) => {
          if (response.data.status == "ok") {
            this.$showSuccess(response.data.message);
          } else {
            if (typeof response.data.message == "string")
              this.$showError(response.data.message);
            else this.$showError(response.data.message.join(","));
          }
        });
    },
  },
};
</script>
