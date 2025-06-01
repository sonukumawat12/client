<template>
  <div>
    <pre-loader v-if="isLoading" />
    <div v-else>
      <form @keydown="errors.clear($event.target.name)">
        <div class="card">
          <div class="card-header">
            <a
              data-toggle="collapse"
              class="text-dark w-100 d-block"
              data-target="#generalSettings"
              aria-expanded="true"
              aria-controls="generalSettings"
            >
              <i class="bi bi-sliders"></i> {{ $t("general") }}
            </a>
          </div>
          <div class="card-body" id="generalSettings">
            <div class="form-floating my-2">
              <input
                type="text"
                class="form-control"
                v-bind:class="{ 'is-invalid': errors.has('app_name') }"
                id="app_name"
                name="app_name"
                v-model="settings.app_name"
                @blur="save('app_name', settings.app_name)"
                :placeholder="$t('app name')"
              />
              <label for="app_name">{{ $t("App name") }}</label>
              <div class="invalid-feedback">{{ errors.get("app_name") }}</div>
            </div>

            <div class="row my-2">
              <div class="col">
                <div class="row">
                  <div class="col-auto">
                    <img :src="settings.app_logo" width="80" class="border" />
                  </div>
                  <div class="col">
                    <label for="app_logo" class="form-label">{{
                      $t("App logo")
                    }}</label>
                    <input
                      class="form-control"
                      data-item="app_logo"
                      name="upload_app_logo"
                      @change="doUpload"
                      id="upload_app_logo"
                      type="file"
                      v-bind:class="{ 'is-invalid': errors.has('app_logo') }"
                    />
                    <div class="invalid-feedback">
                      {{ errors.get("app_logo") }}
                    </div>
                  </div>
                </div>
              </div>

              <div class="col">
                <div class="row">
                  <div class="col-auto">
                    <img :src="settings.favicon" width="80" class="border" />
                  </div>
                  <div class="col">
                    <label for="favicon" class="form-label">{{
                      $t("App favicon")
                    }}</label>
                    <input
                      class="form-control"
                      data-item="favicon"
                      name="upload_favicon"
                      @change="doUpload"
                      id="upload_favicon"
                      type="file"
                      v-bind:class="{ 'is-invalid': errors.has('favicon') }"
                    />
                    <div class="invalid-feedback">
                      {{ errors.get("favicon") }}
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="form-check my-2">
              <input
                class="form-check-input"
                v-model="settings.user_can_register"
                type="checkbox"
                v-bind:true-value="1"
                v-bind:false-value="0"
                @change="save('user_can_register', settings.user_can_register)"
                id="user_can_register"
              />
              <label class="form-check-label" for="user_can_register">
                {{ $t("user can register") }}
              </label>
            </div>

            <div class="form-check my-2">
              <input
                class="form-check-input"
                v-model="settings.verify_email"
                type="checkbox"
                v-bind:true-value="1"
                v-bind:false-value="0"
                @change="save('verify_email', settings.verify_email)"
                id="verify_email"
              />
              <label class="form-check-label" for="verify_email">
                {{ $t("users must verify their email address") }}
              </label>
            </div>

            <div class="form-floating my-2">
              <input
                type="email"
                class="form-control"
                id="admin_email"
                v-model="settings.admin_email"
                v-bind:class="{ 'is-invalid': errors.has('admin_email') }"
                @blur="save('admin_email', settings.admin_email)"
                :placeholder="$t('admin email')"
              />
              <label for="admin_email">{{ $t("admin email") }}</label>
              <div class="invalid-feedback">
                {{ errors.get("admin_email") }}
              </div>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-header">
            <a
              data-toggle="collapse"
              class="text-dark w-100 d-block"
              data-target="#localizationSettings"
              aria-expanded="true"
              aria-controls="localizationSettings"
            >
              <i class="bi bi-globe"></i> {{ $t("localization") }}
            </a>
          </div>
          <div class="card-body collapse show" id="localizationSettings">
            <div class="form-floating mb-2">
              <select
                name="default_lang"
                id="default_lang"
                v-bind:class="{ 'is-invalid': errors.has('default_lang') }"
                class="form-control"
                v-model="settings.default_lang"
                @change="save('default_lang', settings.default_lang)"
              >
                <option
                  v-for="(lang, idx) in languages"
                  v-bind:key="idx"
                  :value="idx"
                >
                  {{ lang }}
                </option>
              </select>
              <label for="default_lang">{{ $t("language") }}</label>
              <div class="invalid-feedback">
                {{ errors.get("default_lang") }}
              </div>
            </div>

            <div class="form-floating mb-2">
              <select
                name="timezone"
                id="timezone"
                v-bind:class="{ 'is-invalid': errors.has('timezone') }"
                class="form-control"
                v-model="settings.timezone"
                @change="save('timezone', settings.timezone)"
              >
                <option
                  v-for="(tz, idx) in timezones"
                  v-bind:key="idx"
                  :value="tz"
                >
                  {{ tz }}
                </option>
              </select>
              <label for="timezone">{{ $t("Timezone") }}</label>
            </div>

            <div class="form-floating mb-2">
              <select
                name="date_format"
                id="date_format"
                v-bind:class="{ 'is-invalid': errors.has('date_format') }"
                class="form-control"
                v-model="settings.date_format"
                @change="save('date_format', settings.date_format)"
              >
                <option value="m/d/Y">MM/DD/YYYY</option>
                <option value="m.d.y">MM.DD.YY</option>
                <option value="j, n, Y">DD, MM, YYYY</option>
                <option value="M j, Y">Dec DD, YYYY</option>
                <option value="D, M j, Y">Sat, Dec DD, YYYY</option>
              </select>
              <label for="date_format">{{ $t("Date format") }}</label>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-header">
            <a
              data-toggle="collapse"
              class="text-dark w-100 d-block"
              data-target="#mailSettings"
              aria-expanded="true"
              aria-controls="mailSettings"
            >
              <i class="bi bi-envelope-check"></i> {{ $t("Mail settings") }}
            </a>
          </div>

          <div class="card-body collapse show" id="mailSettings">
            <div class="row">
              <div class="col-sm-6 mb-2">
                <div class="form-floating">
                  <input
                    type="text"
                    class="form-control"
                    v-bind:class="{
                      'is-invalid': errors.has('mail_from_address'),
                    }"
                    id="mail_from_address"
                    name="mail_from_address"
                    v-model="settings.mail_from_address"
                    @blur="
                      save('mail_from_address', settings.mail_from_address)
                    "
                    :placeholder="$t('From Email')"
                  />
                  <label for="mail_from_address">{{ $t("From Email") }}</label>
                  <div class="invalid-feedback">
                    {{ errors.get("mail_from_address") }}
                  </div>
                </div>
              </div>
              <div class="col-sm-6 mb-2">
                <div class="form-floating">
                  <input
                    type="text"
                    class="form-control"
                    v-bind:class="{
                      'is-invalid': errors.has('mail_from_name'),
                    }"
                    id="mail_from_name"
                    name="mail_from_name"
                    v-model="settings.mail_from_name"
                    @blur="save('mail_from_name', settings.mail_from_name)"
                    :placeholder="$t('From name')"
                  />
                  <label for="mail_from_name">{{ $t("From name") }}</label>
                  <div class="invalid-feedback">
                    {{ errors.get("mail_from_name") }}
                  </div>
                </div>
              </div>
              <div class="col-12 mb-2">
                <div class="form-floating">
                  <select
                    name="mail_channel"
                    id="mail_channel"
                    v-bind:class="{ 'is-invalid': errors.has('mail_channel') }"
                    class="form-control w-100 px-2"
                    v-model="settings.mail_channel"
                    @change="save('mail_channel', settings.mail_channel)"
                  >
                    <option selected="selected" value="sendmail">
                      PHP mail
                    </option>
                    <option value="smtp">SMTP</option>
                  </select>
                  <label for="mail_from_name">{{ $t("Mail channel") }}</label>
                </div>
              </div>

              <template v-if="settings.mail_channel == 'smtp'">
                <div class="col-sm-6 mb-2">
                  <div class="form-floating">
                    <input
                      type="text"
                      class="form-control"
                      v-bind:class="{ 'is-invalid': errors.has('smtp_host') }"
                      id="smtp_host"
                      name="smtp_host"
                      v-model="settings.smtp_host"
                      @blur="save('smtp_host', settings.smtp_host)"
                      :placeholder="$t('SMTP host')"
                    />
                    <label for="smtp_host">{{ $t("SMTP host") }}</label>
                    <div class="invalid-feedback">
                      {{ errors.get("smtp_host") }}
                    </div>
                  </div>
                </div>

                <div class="col-sm-6 mb-2">
                  <div class="form-floating">
                    <input
                      type="text"
                      class="form-control"
                      v-bind:class="{
                        'is-invalid': errors.has('smtp_username'),
                      }"
                      id="smtp_username"
                      name="smtp_username"
                      v-model="settings.smtp_username"
                      @blur="save('smtp_username', settings.smtp_username)"
                      :placeholder="$t('SMTP username')"
                    />
                    <label for="smtp_username">{{ $t("SMTP username") }}</label>
                    <div class="invalid-feedback">
                      {{ errors.get("smtp_username") }}
                    </div>
                  </div>
                </div>

                <div class="col-sm-6 mb-2">
                  <div class="form-floating">
                    <label for="smtp_password">{{ $t("SMTP password") }}</label>
                    <input
                      type="text"
                      class="form-control"
                      v-bind:class="{
                        'is-invalid': errors.has('smtp_password'),
                      }"
                      id="smtp_password"
                      name="smtp_password"
                      v-model="settings.smtp_password"
                      @blur="save('smtp_password', settings.smtp_password)"
                      :placeholder="$t('SMTP password')"
                    />
                    <div class="invalid-feedback">
                      {{ errors.get("smtp_password") }}
                    </div>
                  </div>
                </div>

                <div class="col-sm-6 mb-2">
                  <div class="form-floating">
                    <input
                      type="text"
                      class="form-control"
                      v-bind:class="{ 'is-invalid': errors.has('smtp_port') }"
                      id="smtp_port"
                      name="smtp_port"
                      v-model="settings.smtp_port"
                      @blur="save('smtp_port', settings.smtp_port)"
                      :placeholder="$t('SMTP port')"
                    />
                    <div class="invalid-feedback">
                      {{ errors.get("smtp_port") }}
                    </div>
                    <label for="smtp_port">{{ $t("SMTP port") }}</label>
                  </div>
                </div>
                <div class="col-sm-6 mb-2">
                  <div class="form-floating">
                    <input
                      type="text"
                      class="form-control"
                      v-bind:class="{
                        'is-invalid': errors.has('smtp_encryption'),
                      }"
                      id="smtp_encryption"
                      name="smtp_encryption"
                      v-model="settings.smtp_encryption"
                      @blur="save('smtp_encryption', settings.smtp_encryption)"
                      :placeholder="$t('SMTP encryption')"
                    />
                    <label for="smtp_encryption">{{
                      $t("SMTP encryption")
                    }}</label>
                    <div class="invalid-feedback">
                      {{ errors.get("smtp_encryption") }}
                    </div>
                  </div>
                </div>
              </template>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-header">
            <a
              data-toggle="collapse"
              class="text-dark w-100 d-block"
              data-target="#notificationsModule"
              aria-expanded="true"
              aria-controls="notificationsModule"
            >
              <i class="bi bi-envelope-check"></i> {{ $t("Notifications Module") }}
            </a>
          </div>

          <div class="card-body collapse show" id="notificationsModule">
            <div class="row">
              <div class="col-sm-4 mb-2">
                <div class="form-floating">
                  <input
                    type="text"
                    class="form-control"
                    id="hutch_username"
                    name="hutch_username"
                    v-model="settings.hutch_username"
                    @blur="
                      save('hutch_username', settings.hutch_username)
                    "
                    :placeholder="$t('Hutch Username')"
                  />
                  <label for="hutch_username">{{ $t("Hutch Username") }}</label>
                  
                </div>
              </div>
              <div class="col-sm-4 mb-2">
                <div class="form-floating">
                  <input
                    type="text"
                    class="form-control"
                    id="hutch_password"
                    name="hutch_password"
                    v-model="settings.hutch_password"
                    @blur="save('hutch_password', settings.hutch_password)"
                    :placeholder="$t('From name')"
                  />
                  <label for="hutch_password">{{ $t("Hutch Password") }}</label>
                 
                </div>
              </div>
              <div class="col-4 mb-2">
                <div class="form-floating">
                  <input
                    type="text"
                    class="form-control"
                    id="hutch_mask"
                    name="hutch_mask"
                    v-model="settings.hutch_mask"
                    @blur="save('hutch_mask', settings.hutch_mask)"
                    :placeholder="$t('From name')"
                  />
                  <label for="hutch_mask">{{ $t("Hutch Password") }}</label>
                  
              </div>
              </div>

            </div>

            <div class="row">
              <div class="col-sm-6 mb-2">
                <div class="form-floating">
                  <input
                    type="text"
                    class="form-control"
                    id="ultimate_token"
                    name="ultimate_token"
                    v-model="settings.ultimate_token"
                    @blur="save('ultimate_token', settings.ultimate_token)"
                    :placeholder="$t('From name')"
                  />
                  <label for="ultimate_token">{{ $t("Ultimate Token") }}</label>
                 
                </div>
              </div>
              <div class="col-6 mb-2">
                <div class="form-floating">
                  <input
                    type="text"
                    class="form-control"
                    id="ultimate_sender_id"
                    name="ultimate_sender_id"
                    v-model="settings.ultimate_sender_id"
                    @blur="save('ultimate_sender_id', settings.ultimate_sender_id)"
                    :placeholder="$t('From name')"
                  />
                  <label for="ultimate_sender_id">{{ $t("Ultimate Sender ID") }}</label>
                  
              </div>
              </div>

            </div>
          </div>
        </div>

        <ouath
          :settings="settings"
          v-on:save="save($event.key, $event.val)"
          :errors="errors"
        />

        <div class="card">
          <div class="card-header">
            <a
              data-toggle="collapse"
              class="text-dark w-100 d-block"
              data-target="#appCacheSettings"
              aria-expanded="true"
              aria-controls="appCacheSettings"
            >
              <i class="bi bi-mail-bulk"></i> {{ $t("System cache") }}
            </a>
          </div>
          <div class="card-body collapse show" id="appCacheSettings">
            <clearCache />
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import ouath from "./OAuth";
import clearCache from "./ClearCache";
import ErrorManager from "./../../../../common/components/ErrorManager";

export default {
  data() {
    return {
      isLoading: false,
      errors: new ErrorManager(),
      settings: {
        app_name: "",
        user_can_register: 0,
        verify_email: 0,
        app_logo: "",
        date_format: "",
        default_lang: "",
        favicon: "",
        from_email: "",
        from_name: "",
        mail_channel: "",
        mail_from_address: "",
        mail_from_name: "",
        site_description: "",
        smtp_encryption: "",
        smtp_host: "",
        smtp_password: "",
        smtp_port: "",
        smtp_username: "",
        time_format: "",
        timezone: "",
        hutch_mask: "",
        hutch_password: "",
        hutch_username: "",
        ultimate_sender_id: "",
        ultimate_token: "",
        envato_oauth_enabled: 0,
        envato_oauth_app_id: "",
        envato_oauth_app_secret: "",

        facebook_oauth_enabled: 0,
        facebook_oauth_app_id: "",
        facebook_oauth_app_secret: "",

        google_oauth_enabled: 0,
        google_oauth_app_id: "",
        google_oauth_app_secret: "",
      },
      timezones: [],
      languages: [],
    };
  },
  mounted() {
    this.loadSettings();
  },
  components: {
    ouath,
    clearCache
  },
  methods: {
    loadSettings() {
      this.isLoading = true;
      axios
        .post(this.$myaccount_url + "settings/fetch")
        .then((response) => {
          this.settings = response.data.settings;
          this.timezones = response.data.timezones;
          this.languages = response.data.languages;
          this.isLoading = false;
        })
        .catch((e) => {
          this.$showError(e);
        });
    },
    save(key, val) {
      console.log("hew");
      this.settings[key] = val;
      this.$toastClear();
      axios
        .post(this.$myaccount_url + "settings/save", { key, val })
        .then((response) => {
          if (response.data.status == "ok") {
            this.$showSuccess(response.data.message);
          } else {
            if (response.data.errors) {
              this.$showError(this.$t("Some settings are empty or invalid"));
            }
            this.errors.record(response.data.errors);
          }
        })
        .catch((e) => {
          this.$showError(e);
        });
    },
    doUpload(updatefile) {
      var field = updatefile.target;
      var formData = new FormData();

      formData.append("file", field.files[0]);
      formData.append("type", "setting");

      const config = {
        headers: { "Content-Type": "multipart/form-data" },
        onUploadProgress: (progressEvent) => {
          var percentCompleted = Math.round(
            (progressEvent.loaded * 100) / progressEvent.total
          );
          this.uploadProgress = percentCompleted;
        },
      };

      axios
        .post(this.$myaccount_url + "upload", formData, config)
        .then((response) => {
          if (!response) {
            this.$showError(this.$t("An error occured please try again"));
            return;
          }

          if (response.data.status == "ok") {
            // update field
            if (field.getAttribute("data-item") == "favicon") {
              this.save("favicon", response.data.url);
            }

            if (field.getAttribute("data-item") == "app_logo") {
              this.save("app_logo", response.data.url);
            }
          } else {
            this.updateFailed = true;
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
  },
};
</script>
