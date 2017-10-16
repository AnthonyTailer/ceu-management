<template>
  <v-container fluid>
    
    <app-modal :dialog="newOneStudent">
      <p slot="titleModal">Cadastro de novo Aluno</p>
      <app-one-student slot="mainModal"></app-one-student>
      <v-btn class="white--text green accent-3" dark slot="footerModal" @click.prevent="createUser">Salvar</v-btn>
    </app-modal>
  
    <app-modal :dialog="seeStudent">
      <p slot="titleModal">Mais informações do Aluno</p>
      <p slot="mainModal">
          <strong>Nome Completo</strong>: {{this.studentData['fullName']}}<br>
          <strong>E-mail</strong>: {{this.studentData['email']}}<br>
          <strong>Matrícula</strong>: {{this.studentData['registration']}}<br>
          <strong>CPF</strong>: {{this.studentData['cpf']}}<br>
          <strong>RG</strong>: {{this.studentData['rg']}}<br>
          <strong>Tem Benefício</strong>: {{this.studentData['is_bse_active'] !== null && this.studentData['is_bse_active'] === 1 ? 'Sim' : 'Não' }}<br>
          <strong>É da Diretoria</strong>: {{this.studentData['is_admin'] !== null && this.studentData['is_admin'] === 1  ? 'Sim' : 'Não' }}<br>
      </p>
    </app-modal>
  
    <app-modal :dialog="editStudent">
      <p slot="titleModal">Edição de Aluno</p>
      <app-one-student slot="mainModal"></app-one-student>
      <v-btn class="white--text green accent-3" dark slot="footerModal" @click.native="alert()">Alterar</v-btn>
    </app-modal>
    
    <app-modal-full :dialogFull="newManyStudents" :loading="loading">
      <p slot="modalTitle activator">Cadastro de Alunos de um arquivo Excel</p>
      
      <vue-xlsx-table slot="mainContent" class="ml-2" @on-select-file="handleSelectedFile">
        <span id="btn-import-csv" class="d-flex align-center"><i class="material-icons">attachment</i> Selecione um arquivo Excel</span>
      </vue-xlsx-table>
      
    </app-modal-full>
    <v-layout row wrap>
      <v-flex xs12>
        <v-card id="studentsDatatable" >
          <!--<v-server-table url="api/users" :columns="columns" :options="options"></v-server-table>-->
          <v-toolbar class="teal lighten-1" extended>
            <v-card-title class="white--text headline"><v-icon dark>list</v-icon>&nbsp;Lista de todos os Moradores da CEU</v-card-title>
            <v-fab-transition>
              <v-bottom-sheet v-model="sheet">
                <v-btn slot="activator" class="teal darken-4"
                       dark
                       fab
                       absolute
                       right
                       bottom>
                  <v-icon>add</v-icon>
                </v-btn>
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
            </v-fab-transition>
          </v-toolbar>
          <v-card-title>
            <v-text-field
              append-icon="search"
              label="Pesquisar"
              single-line
              hide-details
              v-model="datatable.search"
            ></v-text-field>
            <v-spacer></v-spacer>
          </v-card-title>
          <v-data-table
            :headers="datatable.headers"
            :items="datatable.items"
            :pagination.sync="datatable.pagination"
            :loading="datatable.loading"
            :no-data-text="datatable.no_data_text"
            :no-results-text="datatable.no_found_text"
            :rows-per-page-items="datatable.row_per_page"
            :rows-per-page-text	="datatable.row_per_page_text"
            :search="datatable.search"
            class="elevation-1"
          >
            <template slot="items" scope="props">
              <td class="text-xs-left">{{ props.item.fullName }}</td>
              <td class="text-xs-left">{{ props.item.email }}</td>
              <td class="text-xs-left">{{ props.item.registration }}</td>
              <td class="text-xs-left">
                <div  class="ma-0 pa-0">
                  <v-btn class="green darken-1" fab dark small color="success" @click.stop="seeUser(props.item)">
                    <v-icon dark>zoom_in</v-icon>
                  </v-btn>
                  <v-btn class="blue darken-1" fab dark small color="primary" @click.stop="editUser(props.item)">
                    <v-icon dark>edit</v-icon>
                  </v-btn>
                  <v-btn class="red darken-1" fab dark small color="error" @click.stop="deleteUser(props.item)">
                    <v-icon dark>delete_forever</v-icon>
                  </v-btn>
                </div>
              </td>
            </template>
          </v-data-table>
        </v-card>
        <div class="text-xs-center pt-2">
          <v-pagination v-model="datatable.pagination.page" :length="pages"></v-pagination>
        </div>
      </v-flex>
    </v-layout>
  </v-container>
</template>

<script>
  import ModalForm from './shared/Modal.vue'
  import ModalFormFull from './shared/ModalFull.vue'
  import OneStudentModal from './forms/OneStudentForm.vue'
  import { eventBus } from '../main'

  export default {
    created () {
      this.getUsers()
    },
    updated () {
      eventBus.listen('closeModal', (data) => {
        this.newOneStudent = data
        this.seeStudent = data
        this.deleteStudent = data
        this.editStudent = data
        this.studentData = []
        this.newManyStudents = data
        this.manyResponse = []
        this.loading = false
      })
    },
    data () {
      return {
        sheet: false,
        loading: false,
        hidden: false,
        newOneStudent: false,
        editStudent: false,
        deleteStudent: false,
        seeStudent: false,
        studentData: '',
        studentsCsv: [],
        newManyStudents: false,
        manyResponse: [
          {erro: '', status: false}
        ],
        tiles: [
          {icon: 'add_box', title: 'Um Aluno', wichComponent: 'OneStudentFom'},
          {icon: 'attachment', title: 'Importar de arquivo Excel', wichComponent: 'ManyStudentFom'}
        ],
        selectedComponent: '',
        datatable: {
          tmp: '',
          search: '',
          no_data_text: 'Não Existe nenhum Aluno cadastrado ainda',
          no_found_text: 'Nenhum Aluno encontrado com esse filtro',
          row_per_page_text: 'Alunos por página',
          row_per_page: [5, 15, 25, { text: 'Todos', value: -1 }],
          pagination: {},
          loading: true,
          items: [],
          headers: [
            {value: 'fullName', text: 'Nome Completo', align: 'left', color: ''},
            {value: 'email', text: 'E-mail', align: 'left'},
            {value: 'registration', text: 'Matrícula', align: 'left'},
            {value: 'actions', text: 'Ações', align: 'left'}
          ]
        }
      }
    },
    methods: {
      getUsers () {
        this.$http.get('api/users?token='+ this.$auth.getToken())
          .then(response => {
            console.log(response)
            this.datatable.loading = false
            let result = response.body.data
            for (let i = 0; i < result.length; i++) {
              Object.assign(result[i], {'value': false })
            }
            this.datatable.items = result
            console.log(this.datatable.items)
          })
      },
      createUser () {
        eventBus.fire('createUser')
      },
      editUser (data) {
        console.log("Edit delete -> ", data)
        eventBus.fire('getUserData', data)
        this.editStudent = true
      },
      deleteUser (data) {
        console.log("Delete User -> ", data)
        
      },
      seeUser (data) {
        console.log("See User -> ", data)
        this.seeStudent = true
        this.studentData = data
      },
      componentSelector (param) {
        this.selectedComponent = param
        if (this.selectedComponent === 'OneStudentFom') {
          this.newOneStudent = true
        }
        if (this.selectedComponent === 'ManyStudentFom') {
          this.newManyStudents = true
        }
        this.sheet = false
      },
      handleSelectedFile (convertedData) {
        this.loading = true
        let obj = {}
        this.studentsCsv = []
        this.manyResponse = []
        eventBus.fire('alerts', this.manyResponse)

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

        this.$http.post('api/users/register?token='+ this.$auth.getToken(),
          {body: this.studentsCsv}
        ).then(response => {
          this.loading = false
          console.log(response)
          if (response.body !== null) {
            this.manyResponse.push({ // erros totais
              total_erros_text: 'Atenção! ' + response.body.total_erros + ' registros falharam no cadastro',
              total_erros: response.body.total_erros,
              status: true
            })

            this.manyResponse.push({ // todos com sucessos
              total_success_text: response.body.total_success + ' Alunos cadastrados com sucesso',
              total_success: response.body.total_success,
              status: true
            })

            for (let i in response.body.erros) {
              this.manyResponse.push({
                erro: response.body.erros[i],
                status: true
              })
            }
            eventBus.fire('alerts', this.manyResponse)
          }
        }).then(e => {
          console.log(e)
          this.loading = false
          this.manyResponse.push({
            erro: 'Erro inesperado tente novamente',
            status: true
          })
          eventBus.fire('alerts', this.manyResponse)
        })
      }
    },
    computed: {
      pages () {
        return this.datatable.pagination.rowsPerPage ? Math.ceil(this.datatable.items.length / this.datatable.pagination.rowsPerPage) : 0
      }
    },
    components: {
      appModal: ModalForm,
      appOneStudent: OneStudentModal,
      appModalFull: ModalFormFull
    }
  }
</script>

<style scoped>
  .newStudentList {
    cursor: pointer;
  }
</style>
