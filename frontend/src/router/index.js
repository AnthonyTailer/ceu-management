import Vue from 'vue'
import Router from 'vue-router'
import Apartaments from '@/components/ApartamentManagement'
import Apartament from '@/components/ApartamentHome'
import Students from '@/components/StudentManagement'
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
      name: 'login',
      component: Login,
      meta: {forVisitors: true}
    },
    {
      path: '/dash',
      name: 'dash',
      component: Dash,
      meta: {forAuth: true}
    },
    {
      path: '/alunos',
      name: 'alunos',
      component: Students,
      meta: {forAuth: true}
    },
    {
      path: '/aptos',
      name: 'apartamentos',
      component: Apartaments,
      meta: {forAuth: true}
    },
    {
      path: '/aptos/:number',
      name: 'apartamento',
      component: Apartament,
      meta: {forAuth: true}
    }
  ]
})
