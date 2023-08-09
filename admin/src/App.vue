<template>
  <v-layout>
    <Header />

    <v-main>
      <router-view />
    </v-main>
  </v-layout>
</template>

<script>
import { defineComponent, computed } from "vue";
import Header from "@/components/Header.vue";

export default defineComponent({
  name: "App",
  components: {
    Header,
  },
  setup() {
    const isAuth = computed(() => {
      return localStorage.getItem("token") !== null;
    });

    return {
      isAuth,
    };
  },
});
</script>
