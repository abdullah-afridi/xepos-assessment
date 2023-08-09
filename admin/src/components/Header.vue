<template>
  <!-- <v-system-bar color="deep-purple darken-3"></v-system-bar> -->
<div >
  <v-app-bar color="primary" prominent>
    <v-app-bar-nav-icon
      variant="text"
      @click.stop="drawer = !drawer"
    ></v-app-bar-nav-icon>

    <v-toolbar-title>WELCOME TO XEPOS</v-toolbar-title>

    <v-spacer></v-spacer>
  
    <v-menu>
      <template v-slot:activator="{ props }">
        <v-btn icon="mdi-dots-vertical" v-bind="props"></v-btn>
      </template>

      <v-list v-if="userInfo !== null">
        <v-list-item>
          {{ userInfo.name }}
        </v-list-item>
        <v-list-item>
          <v-btn class="" @click="logout">Logout</v-btn>
        </v-list-item>
      </v-list>
      <v-list v-else>
        <v-list-item> No User </v-list-item>
      </v-list>
    </v-menu>

    <!-- <v-btn variant="text" icon="mdi-magnify"></v-btn>

    <v-btn variant="text" icon="mdi-filter"></v-btn>

    <v-btn variant="text" icon="mdi-dots-vertical"></v-btn> -->
  </v-app-bar>

  <v-navigation-drawer
    elevation="5"
    v-model="drawer"
    location="left"
    permanent
    App
  >
    <v-list>
      <v-list-item
        v-for="(item, i) in items"
        :key="i"
        :value="item"
        color="primary"
        variant="plain"
      >
        <v-list-item-title
          @click="goToRoute(item)"
          v-text="item.title"
        ></v-list-item-title>
      </v-list-item>
    </v-list>
  </v-navigation-drawer>
  </div>
</template>

<script>
import { reactive,watch, ref } from "vue";
import { onMounted } from "vue";
import { defineComponent } from "vue";
import { useRouter } from "vue-router";
// import { VMenu } from "vuetify/lib/components/index.mjs";

export default defineComponent({
  name: "Header",
  components: {
    // VMenu
  },
  setup() {
    const router = useRouter();
    const base_Url = "http://127.0.0.5:8000/api";
    let isAuth = ref(null)

   onMounted(() => {
   
    isAuth = localStorage.getItem("token")
   });

    const userInfo = JSON.parse(localStorage.getItem("user_info"));

    const logout = async () => {
      try {
        const response = await fetch(
            `${base_Url}/v1/logout`,
            {
              method: "POST",
              headers: {
                Authorization: "Bearer " + localStorage.getItem("token"),
              },
            }
          );
          if(response){
            localStorage.removeItem("token")
            router.push("/");
          }
      } catch (error) {
        console.log(error)
      }
    }

    const goToRoute = (val) => {
      switch (val.value) {
        case "dashboard":
          router.push({
            name: "dashboard",
          });
          break;
        case "employees":
          router.push({
            name: "employees",
          });
          break;
        case "company":
          router.push({
            name: "company",
          });

          break;
        default:
          router.push({
            path: "/",
          });
      }
    };

    return {
      goToRoute,
      logout,
      userInfo,
      isAuth
    };
  },
  data: () => ({
    drawer: false,
    group: null,

    items: [
      {
        title: "Dashboard",
        value: "dashboard",
      },
      {
        title: "Employees",
        value: "employees",
      },
      {
        title: "Company",
        value: "company",
      },
    ],
  }),

  watch: {
    group() {
      this.drawer = true;
    },
  },
  
});
</script>
