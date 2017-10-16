<template>
  <v-layout row justify-center>
    <v-dialog v-model="dialog" persistent width="80%" scrollable>
      <!--<v-btn primary dark slot="activator">Open Dialog</v-btn>-->
      <v-card>
        <v-card-title>
          <span class="headline"><slot name="titleModal"></slot></span>
        </v-card-title>
        <v-card-text>
          <v-container grid-list-md>
              <!-- Conteudo principal do Modal-->
              <slot name="mainModal"></slot>
          </v-container>
        </v-card-text>
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
