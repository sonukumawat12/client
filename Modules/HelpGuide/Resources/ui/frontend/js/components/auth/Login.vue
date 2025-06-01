<template>
  <div>
    <div
      class="alert alert-success d-flex align-items-center p-2"
      role="alert"
      v-if="loggedIn"
    >
      <div><i class="bi bi-check2 fs-5"></i> {{ $t("Login successful, redirecting") }}...</div>
    </div>

    <div
      class="alert alert-danger d-flex align-items-center p-2"
      role="alert"
      v-if="loginFailed"
    >
      <div><i class="bi bi-x-lg fs-5"></i> {{ "The login is invalid" }}</div>
    </div>

    <form action="#" @submit.prevent="login">
      <div class="form-floating mb-3">
        <input
          type="text"
          class="form-control"
          id="loginUsername"
          required
          autofocus
          v-model="formData.username"
          v-bind:class="{ 'is-invalid': errors.has('username') }"
          :placeholder="$t('E-Mail Address')"
        />
        <label for="loginUsername">{{ $t("E-Mail Address") }}</label>
        <div class="invalid-feedback">{{ errors.get("username") }}</div>
      </div>

      <div class="clearfix">
        <a :href="$base_url + 'password/reset'" class="float-end small mb-1">{{
          $t("Forgot your password")
        }}</a>
      </div>

      <div class="form-floating mb-3">
        <input
          v-bind:type="[passwordIsShow ? 'text' : 'password']"
          class="form-control"
          id="loginPassword"
          placeholder="Password"
          name="password"
          minlength="8"
          maxlength="32"
          required
          v-bind:class="{ 'is-invalid': errors.has('password') }"
          v-model="formData.password"
        />
        <label for="loginPassword">{{ $t("Password") }}</label>
        <i
          class="bi bi-eye showpassword"
          role="button"
          @click="passwordIsShow = !passwordIsShow"
        ></i>
        <div class="invalid-feedback">{{ errors.get("password") }}</div>
      </div>

      <div class="d-flex align-items-center">
        <div class="form-check">
          <input
            type="checkbox"
            v-model="formData.remember"
            name="remember"
            id="remember"
            class="form-check-input"
          />
          <label for="remember" class="form-check-label">{{
            $t("Remember Me")
          }}</label>
        </div>
        <save-button
          :text="$t('Login')"
          :in-action="isSaving"
          in-action-text="Authenticating"
          class="btn btn-primary ms-auto"
        />
      </div>
    </form>
  </div>
</template>

<script>
import SaveButton from "../../../../common/components/SaveButton.vue";
import Errors from "../../../../common/components/ErrorManager";
export default {
  data() {
    return {
      errors: new Errors(),
      isSaving: false,
      formData: {
        username: null,
        password: null,
        remember: null,
      },
      loggedIn: false,
      loginFailed: false,
      passwordIsShow: false,
    };
  },
  components: {
    SaveButton,
  },
  props: {
    canRegister: {},
  },
  methods: {
    login() {
      
      this.isSaving = true;
      this.loginFailed = false;

      axios
        .get(this.$base_url + "sanctum/csrf-cookie")
        .then(() => {

          axios
            .post(this.$base_url + "login", this.formData)
            .then(() => {
              this.loggedIn = true;
              window.location.replace(this.$mya_url);
            })
            .catch((e) => {
              if (e.response.data.errors) {
                this.errors.record(e.response.data.errors);
              }
              this.loginFailed = true;
            })
            .finally(() => {
              this.isSaving = false;
            });

        })
        .catch((e) => {
          this.loginFailed = true;
          this.isSaving = false;
        })
    },
  },
};
</script>


<style scoped>
.showpassword {
  position: absolute;
  top: calc(50% - 10px);
  height: 20px;
  width: 20px;
  right: 10px;
}
</style>
