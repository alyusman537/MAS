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
      <!-- <v-main style="background-color:#B2DFDB;"> -->
      <v-main>
        <admin-nav-bar :title="title"></admin-nav-bar>

        <v-container>
          <v-card class="mx-auto justify-center mt-5 pb-7" max-width="800" flat color="teal lighten-5">
            <!--v-card color="teal" flat class="text-center mx-auto py-3" dark>
              <h3 class="mx-auto">Daftar Infaq</h3>
            </v-card-->
            <v-card-text class="px-2">
              <v-row class="px-3">
                <v-col cols="8">
                  <v-text-field
                    label="Cari Anggota"
                    v-model="cari"></v-text-field>
                </v-col>
                <v-col cols="4">
                  <v-btn class="mt-4" color="info" small depressed @click="loadDialogBaru()">
                    <v-icon>mdi-plus</v-icon>Anggota
                  </v-btn>
                </v-col>
              </v-row>
              <v-data-table
                :headers="header"
                :items="listAnggota"
                :search="cari"
                class="elevation-0">
                <!--status: el.aktif == '1' ? true : false,
                  aktif: el.aktif == '1' ? 'Aktif' : 'Nonaktif',
                  warna: el.aktif == '1' ? 'success' : 'error'-->
                <template v-slot:item.aktif="{ item }">
                  <div v-if="item.status" class="green--text">{{ item.aktif }}</div>
                  <div v-else class="red--text">{{ item.aktif }}</div>
                </template>
                <template v-slot:item.aksi="{ item }">
                  <v-btn
                    color="info"
                    depressed
                    rounded
                    small
                    dark
                    @click="lihatDetail(item.id)">detail</v-btn>
                  <v-btn
                    :color="item.warna"
                    depressed
                    rounded
                    small
                    dark
                    @click="loadDialogEdit(item.id)">
                    Edit
                  </v-btn>
                  <v-btn color="error" small rounded depressed dark @click="hapusAnggota(item.id)">hapus</v-btn>
                </template>
              </v-data-table>
            </v-card-text>
          </v-card>

          <v-dialog
            v-model="dialog">
            <v-card flat max-width="500" class="mx-auto">
              <v-toolbar color="primary" flat dark>
                <v-toolbar-title>{{ titileDialog }}</v-toolbar-title>
              </v-toolbar>
              <v-card-text class="py-8">
                <v-text-field
                  label="NIA"
                  v-model="anggota.nia"
                  :disabled="isEdit"></v-text-field>
                <v-text-field
                  label="Nama"
                  v-model="anggota.nama"></v-text-field>
                <v-textarea
                  rows="2"
                  label="Alamat"
                  v-model="anggota.alamat"></v-textarea>
                <v-text-field
                  label="WA"
                  v-model="anggota.wa"></v-text-field>
                <v-text-field
                  label="Email"
                  v-model="anggota.email"></v-text-field>
                <v-autocomplete
                  :items="listWilayah"
                  item-text="kode"
                  item-value="kode"
                  v-model="anggota.wilayah"
                  label="Pilih Wilayah"></v-autocomplete>

                <v-radio-group
                  v-if="isEdit"
                  v-model="anggota.level"
                  row>
                  <v-radio
                    label="Admin"
                    value="admin"></v-radio>
                  <v-radio
                    label="User"
                    value="user"></v-radio>
                </v-radio-group>

                <!-- v-radio-group
                :disabled="isEdit"
                  v-if="isEdit"
                  v-model="anggota.aktif"
                  row>
                  <v-radio
                    label="Aktif"
                    value="aktif"></v-radio>
                  <v-radio
                    label="Nonaktif"
                    value="nonaktif"></v-radio>
                </v-radio-group-->
                <v-row>
                  <v-col cols="12">
                    <v-btn v-if="isEdit" color="success" block depressed @click="update()">Simpan</v-btn>
                    <v-btn v-else="isEdit" color="success" block depressed @click="simpan()">Simpan</v-btn>
                  </v-col>
                  <v-col cols="12">
                    <v-btn color="error" block depressed @click="dialog = false">Batal</v-btn>
                  </v-col>
                </v-row>
              </v-card-text>
            </v-card>
          </v-dialog>

          <v-dialog
            v-model="dialogDetail">
            <v-card flat max-width="500" class="mx-auto">
              <v-toolbar color="primary" flat dark>
                <v-toolbar-title>Data Anggota {{ anggota.nia }}</v-toolbar-title>
                <v-spacer></v-spacer>
                <v-btn outlined @click="dialogDetail = false"><v-icon>mdi-close</v-icon></v-btn>
              </v-toolbar>
              <v-card-text class="py-8">
                <v-card color="teal" flat class="text-center py-5">
                  <div class="mx-auto">
                    <img :src="anggota.foto" width="100%" height="150" alt="alt">
                  </div>
                </v-card>
                <v-card-text class="px-10">
                  <v-row class="mt-2">
                    <v-col cols="4">
                      <div>ID</div>
                    </v-col>
                    <v-col cols="8">
                      <div class="font-weight-bold text-right">{{anggota.nia}}</div>
                    </v-col>
                  </v-row>
                  <v-divider></v-divider>
                  <v-row class="mt-2">
                    <v-col cols="4">
                      <div>Nama</div>
                    </v-col>
                    <v-col cols="8">
                      <div class="font-weight-bold text-right">{{anggota.nama}}</div>
                    </v-col>
                  </v-row>
                  <v-divider></v-divider>
                  <v-row class="mt-2">
                    <v-col cols="4">
                      <div>Alamat</div>
                    </v-col>
                    <v-col cols="8">
                      <div class="font-weight-bold text-right">{{anggota.alamat}}</div>
                    </v-col>
                  </v-row>
                  <v-divider></v-divider>

                  <v-row class="mt-2">
                    <v-col cols="4">
                      <div>WA</div>
                    </v-col>
                    <v-col cols="8">
                      <div class="font-weight-bold text-right">{{anggota.wa}}</div>
                    </v-col>
                  </v-row>
                  <v-divider></v-divider>

                  <v-row class="mt-2">
                    <v-col cols="4">
                      <div>Email</div>
                    </v-col>
                    <v-col cols="8">
                      <div class="font-weight-bold text-right">{{anggota.email}}</div>
                    </v-col>
                  </v-row>
                  <v-divider></v-divider>

                  <v-row class="mt-2">
                    <v-col cols="6">
                      <div>Status</div>
                    </v-col>
                    <v-col cols="6">
                      <div class="font-weight-bold text-right">{{ anggota.aktif }}</div>
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
  <script src="<?= base_url(); ?>api/render/js/dash-admin.js"></script>
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
              accent: '#E8F5E9',
              anchor: '#B2DFDB'
            },
          },
        },
      }),
      data: {
        title: "DATA ANGGOTA",
        config: null,
        token: null,
        header: [{
            text: 'NIA',
            align: 'start', // sortable: false,
            value: 'nia',
          },
          {
            text: 'Nama',
            value: 'nama'
          },
          {
            text: 'Whatsapp',
            value: 'wa'
          },
          {
            text: 'Wilayah',
            value: 'wilayah'
          },
          {
            text: 'Level',
            value: 'level'
          },
          {
            text: 'Status',
            value: 'aktif',
          },
          {
            text: 'Aksi',
            value: 'aksi',
          },
        ],
        listAnggota: [],
        dialogDetail: false,
        cari: null,
        dialog: false,
        titileDialog: null,
        anggota: {
          id: null,
          nia: null,
          foto: null,
          nama: null,
          alamat: null,
          wa: null,
          email: null,
          level: null,
          wilayah: null,
          aktif: null,
        },
        error: {
          id: null,
          nia: null,
          nama: null,
          alamat: null,
          wa: null,
          email: null,
          level: null,
          wilayah: null,
          aktif: null,
        },
        isEdit: false,
        listWilayah: [],
      },
      watch: {},
      created() {
        this.token = localStorage.getItem('admin-token')
        this.config = {
          headers: {
            Authorization: `Bearer ${this.token}`
          }
        }
        this.getAnggota()
      },
      methods: {
        toast(ikon, pesan) {
          Toast.fire({
            icon: ikon,
            title: pesan
          });
        },
        async getAnggota() {
          await axios.get('<?= base_url(); ?>api/admin/anggota', this.config)
            .then((res) => {
              console.log(res.data);
              this.listAnggota = res.data.map((el) => {
                const dorong = {
                  id: el.id,
                  nia: el.nia,
                  nama: String(el.nama).toUpperCase(),
                  alamat: el.alamat,
                  wa: el.wa,
                  email: el.email,
                  level: el.level,
                  wilayah: el.wilayah,
                  status: el.aktif == 'aktif' ? true : false,
                  aktif: el.aktif == 'aktif' ? 'Aktif' : 'Nonaktif',
                  warna: el.aktif == 'aktif' ? 'success' : 'error'
                }
                return dorong
              })
            })
            .catch((err) => {
              console.log('getlist infq ', err);
            })
        },
        async lihatDetail(id) {
          await axios.get('<?= base_url(); ?>api/admin/anggota/' + id, this.config)
            .then((res) => {
              console.log('detail ', res.data);
              this.anggota.id = res.data.id
              this.anggota.nia = res.data.nia
              this.anggota.foto = res.data.foto
              this.anggota.nama = res.data.nama
              this.anggota.alamat = res.data.alamat
              this.anggota.wa = res.data.wa
              this.anggota.email = res.data.email
              this.anggota.level = res.data.level
              this.anggota.wilayah = res.data.wilayah
              this.anggota.aktif = res.data.aktif
              this.dialogDetail = true
            })
            .catch((err) => {
              console.log(err.response.data);

            })
        },
        async loadDialogBaru() {
          await axios.get('<?= base_url(); ?>api/admin/anggota/new', this.config)
            .then((res) => {
              console.log(res.data);
              this.anggota.alamat = null
              this.anggota.email = null
              this.anggota.nama = null
              this.anggota.nia = null
              this.anggota.wa = null
              this.anggota.wilayah = null
              this.isEdit = false
              this.titileDialog = 'Tambah Anggota Baru'
              this.dialog = true
              this.listWilayah = res.data.wilayah
            })
            .catch((err) => {
              this.toast('error', JSON.stringify(err.response.data));
            })

        },
        async simpan() {
          const param = {
            alamat: this.anggota.alamat,
            email: this.anggota.email,
            nama: this.anggota.nama,
            nia: this.anggota.nia,
            wa: this.anggota.wa,
            wilayah: this.anggota.wilayah,
          }
          await axios.post('<?= base_url() ?>api/admin/anggota', param, this.config)
            .then((res) => {
              console.log(res.data);
              this.toast('success', 'Tambah data anggota ' + this.anggota.nia + ' berhasil disimpan.')
              this.dialog = false
              this.isEdit = true
              this.getAnggota()
            })
            .catch((err) => {
              if (err.response.status === 409) {
                const errNia = err.response.data.messages.nia ? err.response.data.messages.nia + '\n' : ''
                const errNama = err.response.data.messages.nama ? err.response.data.messages.nama + '\n' : ''
                const errAlamat = err.response.data.messages.alamat ? err.response.data.messages.alamat + '\n' : ''
                const errWa = err.response.data.messages.wa ? err.response.data.messages.wa + '\n' : ''
                const errEmail = err.response.data.messages.email ? err.response.data.messages.email + '\n' : ''
                const errWilayah = err.response.data.messages.wilayah ? err.response.data.messages.wilayah : ''
                const pesan = errNia + errNama + errAlamat + errWa + errEmail + errWilayah
                this.toast('error', pesan)
                return false
              }
              if (err.response.status === 402) {
                this.toast('error', err.response.data.messages.error)
              } else {
                this.toast('error', JSON.stringify(err.response.data));
              }
              console.log(err.response.data);

            })
        },
        async loadDialogEdit(id) {
          await axios.get('<?= base_url() ?>api/admin/anggota/edit/' + id, this.config)
            .then((res) => {
              console.log(res.data);
              const ra = res.data.anggota
              this.anggota.alamat = ra.alamat
              this.anggota.email = ra.email
              this.anggota.nama = ra.nama
              this.anggota.nia = ra.nia
              this.anggota.wa = ra.wa
              this.anggota.wilayah = ra.wilayah
              this.anggota.id = ra.id
              this.anggota.aktif = ra.aktif
              this.anggota.level = ra.level

              this.listWilayah = []
              this.listWilayah = res.data.wilayah
              this.isEdit = true
              this.titileDialog = 'Edit Data Anggota'
              this.dialog = true

              console.log('anggota ', this.anggota);

            })
            .catch((err) => {
              console.log(err.response.data);
            })
        },
        async update() {
          const param = {}
          await axios.put('<?= base_url() ?>api/admin/anggota/' + this.anggota.id, param, this.config)
            .then((res) => {
              console.log(res.data);
              this.toast('success', 'Data anggota ' + this.anggota.nia + ' berhasil diperbarui.')
              this.getAnggota()
              this.dialog = false
            })
            .catch((err) => {
              if (err.response.status == 409) {
                // const errKode = err.response.data.messages.kode ? err.response.data.messages.kode : ''
                // const errKeterangan = err.response.data.messages.keterangan ? err.response.data.messages.keterangan : ''
                // const pesan =
                //   this.toast('error', errKode + '\n' + errKeterangan)
                return false
              }
              if (err.response.status == 402) {
                this.toast('error', err.response.data.messages.error)
              } else {
                this.toast('error', JSON.stringify(err.response.data));
              }
              console.log('status error : ', err.response.status);
            })
        },
      }
    })
  </script>
</body>

</html>