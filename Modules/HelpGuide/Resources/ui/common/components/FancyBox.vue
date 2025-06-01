<template>
    <a class="js-fancybox"><slot /></a>
</template>

<script>
import { Fancybox } from "@fancyapps/ui";
import "@fancyapps/ui/dist/fancybox.css";
export default {
    components: {
        Fancybox
    }
}
</script>
