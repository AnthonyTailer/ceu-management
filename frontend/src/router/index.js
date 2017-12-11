import Vue from 'vue'
import Router from 'vue-router'
import Apartaments from '@/components/ApartamentManagement'
import ApartamentsList from '@/components/ApartamentVacancyList'
import Apartament from '@/components/ApartamentHome'
import Students from '@/components/StudentManagement'
import Dash from '@/components/Dash'
import Login from '@/components/Login'
import Notifications from '@/components/Notifications'
import Laundry from '@/components/Laundry'

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
      meta: {forAdmin: true}
    },
    {
      path: '/alunos',
      name: 'alunos',
      component: Students,
      meta: {forAdmin: true}
    },
    {
      path: '/aptos',
      name: 'apartamentos',
      component: Apartaments,
      meta: {forAdmin: true}
    },
    {
      path: '/aptos/vacancy',
      name: 'vagos',
      component: ApartamentsList,
      meta: {forAuth: true}
    },
    {
      path: '/aptos/:number',
      name: 'apartamento',
      component: Apartament,
      meta: { forAdmin: true}
    },
    {
      path: '/notifications',
      name: 'notificacoes',
      component: Notifications,
      meta: {forAuth: true}
    },
    {
      path: '/laundry',
      name: 'lavanderia',
      component: Laundry,
      meta: {forAuth: true}
    }
  ]
})
