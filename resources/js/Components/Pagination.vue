<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
  links: Array,
});
</script>

<template>
  <div v-if="links.length > 3">
    <div class="flex flex-wrap">
      <template v-for="(link, key) in links" :key="key">
        <div
          v-if="link.url === null"
          class="mr-1 mb-1 px-4 py-2 text-sm border rounded text-gray-400"
          v-html="link.label"
        />
        <Link
          v-else
          :href="link.url"
          class="mr-1 mb-1 px-4 py-2 text-sm border rounded"
          :class="{
            'bg-blue-500 text-white': link.active,
            'text-gray-700 hover:bg-gray-100': !link.active,
          }"
          v-html="link.label"
        />
      </template>
    </div>
  </div>
</template>