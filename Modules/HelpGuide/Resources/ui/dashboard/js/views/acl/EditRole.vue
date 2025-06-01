<template>
  <div class="card">
    <div class="card-body">
      <pre-loader v-if="isLoading" />
      <empty-state v-else-if="emptyResult" />
      <div v-else>
        <router-link class="btn btn-sm btn-light rounded" :to="{ name: 'settings.roles_and_permissions'}">
            <i class="bi bi-box-arrow-left"></i>
        </router-link>
        <span class="h3 text-capitalize">{{ role.name.replaceAll("_", " ") }}</span>
        <div class="row my-2 text-capitalize">

            <div
              class="card mx-2 card-body p-2 border shadow-none col-auto"
              v-for="(permissionList, index) in permissions"
              v-bind:key="index"
            >
              <p class="p-2 m-0 h4">{{ index.replaceAll("_", " ") }}</p>

              <div
                class="form-check form-switch my-2"
                v-for="(permission, index) in permissionList"
                v-bind:key="index"
              >
                <input
                  type="checkbox"
                  :checked="hasPermission(permission)"
                  class="form-check-input"
                  :true-value="true"
                  :false-value="false"
                  @change="togglePermission($event.target.checked, permission)"
                  :id="'p-switch-' + permission"
                />
                <label
                  class="form-check-label"
                  :for="'p-switch-' + permission"
                  >{{ permission.replaceAll("_", " ") }}</label
                >
              </div>
            </div>

            <div
              class="card mx-2 card-body p-2 border shadow-none col-auto"
              v-for="(mp, index) in modulesPermissions"
              v-bind:key="index"
            >
              <p class="p-2 m-0 h4">
                {{ $t("Module") + " : " + index.replaceAll("_", " ") }}
              </p>
              <div
                class="form-check mx-2 my-1"
                v-for="(p, index) in mp"
                v-bind:key="index"
              >
                <input
                  type="checkbox"
                  :checked="hasPermission(p)"
                  class="form-check-input"
                  :true-value="true"
                  :false-value="false"
                  @change="togglePermission($event.target.checked, p)"
                  :id="'p-switch-' + p"
                />
                <label class="form-check-label" :for="'p-switch-' + p">
                  {{ p.replaceAll("_", " ") }}
                </label>
              </div>
            </div>

        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Axios from "axios";
export default {
  data() {
    return {
      isLoading: true,
      role: null,
      permissions: [],
      rolePermissions: [],
      modulesPermissions: [],
      emptyResult: false,
    };
  },
  props: ["id"],
  mounted() {
    this.loadRolePermissions();
  },
  methods: {
    loadRolePermissions() {
      this.isLoading = true;
      Axios.post(this.$myaccount_url + "acl/roles/" + this.id + "/permissions")
        .then((res) => {
          this.permissions = res.data.permissions;
          this.rolePermissions = res.data.role_permissions;
          this.modulesPermissions = res.data.modules_permissions;
          this.role = res.data.role;
        })
        .catch((e) => {
          this.emptyResult = true;
          this.$showError(e);
        })
        .then((r) => {
          this.isLoading = false;
        });
    },
    hasPermission(p) {
      var isExists = this.rolePermissions.filter(function (key) {
        return key.name == p;
      });

      if (isExists.length) {
        return true;
      }
      return false;
    },
    togglePermission(action, p) {
      Axios.post(
        this.$myaccount_url +
          "acl/roles/" +
          this.id +
          "/toggle_permission/" +
          p,
        { action: action ? "assign" : "revoke" }
      )
        .then((res) => {
          this.$showSuccess(res.data.message);
          this.permissions = res.data.permissions;
          this.rolePermissions = res.data.role_permissions;
          this.modulesPermissions = res.data.modules_permissions;
          this.role = res.data.role;
        })
        .catch((e) => {
          this.$showError(e);
        });
    },
  },
};
</script>
