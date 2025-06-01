<template>
  <div>
    <div class="col-lg-12 text-center p-2">{{$t('was_this_article_helpful')}}</div>
    <div class="col-auto text-center">
      <button
        :disabled="btn_yes"
        v-on:click="update('yes')"
        type="button"
        class="btn btn-outline-success btn-sm m-2"
        data-toggle="button"
        aria-pressed="false"
        autocomplete="off"
      >
        <i class="bi bi-check-lg"></i> {{$t('Yes')}}
      </button>
      <button
        :disabled="btn_no"
        v-on:click="update('no')"
        type="button"
        class="btn btn-outline-danger btn-sm m-2"
        data-toggle="button"
        aria-pressed="false"
        autocomplete="off"
      >
        <i class="bi bi-x-lg"></i> {{$t('No')}}
      </button>
    </div>

    <div class="col-lg-12 text-center p-2" v-if="rate_total > 0">
      {{ $t('out_of_found_this_helpful', {helpful: rate_helpful, total: rate_total}) }}
    </div>
  </div>
</template>

<script>
import axios from "axios";
export default {
  data() {
    return {
      rate_total: 0,
      rate_helpful: 0,
      btn_yes: false,
      btn_no: false
      
    }
  },
  props: ['total', 'rate', 'article_id'],
  mounted() {
    this.rate_total = parseInt(this.total)
    this.rate_helpful = parseInt(this.rate)
  },
  methods: {
    update(rate){
        this.btn_yes = true
        this.btn_no = true

        if (rate=='yes') this.btn_no = false
        else this.btn_yes = false

        axios
        .post(this.$base_url+'articles/rate',{
            rate: rate,
            article_id: this.article_id
        })
        .then(response => {
            this.rate_total = response.data.data.rate_total
            this.rate_helpful = response.data.data.rate_helpful
        })
    }
    }
}
</script>