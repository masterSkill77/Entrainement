<script setup>
import { ref } from "vue";
import { Head, router } from '@inertiajs/vue3';
import Button from 'primevue/button';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Paginator from 'primevue/paginator';
import Menubar from 'primevue/menubar';
import InputText from 'primevue/inputtext';
import Avatar from 'primevue/avatar';
import Badge from 'primevue/badge';
import Heading from '@/components/Heading.vue'; 

const props = defineProps({
  log_portiques: {
    type: Object,
    required: true
  }
});

const total = props.log_portiques.total;
const currentPage = props.log_portiques.current_page;
const perPage = props.log_portiques.per_page;
const offset = (currentPage - 1) * perPage;

const onPageChange = (event) => {
  const page = event.page + 1;
  const url = new URL(window.location.href);
  url.searchParams.set('page', page);
  router.get(url.toString(), {}, { preserveState: true, preserveScroll: true });
};

const items = ref([
  { label: 'Collaborateurs', icon: 'pi pi-home' },
  {
    label: 'Logs',
    icon: 'pi pi-search',
    badge: 3,
    items: [
      { label: 'Core', icon: 'pi pi-bolt', shortcut: '⌘+S' },
      { label: 'Blocks', icon: 'pi pi-server', shortcut: '⌘+B' },
      { separator: true },
      { label: 'UI Kit', icon: 'pi pi-pencil', shortcut: '⌘+U' }
    ]
  }
]);
</script>

<template>
   <Head title="Collaborateur" />
   <Heading 
    title="Liste des Collaborateurs"
    :menuItems="items"
  />
  <div class="card-custom">
    <DataTable :value="props.log_portiques.data" tableStyle="min-width: 50rem">
      <Column field="Name" header="Name" />
      <Column field="pin" header="Matricule" />
      <Column field="card" header="Cartes" />
    </DataTable>

    <Paginator
      :rows="perPage"
      :totalRecords="total"
      :first="offset"
      @page="onPageChange"
      template="PrevPageLink PageLinks NextPageLink"
      class="mt-4"
    />
  </div>
</template>

<style scoped>
.card-custom {
  max-width: 1200px;
  margin: 20px auto;
  padding: 20px 40px;
  background-color: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.menubar-wrapper {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px 40px;
  background-color: #f5f5f5;
  margin-bottom: 10px;
}

.menubar-left {
  display: flex;
  align-items: center;
  gap: 12px;
}

.menubar-center {
  flex: 1;
  text-align: center;
}

.menubar-right {
  margin-left: auto;
}

.brand {
  font-weight: bold;
  font-size: 1.25rem;
}

.title {
  font-size: 1.5rem;
  font-weight: bold;
  margin: 0;
}

::v-deep(.p-avatar img) {
  width: 48px !important; /* ⬅️ agrandit l'image */
  height: 48px !important;
}
</style>
