<template>
<button class="btn btn-light" type="button" id="button-addon2" @click="generate()">
    <i class="bi bi-arrow-repeat"></i> {{$t('Generate password')}}
</button>
</template>
<script>
export default {
  props: {
    type: {
      type: String,
      default: "text"
    },
    size: {
      type: String,
      default: "8"
    },
    characters: {
      type: String,
      default: "a-z,A-Z,0-9"
    },
    auto: [String, Boolean],
    value: ""
  },
  data: function() {
    return {
      password: this.value
    };
  },
  mounted: function() {
    if (this.auto == "true" || this.auto == 1) {
      this.generate();
    }
  },
  methods: {
    generate() {
      let charactersArray = this.characters.split(",");
      let CharacterSet = "";
      let password = "";

      if (charactersArray.indexOf("a-z") >= 0) {
        CharacterSet += "abcdefghijklmnopqrstuvwxyz";
      }
      if (charactersArray.indexOf("A-Z") >= 0) {
        CharacterSet += "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
      }
      if (charactersArray.indexOf("0-9") >= 0) {
        CharacterSet += "0123456789";
      }
      if (charactersArray.indexOf("#") >= 0) {
        CharacterSet += "![]{}()%&*$#^<>~@|";
      }

      for (let i = 0; i < this.size; i++) {
        password += CharacterSet.charAt(
          Math.floor(Math.random() * CharacterSet.length)
        );
      }
      this.password = password;
      this.$emit('generated', password)
    }
  }
};
</script>