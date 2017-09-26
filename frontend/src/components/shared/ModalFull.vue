<template>
  <v-layout row justify-center>
    <v-dialog v-model="dialogFull" fullscreen transition="dialog-bottom-transition" :overlay=false  >
      <v-card>
        <v-toolbar dark class="primary">
          <v-btn icon @click.native="closeModalFull" dark>
            <v-icon>close</v-icon>
          </v-btn>
          <v-toolbar-title>
            <slot name="modalTitle"></slot>
          </v-toolbar-title>
          <v-spacer></v-spacer>
          <v-toolbar-items>
            <v-btn dark flat @click.native="alert('TODO - salvar no banco')">Salvar</v-btn>
          </v-toolbar-items>
        </v-toolbar>
        <v-divider></v-divider>
        <br>
        <vue-xlsx-table class="ml-2" @on-select-file="handleSelectedFile">
          <span class="d-flex align-center"><i class="material-icons">attachment</i> Selecione um arquivo Excel</span>
        </vue-xlsx-table>
        <slot name="modalContent"></slot>

      </v-card>
    </v-dialog>
  </v-layout>
</template>
<script>
  import { eventBus } from '../../main'
  export default {
    props: {
      dialogFull: {
        type: Boolean
      }
    },
    watch: {
      dialogFull (value) {
        // this.modal = value
        console.log('DialogFull -> ' + value)
      }
    },
    methods: {
      closeModalFull () {
        eventBus.closeModal(this.dialogFull)
      },
      handleSelectedFile (convertedData) {
        console.log(convertedData)
      }
    }
  }
</script>
<style>
</style>
