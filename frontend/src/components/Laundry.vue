<template>
  <div>
    <v-layout row justify-center>
      <v-flex xs8 >
        <v-text-field
          name="input-search-apto"
          label="Procure pelo número do apartamento"
          single-line
          prepend-icon="search"
          v-model="search"
        ></v-text-field>
      </v-flex>
    </v-layout>
    <v-layout row justify-center fill-height class="d-flex align-center">
      <v-flex xs8>
        <v-radio-group label="Filtre pelo gênero das vagas" class="input-group" v-model="vacancy_gender" :mandatory="false" row >
          <v-radio color="red" label="Todos" value=""></v-radio>
          <v-radio color="blue" label="Masculino" value="M"></v-radio>
          <v-radio color="pink" label="Feminino" value="F"></v-radio>
          <v-radio color="green" label="Misto" value="MF"></v-radio>
        </v-radio-group>
      </v-flex>
    </v-layout>
    <v-layout row justify-center>
      <v-flex xs8>
        <p v-show="no_records === 0" class="text-xs-center"> Nenhum apartamento encontrado com '{{search}}'</p>
      </v-flex>
    </v-layout>
    
    <v-layout row class="mt-2" v-for="(apto, i) in filteredAptos" :key="i">
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
              <v-flex xs12 sm3>
                <p><strong>Bloco</strong>: {{apto.block}}</p>
              </v-flex>
              <v-flex xs12 sm3>
                <p><strong>Prédio</strong>: {{apto.building}}</p>
              </v-flex>
              <v-flex xs12 sm3>
                <p><strong>Capacidade</strong>: {{apto.capacity}}</p>
              </v-flex>
              <v-flex x12 sm3>
                <v-layout row>
                  <v-flex xs6>
                    <p><strong>Tipo de vagas</strong>:</p>
                  </v-flex>
                  <v-flex xs6>
                    <div class="tooltip" v-if="apto.vacancy_type == 'M'" >
                      <img src="/static/mars.png">
                      <span class="tooltiptext">Vagas Masculinas</span>
                    </div>
                    <div class="tooltip" v-else-if="apto.vacancy_type == 'F'">
                      <img src="/static/venus.png">
                      <span class="tooltiptext">Vagas Femininas</span>
                    </div>
                    <div class="tooltip" v-else>
                      <img src="/static/genders.png">
                      <span class="tooltiptext">Vagas Mistas</span>
                    </div>
                  </v-flex>
                </v-layout>
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
    computed: {
      filteredAptos: function() {
        return this.vacancy_aptos.filter((apto) => {
          return apto.vacancy_type.match(this.vacancy_gender) && apto.number.match(this.search)
        })
      }
    },
    watch: {
      filteredAptos (value) {
        this.no_records = value.length
      }
    },
    created () {

      console.log(this.$router.currentRoute)

      this.getVacancyAptos()
    },
    data () {
      return {
        vacancy_aptos: [],
        vacancy_gender: '',
        search: '',
        no_records: 0
      }
    },
    methods: {
      getVacancyAptos () {
        this.$http.get('api/apto?token='+ this.$auth.getToken()).then( (response) => {
          console.log(response)
          for( let i in response.body.aptos) {
            this.vacancy_aptos.push(response.body.aptos[i])
          }
        })
      },
    }

  }

</script>