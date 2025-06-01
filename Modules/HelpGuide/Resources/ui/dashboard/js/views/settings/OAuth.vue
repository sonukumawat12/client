<template>
  <div class="card">
    <div class="card-header">
      <a
        data-toggle="collapse"
        class="text-dark w-100 d-block"
        data-target="#OAuthSettings"
        aria-expanded="true"
        aria-controls="OAuthSettings"
      >
        <i class="bi bi-shield-check"></i> {{ $t("OAuth login") }}
      </a>
    </div>
    <div class="card-body collapse show" id="OAuthSettings">
      <div v-for="(provider, index) in providers" v-bind:key="index">
        <div class="row card card-body p-2 py-3">
          <div class="col-12">

            <div class="form-check form-switch my-2">
                <input
                  class="form-check-input"
                  v-model="oAuthSettings[index].isEnabled"
                  :name="index + '_oauth_enabled'"
                  :aria-describedby="index + '_oauth_enabledFeedback'"
                  type="checkbox"
                  v-bind:true-value="1"
                  v-bind:false-value="0"
                  :id="'customSwitch-' + index + '_oauth_enabled'"
                  @change="
                    $emit('save', {
                      key: index + '_oauth_enabled',
                      val: oAuthSettings[index].isEnabled,
                    })
                  "
                >
                <label class="form-check-label" :for="'customSwitch-' + index + '_oauth_enabled'">
                    {{ provider }}
                </label>
            </div>

          </div>
          <div class="col-sm-6 mb-2">
            <input
              type="text"
              v-model="oAuthSettings[index].app_id"
              :name="index + '_oauth_app_id'"
              @blur="
                $emit('save', {
                  key: index + '_oauth_app_id',
                  val: oAuthSettings[index].app_id,
                })
              "
              class="form-control"
              v-bind:class="{
                'is-invalid': errors.has(index + '_oauth_app_id'),
              }"
              :placeholder="$t('App id')"
            />
            <div class="invalid-feedback">
              {{ errors.get(index + "_oauth_app_id") }}
            </div>
          </div>
          <div class="col-sm-6 mb-2">
            <input
              type="text"
              :name="index + '_oauth_app_secret'"
              v-model="oAuthSettings[index].app_secret"
              @blur="
                $emit('save', {
                  key: index + '_oauth_app_secret',
                  val: oAuthSettings[index].app_secret,
                })
              "
              class="form-control"
              v-bind:class="{
                'is-invalid': errors.has(index + '_oauth_app_secret'),
              }"
              :placeholder="$t('App secret')"
            />
            <div class="invalid-feedback">
              {{ errors.get(index + "_oauth_app_secret") }}
            </div>
          </div>
          <div class="col-12">
            <small class="small"
              >Your {{ provider }} callback URL is : {{ $base_url }}login/{{
                index
              }}/callback
              <i
                class="bi bi-copy btn btn-link m-0 p-0"
                @click="copyThis($event)"
                :data-copy="$base_url + 'login/' + index + '/callback'"
              ></i
            ></small>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      providers: { envato: "Envato", google: "Google", facebook: "Facebook" },
      oAuthSettings: {
        envato: {
          isEnabled: 0,
          app_id: null,
          app_secret: null,
        },
        facebook: {
          isEnabled: 0,
          app_id: null,
          app_secret: null,
        },
        google: {
          isEnabled: 0,
          app_id: null,
          app_secret: null,
        },
      },
    };
  },
  props: {
    settings: { type: Object },
    errors: { type: Object },
  },
  methods: {
    copyThis(e) {
      const el = document.createElement("textarea");
      el.value = e.target.getAttribute("data-copy");
      document.body.appendChild(el);
      el.select();
      document.execCommand("copy");
      document.body.removeChild(el);
      this.$showSuccess(
        this.$t("Link has been copied to clipboard")
      );
    },
    settingsUpdated: function () {
      this.oAuthSettings.envato.isEnabled = this.settings.envato_oauth_enabled;
      this.oAuthSettings.envato.app_id = this.settings.envato_oauth_app_id;
      this.oAuthSettings.envato.app_secret =
        this.settings.envato_oauth_app_secret;

      this.oAuthSettings.facebook.isEnabled =
        this.settings.facebook_oauth_enabled;
      this.oAuthSettings.facebook.app_id = this.settings.facebook_oauth_app_id;
      this.oAuthSettings.facebook.app_secret =
        this.settings.facebook_oauth_app_secret;

      this.oAuthSettings.google.isEnabled = this.settings.google_oauth_enabled;
      this.oAuthSettings.google.app_id = this.settings.google_oauth_app_id;
      this.oAuthSettings.google.app_secret =
        this.settings.google_oauth_app_secret;
    },
  },
  mounted() {
    this.settingsUpdated();
  },
};
</script>
