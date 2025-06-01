<template>
  <div class="card flex-fill border" v-if="props.module">
    <div class="card-body p-0">
      <h5
        class="card-title p-2"
        :style="'background-image: url(' + props.module.thumbnail + ')'"
      >
        <span class="text-capitalize">{{ props.module.name }}</span>
        <small>v{{ props.module.version }}</small>
        <div
          class="border border-success p-1 rounded-circle float-end"
          v-if="props.module.is_enabled"
        >
          <i class="bi bi-check text-success"></i>
        </div>
      </h5>

      <div class="px-2">
        <div class="mb-1 rounded small p-2 bg-light">
          {{ props.module.description }}
        </div>

        <div>
          {{ $t("Author") }} :
          <a :href="props.module.author_url" target="_blank">{{
            props.module.author
          }}</a>
        </div>

        <div>
          {{ $t("Price") }} :
          <span v-if="props.module.price == 0">{{ $t("Free") }}</span>
          <span v-else>
            <span
              v-if="
                props.module.new_price && props.module.new_price < props.module.price
              "
            >
              <s class="text-danger">{{ props.module.price }} USD</s>
              <span class="border border-success rounded text-success px-1">
                <span v-if="props.module.new_price == 0">{{ $t("Free") }}</span>
                <span v-else>{{ props.module.new_price }} USD</span>
              </span>
            </span>
            <span v-else>
              <span class="border border-success rounded text-success px-1"
                >{{ $module["price"] }} USD</span
              >
            </span>
          </span>
        </div>
      </div>
    </div>

    <div class="card-footer">
      <span v-if="props.module.is_installed">
        <save-btn
          :text="'Disable'"
          :inActionText="'Disabling'"
          :inAction="props.module.is_changing"
          v-if="props.module.is_enabled"
          v-on:click.native="moduleStore.toggleModuleStatus( props.module )"
          class="btn btn-sm btn-outline-secondary"
        />
        <save-btn
          v-else
          :text="'Enable'"
          :inActionText="'Enabling'"
          :inAction="props.module.is_changing"
          v-on:click.native="moduleStore.toggleModuleStatus( props.module )"
          class="btn btn-sm btn-outline-primary"
        />

        <a
          class="btn btn-link text-warning btn-sm float-end mx-2"
          :href="props.module.download_link"
          target="_blank"
          v-if="props.module.has_update">
          {{ $t("New update") }}
        </a>

      </span>

      <span v-else>
        <save-btn
          class="btn btn-outline-success btn-sm"
          @click.native="moduleStore.install(props.module)"
          :in-action="props.module.is_installing"
          in-action-text="Installing"
          text="Install">
        </save-btn>
      </span>
      
    </div>
  </div>
</template>

<script setup>
import { useModuleStore } from './../../stores/ModuleStore'
const moduleStore = useModuleStore()
const props = defineProps(['module'])
</script>