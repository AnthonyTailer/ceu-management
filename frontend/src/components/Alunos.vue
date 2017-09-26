<template>
  <main>
    <v-container>
      <div class="text-xs-right">
        <v-bottom-sheet v-model="sheet">
          <v-btn slot="activator" class="green" dark><i class="material-icons">add_circle</i>&nbsp;Novo Aluno</v-btn>
          <br>
          <v-list>
            <v-subheader>Escolha uma opção abaixo</v-subheader>
            <v-list-tile
              class="newStudentList"
              v-for="tile in tiles"
              :key="tile.title"
              @click.native.stop="componentSelector(tile.wichComponent)"
            >
              <v-list-tile-avatar>
                <v-avatar size="35px" tile>
                  <i :alt="tile.title" class="material-icons">{{tile.icon}}</i>
                </v-avatar>
              </v-list-tile-avatar>
              <v-list-tile-title>{{ tile.title }}</v-list-tile-title>
            </v-list-tile>
          </v-list>
        </v-bottom-sheet>
      </div>

      <app-modal :dialog="newOneStudent">
        <p slot="titleModal">Cadastro de novo Aluno</p>
        <app-one-student slot="mainModal"></app-one-student>
        <v-btn class="blue--text darken-1" flat slot="footerModal">Salvar</v-btn>
      </app-modal>

      <app-modal-full :dialogFull="newManyStudents">
        <p slot="modalTitle activator">Cadastro de Alunos de um arquivo Excel</p>
        <!--<app-many-students slot="mainModal"></app-many-students>-->
      </app-modal-full>

      <div id="people">
        <v-server-table url="api/students" :columns="columns" :options="options"></v-server-table>
      </div>
    </v-container>
  </main>
</template>

<script>
  import ModalForm from './shared/Modal.vue'
  import ModalFormFull from './shared/ModalFull.vue'
  import OneStudentModal from './forms/OneStudentForm.vue'
  import ManyStudentsModal from './forms/ManyStudentForm.vue'
  import { eventBus } from '../main'

  export default {
    created () {
      eventBus.$on('closeModal', (data) => {
        this.newOneStudent = data
        this.newManyStudents = data
      })
    },
    data () {
      return {
        sheet: false,
        newOneStudent: false,
        newManyStudents: false,
        tiles: [
          {icon: 'add_box', title: 'Um Aluno', wichComponent: 'OneStudentFom'},
          {icon: 'attachment', title: 'Importar de arquivo Excel', wichComponent: 'ManyStudentFom'}
        ],
        selectedComponent: '',
        tmp: '',
        search: '',
        pagination: {},
        headers: [
          {text: 'Nome', value: 'name'},
          {text: 'E-mail', value: 'email'},
          {text: 'Idade', value: 'age'},
          {text: 'Apartamento', value: 'apto'}
        ],
        items: [],
        columns: ['fullName', 'email', 'age', 'registration'],
        options: {
          responseAdapter (resp) {
            return {data: resp.body, count: 100}
          },
          requestFunction (data) {
            return this.$http.get('api/students/', {body: data})
              .catch(function (e) {
                this.dispatch('error', e)
              }.bind(this))
          }
        }
      }
    },
    methods: {
      componentSelector (param) {
        this.selectedComponent = param
        if (this.selectedComponent === 'OneStudentFom') {
          this.newOneStudent = true
        }
        if (this.selectedComponent === 'ManyStudentFom') {
          this.newManyStudents = true
        }
        this.sheet = false
      }
    },
    components: {
      appModal: ModalForm,
      appOneStudent: OneStudentModal,
      appModalFull: ModalFormFull,
      appManyStudents: ManyStudentsModal
    }
  }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
  .newStudentList {
    cursor: pointer;
  }
</style>
