<template>
   <!-- Menu Fixado em cima -->
    <v-toolbar fixed class="teal darken-1" dark>
      <v-toolbar-side-icon @click.stop="drawer = !drawer"></v-toolbar-side-icon>
      <v-toolbar-title>CEU II Management</v-toolbar-title>
      <v-spacer></v-spacer>
      <v-btn icon class="pr-1">
        <v-icon large v-badge="{ value: 6 , left: true}" class="red--after">mail</v-icon>
      </v-btn>
      <v-btn icon class="pr-1">
        <v-icon large>account_circle</v-icon>
        <v-icon>keyboard_arrow_down</v-icon>
      </v-btn>
    </v-toolbar>
</template>

<script>
  module.exports = {
    data () {
      return {
        drawer: true
      }
    }
  }
</script>

<style lang="stylus">
  @import '../stylus/main'
</style>
