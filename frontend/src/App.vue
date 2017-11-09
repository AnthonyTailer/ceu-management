<template>
  <v-app toolbar footer :style="{ 'background-image': 'url(' + backgroundLogin + ')', 'background-size': 'cover' }">
    <v-navigation-drawer v-if="this.$router.currentRoute.name !== 'login'"  persistent overflow light :mini-variant.sync="mini" v-model="drawer">
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
      
      <v-btn icon class="mr-5">
        <v-icon large v-badge="{ value: 6 , left: true}" class="red--after">mail</v-icon>
      </v-btn>
      
      <div class="text-xs-center">
        <v-menu open-on-hover right offset-y class="mr-0">
          <v-btn icon slot="activator">{{ user.name }} <v-icon>keyboard_arrow_down</v-icon></v-btn>
          <v-list>
            <v-list-tile style="cursor: pointer">
              <v-icon>exit_to_app</v-icon><v-list-tile-title @click="logout()"> Sair</v-list-tile-title>
            </v-list-tile>
          </v-list>
        </v-menu>
      </div>
    
    </v-toolbar>
    <main fullscreen>
      <router-view></router-view>
    </main>
    <!--<v-footer class="pa-3"  v-if="footer">-->
      <!--<v-spacer></v-spacer>-->
      <!--<div>© {{ new Date().getFullYear() }}</div>-->
    <!--</v-footer>-->
  </v-app>
</template>

<script>
  import { eventBus } from './main'
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
      console.log('created menu')
      this.user.name = localStorage.getItem('user') ? localStorage.getItem('user').split(' ')[0] : ''
      this.backgroundLogin = this.$router.currentRoute.name === 'login' ? '/static/back-login.jpg' : ''
      this.footer = this.$router.currentRoute.name !== 'login'
    },
    updated () {
      console.log('updated menu')
      this.user.name = localStorage.getItem('user') ? localStorage.getItem('user').split(' ')[0] : ''
      this.backgroundLogin = this.$router.currentRoute.name === 'login' ? '/static/back-login.jpg' : ''
      this.footer = this.$router.currentRoute.name !== 'login'
      
    },
    data () {
      return {
        drawer: true,
        footer: false,
        backgroundLogin: '/static/back-login.jpg',
        items: [
          { title: 'Home/Dashboard', icon: 'dashboard', route: '/dash' },
          { title: 'Alunos', icon: 'face', route: '/alunos' },
          { title: 'Apartamentos/Blocos', icon: 'domain', route: '/aptos' },
          { title: 'Vagas', icon: 'domain', route: '/aptos/vacancy' }
        ],
        user: {
          name: ''
        },
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
      },
      logout: function () {
        this.$auth.destroyToken()
        this.$router.push('/')
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

  .sideMenuItens:hover {
    background-color: #babaca;
    color: #fff !important;
  }

  /* Tooltip container */
  .tooltip {
    position: relative;
    display: inline-block;
  }

  /* Tooltip text */
  .tooltip .tooltiptext {
    visibility: hidden;
    width: 120px;
    background-color: black;
    color: #fff;
    text-align: center;
    padding: 5px 0;
    border-radius: 6px;
  
    /* Position the tooltip text - see examples below! */
    position: absolute;
    z-index: 1;
  }

  /* Show the tooltip text when you mouse over the tooltip container */
  .tooltip:hover .tooltiptext {
    visibility: visible;
  }
  
  .input-group {
    padding: 18px 5px 0;
  }
</style>
