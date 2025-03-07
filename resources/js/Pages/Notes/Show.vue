<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Swal from 'sweetalert2';

const props = defineProps({
  note: Object
});

const showEditDialog = ref(false);
const form = ref({
  id: props.note.id,
  title: props.note.title,
  content: props.note.content
});

const editNote = () => {
  showEditDialog.value = true;
};

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

  router.put(`/notes/${props.note.id}`, form.value, {
    onSuccess: () => {
      showEditDialog.value = false;
      Swal.fire({
        title: 'Success!',
        text: 'Note updated successfully',
        icon: 'success',
        timer: 1500
      });
    }
  });
};

const deleteNote = () => {
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
      router.delete(`/notes/${props.note.id}`, {
        onSuccess: () => {
          Swal.fire(
            'Deleted!',
            'Your note has been deleted.',
            'success'
            ).then(() => {
            // Redirect to dashboard
            router.push({ name: 'dashboard' });  
          });
        }
      });
    }
  });
};

const goBack = () => {
  router.get('/dashboard');
};
</script>

<template>
  <AppLayout title="View Note">
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Note Details
        </h2>
        <button 
          @click="goBack" 
          class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-md transition"
        >
          Back to Dashboard
        </button>
      </div>
    </template>

    <Head :title="note.title" />

    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
          <div class="mb-4">
            <h1 class="text-2xl font-bold text-gray-900">{{ note.title }}</h1>
            <div class="mt-2 text-sm text-gray-500">
              Created: {{ new Date(note.created_at).toLocaleString() }}
              <span v-if="note.updated_at !== note.created_at">
                (Updated: {{ new Date(note.updated_at).toLocaleString() }})
              </span>
            </div>
          </div>
          
          <div class="border-t border-gray-200 pt-4 mt-4">
            <div class="prose max-w-none">
              <p class="whitespace-pre-wrap">{{ note.content }}</p>
            </div>
          </div>

          <div class="mt-8 flex justify-end          space-x-4">
            <button
              @click="editNote"
              class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md transition"
            >
              Edit Note
            </button>
            <button
              @click="deleteNote"
              class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md transition"
            >
              Delete Note
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Edit Note Modal -->
    <div v-if="showEditDialog" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center p-4 z-50">
      <div class="bg-white rounded-lg shadow-xl max-w-lg w-full p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">
          Edit Note
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
            @click="showEditDialog = false"
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