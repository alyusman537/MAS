<!DOCTYPE html>
<html>

<head>
<link rel="shortcut icon" type="image/png" href="/favicon.ico">
  <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/@mdi/font@6.x/css/materialdesignicons.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.min.css" rel="stylesheet">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui">
</head>

<body style="background-color:#B2DFDB;">
  <div id="app">
    <v-app>
      <v-main style="background-color:#B2DFDB;">
        <nav-bar :title="title"></nav-bar>

        <v-container>
          <v-card class="mx-auto justify-center mt-5 pb-7" max-width="600" flat> <!--color="green lighten-5" -->
            <v-card color="teal" flat class="text-center py-5">
              <div class="mx-auto">
                <v-avatar
                  size="165"
                  class="profile"
                  color="grey">
                  <img :src="foto" alt="alt">
                </v-avatar>
              </div>
              <h3 class="text-center white--text mt-3">{{ String(nama_user).toUpperCase()}}</h3>
            </v-card>
            <v-card-text class="px-10">
              <v-row class="mt-2">
                <v-col cols="4">
                  <div>ID Anggota</div>
                </v-col>
                <v-col cols="8">
                  <div class="font-weight-bold text-right">{{id_user}}</div>
                </v-col>
              </v-row>
              <v-divider></v-divider>
              <v-row class="mt-2">
                <v-col cols="4">
                  <div>Alamat</div>
                </v-col>
                <v-col cols="8">
                  <div class="font-weight-bold text-right">{{alamat_user}}</div>
                </v-col>
              </v-row>
              <v-divider></v-divider>

              <v-row class="mt-2">
                <v-col cols="4">
                  <div>WA</div>
                </v-col>
                <v-col cols="8">
                  <div class="font-weight-bold text-right">{{wa}}</div>
                </v-col>
              </v-row>
              <v-divider></v-divider>

              <v-row class="mt-2">
                <v-col cols="4">
                  <div>Email</div>
                </v-col>
                <v-col cols="8">
                  <div class="font-weight-bold text-right">{{email}}</div>
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
                  <v-btn color="info" depressed block @click="loadDialogDiri()">Ubah Data Diri</v-btn>
                </v-col>
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
            v-model="dialogDiri">
            <v-card flat max-width="500" class="mx-auto">
              <v-toolbar color="primary" flat dark>
                <v-toolbar-title>Ubah Data Diri</v-toolbar-title>
              </v-toolbar>
              <v-card-text class="py-8">
                <v-row>
                  <v-col cols="12">
                    <v-text-field
                      outlined
                      label="Nama"
                      v-model="diri.nama"></v-text-field>
                    <v-text-field
                      outlined
                      label="Alamat"
                      v-model="diri.alamat"></v-text-field>
                    <v-text-field
                      outlined
                      label="Nomor WA"
                      v-model="diri.wa"></v-text-field>
                      <v-text-field
                      outlined
                      label="Alamat Email"
                      v-model="diri.email"></v-text-field>
                  </v-col>
                  <v-col cols="12">
                    <v-btn color="success" block depressed @click="updatePassword()">Simpan</v-btn>
                  </v-col>
                  <v-col cols="12">
                    <v-btn color="error" block depressed @click="dialogDiri = false">Batal</v-btn>
                  </v-col>
                </v-row>
              </v-card-text>
            </v-card>
          </v-dialog>

          <v-dialog
            v-model="dialogFoto">
            <v-card flat max-width="500" class="mx-auto">
              <v-toolbar color="primary" flat dark>
                <v-toolbar-title>Ubah Foto Profile</v-toolbar-title>
              </v-toolbar>
              <v-card-text class="py-8">
                <v-form id="upload-form" enctype="multipart/form-data" method="post" accept-charset="utf-8">
                  <v-row>
                    <v-col cols="12">
                      <v-file-input
                        label="Pilih Foto"
                        outlined
                        dense
                        show-size
                        hint="Harus berupa file jpg atau jpeg dan ukuran maximal 4mb"
                        @change="upload"></v-file-input>
                    </v-col>
                    <v-col cols="12">
                      <v-btn color="success" block depressed type="submit" @click.prevent="gantiFoto()">Simpan</v-btn>
                    </v-col>
                    <v-col cols="12">
                      <v-btn color="error" block depressed @click="dialogFoto = false">Batal</v-btn>
                    </v-col>
                  </v-row>
                </v-form>
              </v-card-text>
            </v-card>
          </v-dialog>

          <v-dialog
            v-model="dialogPassword">
            <v-card flat max-width="500" class="mx-auto">
              <v-toolbar color="primary" flat dark>
                <v-toolbar-title>Ubah Password</v-toolbar-title>
              </v-toolbar>
              <v-card-text class="py-8">
                <v-row>
                  <v-col cols="12">
                    <v-text-field
                      outlined
                      label="Password Lama"
                      :error="error_password.lama"
                      v-model="password_lama"></v-text-field>
                    <v-text-field
                      outlined
                      label="Password Baru"
                      :error="error_password.baru"
                      hint-error="Password harus terdiri dari 4 karakter atau lebih"
                      v-model="password_baru"></v-text-field>
                    <v-text-field
                      outlined
                      label="Konfirmasi Password"
                      :error="error_password.konfirmasi"
                      v-model="konfirmasi_password"></v-text-field>
                  </v-col>
                  <v-col cols="12">
                    <v-btn color="success" block depressed @click="updatePassword()">Simpan</v-btn>
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
  <script src="<?= base_url(); ?>api/render/js/dash.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
        title: "PROFILE",
        config: null,
        token: null,
        id_user: null,
        nama_user: null,
        alamat_user: null,
        wa: null,
        email: null,
        aktif_user: null,
        warna_aktif: null,
        iuran_belum_bayar: 0,
        foto: null,
        dialogDiri: false,
        diri: {
          alamat: null,
          email: null,
          nama: null,
          wa: null
        },
        dialogFoto: false,
        attFile: null,
        dialogPassword: false,
        password_lama: null,
        password_baru: null,
        konfirmasi_password: null,
        error_password: {
          lama: null,
          baru: null,
          konfirmasi: null
        }
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
              this.email = res.data.email
              this.wa = res.data.wa
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
        async loadDialogDiri() {
          await axios.get('<?= base_url(); ?>api/user/profile/edit/' + this.id_user, this.config)
            .then((res) => {
              console.log(res.data);
              this.diri.nama = res.data.nama
              this.diri.alamat = res.data.alamat
              this.diri.wa = res.data.wa
              this.diri.email = res.data.email
              this.dialogDiri = true
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
        async updatePassword() {
          const param = {
            password_lama: this.password_lama,
            password_baru: this.password_baru,
            konfirmasi_password: this.konfirmasi_password,
          }
          await axios.put('<?= base_url(); ?>api/user/profile/update-password/' + this.id_user, param, this.config)
            .then((res) => {

              this.dialogPassword = false
              console.log(res.data);
              Swal.fire({
                title: "Pergantian password telah berhasil Anda lakukan.",
                showDenyButton: false,
                confirmButtonText: "OK",
              }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                  localStorage.clear()
                  window.open('<?= base_url(); ?>login', '_self')
                }
              });
            })
            .catch((err) => {
              console.log(err.response.data);
              // if(err.response.data.messages.konfirmasi_password) {
              //   console.log('konfir masine error');

              // }
              if (err.response.status === 409) {
                const err_respon = err.response.data.messages
                if (err_respon.password_baru) this.error_password.baru = true // err_respon.password_baru
                if (err_respon.password_lama) this.error_password.lama = true // err_respon.password_baru
                if (err_respon.konfirmasi_password) this.error_password.konfirmasi = true // err_respon.password_baru
                Swal.fire({
                  title: "Gagal!",
                  text: "Password anda gagal diubah.",
                  icon: "error"
                });
              }

            })
        },

        upload(event) {
          console.log("nama", event);
          console.log("type", event.type);
          console.log("ukuran", event.size);

          if (event.type != "image/jpeg") {
            alert("Silakan upload file yang dengan ekstensi .jpeg atau.jpg");
            this.linkFoto = "";
            this.attFile = null;
            return false;
          }
          this.attFile = event;
          this.linkFoto = URL.createObjectURL(event);
        },
        gantiFoto() {
          if (this.attFile == null) {
            alert("Anda belum memilih foto");
            return false;
          }
          let fdata = new FormData();
          fdata.append("foto", this.attFile);
          axios
            .post('<?= base_url(); ?>api/user/profile/foto', fdata, this.config)
            .then((res) => {
              console.log(res.data);
              const localData = JSON.parse(localStorage.getItem('user'))
              const store = {
                nama: localData.nama,
                nia: localData.nia,
                foto: res.data.foto
              }
              localStorage['user'] = JSON.stringify(store)
              Swal.fire({
                title: "Foto profile Anda telah berhasil diubah.",
                showDenyButton: false,
                confirmButtonText: "OK",
              }).then((result) => {
                /* Read more about isConfirmed, isDenied below */
                if (result.isConfirmed) {
                  location.reload();
                }
              });

            })
            .catch((err) => {
              console.log(err.response);
            });
        },
        ///////

      }
    })
  </script>
</body>

</html>