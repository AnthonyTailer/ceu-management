<template>
  <v-container fluid>
    <v-layout row wrap justify-left>
      <v-flex xs12 sm6 md4 v-for="(item, i) in quickAccess" :key="i" class="my-2">
        <v-card height="100%" @click.stop="targetRoute(item.route)" dark :class="'ma-1 ' + 'cardItens ' +  `blue-grey darken-${i+1}`">
          <v-card-text>
            <v-layout>
              <v-flex xs10>
                <v-icon dark xLarge >{{ item.icon }}</v-icon>
                <h3 class="headline mb-0">{{ item.title }}</h3>
                <div>{{ item.description }}</div>
              </v-flex>
              <v-flex xs2>
                <v-chip class="cyan badge-card">{{item.badge}}</v-chip>
              </v-flex>
            </v-layout>
          </v-card-text>
        </v-card>
      </v-flex>
    </v-layout>
    <v-divider class="mt-3 mb-3"></v-divider>
    <v-layout row layout justify-left>
      <v-flex x12 sm6 md6 v-for="i in charts">
        <v-card :id="i.id"></v-card>
      </v-flex>
    </v-layout>
  </v-container>
</template>

<script>
  export default {
    name: 'dash',
    mounted () {
      this.getStats()
    },
    data () {
      return {
        quickAccess: [
          { title: 'Alunos', icon: 'face', description: "Gestão de Todos os alunos", route: '/alunos', badge: '' },
          { title: 'Todos Apartamentos', description: "Gestão de Todos os Apartamentos", icon: 'domain', route: '/aptos', badge: '' },
          { title: 'Lista de Vagas', description: "Lista dos Apartamentos com vagas", icon: 'domain', route: '/aptos', badge: '' }
        ],
        charts: [
          {
            id: "students-chart",
            title: 'Porcentagem de alunos por sexo',
            data: []
          },
          {
            id: "aptos-chart",
            title: 'Alunos/Vagas e Qtde de Apartamentos',
            data: []
          }
        ]

      }
    },
    methods: {
      targetRoute: function (target) {
        return this.$router.push(target)
      },
      getStats () {
        this.$http.get(`api/user/stats?token=`+ this.$auth.getToken())
          .then( (response) => {
            console.log(response)
            let data = response.body

            this.quickAccess[0]["badge"] = data["students"]["total"]["value"]
            this.quickAccess[1]["badge"] = data["aptos"]["total"]["value"]
            this.quickAccess[2]["badge"] = data["aptos"]["vacancy"]["value"]

            for(let i in data){
              let dataChart = []

              if(i == "students"){
                let total = data["students"]["total"]["value"]
                let male = data["students"]["male"]
                let female = data["students"]["female"]

                dataChart.push({name: male.text, y: (male.value * 100) / total})
                dataChart.push({name: female.text, y: (female.value * 100) / total})

                this.charts[0].data = dataChart

                this.$charts.pieChart(this.charts[0])

              }else if(i == "aptos"){
                dataChart = [{
                  name: data["aptos"]["no_apto"]["text"],
                  data: [data["aptos"]["no_apto"]["value"]]

                }, {
                  name: data["aptos"]["vacancy"]["text"],
                  data: [data["aptos"]["vacancy"]["value"]]

                }, {
                  name: data["aptos"]["total"]["text"],
                  data: [data["aptos"]["total"]["value"]]

                }]

                this.charts[1].data = dataChart
                this.$charts.barChart(this.charts[1])
              }
            }

          })
      }
    }
  }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
  
  .cardItens {
    cursor: pointer;
  }
  
  .badge-card {
    float: right;
    color: white;
    font-weight: bold;
  }
</style>
