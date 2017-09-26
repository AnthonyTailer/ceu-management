<template>
    <main>
    <v-container fluid>
      <h1 class="text--accent-4">{{msg}}</h1>
    </v-container>
  </main>
</template>

<script>
  export default {
    name: 'dash',
    data () {
      return {
        msg: 'Rodou Bonito...'
      }
    }
  }
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style>
  /*main {*/
  /*background: none;*/
  /*}*/
</style>
