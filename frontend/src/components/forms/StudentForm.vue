<template>
  <form data-vv-scope="user-form" @submit.prevent="submitStudent()">
    <v-layout row wrap>
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
          name="input-course"
          clearable
          cache-items
          :items="courses"
          label="Selecione o Curso do aluno"
          autocomplete
          required
          v-model="student.id_course"
        ></v-select>
      </v-flex>
      <v-flex x12 sm6 md6>
        <v-select
          class="input-group"
          name="input-apto"
          clearable
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
    <v-divider></v-divider>
    <v-card-actions>
      <v-spacer></v-spacer>
      <v-btn
        type="submit"
        class="primary"
        dark
      >
        Salvar
      </v-btn>
      <v-btn class="grey lighten-1 black--text" dark @click.prevent="closeModal">Fechar</v-btn>
    </v-card-actions>
  </form>
</template>
<script>
  import { eventBus } from '../../main'

  export default {
    $validate: true,
    props: ['courses', 'aptos'],
    $validate: true,
    computed: {
      student: {
        get () {
          return this.$store.getters.getStudentState
        },
        set (value) {
          this.$store.dispatch('newStudent', value)
        }
      },
      studentNewState: {
        get () {
          return this.$store.getters.getStudentNewState
        },
        set (value) {
          this.$store.dispatch('setStudentNewState', value)
        }
      },
      studentEditState: {
        get () {
          return this.$store.getters.getStudentEditState
        },
        set (value) {
          this.$store.dispatch('setStudentEditState', value)
        }
      }
    },
    mounted () {

      if ( this.studentEditState ) {

        let aux = JSON.parse( JSON.stringify( this.student ) )

        let id_course = aux.id_course
        let id_apto = aux.id_apto

        let courses = this.courses
        let aptos = this.aptos

        let i, j;

        for (i= 0; i <  courses.length; i++) {
          if(courses[i]['id'] == id_course){
            this.student.id_course = courses[i];
            break
          }
        }

        for(j = 0; j <  aptos.length; j++) {
          if(aptos[j]['id'] == id_apto){
            this.student.id_apto = aptos[j]
            break
          }
        }

      }

    },
    data () {
      return {
        scopeValidation: 'apartament-form',
      }
    },
    methods: {
      closeModal() {
        this.$validator.reset()
        this.$store.commit('setInitialData')
        eventBus.fire('closeModal', false)
      },
      submitStudent () {
        if ( this.studentNewState ){
          this.$validator.validateAll(this.scopeValidation).then(result => {
            if (!result) {
              console.log('Student Created-> validation failed.')
            } else {

              let aux =  this.student
              aux.id_course = this.student.id_course.id
              aux.id_apto = this.student.id_apto.id
              console.log('Submited User', aux )

              this.$store.dispatch('newStudent', aux)
            }
            // success stuff.
          }).catch(() => {
            console.log('something went wrong (non-validation related')
          })
        }
        else if ( this.studentEditState ) {
          this.$validator.validateAll(this.scopeValidation).then(result => {
            if (!result) {
              console.log('Student Edit -> validation failed.', result)
            } else {

              let aux =  JSON.parse( JSON.stringify( this.student ) )
              aux.id_course = aux.id_course !== null ? aux.id_course.id : null
              aux.id_apto = aux.id_apto !== null ? aux.id_apto.id : null
              
              console.log('Submited Student', aux )
              this.$store.dispatch('editStudent', aux )
            }
          }).catch(() => {
            console.log('something went wrong (non-validation related')
          })
        }
      }
    }
  }
</script>
<style>

</style>
