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
        <app-many-students :students="studentsCsv"></app-many-students>
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
    data () {
      return {
        studentsCsv: []
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
        let obj = {}
        this.studentsCsv = []
        convertedData.body.forEach((item, index) => {
          obj['cpf'] = item['CPF']
          obj['corse'] = item['Curso']
          obj['age'] = item['Idade']
          obj['registration'] = item['Matrícula']
          obj['fullName'] = item['Nome Completo']
          obj['rg'] = item['RG']
          obj['phone'] = item['Telefone']
          obj['email'] = item['email']
          this.studentsCsv.push(obj)
          obj = {}
        })

        this.$http.post('api/users/register',
          {body: this.studentsCsv}
        ).then(response => {
          console.log(response)
        })
      }
    }
  }
</script>
<style>
</style>
