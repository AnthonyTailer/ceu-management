<template>
  <v-layout row justify-center>
    <v-dialog v-model="dialogFull" fullscreen transition="dialog-bottom-transition" :overlay=false  >
      <v-card>
        <v-toolbar dark class="primary">
          <slot name="closeModalBtn" :disabled="loading" @click.native="closeModalFull"></slot>
          <v-toolbar-title>
            <slot name="modalTitle"></slot>
          </v-toolbar-title>
        </v-toolbar>
        <v-divider></v-divider>
        <br>
        <slot name="mainContent" v-show="!loading"></slot>
        <br>
        <br>
        <v-divider></v-divider>
  
        <div class="text-xs-center">
          <br>
          <v-progress-circular slot="loader" v-show="loading" indeterminate v-bind:size="70" v-bind:width="7" class="primary--text"></v-progress-circular>
        </div>
        
        <div v-for="(err, key) in alertsError">
          <v-alert v-if="err.total_success > 0" success dismissible transition="scale-transition" v-model="err.status">
            {{ err.total_success_text }}
          </v-alert>
          
          <v-alert v-if="err.total_erros > 0" warning dismissible transition="scale-transition" v-model="err.status">
            {{ err.total_erros_text }}
          </v-alert>
          
          <v-alert v-if="err.erro" error dismissible transition="scale-transition" v-model="err.status">
            <strong> A linha {{ key }} do arquivo upado possui erro nos campos: </strong>
            <br>
            {{ err.erro.email }}
            {{ err.erro.registration }}
            {{ err.erro.cpf }}
            {{ err.erro.rg }}
            {{ err.erro.genre }}
            {{ err.erro.id_course }}
            {{ err.erro.id_apto }}
            {{ err.erro.is_admin }}
            {{ err.erro.is_bse_active }}
          </v-alert>
        </div>
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
      },
      loading: {
        type: Boolean
      }
    },
    updated () {
      eventBus.listen('alerts', (data) => {
        this.alertsError = data
      })
    },
    data () {
      return {
        alertsError: null
      }
    },
    watch: {
      dialogFull (value) {
        console.log('DialogFull -> ' + value)
      }
    },
    methods: {
      closeModalFull () {
        eventBus.fire('closeModal', !this.dialogFull)
      }
    }
  }
</script>
<style>
</style>
