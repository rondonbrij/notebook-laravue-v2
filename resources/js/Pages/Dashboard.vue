<script setup>
import { ref, watch } from 'vue';
import { router, Link, usePage } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import Sweetalert2 from 'sweetalert2';

const props = defineProps({
  notes: Object,
  filters: Object
});

const page = usePage();
const showDialog = ref(false);
const isEditing = ref(false);
const form = ref({ id: null, title: "", content: "" });
const search = ref(props.filters.search || '');
const sortField = ref(props.filters.sort_field || 'created_at');
const sortDirection = ref(props.filters.sort_direction || 'desc');

watch(search, _.debounce(function (value) {
  router.get('/notes', { search: value, sort_field: sortField.value, sort_direction: sortDirection.value }, { preserveState: true });
}, 300));

const sort = (field) => {
  sortField.value = field;
  sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
  router.get('/notes', { search: search.value, sort_field: field, sort_direction: sortDirection.value }, { preserveState: true });
};

const showCreateDialog = () => {
  form.value = { id: null, title: "", content: "" };
  isEditing.value = false;
  showDialog.value = true;
};

const openNote = (note) => {
  router.get(`/notes/${note.id}`);
};

const editNote = (note) => {
  form.value = { ...note };
  isEditing.value = true;
  showDialog.value = true;
};

const saveNote = () => {
  if (!form.value.title.trim()){
    Sweetalert2.fire({
      title: 'Error!',
      text: 'Title cannot be empty!',
      icon: 'error',
      confirmButtonText: 'OK'
    });
    return;
  } else if (!form.value.content.trim()) {
    Sweetalert2.fire({
      title: 'Error!',
      text: 'Note content cannot be empty!',
      icon: 'error',
      confirmButtonText: 'OK'
    });
    return;
  };

  if (isEditing.value) {
    router.put(`/notes/${form.value.id}`, form.value, {
      onSuccess: () => {
        showDialog.value = false;
        Sweetalert2.fire({
          title: 'Success!',
          text: 'Note updated successfully!',
          icon: 'success',
          timer: 2000,
          showConfirmButton: false
        });
      }
    });
  } else {
    router.post('/notes', form.value, {
      onSuccess: () => {
        showDialog.value = false;
        Sweetalert2.fire({
          title: 'Success!',
          text: 'Note created successfully!',
          icon: 'success',
          timer: 2000,
          showConfirmButton: false
        });
      }
    });
  }
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

const archiveNote = (id) => {
  Sweetalert2.fire({
    title: 'Archive this note?',
    text: "You can find it in the archive section",
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Yes, archive it!'
  }).then((result) => {
    if (result.isConfirmed) {
      router.put(`/notes/${id}/archive`, {}, {
        onSuccess: () => {
          Sweetalert2.fire(
            'Archived!',
            'Your note has been archived.',
            'success'
          )
        }
      });
    }
  });
};
</script>

<template>
  <MainLayout title="Dashboard">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
    </template>

    <v-container>
      <v-row>
        <v-col cols="12" md="8">
          <v-btn @click="showCreateDialog()" color="primary" class="mb-4">
            <v-icon start>mdi-plus</v-icon>
            Add Note
          </v-btn>
        </v-col>
        <v-col cols="12" md="4">
          <v-text-field
            v-model="search"
            label="Search notes"
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
            <v-card-title class="d-flex justify-space-between align-center">
              My Notes
              <Link :href="route('notes.archived')" class="text-decoration-none">
                <v-btn color="grey">
                  <v-icon start>mdi-archive</v-icon>
                  Archived Notes
                </v-btn>
              </Link>
            </v-card-title>
            <v-card-text>
              <v-row>
                <v-col v-for="note in notes.data" :key="note.id" cols="12" md="4">
                  <v-card elevation="2" class="h-100">
                    <v-card-title class="text-truncate">{{ note.title }}</v-card-title>
                    <v-card-text class="text-truncate">{{ note.content }}</v-card-text>
                    <v-card-text class="text-caption text-grey">
                      Created: {{ new Date(note.created_at).toLocaleDateString() }}
                    </v-card-text>
                    <v-card-actions>
                      <v-btn color="primary" variant="tonal" @click="openNote(note)" size="small">
                        <v-icon>mdi-eye</v-icon>
                      </v-btn>
                      <v-btn color="info" variant="tonal" @click.stop="editNote(note)" size="small">
                        <v-icon>mdi-pencil</v-icon>
                      </v-btn>
                      <v-btn color="warning" variant="tonal" @click.stop="archiveNote(note.id)" size="small">
                        <v-icon>mdi-archive</v-icon>
                      </v-btn>
                      <v-btn color="error" variant="tonal" @click.stop="deleteNote(note.id)" size="small">
                        <v-icon>mdi-delete</v-icon>
                      </v-btn>
                    </v-card-actions>
                  </v-card>
                </v-col>
              </v-row>
              
              <div v-if="notes.data.length === 0" class="text-center py-4">
                <v-icon size="64" color="grey-lighten-1">mdi-note-outline</v-icon>
                <p class="text-grey-darken-1 mt-2">No notes found. Create a new note to get started!</p>
              </div>
            </v-card-text>
            
            <div class="d-flex justify-end px-4 pb-3">
              <Pagination :links="notes.links" />
            </div>
          </v-card>
        </v-col>
      </v-row>

      <!-- Create/Edit Note Dialog -->
      <v-dialog v-model="showDialog" max-width="600px">
        <v-card>
          <v-card-title>{{ isEditing ? "Edit Note" : "Create Note" }}</v-card-title>
          <v-card-text>
            <v-form @submit.prevent="saveNote">
              <v-text-field
                v-model="form.title"
                label="Title"
                required
                :rules="[v => !!v.trim() || 'Title is required']"
              ></v-text-field>
              <v-textarea
                v-model="form.content"
                label="Content"
                required
                :rules="[v => !!v.trim() || 'Content is required']"
                rows="8"
              ></v-textarea>
            </v-form>
          </v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="error" variant="text" @click="showDialog = false">Cancel</v-btn>
            <v-btn color="success" @click="saveNote">Save</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </v-container>
  </MainLayout>
</template>