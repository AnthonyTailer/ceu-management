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
                </v-list-tile-action>
              </v-list-tile>
            </template>
          </v-list>
        </v-card>
      </v-flex>
    </v-layout>
    
    <!---->
    <!--<app-modal v-show="removeStudent" :dialog="removeStudent">-->
      <!--<p slot="titleModal">Remover Aluno do Apartamento</p>-->
      <!--<p slot="mainModal">-->
        <!--Você deseja mesmo remover este Aluno deste Apartamento?-->
      <!--</p>-->
      <!--<v-btn class="white&#45;&#45;text red accent-3" dark slot="footerModal" @click.native.stop="">Remover</v-btn>-->
    <!--</app-modal>-->
    
    
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

  export default {
    created () {
      
      console.log(this.$router.currentRoute)

      this.getApto()

    },
    data () {
      return {
        snackbar: false,
        snackError: false,
        snackSuccess: false,
        snackMsg: '',
        apartament: '',
        students: '',
        removeStudent: false
      }
    },
    methods: {
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
      }
    },
    components: {
      appModal: Modal
    }

  }

</script>