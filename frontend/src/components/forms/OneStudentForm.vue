<template>
  <form v-on:submit.prevent="submit()">
    <v-layout row>
      <v-flex x12>
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
    </v-layout>
    <v-layout row wrap>
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
    </v-layout>
    <v-layout row wrap>
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
  </form>
</template>
<script>
  export default {
    $validate: true,
    mounted: function () {
      this.getCourses()
    },
    data () {
      return {
        courses: [],
        student: {
          fullName: '',
          registration: '',
          id_course: null,
          age: null,
          email: '',
          rg: '',
          cpf: '',
          phone1: ''
        }
      }
    },
    methods: {
      getCourses () {
        this.$http.get('api/course').then((response) => {
          console.log(response)
          for (let i in response.body) {
            this.courses.push(response.body[i]['courseName'])
          }
        })
      },
      submit () {
        this.$validator.validateAll(this.student).then(result => {
          if (!result) {
            console.log('validation failed.')
          } else {
            // TODO Ajax Call to save Student
          }
          // success stuff.
        }).catch(() => {
          console.log('something went wrong (non-validation related')
        })
      }
    }
  }
</script>
<style>

</style>
