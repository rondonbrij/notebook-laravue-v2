<script setup>
import { ref, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import Swal from 'sweetalert2';

const props = defineProps({
  notes: Object, // Changed to Object to receive paginated data
  filters: Object
});

// Form for creating/editing notes
const showDialog = ref(false);
const isEditing = ref(false);
const form = ref({ id: null, title: "", content: "" });
const search = ref(props.filters?.search || '');

// Show create dialog
const showCreateDialog = () => {
  form.value = { id: null, title: "", content: "" };
  isEditing.value = false;
  showDialog.value = true;
};

// View note in a new page
const viewNote = (note) => {
  router.get(`/notes/${note.id}`);
};

// Open edit dialog
const editNote = (note) => {
  form.value = { ...note };
  isEditing.value = true;
  showDialog.value = true;
};

// Save note (create or update)
const saveNote = () => {
  // Validation
  if (!form.value.title.trim()) {
    Swal.fire({
      title: 'Validation Error',
      text: 'Title cannot be empty!',
      icon: 'error',
      confirmButtonText: 'OK'
    });
    return;
  } else if (!form.value.content.trim()) {
    Swal.fire({
      title: 'Validation Error',
      text: 'Note content cannot be empty!',
      icon: 'error',
      confirmButtonText: 'OK'
    });
    return;
  }

  if (isEditing.value) {
    router.put(`/notes/${form.value.id}`, form.value, {
      onSuccess: () => {
        showDialog.value = false;
        Swal.fire({
          title: 'Success!',
          text: 'Note updated successfully',
          icon: 'success',
          timer: 1500
        });
      }
    });
  } else {
    router.post('/notes', form.value, {
      onSuccess: () => {
        showDialog.value = false;
        Swal.fire({
          title: 'Success!',
          text: 'Note created successfully',
          icon: 'success',
          timer: 1500
        });
      }
    });
  }
};

// Delete note
const deleteNote = (id) => {
  Swal.fire({
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
          Swal.fire(
            'Deleted!',
            'Your note has been deleted.',
            'success'
          );
        }
      });
    }
  });
};

// Search notes
const searchNotes = () => {
  router.get('/dashboard', { search: search.value });
};

// Reset search
const resetSearch = () => {
  search.value = '';
  router.get('/dashboard');
};
</script>

<template>
  <AppLayout title="Dashboard">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        My Notes
      </h2>
    </template>

    <Head title="Dashboard" />

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
          <!-- Search and Add Note Bar -->
          <div class="flex items-center justify-between mb-6">
            <div class="flex items-center">
              <input
                v-model="search"
                class="border rounded-md px-4 py-2 w-64"
                type="text"
                placeholder="Search notes..."
                @keyup.enter="searchNotes"
              />
              <button
                @click="searchNotes"
                class="ml-2 bg-blue-500 text-white px-4 py-2 rounded-md"
              >
                Search
              </button>
              <button
                v-if="search"
                @click="resetSearch"
                class="ml-2 bg-gray-300 text-gray-700 px-4 py-2 rounded-md"
              >
                Reset
              </button>
            </div>
            <button
              @click="showCreateDialog()"
              class="bg-green-500 text-white px-4 py-2 rounded-md"
            >
              Add Note
            </button>
          </div>

          <!-- Notes Grid -->
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div
              v-for="note in notes.data"
              :key="note.id"
              class="border p-4 rounded-lg shadow hover:shadow-md transition cursor-pointer"
              @click="viewNote(note)"
            >
              <h3 class="text-lg font-bold truncate">{{ note.title }}</h3>
              <p class="text-gray-500 truncate mt-2">{{ note.content }}</p>
              <div class="mt-4 flex space-x-2">
                <button
                  @click.stop="editNote(note)"
                  class="bg-blue-500 text-white px-3 py-1 rounded-md text-sm"
                >
                  Edit
                </button>
                <button
                  @click.stop="deleteNote(note.id)"
                  class="bg-red-500 text-white px-3 py-1 rounded-md text-sm"
                >
                  Delete
                </button>
              </div>
            </div>
          </div>

          <!-- Empty State -->
          <div v-if="notes.data.length === 0" class="text-center py-12">
            <h3 class="text-lg font-medium text-gray-900">No notes found</h3>
            <p class="mt-1 text-sm text-gray-500">
              Get started by creating a new note
            </p>
            <div class="mt-6">
              <button
                @click="showCreateDialog()"
                class="bg-green-500 text-white px-4 py-2 rounded-md"
              >
                Add Note
              </button>
            </div>
          </div>

          <!-- Pagination -->
          <div class="mt-6 flex justify-end">
            <Pagination :links="notes.links" />
          </div>
        </div>
      </div>
    </div>

    <!-- Create/Edit Note Modal -->
    <div v-if="showDialog" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center p-4 z-50">
      <div class="bg-white rounded-lg shadow-xl max-w-lg w-full p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">
          {{ isEditing ? "Edit Note" : "Create Note" }}
        </h3>
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700">Title</label>
            <input
              v-model="form.title"
              type="text"
              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Content</label>
            <textarea
              v-model="form.content"
              rows="6"
              class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3"
            ></textarea>
          </div>
        </div>
        <div class="mt-5 flex justify-end space-x-3">
          <button
            @click="showDialog = false"
            class="bg-gray-200 text-gray-800 px-4 py-2 rounded-md"
          >
            Cancel
          </button>
          <button
            @click="saveNote"
            class="bg-green-500 text-white px-4 py-2 rounded-md"
          >
            Save
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>