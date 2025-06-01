<template>
<div>
    <transition name="modal">
      <div class="modal-mask">
        <div class="modal-wrapper">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-body">
                  <slot name="body"></slot>
              </div>
              <div class="modal-footer p-2">
                <button type="button" class="btn btn-secondary" v-on:click="close()" @click="close">{{$t('Close')}}</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<script>
export default {
    data(){
        return {
          isModalVisible: false
        }
    },
    methods: {
      close() {
        this.isModalVisible = false;
        this.$emit('action', 'cancel')
      }
    }
    
}
</script>