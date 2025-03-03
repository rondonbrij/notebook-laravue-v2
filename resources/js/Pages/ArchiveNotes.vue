<script setup>
import { ref, watch } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import Sweetalert2 from 'sweetalert2';

const props = defineProps({
  notes: Object,
  filters: Object
});

const search = ref(props.filters.search || '');

watch(search, _.debounce(function (value) {
  router.get('/archived-notes', { search: value }, { preserveState: true });
}, 300));

const openNote = (note) => {
  router.get(`/notes/${note.id}`);
};

const deleteNote = (id) => {
  Sweetalert2.fire({
    title: 'Are you sure?',
    text: "You won't be able to revert this!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Yes, delete it!'
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(`/notes/${id}`, {
        onSuccess: () => {
          Sweetalert2.fire(
            'Deleted!',
            'Your note has been deleted.',
            'success'
          )
        }
      });
    }
  });
};

const unarchiveNote = (id) => {
  Sweetalert2.fire({
    title: 'Restore this note?',
    text: "The note will be moved back to your active notes",
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Yes, restore it!'
  }).then((result) => {
    if (result.isConfirmed) {
      router.put(`/notes/${id}/unarchive`, {}, {
        onSuccess: () => {
          Sweetalert2.fire(
            'Restored!',
            'Your note has been restored.',
            'success'
          )
        }
      });
    }
  });
};
</script>

<template>
  <MainLayout title="Archived Notes">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Archived Notes</h2>
    </template>

    <v-container>
      <v-row>
        <v-col cols="12" md="8">
          <Link :href="route('notes.index')" class="text-decoration-none">
            <v-btn color="primary" class="mb-4">
              <v-icon start>mdi-arrow-left</v-icon>
              Back to Notes
            </v-btn>
          </Link>
        </v-col>
        <v-col cols="12" md="4">
          <v-text-field
            v-model="search"
            label="Search archived notes"
            prepend-inner-icon="mdi-magnify"
            density="compact"
            variant="outlined"
            hide-details
          ></v-text-field>
        </v-col>
      </v-row>

      <v-row>
        <v-col cols="12">
          <v-card>
            <v-card-title>Archived Notes</v-card-title>
            <v-card-text>
              <v-row>
                <v-col v-for="note in notes.data" :key="note.id" cols="12" md="4">
                  <v-card elevation="2" class="h-100">
                    <v-card-title class="text-truncate">{{ note.title }}</v-card-title>
                    <v-card-text class="text-truncate">{{ note.content }}</v-card-text>
                    <v-card-text class="text-caption text-grey">
                      Archived: {{ new Date(note.updated_at).toLocaleDateString() }}
                    </v-card-text>
                    <v-card-actions>
                      <v-btn color="primary" variant="tonal" @click="openNote(note)" size="small">
                        <v-icon>mdi-eye</v-icon>
                      </v-btn>
                      <v-btn color="success" variant="tonal" @click.stop="unarchiveNote(note.id)" size="small">
                        <v-icon>mdi-archive-restore</v-icon>
                      </v-btn>
                      <v-btn color="error" variant="tonal" @click.stop="deleteNote(note.id)" size="small">
                        <v-icon>mdi-delete</v-icon>
                      </v-btn>
                    </v-card-actions>
                  </v-card>
                </v-col>
              </v-row>
              
              <div v-if="notes.data.length === 0" class="text-center py-4">
                <v-icon size="64" color="grey-lighten-1">mdi-archive-outline</v-icon>
                <p class="text-grey-darken-1 mt-2">No archived notes found.</p>
              </div>
            </v-card-text>
            
            <div class="d-flex justify-end px-4 pb-3">
              <Pagination :links="notes.links" />
            </div>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </MainLayout>
</template>