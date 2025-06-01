<template>
  <div class="data-table">
    <table class="table table-striped">
      <thead>
      <tr>
        <th class="table-head">
          <div class="form-check">
            <input type="checkbox" id="customCheckAll" v-on:change="event => checkAll(event)" v-indeterminate='indeterminateState' class="form-check-input" v-model="checkAllState">
            <label class="form-check-label" for="customCheckAll">&nbsp;</label>
          </div></th>
        <th v-for="column in columns" :key="column"
            class="table-head">
          {{ column | columnHead }}
        </th>
      </tr>
      </thead>
      <tbody>
      <tr class="" v-if="tableData.length === 0">
        <td class="lead text-center" :colspan="columns.length + 1">{{$t('No data found.')}}</td>
      </tr>
      <tr v-for="(data, key1) in tableData" :key="data.id" class="m-datatable__row" v-else>
        <td>
          <div class="form-check">
            <input :id="serialNumber(key1)" type="checkbox" v-on:change="event => check(event)" class="form-check-input" :checked="checkAllState">
            <label :for="serialNumber(key1)" class="form-check-label">&nbsp;</label>
          </div>
          </td>
        <td v-for="(value, key) in data" v-bind:key="key">{{ value }}</td>
        <td><button class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i></button></td>
      </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
export default {
  props: {
    columns: { type: Array, required: true },
    tableData: {},
  },
  data() {
    return {
      checkAllState: false,
      indeterminateState: false,
    }
  },
  methods: {
    serialNumber(key) {
      return key + 1;
    },
    check(event) {
     if(!event.target.checked){
       this.indeterminateState = true;
     }
    },
    checkAll(event) { this.indeterminateState = false;}
  },
  filters: {
    columnHead(value) {
      return value.split('_').join(' ').toUpperCase()
    }
  },
  name: 'DataTable',
  directives: {
    indeterminate: function(el, binding) {
      el.indeterminate = Boolean(binding.value)
    }
  }
}
</script>