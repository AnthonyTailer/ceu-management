<template>
  <form data-vv-scope="apto-user-form">
    <v-layout row wrap>
      <v-flex xs12 sm6 md6>
        <v-text-field
          class="input-group"
          v-model="studentProp.fullName"
          label="Aluno"
          disabled
        ></v-text-field>
      </v-flex>
      <v-flex x12 sm6 md6>
        <v-text-field
          class="input-group"
          v-model="aptoProp.number"
          label="Apartamento"
          disabled
        ></v-text-field>
      </v-flex>
    </v-layout>
    <v-layout row wrap>
      <v-flex xs12 md12>
        <v-subheader>Escolha uma opção</v-subheader>
        <v-card flat>
          <v-card-text>
            <v-radio-group v-model="option" row>
              <v-radio label="Trocar para apto com vaga" value="option-1" ></v-radio>
              <v-radio label="Trocar apartamento entre alunos" value="option-2"></v-radio>
            </v-radio-group>
          </v-card-text>
        </v-card>
      </v-flex>
    </v-layout>
    <v-layout row wrap>
      <v-flex xs12 md12 v-if="option === 'option-1'">
        <v-flex x12 sm6 md6>
          <v-select
            class="input-group"
            v-bind:items="vacancy_aptos"
            v-model="newApto"
            label="Novo Apartamento"
            autocomplete
          ></v-select>
        </v-flex>
      </v-flex>
      <v-flex xs12 md12 v-if="option === 'option-2'">
        <v-layout row wrap>
          <v-flex x12 sm6 md6>
            <v-select
              class="input-group"
              v-bind:items="aptos"
              v-model="newApto2"
              label="Apartamento"
              autocomplete
              clearable
              cache-items
            ></v-select>
          </v-flex>
          <v-flex x12 sm6 md6>
            <v-select
              class="input-group"
              v-bind:items="studentsFromApto"
              v-model="newStudent"
              label="Aluno"
              autocomplete
              clearable
              cache-items
              :disabled="!selectedApto"
            ></v-select>
          </v-flex>
        </v-layout>
      </v-flex>
    </v-layout>
    <v-divider></v-divider>
    <v-card-actions>
      <v-spacer></v-spacer>
      <v-btn class="grey lighten-1 black--text" dark @click.prevent="closeModal()">Fechar</v-btn>
      <v-btn class="white--text success accent-3" dark @click.prevent.stop="switchStudentsApto()">Trocar</v-btn>
    </v-card-actions>
  </form>
</template>
<script>
  import { eventBus } from '../../main'

  export default {
    $validate: true,
    props: ['student', 'apto'],
    computed: {
      studentProp () {
        return JSON.parse(JSON.stringify(this.student))
      },

      aptoProp () {
        return JSON.parse(JSON.stringify(this.apto))
      }
    },
    watch: {
      option (value) {
        if( value === 'option-1'){
          this.getVacancyAptos()
        }else if (value === 'option-2') {
          this.getAptos()
        }
        else{

        }
      },

      newApto2 (value) {
        this.selectedApto = true
        this.getStudentsFromApto(value.id)
      }
    },
    mounted () {
      
      eventBus.listen('closeModal', (data) => {
        this.option = data
        this.newApto = ''
        this.newApto2 = ''
        this.newStudent = ''
        this.no_vacancy_aptos = []
        this.aptos = []
        this.vacancy_aptos = []
        this.studentsFromApto = []
        this.selectedApto = false
        this.$validator.reset()
      })

    },
    data () {
      return {
        option: '',
        selectedStudent: {},
        selectedApto: false,
        newApto: '',
        newApto2: '',
        newStudent: '',
        aptos: [],
        vacancy_aptos: [],
        studentsFromApto: []
      }
    },
    methods: {
      switchStudentsApto () {
        this.$validator.validateAll().then(result => {
          if (!result) {
            console.log('Switch Students Apto -> validation failed.')
          } else {
            console.log('Switch Students Apto Submit')
            this.$validator.reset()

            let data = {}

            if(this.option === 'option-1'){
              data = {
                option: 'option-1',
                id: this.studentProp.id,
                id_apto: this.aptoProp.id,
                new_id_apto: this.newApto.id
              }
            }else if(this.option === 'option-2') {
              data = {
                option: 'option-2',
                id: this.studentProp.id,
                id_apto: this.aptoProp.id,
                new_id_apto: this.newApto2.id,
                new_student: this.newStudent.id
              }
            }else {
              data = {
                option: false,
                id: this.studentProp.id,
                id_apto: this.aptoProp.id
              }
            }

            this.$http.put(`api/user/apto/change?token=`+ this.$auth.getToken(), data)
              .then( (response) => {

                let snack = {
                  activator: true,
                  error: false,
                  success: true,
                  msg: response.body.message
                }

                this.$store.commit('setSnack', snack)

                eventBus.closeModal(true)
                eventBus.fire('studentSwitchedApt')

              }).catch( (response) =>  {

              let msg = ' '

              if( response.body.errors ){
                for( let i in response.body.errors){
                  response.body.errors[i].forEach( (item) => {
                    msg += item + '<br>'
                  })
                }

              }else {
                msg = response.body.message
              }

              let snack = {
                activator: true,
                error: true,
                success: false,
                msg: response.body.message
              }

              this.$store.commit('setSnack', snack)
            })
          }
        }).catch( () => {
          console.log('something went wrong (non-validation related')
        })
      },
      getVacancyAptos () {
        this.$http.get('api/apto?token='+ this.$auth.getToken()).then( (response) => {
          for( let i in response.body.aptos) {
            this.vacancy_aptos.push({
              'text': response.body.aptos[i]['number'] + ' - ' + response.body.aptos[i]['vacancy'] + ' vagas',
              'id': response.body.aptos[i]['id']
            })
          }
        })
      },
      getAptos () {
        this.$http.get('api/apto/with-students?token='+ this.$auth.getToken()).then( (response) => {
          for( let i in response.body.aptos) {
            this.aptos.push({
              'text': response.body.aptos[i]['number'] + ' - ' + (response.body.aptos[i]['capacity'] - response.body.aptos[i]['vacancy']) + ' moradores',
              'id': response.body.aptos[i]['id']
            })
          }
        })
      },
      getStudentsFromApto (apto) {
        this.studentsFromApto = []
        this.$http.get(`api/users/from/${apto}?token=`+ this.$auth.getToken()).then( (response) => {
          for( let i in response.body.data) {
            this.studentsFromApto.push({
              'text': response.body.data[i]['fullName'],
              'id': response.body.data[i]['id']
            })
          }
        })
      },
      closeModal(){
        eventBus.closeModal(true)
      }
    }
  }
</script>
<style>

</style>
