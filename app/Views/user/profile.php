<!DOCTYPE html>
<html>

<head>
  <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/@mdi/font@6.x/css/materialdesignicons.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.min.css" rel="stylesheet">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui">
</head>

<body style="background-color:#B2DFDB;">
  <div id="app">
    <v-app>
      <v-main style="background-color:#B2DFDB;">
        <v-toolbar color="primary" flat dark>
          <v-app-bar-nav-icon @click.stop="drawer = !drawer"></v-app-bar-nav-icon>
          <v-toolbar-title>MENU</v-toolbar-title>
          <v-spacer></v-spacer>
          <v-btn text><v-icon>mdi-export</v-icon></v-btn>
        </v-toolbar>

        <v-navigation-drawer
          v-model="drawer"
          absolute
          temporary
          class="px-8"
          >
          <v-list>
            <v-list-item>
              {{nama_user}}<br>
              {{id_user}}
            </v-list-item>
          </v-list>
          <v-list
            nav
            dense>
            <v-list-item-group
              v-model="group"
              active-class="deep-purple--text text--accent-4">
              <v-list-item>
                <v-list-item-title>Foo</v-list-item-title>
              </v-list-item>

              <v-list-item>
                <v-list-item-title>Bar</v-list-item-title>
              </v-list-item>

              <v-list-item>
                <v-list-item-title>Fizz</v-list-item-title>
              </v-list-item>

              <v-list-item>
                <v-list-item-title>Buzz</v-list-item-title>
              </v-list-item>
            </v-list-item-group>
          </v-list>
        </v-navigation-drawer>


        <v-container>
          <v-card class="mx-auto justify-center mt-5 pb-7" max-width="600" flat color="green lighten-5">
            <v-card color="teal" flat class="text-center py-5">
              <!-- <v-img
            class="mx-auto"
                  :src="foto" max-width="160" height="100%">

                  </v-img> -->
              <div class="mx-auto">
                <v-avatar
                  size="130"
                  outlined>
                  <img :src="foto" alt="alt">
                </v-avatar>
              </div>
              <h3 class="text-center white--text mt-3">{{ String(nama_user).toUpperCase()}}</h3>
            </v-card>
            <v-card-text class="px-10">
              <v-row class="mt-2">
                <v-col cols="6">
                  <div>ID Anggota</div>
                </v-col>
                <v-col cols="6">
                  <div class="font-weight-bold text-right">{{id_user}}</div>
                </v-col>
              </v-row>
              <v-divider></v-divider>
              <v-row class="mt-2">
                <v-col cols="6">
                  <div>Alamat</div>
                </v-col>
                <v-col cols="6">
                  <div class="font-weight-bold text-right">{{alamat_user}}</div>
                </v-col>
              </v-row>
              <v-divider></v-divider>

              <v-row class="mt-2">
                <v-col cols="6">
                  <div>Status</div>
                </v-col>
                <v-col cols="6">
                  <div class="text-right mb-2">
                    <v-chip small :color="warna_aktif" text-color="white">{{aktif_user}}</v-chip>
                  </div>
                </v-col>
              </v-row>
              <v-divider></v-divider>

              <v-row class="mt-2">
                <v-col cols="6">
                  <div>Iuran Belum Terbayar</div>
                </v-col>
                <v-col cols="6">
                  <div class="text-right mb-2">
                    <v-btn color="warning" x-small rounded depressed width="55" height="25">{{iuran_belum_bayar}}</v-btn>
                    <!-- <v-chip  small :color="warna_aktif" text-color="white">{{iuran_belum_bayar}}</v-chip> -->
                  </div>
                </v-col>
              </v-row>
              <v-divider></v-divider>
              <v-row class="mt-4">
                <v-col cols="12">
                  <v-btn color="info" depressed block @click="dialogFoto = true">Ubah Foto</v-btn>
                </v-col>
                <v-col cols="12">
                  <v-btn color="info" depressed block @click="loadUbahPassword()">Ubah Password</v-btn>
                </v-col>
              </v-row>

            </v-card-text>
          </v-card>

          <v-dialog
            v-model="dialogFoto">
            <v-card flat max-width="500" class="mx-auto">
              <v-toolbar color="info" flat dark>
                <v-toolbar-title>Ubah Foto Profile</v-toolbar-title>
              </v-toolbar>
              <v-card-text class="py-8">
                <v-row>
                  <v-col cols="12">
                    <v-file-input
                      label="Pilih Foto"
                      outlined
                      dense
                      show-size
                      hint="Harus berupa file jpg atau jpeg dan ukuran maximal 4mb"></v-file-input>
                  </v-col>
                  <v-col cols="12">
                    <v-btn color="success" block depressed>Simpan</v-btn>
                  </v-col>
                  <v-col cols="12">
                    <v-btn color="error" block depressed @click="dialogFoto = false">Batal</v-btn>
                  </v-col>
                </v-row>
              </v-card-text>
            </v-card>
          </v-dialog>

          <v-dialog
            v-model="dialogPassword">
            <v-card flat max-width="500" class="mx-auto">
              <v-toolbar color="info" flat dark>
                <v-toolbar-title>Ubah Password</v-toolbar-title>
              </v-toolbar>
              <v-card-text class="py-8">
                <v-row>
                  <v-col cols="12">
                    <v-text-field
                    outlined
                      label="Password Lama"
                      v-model="password_lama"
                    ></v-text-field>
                    <v-text-field
                    outlined
                      label="Password Baru"
                      v-model="password_baru"
                    ></v-text-field>
                    <v-text-field
                    outlined
                      label="Konfirmasi Password"
                      v-model="konfirmasi_password"
                    ></v-text-field>
                  </v-col>
                  <v-col cols="12">
                    <v-btn color="success" block depressed>Simpan</v-btn>
                  </v-col>
                  <v-col cols="12">
                    <v-btn color="error" block depressed @click="dialogPassword = false">Batal</v-btn>
                  </v-col>
                </v-row>
              </v-card-text>
            </v-card>
          </v-dialog>

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
      vuetify: new Vuetify({
        theme: {
          themes: {
            light: {
              primary: '#11A39C', // #E53935
              secondary: '#B2DFDB', // #FFCDD2
              accent: '#E8F5E9',
              anchor: '#B2DFDB'
            },
          },
        },
      }),
      data: {
        id_user: null,
        nama_user: null,
        alamat_user: null,
        aktif_user: null,
        warna_aktif: null,
        iuran_belum_bayar: 0,
        foto: null,
        config: null,
        token: null,
        drawer: false,
        group: null,
        dialogFoto: false,
        dialogPassword: false,
        password_lama: null,
        password_baru: null,
        konfirmasi_password: null,
      },
      watch: {
        group() {
          this.drawer = false
        },
      },
      created() {
        this.token = localStorage.getItem('token')
        this.config = {
          headers: {
            Authorization: `Bearer ${this.token}`
          }
        }
        this.getProfile()

      },
      methods: {
        async getProfile() {
          await axios.get('<?= base_url(); ?>api/user/profile', this.config)
            .then((res) => {
              this.id_user = res.data.nia
              this.nama_user = res.data.nama
              this.alamat_user = res.data.alamat
              this.aktif_user = res.data.aktif
              this.warna_aktif = res.data.aktif == 'aktif' ? 'green' : 'red'
              this.iuran_belum_bayar = res.data.iuran_belum_terbayar
              this.foto = res.data.foto
              console.log(res.data);
            })
            .catch((err) => {
              console.log(err.response.data);

            })
        },
        async loadUbahPassword() {
          await axios.get('<?= base_url(); ?>api/user/profile/edit-password', this.config)
            .then((res) => {
              this.password_lama = res.data.password_lama
              this.password_baru = res.data.password_baru
              this.konfirmasi_password = res.data.konfirmasi_password
              this.dialogPassword = true
              console.log(res.data);
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