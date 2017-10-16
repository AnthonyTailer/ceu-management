<template>
  <form>
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
          v-validate="'required|digits:3'"
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
          v-bind:items="courses"
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
          label="Possui apartamento?"
          autocomplete
          required
        ></v-select>
      </v-flex>
    </v-layout>
    <v-layout row wrap>
      <v-flex x4 sm4 md4>
        <v-checkbox
          class="input-group"
          name="input-bse"
          type="checkbox"
          v-model="student.bse"
          label="BSE Ativo?"
          :error-messages="errors.collect('BSE')"
          v-validate="'required'"
          data-vv-name="BSE"
          required
        ></v-checkbox>
      </v-flex>
      <v-flex x4 sm4 md4>
        <v-checkbox
          class="input-group"
          name="input-admin"
          type="checkbox"
          v-model="student.admin"
          label="É da diretoria?"
          :error-messages="errors.collect('Diretoria')"
          v-validate="'required'"
          data-vv-name="Diretoria"
          required
        ></v-checkbox>
      </v-flex>
      <v-flex x4 sm4 md4>
        <v-radio-group  class="input-group" v-model="student.genre" :mandatory="false" row>
          <v-radio color="blue" label="Masculino" value="M"></v-radio>
          <v-radio color="pink" label="Feminino" value="F"></v-radio>
        </v-radio-group>
      </v-flex>
    </v-layout>
  </form>
</template>
<script>
  import { eventBus } from '../../main'
  
  export default {
    $validate: true,
    updated () {
      eventBus.listen('closeModal', (data) => {
        this.student = []
        this.$validator.reset()
      })

      eventBus.listen('getUserData', data => {
        this.student = data
      })
      
    },
    created () {
      this.getCourses()
      this.getAptos()
      
      eventBus.listen('updateUser', data => {
        this.updateUser()
      })
      
    },
    mounted () {
      
      eventBus.listen('createUser', () => {
        this.submit()
      })
    },
    beforeDestroy () {
      eventBus.$off('createUser')
      eventBus.$off('getUserData')
      eventBus.$off('updateUser')
    },
    data () {
      return {
        courses: [],
        aptos: [],
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
          bse: false,
          admin: false
        }
      }
    },
    methods: {
      getCourses () {
         this.$http.get('api/courses?token='+ this.$auth.getToken()).then((response) => {
           console.log(response)
           for (let i in response.body.courses) {
            this.courses.push({'text': response.body.courses[i]['courseName'], 'id': response.body.courses[i]['id']})
           }
         })
      },
      getAptos () {
        this.$http.get('api/aptos?token='+ this.$auth.getToken()).then((response) => {
          console.log(response)
          for (let i in response.body.aptos) {
            this.aptos.push({'text' : response.body.aptos[i]['number'], 'id': response.body.aptos[i]['id']})
          }
        })
      },
      submit () {
        this.$validator.validateAll().then(result => {
          if (!result) {
            console.log('User Created-> validation failed.')
          } else {
            this.$validator.reset()
            console.log('Submited User' )
            console.log(this.student) //TODO AJAX create user
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
            this.$validator.reset()
            console.log(this.student) //TODO AJAX update user
          }
        }).catch( () => {
          console.log('something went wrong (non-validation related')
        })
      }
    }
  }
</script>
<style>

</style>
