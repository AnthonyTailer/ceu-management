<template>
  <v-container fluid>
    
    <app-modal v-show="newOneApartament" :dialog="newOneApartament">
      <p slot="titleModal">Cadastro de novo Apartamento</p>
      <app-apartament slot="mainModal"></app-apartament>
      <v-btn class="white--text green accent-3" dark slot="footerModal" @click.stop.prevent="createApartamentEvent">Salvar</v-btn>
    </app-modal>
  
  
    <app-modal v-show="editApartament" :dialog="editApartament">
      <p slot="titleModal">Edição de Apartamento</p>
      <app-apartament slot="mainModal"></app-apartament>
      <v-btn class="white--text green accent-3" dark slot="footerModal" @click.stop.prevent="updateApartamentEvent()">Alterar</v-btn>
    </app-modal>
  
    <app-modal v-show="deleteApartament" :dialog="deleteApartament">
      <p slot="titleModal">Remover Apartamento</p>
      <p slot="mainModal">
        Você deseja mesmo remover este apartamento?
      </p>
      <v-btn class="white--text red accent-3" dark slot="footerModal" @click.native.stop="deleteApartamentEvent">Remover</v-btn>
    </app-modal>
    
    <app-modal-full v-if="newManyApartaments" :dialogFull="newManyApartaments" :loading="loading">
      <p slot="modalTitle activator">Cadastro de Apartamentos de um arquivo Excel</p>
      
      <vue-xlsx-table slot="mainContent" class="ml-2" @on-select-file="handleSelectedFile">
        <span id="btn-import-csv" class="d-flex align-center"><i class="material-icons">attachment</i> Selecione um arquivo Excel</span>
      </vue-xlsx-table>
      
    </app-modal-full>
    <v-layout row wrap>
      <v-flex xs12>
        <v-card id="apartamentsDatatable" >
          <v-toolbar class="blue lighten-1" extended>
            <v-card-title class="white--text headline"><v-icon dark>list</v-icon>&nbsp;Lista de todos Apartamentos</v-card-title>
            <v-fab-transition>
              <v-bottom-sheet v-model="sheet">
                <v-btn slot="activator" class="blue darken-4"
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
                    class="newApartamentList"
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
            <template slot="items" slot-scope="props">
              <td class="text-xs-left">{{ props.item.number }}</td>
              <td class="text-xs-left">{{ props.item.capacity }}</td>
              <td class="text-xs-left">{{ props.item.vacancy }}</td>
              <td class="text-xs-left">{{ props.item.block }}</td>
              <td class="text-xs-left">{{ props.item.building }}</td>
              <td class="text-xs-left">
                <div  class="ma-0 pa-0">
                  <v-btn class="green darken-1" fab dark small color="success" @click.stop="seeApto(props.item)">
                    <v-icon dark>zoom_in</v-icon>
                  </v-btn>
                  <v-btn class="blue darken-1" fab dark small color="primary" @click.target.prevent.stop="editApto(props.item)">
                    <v-icon dark>edit</v-icon>
                  </v-btn>
                  <v-btn class="red darken-1" fab dark small color="error" @click.native.stop="deleteApto(props.item)">
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
  
    <v-snackbar
      :timeout="8000"
      :error="snackError"
      :success="snackSuccess"
      :vertical="true"
      v-model="snackbar"
    >
      <div v-html="snackMsg"></div>
      <v-btn dark flat @click.native.stop="snackbar = false">Fechar</v-btn>
    </v-snackbar>
  </v-container>
</template>

<script>
  import Modal from './shared/Modal.vue'
  import ModalFull from './shared/ModalFull.vue'
  import ApartamentForm from './forms/ApartamentForm.vue'
  import { eventBus } from '../main'

  export default {
    created () {
      this.getApartaments()
    },
    mounted () {
      eventBus.listen('closeModal', (data) => {
        this.newOneApartament = data
        this.newManyApartaments = data
        this.deleteApartament = data
        this.editApartament = data
        this.apartamentData = []
        this.manyResponse = []
        this.loading = false
      })
      
      eventBus.listen('apartamentCreated',(data) => {
        this.snackbar = true
        this.snackMsg = data['message']
        this.snackSuccess = true
        this.snackError = false
        this.$validator.reset()
        let result = data['aptos']
        for (let i in result) {
          Object.assign(result[i], {'value': false })
        }
        this.datatable.items = result
      })

      eventBus.listen('apartamentUpdated',(data) => {
        this.snackbar = true
        this.snackMsg = data['message']
        this.snackSuccess = true
        this.snackError = false
        this.$validator.reset()
        let result = data['aptos']
        for (let i in result) {
          Object.assign(result[i], {'value': false })
        }
        this.datatable.items = result
      })

      eventBus.listen('apartamentDeleted',(data) => {
        this.snackbar = true
        this.snackMsg = data
        this.snackSuccess = true
        this.snackError = false
        this.$validator.reset()
        this.getApartaments()
      })
      
    },
    data () {
      return {
        sheet: false,
        loading: false,
        hidden: false,
        newOneApartament: false,
        editApartament: false,
        deleteApartament: false,
        apartamentData: '',
        aptos: [],
        apartamentsCsv: [],
        newManyApartaments: false,
        manyResponse: [
          {erro: '', status: false}
        ],
        tiles: [
          {icon: 'add_box', title: 'Um Apartamento', wichComponent: 'OneApartamentFom'},
          {icon: 'attachment', title: 'Importar de arquivo Excel', wichComponent: 'ManyApartamentFom'}
        ],
        selectedComponent: '',
        datatable: {
          tmp: '',
          search: '',
          no_data_text: 'Nenhum Apartamento disponível',
          no_found_text: 'Nenhum Apartamento encontrado com esse filtro',
          row_per_page_text: 'Aptos por página',
          row_per_page: [5, 15, 25, { text: 'Todos', value: -1 }],
          pagination: {},
          loading: true,
          items: [],
          headers: [
            {value: 'number', text: 'Apartamento', align: 'left', color: ''},
            {value: 'capacity', text: 'Capacidade', align: 'left'},
            {value: 'vacancy', text: 'Vagas', align: 'left'},
            {value: 'block', text: 'Bloco', align: 'left'},
            {value: 'building', text: 'Prédio', align: 'left'},
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
      getApartaments: function() {
        this.$http.get('api/apto/all?token='+ this.$auth.getToken())
          .then((response) => {
            console.log(response)
            this.datatable.loading = false
            let result = response.body.aptos
            for (let i in result) {
              Object.assign(result[i], {'value': false })
            }
            this.datatable.items = result
          })
      },
      createApartamentEvent: function() {
        eventBus.fire('createApartamentSubmit')
      },
      editApto: function(data) {
       
        this.editApartament = true
        console.log("Edit apartament -> ", data)
        let aux = JSON.parse( JSON.stringify( data ) )
        eventBus.fire('getApartamentData', aux)
      },
      updateApartamentEvent: function() {
        eventBus.fire('updateApartamentSubmit')
      },
      deleteApto: function(data) {
        this.deleteApartament = true
        console.log("Delete apartament -> ", data)
        let aux = JSON.parse( JSON.stringify( data ) )
        eventBus.fire('deleteApartamentData', aux)
      },
      deleteApartamentEvent: function() {
       
        eventBus.fire('deleteApartamentSubmit')
      },
      seeApto: function(data) {
        console.log("See Apartament -> ", data)
        this.$router.push(`/aptos/${data.number}`)
      },
      componentSelector (param) {
        this.selectedComponent = param
        if (this.selectedComponent === 'OneApartamentFom') {
          this.newOneApartament = true
        }
        if (this.selectedComponent === 'ManyApartamentFom') {
          this.newManyApartaments = true
        }
        this.sheet = false
      },
      handleSelectedFile (convertedData) {
        this.loading = true
        let obj = {}
        this.apartamentsCsv = []
        this.manyResponse = []
        eventBus.fire('alerts', this.manyResponse)

        convertedData.body.forEach((item, index) => {
          obj['number'] = item['Apartamento']
          obj['capacity'] = item['Capacidade']
          obj['vacancy'] = item['Vagas']
          obj['block'] = item['Bloco']
          obj['building'] = item['Prédio']
          this.apartamentsCsv.push(obj)
          obj = {}
        })

        this.$http.post('api/apto/bulk-register?token='+ this.$auth.getToken(),
          {body: this.apartamentsCsv}
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
              total_success_text: response.body.total_success + ' Apartamentos cadastrados com sucesso',
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
      appModal: Modal,
      appApartament: ApartamentForm,
      appModalFull: ModalFull
    }
  }
</script>

<style scoped>
  .newApartamentList {
    cursor: pointer;
  }
</style>
