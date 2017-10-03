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
          <!--<v-spacer></v-spacer>-->
          <!--<v-toolbar-items>-->
            <!--<v-btn dark flat @click.native="alert('TODO - salvar no banco')">Salvar</v-btn>-->
          <!--</v-toolbar-items>-->
        </v-toolbar>
        <v-divider></v-divider>
        <br>
        <slot name="mainContent"></slot>
        <br>
        <v-divider></v-divider>
        
        <div v-for="(err, key) in alertsError">
          <v-alert error dismissible transition="scale-transition" :value="err.status" @click="err.status = !err.status">
            <strong> A linha {{ key+1 }} do arquivo upado possui erro nos campos: </strong>
            <br>
            {{ err.erro.email }}
            {{ err.erro.registration }}
            {{ err.erro.cpf }}
            {{ err.erro.rg }}
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
      }
    },
    created () {
      eventBus.listen('alerts', (data) => {
        console.log(data)
        this.alertsError = data
      })
    },
    data () {
      return {
        alertsError: null,
        alert: true
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
