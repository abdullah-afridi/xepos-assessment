<template>
  <div class="text-center">
    <v-snackbar v-model="snackbar" >
      {{ message }}

      <template v-slot:actions>
        <v-btn color="blue" variant="text" @click="snackbar = false">
          Close
        </v-btn>
      </template>
    </v-snackbar>
  </div>
</template>

<script>
import { watch } from "vue";
import { defineComponent, ref } from "vue";

export default defineComponent({
    props: {
    message: String,
    snackbar: Boolean // Declare the prop with its type
  },
  setup(props) {
    let message = ref('')
  
    const snackbar = ref(false);
    watch(
      () => props.snackbar,
      () => {
        snackbar.value = props.snackbar
        message.value = props.message

        setTimeout(() =>{
            snackbar.value = false
        }, 2000)
      }
    );
   
    return {
      snackbar,
      message
    };
  },
});
</script>
