<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({ note: Object });

const showDialog = ref(false);
const form = ref({ id: props.note.id, title: props.note.title, content: props.note.content });

const openEditDialog = () => {
  showDialog.value = true;
};

const saveNote = () => {
  if (!form.value.title.trim()){
    alert('Title empty!');
    return;
  } else if (!form.value.content.trim()) {
    alert('Note empty!');
    return;
  };

  router.put(`/notes/${form.value.id}`, form.value, {
    onSuccess: () => showDialog.value = false 
  });
};

const deleteNote = () => {
  router.delete(`/notes/${props.note.id}`);
};

const goBack = () => {
  router.get('/notes');
};
</script>

<style scoped>
.full-content {
  white-space: pre-wrap;
  word-wrap: break-word; 
  overflow-wrap: break-word; 
}
</style>


<template>
  <v-container>
    <v-card>
      <v-card-title class="full-content">{{ note.title }}</v-card-title>
      <v-card-text>{{ note.content }}</v-card-text>
      <v-card-actions>
        <v-btn color="blue" @click="openEditDialog">Edit</v-btn>
        <v-btn color="red" @click="deleteNote">Delete</v-btn>
        <v-btn color="gray" @click="goBack">Back</v-btn>
      </v-card-actions>
    </v-card>

    <!-- Edit Note Dialog -->
    <v-dialog v-model="showDialog">
      <v-card>
        <v-card-title>Edit Note</v-card-title>
        <v-card-text>
          <v-text-field v-model="form.title" label="Title"></v-text-field>
          <v-textarea v-model="form.content" label="Content"></v-textarea>
        </v-card-text>
        <v-card-actions>
          <v-btn color="green" @click="saveNote">Save</v-btn>
          <v-btn color="gray" @click="showDialog = false">Cancel</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>