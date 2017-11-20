<template>
  <v-snackbar
    v-model="snackbar.activator"
    :error="snackbar.error"
    :success="snackbar.success"
    vertical
    :timeout="5000"
  >
    <div v-html="snackbar.msg"></div>
    <v-btn dark flat @click.native="closeSnack">Fechar</v-btn>
  </v-snackbar>
</template>
<script>
  export default {
   computed: {
     snackbar: {
       get () {
        return this.$store.getters.snackState
       },
       set (value) {
         this.$store.state.snackbar = value
       }
     }
   },
    methods: {
     closeSnack () {
       this.snackbar = {
         activator: false,
         error: false,
         success: false,
         msg: ''
       }
     }
    }
  }
</script>