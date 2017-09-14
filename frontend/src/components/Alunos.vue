<template>
  <v-app light toolbar>
    <side-bar></side-bar>
    <top-menu></top-menu>
    <main>
      <v-container>
        <v-card>
          <v-card-title>
            Lista de Alunos
            <v-spacer></v-spacer>
            <v-text-field
              append-icon="search"
              label="Procurar Aluno"
              single-line
              hide-details
              v-model="search"
            ></v-text-field>
          </v-card-title>
          <v-data-table
            :headers="headers"
            :items="items"
            :search="search"
          >
            <template slot="items" scope="props">
              <td>{{props.item.name}}</td>
              <td>{{props.item.email}}</td>
              <td>{{props.item.age}}</td>
              <td>{{props.item.apto}}</td>
            </template>
            <template slot="pageText" scope="{ pageStart, pageStop }">
              de {{ pageStart }} para {{ pageStop }}
            </template>
          </v-data-table>
        </v-card>
      </v-container>
    </main>
  </v-app>
</template>

<script>
  import TopMenu from './TopMenu.vue'
  import SideBar from './SideBar.vue'
  export default {
    data () {
      return {
        tmp: '',
        search: '',
        pagination: {},
        headers: [
          { text: 'Nome', value: 'name' },
          { text: 'E-mail', value: 'email' },
          { text: 'Idade', value: 'age' },
          { text: 'Apartamento', value: 'apto' }
        ],
        items: []
      }
    },
    components: {
      TopMenu,
      SideBar
    }
  }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

</style>
