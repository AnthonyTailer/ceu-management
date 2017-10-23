<template>
  <form data-vv-scope="user-form">
    <v-layout row>
      <v-flex x12 sm6 md6>
        <v-text-field
          name="input-fullName"
          v-model="student.fullName"
          label="Nome Completo"
          class="input-group"
          :error-messages="errors.collect('nome')"
          v-validate="'required'"
          data-vv-name="nome"
          required
        ></v-text-field>
      </v-flex>
      <v-flex x12 sm6 md6>
        <v-text-field
          class="input-group"
          name="input-registration"
          v-model="student.registration"
          label="Matrícula"
          :error-messages="errors.collect('matrícula')"
          v-validate="'required|digits:9'"
          data-vv-name="matrícula"
          required
        ></v-text-field>
      </v-flex>
    </v-layout>
    <v-layout row wrap>
      <v-flex x12 sm8 md8>
        <v-text-field
          class="input-group"
          name="input-email"
          v-model="student.email"
          label="e-mail"
          :error-messages="errors.collect('e-mail')"
          v-validate="'required|email'"
          data-vv-name="e-mail"
          required
        ></v-text-field>
      </v-flex>
      <v-flex x12 sm4 md4>
        <v-text-field
          class="input-group"
          name="input-age"
          v-model="student.age"
          label="Idade"
          :error-messages="errors.collect('idade')"
          v-validate="'required|digits:2'"
          data-vv-name="idade"
          required
        ></v-text-field>
      </v-flex>
    </v-layout>
    <v-layout row wrap>
      <v-flex x12 sm6 md6>
        <v-text-field
          class="input-group"
          name="input-rg"
          type="number"
          v-model="student.rg"
          label="RG"
          :error-messages="errors.collect('RG')"
          v-validate="'required|digits:10'"
          data-vv-name="RG"
          required
        ></v-text-field>
      </v-flex>
      <v-flex x12 sm6 md6>
        <v-text-field
          class="input-group"
          name="input-cpf"
          type="number"
          v-model="student.cpf"
          label="CPF sem pontos"
          :error-messages="errors.collect('CPF')"
          v-validate="'required|digits:11'"
          data-vv-name="CPF"
          required
        ></v-text-field>
      </v-flex>
    </v-layout>
    <v-layout row wrap>
      <v-flex x12 sm6 md6>
        <v-select
          class="input-group"
          :items="courses"
          v-model="student.id_course"
          label="Selecione o Curso do aluno"
          autocomplete
          required
        ></v-select>
      </v-flex>
      <v-flex x12 sm6 md6>
        <v-select
          class="input-group"
          v-bind:items="aptos"
          v-model="student.id_apto"
          label="Apartamento"
          autocomplete
        ></v-select>
      </v-flex>
    </v-layout>
    <v-layout row wrap>
      <v-flex x4 sm4 md4>
        <v-checkbox
          class="input-group"
          name="input-bse"
          type="checkbox"
          v-model="student.is_bse_active"
          label="BSE Ativo?"
          :error-messages="errors.collect('BSE')"
          v-validate="'required'"
          data-vv-name="BSE"
        ></v-checkbox>
      </v-flex>
      <v-flex x4 sm4 md4>
        <v-checkbox
          class="input-group"
          name="input-admin"
          type="checkbox"
          v-model="student.is_admin"
          label="É da diretoria?"
          :error-messages="errors.collect('Diretoria')"
          v-validate="'required'"
          data-vv-name="Diretoria"
        ></v-checkbox>
      </v-flex>
      <v-flex x4 sm4 md4>
        <v-radio-group  class="input-group" v-model="student.genre" :mandatory="false" row required>
          <v-radio color="blue" label="Masculino" value="M"></v-radio>
          <v-radio color="pink" label="Feminino" value="F"></v-radio>
        </v-radio-group>
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
    props: ['courses', 'aptos'],
    beforeCreate () {

      eventBus.listen('getUserData', data => {
        this.student = data
        for(let i in this.courses) {
          if(this.courses[i].id === this.student.id_course){
            this.student.id_course = this.courses[i]
          }
        }
      })

      eventBus.listen('deleteUserData', data => {
        this.student = data
      })

      eventBus.listen('createUserSubmit', (data) => {
        this.createUser(data)
      })

      eventBus.listen('updateUserSubmit', () => {
        this.updateUser()
      })

      eventBus.listen('deleteUserSubmit', () => {
        this.deleteUser()
      })
      
      eventBus.listen('closeModal', (data) => {
        this.student = this.initialData()
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
        }
      }
    },
    methods: {
      createUser (data) {
        this.$validator.validateAll(data).then(result => {
          if (!result) {
            console.log('User Created-> validation failed.')
          } else {
            console.log('Submited User' )
            console.log(this.student)
//            this.student.id_course = this.student.id_course.id
            this.$http.post('api/user/register?token='+ this.$auth.getToken(), this.student)
              .then( (response) => {
                this.snackMsg = response.body.message
                eventBus.fire('userCreated', this.snackMsg)
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
          // success stuff.
        }).catch(() => {
          console.log('something went wrong (non-validation related')
        })
      },
      updateUser () {
        this.$validator.validateAll().then(result => {
          if (!result) {
            console.log('User Updated -> validation failed.')
          } else {
            console.log('User Update Submit')
            this.$validator.reset()
            console.log(this.student)
            this.$http.put(`api/user/${this.student.id}?token=`+ this.$auth.getToken(), this.student)
              .then( (response) => {
                this.snackMsg = response.body.message
                eventBus.fire('userUpdated', this.snackMsg)
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
      deleteUser () {
        this.$http.delete(`api/user/${this.student.id}?token=`+ this.$auth.getToken(), this.student.id)
          .then( (response) => {
            eventBus.closeModal(true)
            this.snackMsg = response.body.message
            eventBus.fire('userDeleted', this.snackMsg)

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
