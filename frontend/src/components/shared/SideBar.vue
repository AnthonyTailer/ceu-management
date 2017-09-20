<template>
  <v-navigation-drawer absolute persistent light :mini-variant.sync="mini" v-model="drawer" overflow>
    <v-toolbar flat class="transparent">
      <v-list class="pa-0">
        <v-list-tile avatar>
          <v-list-tile-avatar>
            <img src="/static/ceu-logo.png"/>
          </v-list-tile-avatar>
          <v-list-tile-content>
            <v-list-tile-title>CEU II</v-list-tile-title>
          </v-list-tile-content>
          <v-list-tile-action>
            <v-btn icon @click.native.stop="mini = !mini">
              <v-icon>chevron_left</v-icon>
            </v-btn>
          </v-list-tile-action>
        </v-list-tile>
      </v-list>
    </v-toolbar>
    <v-list class="pt-0" dense>
      <v-divider></v-divider>
      <v-list-tile v-for="item in items" :key="item.title" @click="">
        <v-list-tile-action>
          <v-icon>{{ item.icon }}</v-icon>
        </v-list-tile-action>
        <v-list-tile-content>
          <v-list-tile-title>{{ item.title }}</v-list-tile-title>
        </v-list-tile-content>
      </v-list-tile>
    </v-list>
  </v-navigation-drawer>
</template>

<script>
  export default {
    props: {
      drawer: {
        default: this.drawer
      }
    },
    mounted () {
      this.$nextTick(() => {
        window.addEventListener('resize', this.getWindowWidth)
        window.addEventListener('resize', this.getWindowHeight)

        this.getWindowWidth()
        this.getWindowHeight()
      })
    },
    data () {
      return {
        items: [
          { title: 'Home', icon: 'dashboard' },
          { title: 'Alunos', icon: 'face' },
          { title: 'Sobre', icon: 'question_answer' }
        ],
        mini: false,
        right: null,
        windowWidth: 0,
        windowHeight: 0
      }
    },
    methods: {
      getWindowWidth (event) {
        this.windowWidth = document.documentElement.clientWidth
      },
      getWindowHeight (event) {
        this.windowHeight = document.documentElement.clientHeight
      }
    },
    watch: {
      windowWidth: function () {
        if (this.windowWidth <= 800) {
          this.mini = true
        } else {
          this.mini = false
        }
      }
    },
    beforeDestroy () {
      window.removeEventListener('resize', this.getWindowWidth)
      window.removeEventListener('resize', this.getWindowHeight)
    }
  }
</script>

<style lang="stylus">
  @import '../../stylus/main'
</style>
