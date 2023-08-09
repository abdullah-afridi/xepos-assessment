<template>
  <div class="pa-16">
    <v-data-table
      :headers="headers"
      :items="state.employees"
      class="elevation-1"
    >
      <template v-slot:top>
        <v-toolbar flat>
          <v-toolbar-title>EMPLOYEES</v-toolbar-title>
          <v-divider class="mx-4" inset vertical></v-divider>
          <v-spacer></v-spacer>
          <v-dialog v-model="dialog" max-width="500px">
            <template v-slot:activator="{ props }">
              <v-btn
                @click="addEmployee"
                color="primary"
                dark
                class="mb-2"
                v-bind="props"
              >
                Add New Employee
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
                        v-model="state.editedItem.first_name"
                        label="First name"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12">
                      <v-text-field
                        v-model="state.editedItem.last_name"
                        label="Last name"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12">
                      <v-text-field
                        v-model="state.editedItem.email"
                        label="Email"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12">
                      <v-text-field
                        v-model="state.editedItem.phone"
                        label="Phone number"
                      ></v-text-field>
                    </v-col>
                    <v-col cols="12">
                      <v-autocomplete
                        v-model="selectedCompany"
                        item-title="name"
                        item-value="id"
                        :items="state.companies"
                        label="Select Company"
                      ></v-autocomplete>
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
  </div>
</template>

<script>
import { onMounted, reactive, ref } from "vue";
import { defineComponent } from "vue";
import { VDataTable } from "vuetify/lib/labs/components.mjs";

export default defineComponent({
  name: "Employee",
  components: {
    VDataTable,
  },

  data: () => ({
    headers: [
      { title: "First Name", key: "first_name" },
      { title: "Last Name", key: "last_name" },
      { title: "Email", key: "email" },
      { title: "Phone", key: "phone" },
      { title: "Company", key: "company.name" },
      { title: "Actions", key: "actions" },
    ],
  }),

  computed: {
    formTitle() {
      return this.editedIndex === -1 ? "Add New Employee" : "Edit Employee";
    },
  },

  setup() {
    const state = reactive({
      employees: [],
      companies: [],
      editedItem: {
        first_name: "",
        last_name: "",
        email: "",
        phone: "",
        company_id: "",
      },
    });
    const selectedCompany = ref(null);
    const baseUrl = "http://127.0.0.5:8000/api";
    let employeeId = ref(null);
    let editedIndex = ref(-1);
    const dialog = ref(false);
    const dialogDelete = ref(false);

    const addEmployee = async () => {
      editedIndex.value = -1;
      state.editedItem = {
        first_name: "",
        last_name: "",
        email: "",
        phone: "",
        company_id: "",
      };

      await fetchCompanies();
    };

    const fetchCompanies = async () => {
      try {
        const response = await fetch(`${baseUrl}/v1/getCompanyList`, {
          method: "GET",
          headers: {
            "content-type": "application/json",
            Authorization: "Bearer " + localStorage.getItem("token"),
          },
        });
        const res = await response.json();
        state.companies = res.body;
      } catch (error) {
        console.error("Error fetching companies:", error);
      }
    };

    const save = async () => {
      const formData = new FormData();
      formData.append("first_name", state.editedItem.first_name);
      formData.append("last_name", state.editedItem.last_name);
      formData.append("email", state.editedItem.email);
      formData.append("phone", state.editedItem.phone);
      formData.append("company_id", selectedCompany.value);
      if (editedIndex.value == -1) {
        try {
          const response = await fetch(`${baseUrl}/v1/employee`, {
            method: "POST",
            body: formData,
            headers: {
              Authorization: "Bearer " + localStorage.getItem("token"),
            },
          });
          if (response) {
            fetchEmployees();
          }

          if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
          }
        } catch (error) {
          console.log(error);
        }
      } else {
        formData.append("_method", "PUT");

        try {
          const response = await fetch(
            `${baseUrl}/v1/employee/${employeeId.value}`,
            {
              method: "PUT",
              body: formData,
              headers: {
                Authorization: "Bearer " + localStorage.getItem("token"),
              },
            }
          );
          if (response) {
            fetchEmployees();
          }

          if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
          }
        } catch (error) {
          console.log(error);
        }
      }
      dialog.value = false;
    };

    const editItem = async (item) => {
      await fetchCompanies();
      selectedCompany.value = state.companies.find(
        (val) => val.id == item.company_id
      );
      employeeId.value = item.id;
      editedIndex.value = state.employees.indexOf(item);
      state.editedItem = Object.assign({}, item);
      dialog.value = true;
    };

    const deleteItem = (item) => {
      employeeId.value = item.id;
      dialogDelete.value = true;
    };

    const deleteItemConfirm = async () => {
      try {
        const response = await fetch(
          `${baseUrl}/v1/employee/${employeeId.value}`,
          {
            method: "DELETE",
            headers: {
              Authorization: "Bearer " + localStorage.getItem("token"),
            },
          }
        );
        dialogDelete.value = false;

        if (response) {
          fetchEmployees();
        }
      } catch (error) {}
    };

    const fetchEmployees = async () => {
      try {
        const response = await fetch(`${baseUrl}/v1/employee`, {
          method: "GET",
          headers: {
            // "content-type": "application/json",
            Authorization: "Bearer " + localStorage.getItem("token"),
          },
        });
        const res = await response.json();
        state.employees = res.body.data;
      } catch (error) {
        console.log(error);
      }
    };
    const close = () => {
      dialog.value = false;
    };

    onMounted(async () => {
      await fetchEmployees();
    });
    return {
      state,
      dialog,
      dialogDelete,
      selectedCompany,
      fetchEmployees,
      editItem,
      addEmployee,
      save,
      deleteItem,
      deleteItemConfirm,
      close,
    };
  },
});
</script>
