export default function (Vue) {
  Vue.auth = {
    // set token
    // get token
    // destroy token
    // isAuthenticated
    setToken (token) {
      // const base64Url = token.split('.')[1]
      // const base64 = base64Url.replace('-', '+').replace('_', '/')
      // const jsonBase64Token = JSON.parse(window.atob(base64))
      localStorage.setItem('token', token)
      // localStorage.setItem('atobToken', jsonBase64Token)
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
      localStorage.removeItem('user')
      // localStorage.removeItem('atobToken')
    },

    isAuthenticated () {
      if (this.getToken() && this.getToken() !== undefined) {
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
