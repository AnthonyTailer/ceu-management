// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import Vuetify from 'vuetify'
import App from './App'
import SideBar from '@/components/SideBar'
import TopMenu from '@/components/TopMenu'
import router from './router'
import VueResource from 'vue-resource'
import VeeValidate, { Validator } from 'vee-validate'
import pt from 'vee-validate/dist/locale/pt_BR.js'

Vue.use(VueResource)

// a constante API
Vue.http.options.root = process.env.API

Vue.use(Vuetify)
Vue.config.productionTip = false

// Add locale helper.
Validator.addLocale(pt)

// Install the Plugin and set the locale.
Vue.use(VeeValidate, {
  locale: 'pt_BR'
})

Vue.router = router

Vue.use(require('@websanova/vue-auth'), {
  auth: require('@websanova/vue-auth/drivers/auth/bearer.js'),
  http: require('@websanova/vue-auth/drivers/http/vue-resource.1.x.js'),
  router: require('@websanova/vue-auth/drivers/router/vue-router.2.x.js'),
  rolesVar: 'type',
  loginData: {url: 'api/login/', method: 'POST', redirect: '/', fetchUser: false},
  fetchData: {url: 'api/token-verify/', method: 'GET'},
  refreshData: {url: 'api/token-refresh/', method: 'GET', atInit: false}
})

/* eslint-disable no-new */
new Vue({
  el: '#app',
  router,
  template: '<App/>',
  components: { App }
})

Vue.component('side-bar', {
  name: 'side-bar',
  template: SideBar
})

Vue.component('top-menu', {
  name: 'top-menu',
  template: TopMenu
})
