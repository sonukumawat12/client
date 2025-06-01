<template>
  <div class="card card-body">
    <pre-loader v-if="isLoading" />
    <div class="row" v-else>
      <div class="col-12 mb-3">
        <div class="form-floating">
          <select
            class="form-select"
            id="floatingSelectGrid"
            aria-label="Floating label select example"
            v-model="form.ticket_auto_close"
          >
            <option value="0">{{ $t("Disabled") }}</option>
            <option value="7">{{ $t(":days days", { days: 7 }) }}</option>
            <option value="15">{{ $t(":days days", { days: 15 }) }}</option>
            <option value="30">{{ $t(":days days", { days: 30 }) }}</option>
          </select>
          <label for="floatingSelectGrid">{{
            $t("Days to auto close ticket")
          }}</label>
        </div>
      </div>

      <div class="col-12 mb-3">
        <div class="form-floating">
          <v-select
            label="name"
            v-model="form.ticket_default_agent"
            :filterable="false"
            :options="agents"
            @search="onSearch"
            :placeholder="$t('Principal agent')"
            class="agents-list"
          >
            <template #no-options="{}">
              {{ $t("Type to search agents") }}..
            </template>

            <template v-slot:option="option">
              <div class="d-center">
                <img :src="option.avatar" class="rounded-circle" width="24" />
                #{{ option.id }} {{ option.name }}
              </div>
            </template>

            <template #selected-option="{ id, name, avatar }">
              <div>
                <img
                  :src="avatar"
                  class="rounded-circle align-middle"
                  width="24"
                />
                <strong>#{{ id }} {{ name }}</strong>
              </div>
            </template>
          </v-select>
        </div>
        <small>
          {{$t('Tickets by default will be assigned to an agent with lowest open ticket, you can choose to assign all new ticket to a specific agent')}}
        </small>
      </div>

      <div class="col-12">
        <save-btn
          text="Save"
          :inAction="isSaving"
          v-on:click.native="save()"
          class="btn btn-sm btn-outline-primary"
        />
      </div>
    </div>
  </div>
</template>

<script>
import vSelect from "vue-select";
import "vue-select/dist/vue-select.css";
export default {
  data() {
    return {
      agents: [],
      isLoading: true,
      form: {
        ticket_default_agent: null,
        ticket_auto_close: 0,
      },
      isSaving: false,
    };
  },
  components: {
    vSelect,
  },
  mounted(){
    this.loadSettings();
  },
  methods: {
    onSearch(search, loading) {
      if (search.length > 1) {
        loading(true);
        this.search(loading, search, this);
      }
    },
    search: _.debounce((loading, search, vm) => {
      axios
        .get(vm.$admin_api + `search?q=${search}&type=employees`)
        .then((r) => {
          vm.agents = r.data.results.employees;
          loading(false);
        });
    }, 350),
    save() {

      let form = {}

      if( this.form.ticket_default_agent ){
        form['ticket_default_agent'] = this.form.ticket_default_agent.id
        this.form.ticket_auto_assign = false
      } else {
        form['ticket_default_agent'] = null
        this.form.ticket_auto_assign = true
      }

      form['ticket_auto_assign'] = this.form.ticket_auto_assign
      form['ticket_auto_close'] = this.form.ticket_auto_close

      axios
        .post(this.$admin_api + "settings", form)
        .then((response) => {
          this.$showSuccess(response.data.message);
        })
        .catch((e) => {
          this.$showError(e);
        });
    },
    loadDefaultAgent(id) {
      axios
      .get(this.$admin_api + `employees/${id}`)
      .then((r) => {
        this.form.ticket_default_agent = r.data.data
      });
    },
    loadSettings() {
      this.isLoading = true;
      axios.post(this.$myaccount_url + "settings/fetch").then((response) => {
        this.form = response.data.settings;
        if( this.form.ticket_default_agent ) {
          this.loadDefaultAgent( this.form.ticket_default_agent )
        }
      })
      .catch((e) => {
        this.$showError( e )
      })
      .finally(() => { this.isLoading = false })
    },
  },
};
</script>