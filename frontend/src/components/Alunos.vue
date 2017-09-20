<template>
  <v-app light toolbar>
    <side-bar :drawer="drawer"></side-bar>
    <top-menu :drawer="drawer" @changeDrawer="drawer = $event"></top-menu>
    <main>
      <v-container>
        <div class="text-xs-right">
          <v-bottom-sheet v-model="sheet">
            <v-btn slot="activator" class="green" dark><i class="material-icons">add_circle</i>&nbsp;Novo Aluno</v-btn>
            <br>
            <v-list>
              <v-subheader>Escolha uma opção abaixo</v-subheader>
              <v-list-tile
                v-for="tile in tiles"
                :key="tile.title"
                @click="sheet = false"
              >
                <v-list-tile-avatar>
                  <v-avatar size="32px" tile>
                    <i :alt="tile.title" class="material-icons">{{tile.icon}}</i>
                  </v-avatar>
                </v-list-tile-avatar>
                <v-list-tile-title>{{ tile.title }}</v-list-tile-title>
              </v-list-tile>
            </v-list>
          </v-bottom-sheet>
        </div>
        <!--<v-card>-->
        <!--<v-card-title>-->
        <!--Lista de Alunos-->
        <!--<v-spacer></v-spacer>-->
        <!--<v-text-field-->
        <!--append-icon="search"-->
        <!--label="Procurar Aluno"-->
        <!--single-line-->
        <!--hide-details-->
        <!--v-model="search"-->
        <!--&gt;</v-text-field>-->
        <!--</v-card-title>-->
        <!--<v-data-table-->
        <!--:headers="headers"-->
        <!--:items="items"-->
        <!--:search="search"-->
        <!--&gt;-->
        <!--<template slot="items" scope="props">-->
        <!--<td>{{props.item.name}}</td>-->
        <!--<td>{{props.item.email}}</td>-->
        <!--<td>{{props.item.age}}</td>-->
        <!--<td>{{props.item.apto}}</td>-->
        <!--</template>-->
        <!--<template slot="pageText" scope="{ pageStart, pageStop }">-->
        <!--de {{ pageStart }} para {{ pageStop }}-->
        <!--</template>-->
        <!--</v-data-table>-->
        <!--</v-card>-->
        <div id="people">
          <v-server-table url="/api/students" :columns="columns" :options="options"></v-server-table>
        </div>
      </v-container>
    </main>
  </v-app>
</template>

<script>
  export default {
    data () {
      return {
        drawer: true,
        sheet: false,
        tiles: [
          {icon: 'add_box', title: 'Um Aluno'},
          {icon: 'attachment', title: 'Importar de arquivo .xls'}
        ],
        tmp: '',
        search: '',
        pagination: {},
        headers: [
          {text: 'Nome', value: 'name'},
          {text: 'E-mail', value: 'email'},
          {text: 'Idade', value: 'age'},
          {text: 'Apartamento', value: 'apto'}
        ],
        items: [],
        columns: ['fullName', 'email', 'age', 'registration'],
        options: {
          responseAdapter (resp) {
            return {data: resp.body, count: 100}
          },
          requestFunction (data) {
            return this.$http.get('api/students/', {params: {body: data}
            }).catch(function (e) {
              this.dispatch('error', e)
            }.bind(this))
          }
        }
      }
    }
  }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

</style>
