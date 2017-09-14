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
import Auth from './packages/auth/Auth.js'

Vue.component('side-bar', {
  template: SideBar
})

Vue.component('top-menu', {
  template: TopMenu
})

Vue.use(VueResource)
Vue.use(Auth)
Vue.use(Vuetify)

// a constante API
Vue.http.options.root = process.env.API
Vue.config.productionTip = false
Vue.router = router

router.beforeEach(
  (to, from, next) => {
    if (to.matched.some(record => record.meta.forVisitors)) {
      if (Vue.auth.isAuthenticated()) {
        next({
          path: '/dash'
        })
      } else next()
    } else if (to.matched.some(record => record.meta.forAuth)) {
      if (!Vue.auth.isAuthenticated()) {
        next({
          path: '/login'
        })
      } else next()
    }
  }
)

// Add locale helper.
Validator.addLocale(pt)

// Install the Plugin and set the locale.
Vue.use(VeeValidate, {
  locale: 'pt_BR'
})

/* eslint-disable no-new */
new Vue({
  el: '#app',
  router,
  template: '<App/>',
  components: { App }
})
