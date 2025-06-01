<template>
  <div v-show="!loaderDisable" class="m-2">
    <slot>
     <pre-loader />
    </slot>
  </div>
</template>

<script>
import 'intersection-observer'
export default {
  props: {
    'loader-method': {
      type: Function,
      required: true
    },
    'loader-disable': {
      type: Boolean,
      default: false
    },
    'loader-distance': {
      type: Number,
      default: 0
    },
    'loader-color': {
      type: String,
      default: '#CCCCCC'
    },
    'loader-size': {
      type: Number,
      default: 50
    },
    'loader-viewport': {
      type: Element,
      default: null
    }
  },
  computed: {
    size () {
      return {
        width: `${this.loaderSize}px`
      }
    },
    color () {
      return {
        stroke: this.loaderColor
      }
    },
    options () {
      return {
        root: this.loaderViewport,
        rootMargin: `0px 0px ${this.loaderDistance}px 0px`
      }
    },
    observer () {
      return new IntersectionObserver(([{ isIntersecting }]) => {
        isIntersecting && !this.loaderDisable && this.loaderMethod()
      }, this.options)
    }
  },
  mounted () {
    this.observer.observe(this.$el)
  },
  activated () {
    this.observer.observe(this.$el)
  },
  deactivated () {
    this.observer.unobserve(this.$el)
  },
  beforeDestroy () {
    this.observer.unobserve(this.$el)
  }
}
</script>
