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
    <v-snackbar
      :timeout="8000"
      :error="snackError"
      :success="snackSuccess"
      :vertical="true"
      v-model="snackbar"
    >
      <div v-html="snackMsg"></div>
      <v-btn dark flat @click.native="snackbar = false">Fechar</v-btn>
    </v-snackbar>
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
    beforeCreate () {

      eventBus.listen('addStudentToApto', () => {
        this.updateUserApto()
      })

      eventBus.listen('closeModal', (data) => {
        this.student = this.initialData()
        this.selectedStudent = {}
        this.$validator.reset()
      })

    },
    beforeDestroy () {
      eventBus.$off('createUserSubmit')
      eventBus.$off('userCreated')
      eventBus.$off('updateUserSubmit')
      eventBus.$off('userUpdated')
      eventBus.$off('getUserData')
      eventBus.$off('closeModal')
    },
    data () {
      return {
        snackbar: false,
        snackError: false,
        snackSuccess: false,
        snackMsg: '',
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
      updateUserApto () {
        this.$validator.validateAll().then(result => {
          if (!result) {
            console.log('User Updated -> validation failed.')
          } else {
            console.log('User Update Submit')
            this.$validator.reset()
            let aux =  this.selectedStudent
            console.log(aux)
            
            this.$http.put(`api/user/${aux.id}/apto/${this.apto.number}?token=`+ this.$auth.getToken())
              .then( (response) => {
                this.snackMsg = response.body.message
                eventBus.fire('userAddedToApto', this.snackMsg)
                eventBus.closeModal(true)

              }).catch( (response) =>  {
              this.snackbar = true
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
              this.snackMsg = msg
              this.snackSuccess = false
              this.snackError = true
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
      }
    }
  }
</script>
<style>

</style>
