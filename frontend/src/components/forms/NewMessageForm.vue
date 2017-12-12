<template>
  <div>
    <form data-vv-scope="apto-user-form" @submit.prevent="sendMessage()">
      <v-layout row wrap>
        <v-flex xs12>
          <v-text-field
            name="input-message"
            label="Mensagem"
            v-model="message"
            multi-line
            required
            v-validate="'required'"
            :error-messages="errors.collect('mensagem')"
            data-vv-name="mensagem"
          ></v-text-field>
        </v-flex>
        <v-flex x12 sm6 md6>
          <v-select
            class="input-group"
            :items="priorities"
            v-model="selectedPriority"
            v-validate="'required'"
            :error-messages="errors.collect('prioridade')"
            data-vv-name="prioridade"
            label="Prioridade"
            required
            clearable
            cache-items
          ></v-select>
        </v-flex>
      </v-layout>
      <v-layout row wrap>
        <v-flex xs12 md12>
          <v-subheader>Escolha uma opção</v-subheader>
          <v-card flat>
            <v-card-text>
              <v-radio-group v-model="option" row>
                <v-radio label="Para 1 Morador" value="option-1" ></v-radio>
                <v-radio label="Para um Apartamento" value="option-2"></v-radio>
                <v-radio label="Para Todos Moradores" value="option-3"></v-radio>
              </v-radio-group>
            </v-card-text>
          </v-card>
        </v-flex>
      </v-layout>
      <v-layout row wrap>
        <v-flex xs12 md12 v-if="option === 'option-1'">
          <v-layout row wrap>
            <v-flex x12 sm6 md6>
              <v-select
                class="input-group"
                :items="students"
                v-model="selectedStudent"
                label="Morador"
                v-validate="'required'"
                :error-messages="errors.collect('morador')"
                data-vv-name="morador"
                autocomplete
                required
                clearable
                cache-items
              ></v-select>
            </v-flex>
          </v-layout>
        </v-flex>
        <v-flex xs12 md12 v-if="option === 'option-2'">
          <v-layout row wrap>
            <v-flex x12 sm6 md6>
              <v-select
                class="input-group"
                :items="aptos"
                v-model="selectedApto"
                label="Apartamento"
                v-validate="'required'"
                :error-messages="errors.collect('apartamento')"
                data-vv-name="apartamento"
                autocomplete
                required
                clearable
                cache-items
              ></v-select>
            </v-flex>
          </v-layout>
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
          Enviar
        </v-btn>
        <v-btn class="grey lighten-1 black--text" dark @click.prevent="closeModal">Fechar</v-btn>
      </v-card-actions>
    </form>
  </div>
</template>
<script>
  import { eventBus } from '../../main'
  import VueNotifications from 'vue-notifications'

  export default {
    $validate: true,
    watch: {
      option (value) {
        if( value === 'option-1'){
          this.getStudents()
        }else if (value === 'option-2') {
          this.getAptos()
        }
      }
    },
    data () {
      return {
        snackMsg: '',
        option: 'option-3',
        message: '',
        selectedStudent: {},
        selectedApto: false,
        selectedPriority: '',
        aptos: [],
        students: [],
        priorities: [{'text': 'Baixa', 'value' : 'low'}, {'text': 'Média', 'value' : 'medium'}, {'text': 'Alta', 'value' : 'high'}]
      }
    },
    methods: {
      sendMessage () {
        this.$validator.validateAll().then(result => {
          if (!result) {
            console.log('User Apto Updated -> validation failed.')
          } else {
            console.log('User Apto Update Submit')
            this.$validator.reset()

            let data = { }

            if(this.option === 'option-1'){
              data = {
                text: this.message,
                priority: this.selectedPriority,
                to: {
                  type: 'user',
                  id: this.selectedStudent.id
                }
              }
            }else if(this.option === 'option-2') {
              data = {
                text: this.message,
                priority: this.selectedPriority,
                to: {
                  type: 'apto',
                  id: this.selectedApto.id
                },
              }
            }else {
              data = {
                text: this.message,
                priority: this.selectedPriority,
                to: {
                  type: 'all',
                  id: null
                },
              }
            }

            this.$http.post(`api/user/create-notification?token=`+ this.$auth.getToken(), data)
              .then( (response) => {
                this.snackMsg = response.body.message
                VueNotifications.success({message: this.snackMsg})
                
                this.closeModal()
                eventBus.fire('notification-created')

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

              VueNotifications.error({message: this.snackMsg})
            })
          }
        }).catch( () => {
          console.log('something went wrong (non-validation related')
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
      getStudents () {
        this.students = []
        this.$http.get(`api/users?token=`+ this.$auth.getToken()).then( (response) => {
          for( let i in response.body.data) {
            this.students.push({
              'text': response.body.data[i]['fullName'],
              'id': response.body.data[i]['id']
            })
          }
        })
      },
      closeModal () {
        this.option = false
        this.aptos = []
        this.students = []
        this.priority = []
        this.message = ''
        this.selectedApto = false
        this.selectedStudent = false
        this.selectedPriority = false
        this.$validator.reset()
        eventBus.closeModal(true)
      }
    }
  }
</script>
<style>

</style>
