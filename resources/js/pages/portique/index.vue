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

// ✅ Props
const props = defineProps({
  log_portiques: {
    type: Object,
    required: true
  }
});

// ✅ Pagination variables
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

// ✅ Menu items
const items = ref([
  {
    label: 'Home',
    icon: 'pi pi-home'
  },
  {
    label: 'Projects',
    icon: 'pi pi-search',
    badge: 3,
    items: [
      {
        label: 'Core',
        icon: 'pi pi-bolt',
        shortcut: '⌘+S'
      },
      {
        label: 'Blocks',
        icon: 'pi pi-server',
        shortcut: '⌘+B'
      },
      {
        separator: true
      },
      {
        label: 'UI Kit',
        icon: 'pi pi-pencil',
        shortcut: '⌘+U'
      }
    ]
  }
]);
</script>

<template>
  <Menubar :model="items">

    <template #start>
    <div class="flex items-center gap-2">
         <Avatar image="https://primefaces.org/cdn/primevue/images/avatar/amyelsner.png" shape="circle" />
        <strong class="font-bold">Vivetic</strong>
    </div>
    </template>

    <template #item="{ item, props, hasSubmenu, root }">
        <a v-ripple class="flex items-center" v-bind="props">
            <span>{{ item.label }}</span>
            <Badge v-if="item.badge" :class="{ 'ml-auto': !root, 'ml-2': root }" :value="item.badge" />
            <span v-if="item.shortcut" class="ml-auto border border-surface rounded bg-emphasis text-muted-color text-xs p-1">
            {{ item.shortcut }}
            </span>
            <i v-if="hasSubmenu" :class="['pi ml-auto', root ? 'pi-angle-down' : 'pi-angle-right']"></i>
        </a>
    </template>

    <template  #end>
      <svg width="35" height="40" viewBox="0 0 35 40" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-8">
        <!-- SVG paths here (non modifiés pour la lisibilité) -->
      </svg>
    </template>

  </Menubar>

  <Head title="Collaborateur" />

  <h1 class="text-xl font-bold my-4">Liste des Collaborateurs</h1>

  <div class="card" >
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


