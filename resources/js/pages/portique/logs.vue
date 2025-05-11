<script setup>
import { ref, watch } from "vue";
import { Head, router } from '@inertiajs/vue3';
import Button from 'primevue/button';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Paginator from 'primevue/paginator';
import Menubar from 'primevue/menubar';
import DatePicker from 'primevue/datepicker';
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
const date = ref();

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

// Fonction pour formater les secondes
function formatSeconds(seconds) {
    if (!seconds || isNaN(seconds)) return '0h 0min';
    const h = Math.floor(seconds / 3600);
    const m = Math.floor((seconds % 3600) / 60);
    return `${h}h ${m}min`;
}

// Fonction pour changer de page
const onPageChange = (event) => {
    const page = event.page + 1;
    const url = new URL(window.location.href);
    url.searchParams.set('page', page);
    router.get(url.toString(), {}, { preserveState: true, preserveScroll: true });
};

// Fonction qui envoie la requête avec la date sélectionnée
const fetchDataByDate = async () => {
    if (date.value) {
        const formattedDate = date.value.toISOString().split('T')[0]; // Formater la date en YYYY-MM-DD

        // Créer l'URL avec les paramètres pour la date
        const url = new URL(window.location.href);
        url.searchParams.set('date', formattedDate); // Ajouter le paramètre 'date'

        // Envoyer la requête GET pour récupérer les logs filtrés
        router.get(url.toString(), {}, { preserveState: true, preserveScroll: true });
    }
};

// Observer le changement de date et appeler la fonction pour charger les données
watch(date, () => {
    fetchDataByDate(); // Recharger les logs avec la nouvelle date
});
</script>

<template>
    <Heading 
        title="Liste logs"
        :menuItems="items"
    />
    <div class="card flex justify-right margin-5">
        <DatePicker v-model="date" placeholder="Sélectionner une date" />
    </div>
    <div class="card" style="padding-left: 1.5rem; padding-right: 1.5rem;">
        <DataTable :value="props.log_portiques.data" tableStyle="min-width: 50rem">
            <Column field="day" header="Date" />
            <Column field="Name" header="Name" />
            <Column field="pin" header="Matricule" />
            <Column field="card" header="Cartes" />
            <Column field="premiere_entree" header="Premier entre" />
            <Column field="derniere_sortie" header="Dernière sortie" />
            <Column field="nb_seconds_pauses" header="Volume pause">
                <template #body="slotProps">
                    {{ formatSeconds(slotProps.data.nb_seconds_pauses) }}
                </template>
            </Column>
            <Column field="nb_pause" header="Nombre pause" />
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
    .margin-5 {
        margin-top: 2rem;
        margin-right: 1rem;
        margin-bottom: 2rem;
        margin-left: 1rem;
    }

</style>
