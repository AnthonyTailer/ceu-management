<template>
  <v-app light toolbar>
    <side-bar :drawer="drawer"></side-bar>
    <top-menu :drawer="drawer" @changeDrawer="drawer = $event"></top-menu>
    <main>
      <v-container fluid>
        <h1 class="text--accent-4">{{msg}}</h1>
      </v-container>
    </main>
  </v-app>
</template>

<script>
  export default {
    name: 'dash',
    data () {
      return {
        msg: 'Rodou Bonito...',
        drawer: true
      }
    }
  }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

</style>
