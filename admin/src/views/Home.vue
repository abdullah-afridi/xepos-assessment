<template>
  <div>
    
  <LoginUser/>
</div>
</template>

<script>

import { defineComponent } from "vue";
import LoginUser from "../components/LoginUser";

export default defineComponent( {
  
  name:"Home",
  components:{LoginUser},
  setup(){

    return{

    }
  }

})

  
</script>
