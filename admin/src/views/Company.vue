<template>
  <div class="pa-16">
    <v-data-table
      :headers="headers"
      :items="state.companies"
      class="elevation-1"
    >
      <template v-slot:top>
        <v-toolbar flat>
          <v-toolbar-title>COMPANIES</v-toolbar-title>
          <v-divider class="mx-4" inset vertical></v-divider>
          <v-spacer></v-spacer>
          <v-dialog v-model="dialog" max-width="500px">
            <template v-slot:activator="{ props }">
              <v-btn
                @click="addCompany"
                color="primary"
                dark
                class="mb-2"
                v-bind="props"
              >
                Add New Company
              </v-btn>
            </template>
            <v-card>
              <v-card-title>
                <span class="text-h5">{{ formTitle }}</span>
              </v-card-title>

              <v-card-text>
                <v-container>
                  <v-row>
                    <v-col cols="12">
                      <v-text-field
                        v-model="state.editedItem.name"
                        label="company name"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12">
                      <v-text-field
                        v-model="state.editedItem.email"
                        label="email"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12">
                      <v-text-field
                        v-model="state.editedItem.website"
                        label="site link"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12">
                      <input type="file" @change="onChangeFile" />
                    </v-col>
                  </v-row>
                </v-container>
              </v-card-text>

              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="blue-darken-1" variant="text" @click="close">
                  Cancel
                </v-btn>
                <v-btn color="blue-darken-1" variant="text" @click="save">
                  Save
                </v-btn>
              </v-card-actions>
            </v-card>
          </v-dialog>
          <v-dialog v-model="dialogDelete" max-width="500px">
            <v-card>
              <v-card-title class="text-h5"
                >Are you sure you want to delete this item?</v-card-title
              >
              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="blue-darken-1" variant="text" @click="closeDelete"
                  >Cancel</v-btn
                >
                <v-btn
                  color="blue-darken-1"
                  variant="text"
                  @click="deleteItemConfirm"
                  >OK</v-btn
                >
                <v-spacer></v-spacer>
              </v-card-actions>
            </v-card>
          </v-dialog>
        </v-toolbar>
      </template>
      <template v-slot:item.logo="{ item }">
        <div style="width: 50px; height: 50px">
          <img
            :src="item.raw.logo"
            alt="company logo"
            style="
              width: 100%;
              height: 100%;
              border-radius: 50px;
              object-fit: contain;
            "
          />
        </div>
      </template>
      <template v-slot:item.actions="{ item }">
        <v-icon size="small" class="me-2" @click="editItem(item.raw)">
          mdi-pencil
        </v-icon>
        <v-icon size="small" @click="deleteItem(item.raw)"> mdi-delete </v-icon>
      </template>
      <template v-slot:no-data>
        <v-btn color="primary" @click="initialize"> Reset </v-btn>
      </template>
    </v-data-table>

    <Snackbar :message="message" :snackbar="snackbar"/>
  </div>
</template>

<script>
import { onMounted, reactive, ref } from "vue";
import { defineComponent } from "vue";
import { VDataTable } from "vuetify/lib/labs/components.mjs";
import Snackbar from "../components/snackbar.vue"

export default defineComponent({
  name: "Company",
  components: {
    VDataTable,
    Snackbar
  },

  data: () => ({
    headers: [
      {
        title: "Name",
        key: "name",
      },
      { title: "Email", key: "email" },
      { title: "Logo", key: "logo" },
      { title: "Website", key: "website" },

      { title: "Actions", key: "actions" },
    ],
  }),

  computed: {
    formTitle() {
      return this.editedIndex === -1 ? "Add New Company" : "Edit Company";
    },
  },

  setup() {
    const state = reactive({
      companies: [],
      editedItem: {
        name: "",
        email: "",
        image: null,
        website: "",
      },
    });
    const base_Url = "http://127.0.0.5:8000/api";
    let companyId = ref(null);
    let editedIndex = ref(-1);
    const dialog = ref(false);
    let message = ref("");
    let snackbar = ref(false);

    const dialogDelete = ref(false);

    const addCompany = () => {
      editedIndex.value = -1;
      state.editedItem = {
        name: "",
        email: "",
        image: null,
        website: "",
      };
    };

    const onChangeFile = (e) => {
      if (e && e.target && e.target.value) {
        state.editedItem.image = e.target.files[0];
      }
    };

    const save = async () => {
      if (editedIndex.value == -1) {
        const formData = new FormData();
        formData.append("name", state.editedItem.name);
        formData.append("email", state.editedItem.email);
        formData.append("image", state.editedItem.image);
        formData.append("website", state.editedItem.website);

        try {
          const response = await fetch(`${base_Url}/v1/company`, {
            method: "POST",
            body: formData,
            headers: {
              Authorization: "Bearer " + localStorage.getItem("token"),
            },
          });
          console.log(response.ok)
          if (response.ok) {
            message.value = "Successfully added company"
            snackbar.value = true
            await fetchCompanies();
          }
          if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
          } else {
            console.error("Error uploading image");
          }
        } catch (error) {
          console.log(error);
        }
        dialog.value = false;
      } else {
        const formData = new FormData();
        formData.append("name", state.editedItem.name);
        formData.append("email", state.editedItem.email);
        formData.append("image", state.editedItem.image);
        formData.append("website", state.editedItem.website);
        try {
          const response = await fetch(
            `${base_Url}/v1/company/${companyId.value}`,
            {
              method: "PUT",
              body: formData,
              headers: {
                Authorization: "Bearer " + localStorage.getItem("token"),
              },
            }
          );
          if (response) {
            await fetchCompanies();
          }
          if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
          }
        } catch (error) {
          console.log(error);
        }
        dialog.value = false;
      }
    };

    const editItem = (item) => {
      companyId.value = item.id;
      editedIndex.value = state.companies.indexOf(item);
      state.editedItem = Object.assign({}, item);
      dialog.value = true;
    };

    const deleteItem = (item) => {
      companyId.value = item.id;
      dialogDelete.value = true;
    };

    const deleteItemConfirm = async () => {
      try {
        const response = await fetch(`${base_Url}/v1/company/${companyId.value}`, {
          method: "DELETE",
          headers: {
            Authorization: "Bearer " + localStorage.getItem("token"),
          },
        });
        dialogDelete.value = false;

        if (response) {
          await fetchCompanies();
        }
      } catch (error) {}
    };

    const fetchCompanies = async () => {
      try {
        const response = await fetch(`${base_Url}/v1/company`, {
          method: "GET",
          headers: {
            Authorization: "Bearer " + localStorage.getItem("token"),
          },
        });
        const res = await response.json();
        state.companies = res.body.data;
      } catch (error) {
        console.log(error);
      }
    };

    onMounted(async () => {
      await fetchCompanies();
    });
    return {
      snackbar,
      message,
      state,
      dialog,
      dialogDelete,
      fetchCompanies,
      editItem,
      addCompany,
      save,
      deleteItem,
      deleteItemConfirm,
      onChangeFile,
    };
  },
});
</script>
