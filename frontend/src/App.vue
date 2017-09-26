<template>
  <v-app>
    <v-navigation-drawer v-if="this.$router.currentRoute.name !== 'login'" absolute persistent light :mini-variant.sync="mini" v-model="drawer">
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
        <v-list-tile class="sideMenuItens" v-for="item in items" :key="item.title" @click.native="targetRoute(item.route)">
          <v-list-tile-action>
            <v-icon>{{ item.icon }}</v-icon>
          </v-list-tile-action>
          <v-list-tile-content>
            <v-list-tile-title>{{ item.title }}</v-list-tile-title>
          </v-list-tile-content>
        </v-list-tile>
      </v-list>
    </v-navigation-drawer>
    <!-- Menu Fixado em cima -->
    <v-toolbar v-if="this.$router.currentRoute.name !== 'login'" fixed class="teal darken-1" dark>
      <v-toolbar-side-icon @click.native.stop="drawer = !drawer"></v-toolbar-side-icon>
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
    <router-view></router-view>
  </v-app>
</template>

<script>
  export default {
    mounted () {
      this.$nextTick(() => {
        window.addEventListener('resize', this.getWindowWidth)
        window.addEventListener('resize', this.getWindowHeight)

        this.getWindowWidth()
        this.getWindowHeight()
      })
    },
    created: function () {
      console.log(this.$router.currentRoute.name)
    },
    data () {
      return {
        drawer: true,
        items: [
          { title: 'Home/Dashboard', icon: 'dashboard', route: '/dash' },
          { title: 'Alunos', icon: 'face', route: '/alunos' }
          // { title: 'Sobre', icon: 'question_answer', route: this.$router.push('/dash') }
        ],
        mini: false,
        right: null,
        windowWidth: 0,
        windowHeight: 0
      }
    },
    methods: {
      targetRoute: function (target) {
        return this.$router.push(target)
      },
      getWindowWidth: function (event) {
        this.windowWidth = document.documentElement.clientWidth
      },
      getWindowHeight: function (event) {
        this.windowHeight = document.documentElement.clientHeight
      }
    },
    watch: {
      windowWidth: function () {
        if (this.windowWidth <= 991) {
          this.mini = true
        } else if (this.windowWidth <= 768) {
          this.drawer = false
          this.mini = false
        } else {
          this.drawer = true
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
  @import './stylus/main'

  .sideMenuItens {
    cursor: pointer
  }
</style>
