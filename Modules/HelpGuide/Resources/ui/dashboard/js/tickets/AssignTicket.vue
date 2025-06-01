<template>
  <div class="card">
    <div class="card-header p-2">
      <div class="w-100">
        {{ $t("Re-assign Ticket") }}
        <button type="button" @click="$emit('close')" class="float-end">
          <i class="bi bi-x-lg"></i>
        </button>
      </div>
    </div>

    <div class="card-body" v-if="isLoading">
      <pre-loader />
    </div>

    <div class="card-body p-3" v-else>
      <div class="list-group re-assign-users-list" v-if="users">

          <v-select
            label="name"
            v-model="selectAgent"
            :filterable="true"
            :options="users"
            :appendToBody="true"
            :placeholder="$t('Select agent')"
            class="agents-list"
          >
            <template #no-options="{}">
              {{ $t("Type to search agents") }}..
            </template>

            <template v-slot:option="option">
              <div class="d-center">
                <img :src="option.avatar" class="rounded-circle" width="32" height="32" />
                #{{ option.id }} {{ option.name }}
              </div>
            </template>

            <template #selected-option="{ id, name, avatar }">
              <div>
                <img
                  :src="avatar"
                  class="rounded-circle align-middle"
                  width="32" height="32"
                />
                <strong>#{{ id }} {{ name }}</strong>
              </div>
            </template>
          </v-select>

      </div>
    </div>

    <div class="card-footer text-right">
      <save-btn
          text="Assign"
          :inAction="isSaving"
          v-on:click.native="reAssign()"
          class="btn btn-sm btn-primary"
        />
    </div>

  </div>
</template>

<script>
import axios from "axios";
export default {
  data() {
    return {
      isLoading: false,
      users: [],
      selectAgent: null,
      isSaving: false
    };
  },
  props: ["ticket", "handlers", "current-agent"],
  beforeMount() {
    this.listEmployee();
  },
  methods: {
    listEmployee() {
      this.isLoading = true;
      axios.get(this.$admin_api + "employees/list").then((response) => {
        var newUsers = response.data.data;
        this.users = this.users.concat(newUsers);

        let currentAgent = this.users.find(u => u.id == this.currentAgent)
        if( currentAgent )  this.selectAgent = currentAgent

      })
      .catch((e) => {
        this.$showError(e);
      })
      .finally(() => {
        this.isLoading = false;
      })
    },
    reAssign(){

        if( this.selectAgent.id == this.currentAgent ){
          this.$showInfo(this.$t('Re-assign canceled'))
          this.$emit('close')
          return false;
        }

        axios.post(this.$admin_api + "tickets/re_assign/" + this.ticket, {
          'assign_to': this.selectAgent.id
        })
        .then((r) => {
            this.$showSuccess(r.data.message)
            this.handlers.update(r.data.data)
            this.$emit('close')
        })
        .catch((e) => {
            this.$showError(e);
        })
        .finally((e) => {
          this.isLoading = false;
        })
    }
  },
};
</script>

<style>
.re-assign-users-list .form-check {
    padding: 0;
}

.re-assign-users-list li {
    padding: 0;
}

.re-assign-users-list .form-check-input {
    right: 10px;
    top: 10px;
}

.re-assign-users-list .form-check-label {
    margin-bottom: 0;
    padding: 5px;
    width: 100%;
}
.card .card-footer {
    display: block;
}

</style>
