<template>
  <button class="btn-save" :disabled='inAction'>
    <template v-if="inAction">
    <div class="dotsloader">
        {{ $t(inActionText) }}
        <i class="dot1">&bull;</i>
        <i class="dot2">&bull;</i>
        <i class="dot3">&bull;</i>
    </div>
    </template>
    <template v-else>{{ $t(text) }}</template>
  </button>
</template>

<script>
export default {
  props: {
    text: { default: 'save' },
    inActionText: { default: 'Saving' },
    inAction: { default: false }
  }
}
</script>

<style scoped>
.dotsloader .dot1{
  animation: load 1s infinite;
}

.dotsloader .dot2{
  animation: load 1s infinite;
  animation-delay: 0.2s;
}

.dotsloader .dot3{
  animation: load 1s infinite;
  animation-delay: 0.4s;
}

@keyframes load{
  0% { opacity: 0; }
  50% { opacity: 1; }
  100% { opacity: 0; }
}
</style>