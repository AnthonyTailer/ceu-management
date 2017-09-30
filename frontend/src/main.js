// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import App from './App'
import router from './router'
import VueResource from 'vue-resource'
import VeeValidate, { Validator } from 'vee-validate'
import pt from 'vee-validate/dist/locale/pt_BR.js'
import Auth from './packages/auth/Auth.js'
import {ServerTable} from 'vue-tables-2'
import Vuetify from 'vuetify'
import vueXlsxTable from 'vue-xlsx-table'

Vue.use(vueXlsxTable, {rABS: false})
Vue.use(ServerTable, {}, false)
Vue.use(VueResource)
Vue.use(Auth)

// a constante API
Vue.http.options.root = process.env.API
Vue.http.headers.common['Authorization'] = 'Bearer ' + Vue.auth.getToken()
Vue.config.productionTip = false
Vue.router = router

// router.beforeEach(
//   (to, from, next) => {
//     if (to.matched.some(record => record.meta.forVisitors)) {
//       if (Vue.auth.isAuthenticated()) {
//         next({
//           path: '/dash'
//         })
//       } else next()
//     } else if (to.matched.some(record => record.meta.forAuth)) {
//       if (!Vue.auth.isAuthenticated()) {
//         next({
//           path: '/login'
//         })
//       } else next()
//     }
//   }
// )

// Add locale helper.
Validator.addLocale(pt)

// Install the Plugin and set the locale.
Vue.use(VeeValidate, {
  locale: 'pt_BR'
})

Vue.use(Vuetify)

export const eventBus = new Vue({
  methods: {
    closeModal (dialog) {
      this.$emit('closeModal', !dialog)
    }
  }
})

/* eslint-disable no-new */
new Vue({
  el: '#app',
  router,
  template: '<App/>',
  components: { App }
})
