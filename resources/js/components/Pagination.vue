<template>
  <Paginator
    :rows="10"
    :totalRecords="total"
    :first="offset"
    @page="onPageChange"
    template="PrevPageLink PageLinks NextPageLink"
    class="mt-4"
  />
</template>

<script setup>
import { router } from '@inertiajs/vue3';

const props = defineProps({
  links: Array,
  total: Number,        
  currentPage: Number,  
  perPage: Number       
});

const offset = (props.currentPage - 1) * props.perPage;

const onPageChange = (event) => {
  const page = event.page + 1; 
  const url = new URL(window.location.href);
  url.searchParams.set('page', page);

  router.get(url.toString(), {}, { preserveState: true, preserveScroll: true });
};
</script>
