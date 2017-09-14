<template>
  <v-app light toolbar>
    <side-bar></side-bar>
    <top-menu></top-menu>
    <main>
      <v-container fluid>
        <h1 class="text--accent-4">{{msg}}</h1>
      </v-container>
    </main>

  </v-app>
</template>

<script>
  import TopMenu from './TopMenu.vue'
  import SideBar from './SideBar.vue'
  export default {
    name: 'dash',
    data () {
      return {
        msg: 'Rodou Bonito...'
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
