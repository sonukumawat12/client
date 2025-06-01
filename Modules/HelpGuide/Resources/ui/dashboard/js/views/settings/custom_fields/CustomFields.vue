<template>
  <div class="card card-body p-0">
    <nav>
      <div class="nav nav-tabs" id="nav-tab" role="tablist">
        <button
          class="nav-link active"
          id="nav-ticket-tab"
          data-bs-toggle="tab"
          data-bs-target="#nav-ticket"
          type="button"
          role="tab"
          aria-controls="nav-ticket"
          aria-selected="true"
        >
          {{ $t("Ticket") }}
        </button>
      </div>
    </nav>
    <div class="tab-content p-2" id="nav-tabContent">
      <div
        class="tab-pane fade show active"
        id="nav-ticket"
        role="tabpanel"
        aria-labelledby="nav-ticket-tab"
      >
        <div class="accordion" id="ticket-custom-fields">
          <draggable v-model="fields" group="fields" handle=".drapHandle">
            <transition-group>
              <div class="card border" v-for="field in fields" :key="field.id">
                <div
                  class="card-header drapHandle"
                  :id="'heading-ticket-' + field.id"
                >
                  <button
                    class="btn btn-link btn-block text-left"
                    type="button"
                    data-toggle="collapse"
                    :data-target="'#collapse-' + field.id"
                    aria-expanded="true"
                    :aria-controls="'collapse-' + field.id"
                  >
                    <i class="bi me-2 bi-bars"></i> {{ field.name }}
                  </button>
                </div>
                <div
                  :id="'collapse-' + field.id"
                  class="collapse"
                  :aria-labelledby="'heading-ticket-' + field.id"
                  data-parent="#ticket-custom-fields"
                >
                  <div class="card-body">
                    <Field :field='field' @addrule="addRule($event)" />
                  </div>
                </div>
              </div>
            </transition-group>
          </draggable>
          <button
            slot="footer"
            class="btn btn-outline-primary mt-2 float-end clearfix"
            @click="addField"
          >
            <i class="bi bi-plus"></i> {{ $t("Add new field") }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import draggable from "vuedraggable";
import axios from "axios";
import Field from "./Field.vue";

export default {
  data() {
    return {
      isLoading: false,
      fields: [
        {
          id: 1,
          name: "test",
          rules: [
              {
                  name: 'required',
                  value: true,
                  message: 'This field is required'
              }
          ],
        },
      ],
      editing: 1,
    };
  },
  components: {
    draggable,
    Field,
  },
  mounted() {
    //this.loadFields()
  },
  methods: {
    loadFields() {
      this.isLoading = true;
      axios
        .post(this.$myaccount_url + "settings/clear_cache")
        .then((response) => {
          if (response.data.status == "ok")
            this.$showSuccess(response.data.message);
          this.isLoading = false;
        });
    },
    updateFields() {},
    updateFieldOrder() {},
    addField() {
      this.fields.push({ id: 0, name: "new field" });
    },
    deleteField() {},
    addRule(field){
        const currentIndex = this.fields.findIndex(u => u.id === field.id);

        this.fields[currentIndex].rules.push( {
                  name: 'required',
                  value: true,
                  message: 'This field is required'
              })
    }
  },
  computed: {},
};
</script>

<style lang="scss">
#nav-ticket #ticket-custom-fields {
  .card.sortable-chosen {
    border: 2px solid #555;
    opacity: 0.5;
  }
}
</style>
