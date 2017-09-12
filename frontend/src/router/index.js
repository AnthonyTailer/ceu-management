import Vue from 'vue'
import Router from 'vue-router'
import Alunos from '@/components/Alunos'
// import Dash from '@/components/Dash'
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
      meta: {auth: false}
    },
    {
      path: '/alunos',
      name: 'Alunos',
      component: Alunos,
      meta: {auth: true}
    }
  ]
})
