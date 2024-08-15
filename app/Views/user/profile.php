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
            <v-card class="mx-auto justify-center" max-width="600" flat color="green lighten-5">
                <v-toolbar color="primary" dark flat class="text-center">
                    <p class="text-center">PROFILE</p>
                </v-toolbar>
                <v-card-text>
                  <div class="text-center">
                    <v-avatar
                      size="75"
                    >
                      <img src="https://blogtimenow.com/wp-content/uploads/2014/06/hide-facebook-profile-picture-notification.jpg" alt="alt">
                    </v-avatar>
                  </div>
                    <v-row>
                    <v-col cols="4">
                          ID Anggota
                        </v-col>
                        <v-col cols="8">
                          {{id_user}}
                        </v-col>    
                    <v-col cols="4">
                          Nama
                        </v-col>
                        <v-col cols="8">
                          {{nama_user}}
                        </v-col>
                    </v-row>
                </v-card-text>
            </v-card>
        </v-container>
      </v-main>
    </v-app>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/vue@2.x/dist/vue.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
  <script>
    new Vue({
      el: '#app',
      vuetify: new Vuetify(),
      data: {
        id_user: localStorage.getItem('nia'),
        nama_user: localStorage.getItem('nama'),
      },
      created(){

      },
      methods: {
        async goLogin() {
            const param = {
                nia: this.nia,
                password: this.password
            }
            await axios.post('<?= base_url();?>api/user-login', param)
            .then((res) => {
                console.log(res.data);
                localStorage.setItem('token', res.data.token)
                localStorage.setItem('nama', res.data.user.nama)
                localStorage.setItem('nia', res.data.user.nia)

                window.open('<?= base_url();?>profile')
            })
            .catch((err) => {
                console.log(err.response.data);
                
            })
        },
      }
    })
  </script>
</body>
</html>