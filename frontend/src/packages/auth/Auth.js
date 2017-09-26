export default function (Vue) {
  Vue.auth = {
    // set token
    // get token
    // destroy token
    // isAuthenticated
    setToken (token) {
      localStorage.setItem('token', token)
    },

    getToken () {
      let token = localStorage.getItem('token')

      if (!token) {
        return null
      } else {
        return token
      }
    },

    destroyToken () {
      localStorage.removeItem('token')
    },

    isAuthenticated () {
      if (this.getToken()) {
        // Vue.http.post('api/token-verify/', {token: this.getToken()}).then((response) => {
        //   return true
        // }).catch((response) => {
        //   if (response.ok === false && response.statusText === 'Bad Request') {
        //     this.destroyToken()
        //     return false
        //   }
        // })
        return true
      } else {
        return false
      }
    }
  }

  Object.defineProperties(Vue.prototype, {
    $auth: {
      get () {
        return Vue.auth
      }
    }
  })
}
