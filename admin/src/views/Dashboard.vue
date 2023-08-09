<template>
  <div class="dashboard">
    <h1>Welcome to the Dashboard</h1>
    <v-row>
      <v-col cols="6">
        <v-card>
          <v-card-title>Chart</v-card-title>
          <v-card-text>
            <v-chart type="bar" :options="chartOptions" :series="chartSeries" />
          </v-card-text>
        </v-card>
      </v-col>
      <v-col cols="6">
        <v-card>
          <v-card-title>Table</v-card-title>
          <v-card-text>
            <v-data-table :headers="tableHeaders" :items="tableData" />
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
    <v-row>
      <v-col cols="12">
        <h2>List of Items</h2>
        <v-list>
          <v-list-item v-for="(item, index) in listItems" :key="index">
            <v-list-item-content>
              <v-list-item-title>{{ item }}</v-list-item-title>
            </v-list-item-content>
          </v-list-item>
        </v-list>
      </v-col>
    </v-row>
  </div>
</template>

<script>
import { onMounted, ref } from "vue";
import { useRouter } from "vue-router";

export default {
  name: "Dashboard",
  setup() {
    const router = useRouter();
     onMounted(() => {});

    // Dummy data for the chart, table, and list
    const chartOptions = {
      // Chart options here
    };
    const chartSeries = [{ name: "Series 1", data: [42, 58, 34, 67, 80] }];
    
    const tableHeaders = [
      { text: "Name", value: "name" },
      { text: "Age", value: "age" },
      { text: "City", value: "city" },
    ];
    const tableData = [
      { name: "John", age: 25, city: "New York" },
      { name: "Alice", age: 30, city: "Los Angeles" },
      // Add more rows...
    ];
    
    const listItems = ["Item 1", "Item 2", "Item 3", "Item 4", "Item 5"];

    return {
      chartOptions,
      chartSeries,
      tableHeaders,
      tableData,
      listItems,
    };
  },
};
</script>

<style scoped>
.dashboard {
  padding: 20px;
}
</style>




<!-- <template>
  <div class="d-flex align-center justify-center" style="">Dashboard</div>
</template>

<script>
import { onMounted } from "vue";
import { useRouter } from "vue-router";

export default {
  name: "Dashboard",
  setup() {
    console.log("dash");
    const router = useRouter();
    onMounted(() => {
      console.log("Reloading the page...");
      window.location.reload();
    });

    return {};
  },
};
</script> -->
