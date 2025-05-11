<script setup>
import { ref } from "vue";
import Avatar from 'primevue/avatar';
import Menubar from 'primevue/menubar';

const props = defineProps({
  title: {
    type: String,
    default: 'Titre par défaut'
  },
  menuItems: {
    type: Array,
    default: () => []
  }
});
</script>

<template>
  <div class="menubar-wrapper">
    <div class="menubar-left">
      <Avatar image="https://primefaces.org/cdn/primevue/images/avatar/amyelsner.png" shape="circle" />
      <strong class="brand">Vivetic</strong>
    </div>
    <div class="menubar-center">
      <h1 class="title">{{ title }}</h1>
    </div>
    <div class="menubar-right">
    <!-- <Menubar :model="menuItems" /> -->
    </div>
  </div>
</template>

<style scoped>
.menubar-wrapper {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px 24px;
  background-color: #f9f9f9;
  border-bottom: 1px solid #ccc;
}
.menubar-left {
  display: flex;
  align-items: center;
  gap: 12px;
}
.brand {
  font-weight: bold;
  font-size: 1.25rem;
}
.title {
  font-size: 1.25rem;
  font-weight: 600;
}
.menubar-right {
  display: flex;
  align-items: center;
}
::v-deep(.p-avatar img) {
  width: 48px !important;
  height: 48px !important;
}
</style>

