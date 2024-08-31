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
      <v-img :src="bgImg">
        <v-main>
          <v-container>
            <v-card class="mx-auto my-auto justify-center" max-width="500" height="425" flat style="background-color: #fff;  opacity: .8;"> <!-- color="green lighten-5"-->
              <v-card style="background-color: #fff; border: 0.5px solid rgba(255, 255, 255, 0.5);backdrop-filter: blur(2px);-webkit-backdrop-filter: blur(8px);" flat>

                <div flat class="py-3">
                  <!-- <div color="primary" dark flat class="py-3"> -->
                  <v-img :src="logo" height="100%" width="150" class="mx-auto"></v-img>
                  <!-- <v-row>
                    <h3 class="mx-auto mt-2">AL WAFA BI'AHDILLAH</h3>
                  </v-row> -->
                </div>
                <v-card-text>


                  <div>
                    <div class="mx-auto mt-5 ml-5 mr-5" max-width="450" flat> <!--color="green lighten-5"-->
                      <v-row class="text-center">
                        <v-col cols="12">
                          <v-text-field
                            label="ID Anggota"
                            rounded
                            append-icon="mdi-account"
                            v-model="nia"
                            color="teal"
                            outlined
                            required
                            filled
                            class="text-green"></v-text-field>

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
                          <v-btn color="primary" height="50" rounded depressed block @click="goLogin">LOGIN</v-btn>
                          <v-row class="mt-1">
                            <v-col cols="6">
                              <a href="#" class="teal--text" style="text-decoration: none;" @click="goAdmin">Admin Page</a>
                            </v-col>
                            <v-col cols="6">
                              <a href="#" class="teal--text text-decoration-none" @click="gotReset">Lupa Password</a>
                            </v-col>
                          </v-row>
                        </v-col>
                      </v-row>
                    </div>
                  </div>
                </v-card-text>
              </v-card>
            </v-card>

          </v-container>
        </v-main>
    </v-app>
    </v-img>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/vue@2.x/dist/vue.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.min.js"></script>
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
        logo: '<?= base_url(); ?>logo_alwafa_hijau.png',
        bgImg: '<?= base_url() ?>payung.jpg',
        config: null,
      },
      created() {
        this.token = localStorage.getItem('token')
        this.config = {
          headers: {
            Authorization: `Bearer ${this.token}`
          }
        }
        if(this.token != null) {
          this.getProfile()
        }
      },
      methods: {
        async getProfile() {
          await axios.get('<?= base_url(); ?>api/user/profile', this.config)
            .then(() => {
              window.open('<?= base_url(); ?>profile', '_self')
            })
            .catch((err) => {
              if(err.response.status === 401) {
                localStorage.removeItem('token')
                return false
              };
              
            })
        },
        async goLogin() {
          const param = {
            nia: String(this.nia).toUpperCase(),
            password: String(this.password).toUpperCase()
          }
          await axios.post('<?= base_url(); ?>api/user-login', param)
            .then((res) => {
              console.log(res.data);

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
              console.log(err.response.data);
              if (err.response.status === 409) {
                Toast.fire({
                  icon: "error",
                  title: err.response.data.error
                });
              }
              if (err.response.status === 400) {
                const err_nia = !err.response.data.messages.nia ? '' : err.response.data.messages.nia
                const err_pass = !err.response.data.messages.password ? '' : err.response.data.messages.password
                const pesan = err_nia + '\n' + err_pass
                Toast.fire({
                  icon: "error",
                  title: pesan
                });
              }
            })
        },
        goAdmin(){
          window.open('<?= base_url();?>administrator/login', '_self')
        },
        gotReset(){
          window.open('<?= base_url();?>reset-password', '_self')
        }
      }
    })
  </script>
</body>

</html>