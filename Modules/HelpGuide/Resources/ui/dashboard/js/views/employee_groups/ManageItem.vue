<template>
  <div class="d-flex justify-content-between align-items-center w-100">
    <div>
      <categoryItemLabel
        :letter="category.name.substring(0, 1)"
        :color="stringToColor(category.name)"
      />
      
      <strong class="text-gray-dark">{{ category.name }}</strong>
    </div>

    <div class="float-end">
      <div class="dropdown d-inline">
        <button
          class="btn btn-light btn-sm rounded-circle"
          type="button"
          id="dropdownMenuButton"
          data-bs-toggle="dropdown"
          aria-haspopup="true"
          aria-expanded="false"
        >
          <i class="bi bi-three-dots-vertical"></i>
        </button>
        <div
          class="dropdown-menu dropdown-menu-right"
          aria-labelledby="dropdownMenuButton"
        >
         
          <button
            class="dropdown-item"
            type="button"
            data-toggle="tooltip"
            data-placement="top"
            :title="$t('Edit')"
            @click="$emit('edit', category.id)"
          >
            <span><i class="bi bi-pencil"></i> {{ $t("Edit") }}</span>
          </button>
          <button
            class="dropdown-item text-danger"
            type="button"
            data-toggle="tooltip"
            data-placement="top"
            :title="$t('Delete')"
            @click="$emit('delete', category.id)"
          >
            <span
              ><i class="bi bi-trash"></i> {{ $t("Delete") }}</span
            >
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import categoryItemLabel from "./ItemLabel";
export default {
  props: {
    category: {
      default: {},
    },
  },
  components: {
    categoryItemLabel,
  },
};
</script>