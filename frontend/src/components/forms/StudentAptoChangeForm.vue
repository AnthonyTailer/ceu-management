<template>
  <form data-vv-scope="apto-user-form">
    <v-layout row wrap>
      <v-flex xs12 sm6 md6>
        <v-text-field
          class="input-group"
          v-model="student.fullName"
          label="Aluno"
          disabled
        ></v-text-field>
      </v-flex>
      <v-flex x12 sm6 md6>
        <v-text-field
          class="input-group"
          v-model="apto.number"
          label="Apartamento"
          disabled
        ></v-text-field>
      </v-flex>
    </v-layout>
    <v-layout row wrap>
      <v-flex xs12 sm6 md6>
        <v-text-field
          class="input-group"
          v-model="apto2"
          label="Aluno"
          disabled
        ></v-text-field>
      </v-flex>
      <v-flex xs12 sm6 md6>
        <v-text-field
          class="input-group"
          v-model="student2"
          label="Aluno"
          disabled
        ></v-text-field>
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
    props: ['student', 'apto'],
    beforeCreate () {

      eventBus.listen('changeStudentApto', () => {
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
      updateUserApto () { //TODO alterar funcção para adicionar apartamento
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
