<template>
  <v-layout row justify-center>
    <v-dialog v-model="dialog" persistent width="100%">
      <!--<v-btn primary dark slot="activator">Open Dialog</v-btn>-->
      <v-card>
        <v-card-title>
          <h5><slot name="titleModal"></slot></h5>
        </v-card-title>
        <v-divider></v-divider>
        <v-card-text class="modal-body">
          <!--<v-container grid-list-md  class="fill-height">-->
              <!-- Conteudo principal do Modal-->
              <slot name="mainModal"></slot>
          <!--</v-container>-->
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn class="grey lighten-1 black--text" dark @click.native="closeModal">Fechar</v-btn>
          <slot name="footerModal"></slot>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-layout>
</template>

<script>
  import { eventBus } from '../../main'
  export default {
    props: {
      dialog: {
        type: Boolean
      }
    },
    methods: {
      closeModal () {
        eventBus.closeModal(this.dialog)
      }
    },
    watch: {
      dialog: function () {
        console.log('OpenModal -> ' + this.dialog)
      }
    }
  }
</script>
<style scoped>

</style>
