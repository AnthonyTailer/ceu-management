<template>
  <div>
    <v-layout row class="mt-2" v-for="apto in vacancy_aptos">
      <v-flex xs12 sm8 offset-sm2>
        <v-card>
          <v-toolbar dark class="cyan">
            <v-toolbar-title class="white--text">Apartamento {{apto.number}}</v-toolbar-title>
            <v-spacer></v-spacer>
            <v-chip class="teal white--text">
              <v-avatar class="teal darken-4">{{apto.vacancy}}</v-avatar>
              {{apto.vacancy === 1 ? 'Vaga' : 'Vagas' }}
            </v-chip>
          </v-toolbar>
          <v-divider></v-divider>
          <v-card-text>
            <v-layout row>
              <v-flex xs12 sm4>
                <p><strong>Bloco</strong>: {{apto.block}}</p>
              </v-flex>
              <v-flex xs12 sm4>
                <p><strong>Prédio</strong>: {{apto.building}}</p>
              </v-flex>
              <v-flex xs12 sm4>
                <p><strong>Capacidade</strong>: {{apto.capacity}}</p>
              </v-flex>
            </v-layout>
          </v-card-text>
        </v-card>
      </v-flex>
    </v-layout>
  </div>
</template>
<script>
  import { eventBus } from '../main'

  export default {
    created () {
      
      console.log(this.$router.currentRoute)

      this.getVacancyAptos()
    },
    data () {
      return {
        vacancy_aptos: []
      }
    },
    methods: {
      getVacancyAptos () {
        this.$http.get('api/apto?token='+ this.$auth.getToken()).then( (response) => {
          for( let i in response.body.aptos) {
            console.log(response)
            this.vacancy_aptos.push(response.body.aptos[i])
          }
        })
      },
    }

  }

</script>