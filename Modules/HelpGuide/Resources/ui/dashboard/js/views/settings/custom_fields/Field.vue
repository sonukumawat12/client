<template>
  <div class="row">
    <div class="form-group col-md-6">
      <label for="fieldName">{{ $t("Field name") }}</label>
      <input type="text" class="form-control" id="fieldName" />
    </div>

    <div class="form-group col-md-6">
      <label for="fieldType">{{ $t("Field Type") }}</label>
      <select class="form-control" id="fieldType">
        <option value="text">text</option>
        <option value="text">checkbox</option>
        <option value="text">select</option>
      </select>
    </div>

    <div class="col-md-12">
      <fieldset class="form-group">
        <legend class="col-form-label">{{ $t("Field rules") }}</legend>
        <div>
          <FieldRule v-for="(rule, index) in field.rules" v-bind:key="index" :rule="rule" />
          <button class="btn btn-outline-secondary my-2" @click="$emit('addrule', field)">
            {{ $t("Add rule") }}
          </button>
        </div>
      </fieldset>
    </div>
  </div>
</template>

<script>
import FieldRule from "./FieldRule.vue";

export default {
  data() {
    return {};
  },
  props: {
    field: {
        type: Object
    }
  },
  components: {
    FieldRule,
  },
};
</script>
