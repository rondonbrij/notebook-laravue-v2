<template>
  <v-container>
    <v-btn @click="showCreateDialog = true" color="primary">Add Note</v-btn>
    
    <v-row>
      <v-col v-for="note in notes" :key="note.id" cols="12" md="4">
        <v-card @click="openNote(note)">
          <v-card-title>{{ note.title }}</v-card-title>
          <v-card-text>{{ note.content }}</v-card-text>
          <v-card-actions>
            <v-btn color="blue" @click.stop="editNote(note)">Edit</v-btn>
            <v-btn color="red" @click.stop="deleteNote(note.id)">Delete</v-btn>
          </v-card-actions>
        </v-card>
      </v-col>
    </v-row>

    <!-- Create & Edit Dialog -->
    <v-dialog v-model="showDialog">
      <v-card>
        <v-card-title>{{ isEditing ? "Edit Note" : "Create Note" }}</v-card-title>
        <v-card-text>
          <v-text-field v-model="form.title" label="Title"></v-text-field>
          <v-textarea v-model="form.content" label="Content"></v-textarea>
        </v-card-text>
        <v-card-actions>
          <v-btn @click="saveNote" color="green">Save</v-btn>
          <v-btn @click="showDialog = false" color="gray">Cancel</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

defineProps({ notes: Array });

const showDialog = ref(false);
const isEditing = ref(false);
const form = ref({ id: null, title: "", content: "" });

const showCreateDialog = () => {
  form.value = { id: null, title: "", content: "" };
  isEditing.value = false;
  showDialog.value = true;
};

const openNote = (note) => {
  // Redirect to NoteDetail.vue
  router.get(`/notes/${note.id}`);
};

const editNote = (note) => {
  form.value = { ...note };
  isEditing.value = true;
  showDialog.value = true;
};

const saveNote = () => {
  if (isEditing.value) {
    router.put(`/notes/${form.value.id}`, form.value);
  } else {
    router.post('/notes', form.value);
  }
  showDialog.value = false;
};

const deleteNote = (id) => {
  router.delete(`/notes/${id}`);
};
</script>
