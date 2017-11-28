<template>
  <v-container fluid>
    
    <app-modal :dialog="newOneStudent" :type="'primary'">
      <span slot="titleModal" icon style="color: white"><v-icon>add_circle</v-icon> Cadastro de novo Aluno</span>
      <app-student slot="mainModal" :courses="courses" :aptos="aptos"></app-student>
    </app-modal>
  
    <app-modal  :dialog="editStudent" :type="'green accent-3'">
      <span slot="titleModal" icon style="color: white"><v-icon>create</v-icon> Edição de Aluno</span>
      <app-student slot="mainModal" :courses="courses" :aptos="aptos"></app-student>
    </app-modal>
    
    <app-modal v-if="seeStudent" :dialog="seeStudent" :type="'primary'">
      <span slot="titleModal" icon style="color: white">Mais informações do Aluno</span>
      <p slot="mainModal">
        <strong>Nome Completo</strong>: {{ studentData['fullName'] }}<br>
        <strong>E-mail</strong>: {{ studentData['email']}}<br>
        <strong>Matrícula</strong>: {{ studentData['registration']}}<br>
        <strong>Curso</strong>: {{ studentData['course']['courseName']}}<br>
        <strong>CPF</strong>: {{ studentData['cpf']}}<br>
        <strong>RG</strong>: {{ studentData['rg']}}<br>
        <strong>Tem Benefício </strong>? {{ studentData['is_bse_active'] !== null &&  studentData['is_bse_active'] === 1 ? 'Sim' : 'Não' }}<br>
        <strong>É da Diretoria </strong>? {{ studentData['is_admin'] !== null &&  studentData['is_admin'] === 1  ? 'Sim' : 'Não' }}<br>
        <strong>Apartamento</strong>: {{ studentData['apto'] !== null ?  studentData['apto']['number'] : 'Nenhum Apartamento associado'}}<br>
        <v-divider></v-divider>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn class="grey lighten-1 black--text" dark @click.prevent="seeStudent = !seeStudent">Fechar</v-btn>
        </v-card-actions>
      </p>
    </app-modal>
    
    <app-modal :dialog="deleteStudent">
      <p slot="titleModal">Remover Aluno</p>
      <p slot="mainModal">
        Você deseja mesmo remover este usuário:?
      </p>
      <v-btn class="white--text red accent-3" dark slot="footerModal" @click.prevent="deleteUserEvent">Remover</v-btn>
    </app-modal>
  
    <app-modal :dialog="deleteStudent" :type="'red accent-3'">
        <span icon slot="titleModal" style="color: white">
          <v-icon>warning</v-icon> Remover Aluno <strong>{{ this.$store.getters.getStudentState.fullName }}</strong>
        </span>
      <div slot="mainModal">
        <h5>Você deseja mesmo remover o Aluno {{ this.$store.getters.getStudentState.fullName }} ?</h5>
        <v-divider></v-divider>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn class="white--text red accent-3" dark
                 @click.prevent.stop="removeStudentEvent"
          >Remover</v-btn>
          <v-btn class="grey lighten-1 black--text" dark @click.prevent="deleteStudent = !deleteStudent">Fechar</v-btn>
        </v-card-actions>
      </div>
    </app-modal>
    
    <app-modal-full v-if="newManyStudents" :dialogFull="newManyStudents" :loading="loading">
      <p slot="modalTitle activator">Cadastro de Alunos de um arquivo Excel</p>
      <v-btn slot="closeModalBtn" icon dark>
        <v-icon>close</v-icon>
      </v-btn>
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
            <v-spacer></v-spacer>
            <v-fab-transition>
              <v-bottom-sheet v-model="sheet">
                <v-btn
                  slot="activator"
                  class="teal darken-4"
                  dark
                  fab
                  absolute
                  right
                  bottom
                  icon
                >
                  <v-icon>add</v-icon>
                </v-btn>
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
            :items="allStudents"
            :pagination.sync="datatable.pagination"
            :loading="datatable.loading"
            :no-data-text="datatable.no_data_text"
            :no-results-text="datatable.no_found_text"
            :rows-per-page-items="datatable.row_per_page"
            :rows-per-page-text	="datatable.row_per_page_text"
            :search="datatable.search"
            class="elevation-1"
          >
            <template slot="items" slot-scope="props">
              <td class="text-xs-left">{{ props.item.fullName }}</td>
              <td class="text-xs-left">{{ props.item.email }}</td>
              <td class="text-xs-left">{{ props.item.registration }}</td>
              <td class="text-xs-left">{{ props.item.updated_at }}</td>
              <td class="text-xs-left">
                <div  class="ma-0 pa-0">
                  <v-btn class="green darken-1" fab dark small color="success" @click.stop="seeUser(props.item)">
                    <v-icon dark>zoom_in</v-icon>
                  </v-btn>
                  <v-btn class="blue darken-1" fab dark small color="primary" @click.stop.prevent="editUser(props.item)">
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
  import Modal from './shared/Modal.vue'
  import ModalFull from './shared/ModalFull.vue'
  import StudentForm from './forms/StudentForm.vue'
  import { eventBus } from '../main'
  import { store } from '../store/store'
  import { mapMutations } from 'vuex'
  import { mapGetters } from 'vuex'

  export default {
    computed: {
      allStudents: {
        get () {
          return this.$store.getters.getAllStudentsState
        }
      },
      newOneStudent: {
        get () {
          return this.$store.getters.getStudentNewState
        },
        set (value) {
          this.$store.dispatch('setStudentNewState', value)
        }
      },
      editStudent: {
        get () {
          return this.$store.getters.getStudentEditState
        },
        set (value) {
          this.$store.dispatch('setStudentEditState', value)
        }
      },
      pages () {
        return this.datatable.pagination.rowsPerPage ? Math.ceil(this.datatable.items.length / this.datatable.pagination.rowsPerPage) : 0
      }
    },
    components: {
      appModal: Modal,
      appStudent: StudentForm,
      appModalFull: ModalFull
    },
    created () {
      this.getStudents()
      this.getCourses()
      this.getAptos()
    },
    mounted () {
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
        deleteStudent: false,
        seeStudent: false,
        studentData: '',
        courses: [],
        aptos: [],
        studentsCsv: [],
        newManyStudents: false,
        manyResponse: [
          {erro: '', status: false}
        ],
        tiles: [
          {icon: 'add_box', title: 'Cadastrar 1 Aluno', wichComponent: 'OneStudentFom'},
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
            {value: 'updated_at', text: 'Última atualização', align: 'left'},
            {value: 'actions', text: 'Ações', align: 'left'}
          ]
        },
        snackbar: false,
        snackError: false,
        snackSuccess: false,
        snackMsg: ''
      }
    },
    methods: {
      getStudents: function() {
        this.datatable.loading = true
        this.$store.dispatch('setAllStudents').then( () => {
          this.datatable.loading = false
        })
      },
      getCourses: function () {
        this.$http.get('api/courses?token='+ this.$auth.getToken()).then((response) => {
          console.log(response)
          for (let i in response.body.courses) {
            this.courses.push({'text': response.body.courses[i]['courseName'], 'id': response.body.courses[i]['id']})
          }
        })
      },
      getAptos () {
        this.$http.get('api/apto/all?token='+ this.$auth.getToken()).then((response) => {
          console.log(response)
          for (let i in response.body.aptos) {
            this.aptos.push({'text' : response.body.aptos[i]['number'] , 'id': response.body.aptos[i]['id']})
          }
        })
      },
      seeUser (data) {
        console.log("See User -> ", data)
        this.seeStudent = true
        this.studentData = data
      },
      editUser: function(data) {

        this.editStudent = true
        this.seeStudent = false
        this.deleteStudent = false

        console.log("Edit Student -> ", data)
        let aux = JSON.parse( JSON.stringify( data ) )

        this.$store.dispatch('setEditStudent', aux)
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
          obj['id_course'] = item['Curso']
          obj['age'] = item['Idade']
          obj['genre'] = item['Gênero']
          obj['email'] = item['E-mail']
          obj['registration'] = item['Matrícula']
          obj['fullName'] = item['Nome Completo']
          obj['rg'] = item['RG']
          obj['phone'] = item['Telefone']
          obj['is_bse_active'] = item['BSE Ativo']
          obj['id_apto'] = item['Apartamento']
          obj['is_admin'] = item['Diretoria'] !== null ? item['Diretoria'] : false
          this.studentsCsv.push(obj)
          obj = {}
        })
        
        console.log(this.studentsCsv)
        debugger

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
   
  }
</script>

<style scoped>
  .newStudentList {
    cursor: pointer;
  }
</style>
