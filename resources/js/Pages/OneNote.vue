<template>
  <v-container>
    <v-card>
      <v-card-title>{{ note.title }}</v-card-title>
      <v-card-text>{{ note.content }}</v-card-text>
      <v-card-actions>
        <v-btn color="blue" @click="editNote">Edit</v-btn>
        <v-btn color="red" @click="deleteNote">Delete</v-btn>
        <v-btn color="gray" @click="goBack">Back</v-btn>
      </v-card-actions>
    </v-card>
  </v-container>
</template>

<script setup>
// import { defineProps } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({ note: Object });

const editNote = () => {
  router.put(`/notes/${props.note.id}`, props.note);
};

const deleteNote = () => {
  router.delete(`/notes/${props.note.id}`);
};

const goBack = () => {
  router.get('/notes');
};
</script>
