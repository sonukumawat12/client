<template>
  <div class="bg-light border p-3 my-2 rounded">

    <div class="form-group">
      <label for="ruleName">{{ $t("Rule") }}</label>
      <select class="form-control" id="ruleName" v-model="rule.selectRule">
        <option disabled selected>{{ $t('Select rule') }}</option>
        <option :value="index" v-for="(r, index) in rules" v-bind:key="index">
            {{ r.label }}
        </option>
      </select>
    </div>

    <div class="form-group" v-if="isMulti(selectRule)">
      <label for="ruleMssage">{{ $t('Rule message') }}</label>
      <input
        v-model="rule.message"
        type="text"
        class="form-control"
        id="ruleMssage"
        aria-describedby="ruleMEssageHelp"
      />
      <small id="ruleMEssageHelp" class="form-text text-muted">{{('Leave empty to display default rule message')}}</small>
    </div>

    <div class="form-group">
      <label for="ruleMssage">{{ $t('Rule message') }}</label>
      <input
        v-model="rule.message"
        type="text"
        class="form-control"
        id="ruleMssage"
        aria-describedby="ruleMEssageHelp"
      />
      <small id="ruleMEssageHelp" class="form-text text-muted">{{('Leave empty to display default rule message')}}</small>
    </div>

  </div>
</template>
<script>  
export default {
    data(){
        return {
            rules:{
                "accepted":         { label: 'Accepted', type: 'single' },
                "after":            { label: 'After (Date)', type: 'multi' },
                "after_or_equal":   { label: 'After Or Equal (Date)', type: 'single' },
                "alpha":            { label: 'Alpha', type: 'single' },
                "alpha_dash":       { label: 'Alpha Dash', type: 'single' },
                "alpha_num":        { label: 'Alpha Numeric', type: 'single' },
                "before":           { label: 'Before (Date)', type: 'single' },
                "before_or_equal":  { label: 'Before Or Equal (Date)', type: 'single' },
                "between":          { label: 'Between', type: 'single' },
                "boolean":          { label: 'Boolean', type: 'single' },
                "confirmed":        { label: 'Confirmed', type: 'single' },
                "date":             { label: 'Date', type: 'single' },
                "date_equals:date": { label: 'Date Equals', type: 'single' },
                "date_format":      { label: 'Date Format', type: 'single' },
                "different":        { label: 'Different', type: 'single' },
                "digits":           { label: 'Digits', type: 'single' },
                "digits_between":   { label: 'Digits Between', type: 'single' },
                "email":            { label: 'Email', type: 'single' },
                "ends_with":        { label: 'Ends With', type: 'single' },
                "exclude_if":       { label: 'Exclude If', type: 'single' },
                "exclude_unless":   { label: 'Exclude Unless', type: 'single' },
                "exists":           { label: 'Exists (Database)', type: 'single' },
                "filled":           { label: 'Filled', type: 'single' },
                "gt":               { label: 'Greater Than', type: 'single' },
                "gte":              { label: 'Greater Than Or Equal', type: 'single' },
                "in":               { label: 'In', type: 'single' },
                "integer":          { label: 'Integer', type: 'single' },
                "lt":               { label: 'Less Than', type: 'single' },
                "lte":              { label: 'Less Than Or Equal', type: 'single' },
                "max":              { label: 'Max', type: 'single' },
                "min":              { label: 'Min', type: 'single' },
                "not_in":           { label: 'Not In', type: 'single' },
                "not_regex":        { label: 'Not Regex', type: 'single' },
                "nullable":         { label: 'Nullable', type: 'single' },
                "numeric":          { label: 'Numeric', type: 'single' },
                "password":         { label: 'Password', type: 'single' },
                "present":          { label: 'Present', type: 'single' },
                "regex":            { label: 'Regular Expression', type: 'single' },
                "required":         { label: 'Required', type: 'single' },
                "required_if":      { label: 'Required If', type: 'single' },
                "required_unless":  { label: 'Required Unless', type: 'single' },
                "required_with":    { label: 'Required With', type: 'single' },
                "required_with_all":{ label: 'Required With All', type: 'single' },
                "required_without": { label: 'Required Without', type: 'single' },
                "required_without_all": { label: 'Required Without All', type: 'single' },
                "same":             { label: 'Same', type: 'single' },
                "sometimes":        { label: 'Sometimes', type: 'single' },
                "starts_with":      { label: 'Starts With', type: 'single' },
                "string":           { label: 'String', type: 'single' },
                "unique":           { label: 'Unique (Database)', type: 'single' },
                "url":              { label: 'URL', type: 'single' },
                "uuid":             { label: 'UUID', type: 'single' }
            },
            selectRule: null
        }
    },
    props: {
        rule: {
            type: Object
        }
    },
    methods: {
        isMulti(rule){
            
            return this.rules[rule] && this.rules[rule].type == 'multi'
        }
    }

}
</script>