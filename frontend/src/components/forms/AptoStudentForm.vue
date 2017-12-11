<template>
    <form data-vv-scope="apto-user-form">
      <v-layout row wrap>
        <v-flex xs12 sm12 md12>
          <v-select
            class="input-group"
            :items="studentInput"
            v-model="student"
            v-validate.initial="'required'"
            :error-messages="errors.collect('aluno')"
            data-vv-name="aluno"
            label="Aluno"
            autocomplete
            clearable
            cache-items
            required
          ></v-select>
        </v-flex>
      </v-layout>
      <v-layout row wrap v-if="Object.keys(selectedStudent).length > 0 && selectedStudent.constructor === Object">
        <v-layout row wrap>
          <v-flex x12 sm6 md6>
            <b>Matrícula</b>: {{selectedStudent.registration}}
          </v-flex>
          <v-flex x12 sm6 md6>
            <b>Curso</b>: {{selectedStudent.course.courseName}}
          </v-flex>
        </v-layout>
        <v-layout row wrap>
          <v-flex x12 sm6 md6>
            <b>RG</b>: {{selectedStudent.rg}}
          </v-flex>
          <v-flex x12 sm6 md6>
            <b>CPF</b>: {{selectedStudent.cpf}}
          </v-flex>
        </v-layout>
      </v-layout>
      <v-layout row wrap v-else>
        <v-flex x12 sm12 md12 class="text-xs-center">
          <h6>Selecione um Aluno acima</h6>
        </v-flex>
      </v-layout>
      <v-divider></v-divider>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn class="grey lighten-1 black--text" dark @click.prevent="closeModal()">Fechar</v-btn>
        <v-btn class="white--text success accent-3" dark @click.prevent.stop="addStudentToApto(selectedStudent)">Adicionar</v-btn>
      </v-card-actions>
    </form>
</template>
<script>
  import { eventBus } from '../../main'

  export default {
    $validate: true,
    props: ['students', 'apto'],
    computed: {
      studentInput () {
        let allStudents = this.students
        let result = []
        for (let i in allStudents){
          result.push(allStudents[i].fullName)
        }
        return result
      }
    },
    watch: {
      student (value){
        for (let i in this.students){
          if (this.students[i].fullName === value)
            this.selectedStudent = this.students[i]
        }
      }
    },
    updated () {
      
      eventBus.listen('closeModal', (data) => {
        this.student = this.initialData()
        this.selectedStudent = {}
        this.$validator.reset()
      })

    },
    data () {
      return {
        student: {
          fullName: '',
          registration: '',
          id_course: null,
          id_apto: null,
          age: null,
          genre: 'M',
          email: '',
          rg: '',
          cpf: '',
          phone1: '',
          is_bse_active: false,
          is_admin: false
        },
        selectedStudent: {}
      }
    },
    methods: {
      addStudentToApto (selectedStudent) {
        this.$validator.validateAll().then(result => {
          if (!result) {
            console.log('Add Student To Apto -> validation failed.')
          } else {
            console.log('Add Student To Apto Submit')
            this.$validator.reset()
            let aux =  selectedStudent
            console.log(aux)

            this.$http.put(`api/user/${aux.id}/apto/${this.apto.number}?token=`+ this.$auth.getToken())
              .then( (response) => {
                
                let snack = {
                  activator: true,
                  error: false,
                  success: true,
                  msg: response.body.message
                }
                
                this.$store.commit('setSnack', snack)
                
                eventBus.closeModal(true)
                eventBus.fire('studentAddedToApt')

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
      initialData () {
        return {
          fullName: '',
          registration: '',
          id_course: null,
          id_apto: null,
          age: null,
          genre: 'M',
          email: '',
          rg: '',
          cpf: '',
          phone1: '',
          is_bse_active: false,
          is_admin: false
        }
      },
      closeModal(){
        eventBus.closeModal(true)
      }
    }
  }
</script>
<style>

</style>
