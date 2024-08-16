<!DOCTYPE html>
<html>

<head>
  <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/@mdi/font@6.x/css/materialdesignicons.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.min.css" rel="stylesheet">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui">
</head>

<body>
  <div id="app">
    <v-app>
      <v-main>
        <v-container>
          <v-card class="mx-auto justify-center" max-width="600" height="425" flat color="green lighten-5">
            <v-toolbar color="primary" dark flat class="text-center">
              <v-row>
                <h3 class="mx-auto">AL WAFA BI'AHDILLAH</h3>
              </v-row>
            </v-toolbar>
            <v-card-text>
              <v-card class="mx-auto mt-5 ml-5 mr-5" flat color="green lighten-5">
                <v-row class="text-center">
                  <v-col cols="12">
                    <v-text-field
                      label="ID Anggota"
                      rounded
                      outlined
                      append-icon="mdi-account"
                      v-model="nia"
                      required></v-text-field>
                  </v-col>
                  <v-col cols="12">
                    <v-text-field
                      label="Password"
                      v-model="password"
                      rounded
                      outlined
                      required
                      :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'"
                      :type="showPassword ? 'text' : 'password'"
                      @click:append="showPassword = !showPassword">
                    </v-text-field>
                  </v-col>
                  <v-col cols="12">
                    <v-btn color="primary" height="50" rounded depressed block @click="goLogin">SUBMIT</v-btn>
                    <v-row class="mt-1">
                      <v-col cols="6">
                        <a href="#" class="teal--text" style="text-decoration: none;">Admin Page</a>
                      </v-col>
                      <v-col cols="6">
                        <a href="#" class="teal--text text-decoration-none">Lupa Password</a>
                      </v-col>
                    </v-row>
                  </v-col>
                </v-row>
              </v-card>
            </v-card-text>
          </v-card>
        </v-container>
      </v-main>
    </v-app>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/vue@2.x/dist/vue.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    const Toast = Swal.mixin({
      toast: true,
      position: "top-end",
      showConfirmButton: false,
      timer: 3000,
      timerProgressBar: true,
      didOpen: (toast) => {
        toast.onmouseenter = Swal.stopTimer;
        toast.onmouseleave = Swal.resumeTimer;
      }
    });
    new Vue({
      el: '#app',
      vuetify: new Vuetify({
        theme: {
          themes: {
            light: {
              primary: '#11A39C', // #E53935
              secondary: '#B2DFDB', // #FFCDD2
              // accent: colors.indigo.base, // #3F51B5
            },
          },
        },
      }),
      data: {
        nia: null,
        password: null,
        showPassword: false,
      },
      methods: {
        async goLogin() {
          const param = {
            nia: this.nia,
            password: this.password
          }
          await axios.post('<?= base_url(); ?>api/user-login', param)
            .then((res) => {
              const store = {
                nama: res.data.user.nama,
                nia: res.data.user.nia,
                foto: res.data.user.foto
              }
              localStorage.setItem('user', JSON.stringify(store))
              localStorage.setItem('token', res.data.token)


              window.open('<?= base_url(); ?>profile', '_self')
            })
            .catch((err) => {
              if (err.response.status === 400) {
                Toast.fire({
                  icon: "error",
                  title: "Gagal Login"
                });
              }

            })
        },
      }
    })
  </script>
</body>

</html>