<template>
  <div class="d-flex align-center justify-center" >
    <v-sheet width="300" class="mx-auto my-8">
      <h4 class="my-2 ">Login XEPOS</h4>
      <v-form fast-fail @submit.prevent="submitHandler">
        <span style="color: red;font-size: 12px;">{{message}}</span>
        <v-text-field
          v-model="formData.email"
          label="Enter Email"
          :rules="fieldRules.emailRules"
        ></v-text-field>

        <v-text-field
          v-model="formData.password"
          label="Enter Password"
          type="password"
          :rules="fieldRules.passwordRules"
        ></v-text-field>

        <v-btn
          type="submit"
          block
          :loading="loading"
          class="mt-2"
          :disabled="
            !fieldRules.emailRules.length || !fieldRules.passwordRules.length
          "
          >Submit</v-btn
        >
      </v-form>
    </v-sheet>
  </div>
</template>

<script>
import { reactive, ref } from "vue";
import { useRouter } from "vue-router";

export default {
  name: "LoginUser",
  setup() {
    const router = useRouter();
    const formData = reactive({
      email: "",
      password: "",
    });

    const fieldRules = reactive({
      emailRules: [
        (value) => {
          if (value?.length > 3 && value.includes("@")) return true;

          return "Please Enter a Valid Email Address.";
        },
      ],
      passwordRules: [
        (value) => {
          if (value?.length > 5) return true;

          return "Please Enter a Valid Password.";
        },
      ],
    });

    const loading = ref(false)
    const message = ref('')

    const submitHandler = async () => {
      if (formData.email.length === 0 || formData.password.length === 0) {
        return;
      }

      try {
        loading.value = true

        const response = await fetch("http://127.0.0.5:8000/api/login", {
          method: "POST",
          body: JSON.stringify(formData),
          headers: {
            "content-type": "application/json",
          },
        });
        const res = await response.json();
        console.log(res);
       
        if (res.status) {
          loading.value = false
          if (res && res.body) {
            localStorage.setItem("user_info", JSON.stringify(res.body.user_info))
            if (res.body.access_token) {
            localStorage.setItem("token", res.body.access_token)
              router.push({
                name: "dashboard",
              });
            }
          }
        }else{
          message.value = "Invalid user credentials!";
        }
        loading.value = false
      } catch (error) {
        loading.value = false

      }
    };
    return {
      loading,
      formData,
      message,
      fieldRules,
      submitHandler,
    };
  },
};
</script>
