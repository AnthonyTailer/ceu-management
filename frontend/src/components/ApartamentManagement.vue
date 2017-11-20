<template>
  <v-container fluid>
    <v-layout>
      <app-modal :dialog="newApto" :type="'primary'">
        <span slot="titleModal" icon style="color: white"><v-icon>add_circle</v-icon> Cadastro de novo Apartamento</span>
        <app-apartament slot="mainModal"></app-apartament>
        <!--<v-btn class="white&#45;&#45;text green accent-3" dark slot="footerModal" @click.stop.prevent="createApartamentEvent">Salvar</v-btn>-->
      </app-modal>
      
      
      <app-modal :dialog="editApto" :type="'green accent-3'">
        <span slot="titleModal" icon style="color: white"><v-icon>add_circle</v-icon> Edição de Apartamento</span>
        <app-apartament slot="mainModal"></app-apartament>
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
                  <v-list-tile class="newAptoList"
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
            :items="allApartaments"
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
              <td class="text-xs-left">
                <v-chip v-if="props.item.vacancy > 0" class="green badge-card white--text">{{props.item.vacancy}}</v-chip>
                <v-chip v-else :class="'secondary badge-card white--text'">{{props.item.vacancy}}</v-chip>
              </td>
              <td class="text-xs-left">
                <div class="tooltip" v-if="props.item.vacancy_type == 'M'" >
                  <img src="/static/mars.png">
                  <span class="tooltiptext">Vagas Masculinas</span>
                </div>
                <div class="tooltip" v-else-if="props.item.vacancy_type == 'F'">
                  <img src="/static/venus.png">
                  <span class="tooltiptext">Vagas Femininas</span>
                </div>
                <div class="tooltip" v-else>
                  <img src="/static/genders.png">
                  <span class="tooltiptext">Vagas Mistas</span>
                </div>
              
              </td>
              <td class="text-xs-left">{{ props.item.block }}</td>
              <td class="text-xs-left">
                <div  class="ma-0 pa-0">
                  <v-btn class="green darken-1" fab dark small color="success" @click.stop="seeApto(props.item)">
                    <v-icon dark>zoom_in</v-icon>
                  </v-btn>
                  <v-btn class="blue darken-1" fab dark small color="primary" @click.target.prevent.stop="editApartament(props.item)">
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
  </v-container>
</template>

<script>
  import Modal from './shared/Modal.vue'
  import ModalFull from './shared/ModalFull.vue'
  import ApartamentForm from './forms/ApartamentForm.vue'
  import { eventBus } from '../main'

  export default {
    computed: {
      allApartaments: {
        get () {
          return this.$store.getters.getAllAptosState
        }
      },
      newApto: {
        get () {
          return this.$store.getters.getAptoNewState
        },
        set (value) {
          this.$store.dispatch('setAptoNewState', value)
        }
      },
      editApto: {
        get () {
          return this.$store.getters.getAptoEditState
        },
        set (value) {
          this.$store.dispatch('setAptoEditState', value)
        }
      },
      pages () {
        return this.datatable.pagination.rowsPerPage ? Math.ceil(this.datatable.items.length / this.datatable.pagination.rowsPerPage) : 0
      },
    },
    created () {
      this.getApartaments()

      eventBus.listen('closeModal', (data) => {
        this.newApto = data
        this.newManyApartaments = data
        this.editApto = data
        this.deleteApartament = data
        this.apartamentData = []
        this.manyResponse = []
        this.loading = false
      })
    },
    data () {
      return {
        sheet: false,
        loading: false,
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
            {value: 'vacancy_type', text: 'Tipo de Vagas', align: 'left'},
            {value: 'block', text: 'Bloco', align: 'left'},
            {value: 'actions', text: 'Ações', align: 'left'}
          ]
        }
      }
    },
    methods: {
      getApartaments: function() {
        this.datatable.loading = true
        this.$store.dispatch('setAllAptos').then( () => {
          this.datatable.loading = false
        })
      },
      editApartament: function(data) {

        this.editApto = true
        this.newApto = false

        console.log("Edit apartament -> ", data)
        let aux = JSON.parse( JSON.stringify( data ) )

        this.$store.dispatch('setEditApartament', aux)
      },
      deleteApto: function(data) {

        console.log("Delete apartament -> ", data)
        let aux = JSON.parse( JSON.stringify( data ) )
        //TODO delete apartament
      },
      seeApto: function(data) {
        console.log("See Apartament -> ", data)
        this.$router.push(`/aptos/${data.number}`)
      },
      componentSelector (param) {
        if (param === 'OneApartamentFom') {
          this.editApto = false
          this.newApto = true

        }
        if (param === 'ManyApartamentFom') {

          this.editApto = false
          this.newApto = false
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
