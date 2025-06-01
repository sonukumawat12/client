<template>
  <span class="categoryLable m-0 p-0 me-2 rounded-circle" :style="'border:1px solid #'+color+'; background-color: #'+color">
        {{letter}}
    </span>  
</template>

<script>
export default {
    props: [
        "color",
        "letter"
    ]
}
</script>

<style lang="scss">
    .categoryLable {
        height: 32px;
        width: 32px;
        text-align: center;
        line-height: 32px;
        color: white;
        font-size: 18px;
        text-transform: uppercase;
    }
</style>