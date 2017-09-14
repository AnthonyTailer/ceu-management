import Vue from 'vue'
import Router from 'vue-router'
import Alunos from '@/components/Alunos'
import Dash from '@/components/Dash'
import Login from '@/components/Login'

Vue.use(Router)

export default new Router({
  routes: [
    {
      path: '/',
      redirect: '/login'
    },
    {
      path: '/login',
      name: 'Login',
      component: Login,
      meta: {forVisitors: true}
    },
    {
      path: '/dash',
      name: 'Dash',
      component: Dash,
      meta: {forAuth: true}
    },
    {
      path: '/alunos',
      name: 'Alunos',
      component: Alunos,
      meta: {forAuth: true}
    }
  ]
})
