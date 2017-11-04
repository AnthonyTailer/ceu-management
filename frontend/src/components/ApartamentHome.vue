<template>
  <div>
    <v-layout row class="mt-5">
      <v-flex xs12 sm8 offset-sm2>
        <v-card>
          <v-toolbar dark class="cyan">
            <v-toolbar-title class="white--text">Apartamento {{apartament.number}}</v-toolbar-title>
            <v-spacer></v-spacer>
            <v-chip class="teal white--text">
              <v-avatar class="teal darken-4">{{apartament.vacancy}}</v-avatar>
              {{apartament.vacancy === 1 ? 'Vaga' : 'Vagas' }}
            </v-chip>
          </v-toolbar>
          <v-divider></v-divider>
          <v-card-text>
            <v-layout row>
              <v-flex xs12 sm4>
                <p><strong>Bloco</strong>: {{apartament.block}}</p>
              </v-flex>
              <v-flex xs12 sm4>
                <p><strong>Pŕedio</strong>: {{apartament.building}}</p>
              </v-flex>
              <v-flex xs12 sm4>
                <p><strong>Capacidade</strong>: {{apartament.capacity}}</p>
              </v-flex>
            </v-layout>
          </v-card-text>
        </v-card>
      </v-flex>
    </v-layout>
    
    
    
    <v-layout row fill-height>
      <v-flex xs12 sm8 offset-sm2>
        <v-card class="mt-5">
          <v-toolbar dark class="cyan">
            <v-toolbar-title class="white--text">Moradores</v-toolbar-title>
            <v-spacer></v-spacer>
            <v-chip class="teal white--text">
              <v-avatar class="teal darken-4">{{students.length}}</v-avatar>
              {{students.length === 1 ? 'Morador' : 'Moradores' }}
            </v-chip>
            <v-fab-transition v-if="apartament.vacancy > 0">
              <v-btn class="info" @click.native.stop="addStudent = !addStudent"
                dark
                fab
                fixed
                bottom
                right
              >
                <v-icon>add</v-icon>
                <v-icon>close</v-icon>
              </v-btn>
            </v-fab-transition>
          </v-toolbar>
          <p class="mt-2 text-"><v-icon>info</v-icon> Para remover um aluno deste apto. basta clicar na lixeira correspondente.</p>
          <v-list three-line>
            <template v-for="student in students">
              <v-subheader v-text="student.fullName"></v-subheader>
              <v-divider inset></v-divider>
              <v-list-tile avatar v-bind:key="student.id">
                <v-list-tile-avatar>
                  <v-icon x-large>account_circle</v-icon>
                </v-list-tile-avatar>
                <v-list-tile-content>
                  <v-list-tile-title> Matrícula: {{student.registration}}</v-list-tile-title>
                  <v-list-tile-sub-title>CPF: {{student.cpf}}</v-list-tile-sub-title>
                </v-list-tile-content>
                <v-list-tile-action>
                  <v-btn class="red darken-1" fab dark small color="error" @click.native.stop="removeStudentFromApto(student.id)">
                    <v-icon dark>delete</v-icon>
                  </v-btn>
                  <div class="mt-1"></div>
                  <v-btn class="success darken-1" fab dark small color="info" @click.native.stop="switchStudentFromApto(student)">
                    <v-icon dark>compare_arrows</v-icon>
                  </v-btn>
                </v-list-tile-action>
              </v-list-tile>
            </template>
          </v-list>
        </v-card>
      </v-flex>
    </v-layout>
    
    <app-modal :dialog="addStudent" v-if="apartament.vacancy > 0">
      <p slot="titleModal">Adicionar novo Aluno ao Apartamento {{apartament.number}}</p>
      <app-apto-student slot="mainModal" :students="allStudents" :apto="apartament"></app-apto-student>
      <v-btn class="white--text success accent-3" dark slot="footerModal" @click.native.stop="addStudentToApto">Adicionar</v-btn>
    </app-modal>
  
    <app-modal :dialog="switchStudent">
      <p slot="titleModal">Trocar Aluno de Apartamento</p>
      <app-student-change-apto slot="mainModal" :student="oneStudent" :apto="apartament"></app-student-change-apto>
      <v-btn class="white--text success accent-3" dark slot="footerModal" @click.native.stop="changeStudentApto">Trocar</v-btn>
    </app-modal>
    
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
  </div>
</template>
<script>
  import { eventBus } from '../main'
  import Modal from './shared/Modal.vue'
  import AptoStudentForm from './forms/AptoStudentForm.vue'
  import StudentChangeAptoForm from './forms/StudentAptoChangeForm.vue'

  export default {
    created () {
      
      console.log(this.$router.currentRoute)

      this.getApto()

      eventBus.listen('closeModal', (data) => {
        this.addStudent = data
        this.switchStudent = data
        this.allStudents = []
      })
      
      eventBus.listen('userAddedToApto', (data) => {
          this.snackbar = true
          this.snackError = false
          this.snackSuccess = true
          this.snackMsg = data

        this.getApto()
      })
      
      eventBus.listen('userChangedApto', (data) => {
        this.snackbar = true
        this.snackError = false
        this.snackSuccess = true
        this.snackMsg = data

        this.getApto()
      })

    },
    data () {
      return {
        snackbar: false,
        snackError: false,
        snackSuccess: false,
        snackMsg: '',
        apartament: '',
        students: '',
        oneStudent: '',
        removeStudent: false,
        addStudent: false,
        switchStudent: false,
        allStudents: []
      }
    },
    computed: {
      addStudentValue () {
        return this.addStudent
      }
    },
    watch: {
      addStudentValue (value) {
        if(value)
          this.getStudentsWithoutApto()
      }
    },
    methods: {
      getStudentsWithoutApto () {
        this.$http.get('api/users/noapto?token='+ this.$auth.getToken())
          .then(response => {
            console.log(response)
            let result = response.body.data
            for (let i = 0; i < result.length; i++) {
              Object.assign(result[i], {'value': false })
            }
            this.allStudents = result
            console.log('GET Users -> ',this.allStudents)
          })
      },
      getApto () {
        let apto = this.$router.currentRoute.path.split('/')
        this.$http.get(`api/apto/${apto[2]}?token=`+ this.$auth.getToken())
          .then((response) => {
            console.log(response)
            this.apartament = response.body.apto
            this.students = response.body.users
          })
      },
      removeStudentFromApto (student) {
        this.$http.put(`api/user/${student}/apto?token=` + this.$auth.getToken())
          .then((response) => {
            console.log(response)
            this.snackbar = true
            this.snackError = false
            this.snackSuccess = true
            this.snackMsg = response.body.message
            this.getApto()
          }).catch((response) => {
          this.snackbar = true
          this.snackMsg = response.body.message
          this.snackSuccess = false
          this.snackError = true
        })
      },
      addStudentToApto () {
        eventBus.fire('addStudentToApto')
      },
      changeStudentApto () {
        eventBus.fire('changeStudentApto')
      },
      switchStudentFromApto (data) {
        this.switchStudent = true
        this.oneStudent = data
      }
    },
    components: {
      appModal: Modal,
      appAptoStudent: AptoStudentForm,
      appStudentChangeApto: StudentChangeAptoForm,
    }

  }

</script>