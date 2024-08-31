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
                    label="Cari Wilayah"
                    v-model="cari"></v-text-field>
                </v-col>
                <v-col cols="4">
                  <v-btn class="mt-4" color="info" small depressed @click="loadDialogWilayahBaru()"><v-icon>mdi-plus</v-icon>Wilayah</v-btn>
                </v-col>
              </v-row>
              <v-data-table
                :headers="headerWilayah"
                :items="listWilayah"
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
                    :color="item.warna"
                    depressed
                    rounded
                    small
                    dark
                    @click="loadDialogWilayahEdit(item.id)">
                    Edit
                  </v-btn>
                  <v-btn color="error" small rounded depressed dark @click="hapusWilayah(item.id)">hapus</v-btn>
                </template>
              </v-data-table>
            </v-card-text>
          </v-card>

          <v-dialog
            v-model="dialogWilayah">
            <v-card flat max-width="500" class="mx-auto">
              <v-toolbar color="primary" flat dark>
                <v-toolbar-title>{{ titileDialog }}</v-toolbar-title>
              </v-toolbar>
              <v-card-text class="py-8">
                <v-text-field
                  label="Kode"
                  v-model="wilayah.kode"
                  :disabled="isEdit"></v-text-field>
                <v-text-field
                  label="Keterangan"
                  v-model="wilayah.keterangan"></v-text-field>

                <v-radio-group
                v-if="isEdit"
                  v-model="wilayah.aktif"
                  row>
                  <v-radio
                    label="Aktif"
                    value="1"></v-radio>
                  <v-radio
                    label="Nonaktif"
                    value="0"></v-radio>
                </v-radio-group>
                <v-row>
                  <v-col cols="12">
                    <v-btn v-if="isEdit" color="success" block depressed @click="updateDataWilayah()">Simpan Update</v-btn>
                    <v-btn v-else="isEdit" color="success" block depressed @click="addWilayah()">Simpan Baru</v-btn>
                  </v-col>
                  <v-col cols="12">
                    <v-btn color="error" block depressed @click="dialogWilayah = false">Batal</v-btn>
                  </v-col>
                </v-row>
              </v-card-text>
            </v-card>
          </v-dialog>

        </v-container>
      </v-main>
    </v-app>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/vue@2.x/dist/vue.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.min.js"></script>
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
        title: "DATA WILAYAH",
        config: null,
        token: null,
        headerWilayah: [{
            text: 'Kode',
            align: 'start', // sortable: false,
            value: 'kode',
          },
          {
            text: 'Keterangan',
            value: 'keterangan'
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
        listWilayah: [],
        cari: null,
        dialogWilayah: false,
        titileDialog: null,
        wilayah: {
          id: 0,
          kode: null,
          keterangan: null,
          aktif: true,
        },
        isEdit: false,
      },
      watch: {},
      created() {
        this.token = localStorage.getItem('admin-token')
        this.config = {
          headers: {
            Authorization: `Bearer ${this.token}`
          }
        }
        this.getWilayah()
      },
      methods: {
        toast(ikon, pesan) {
          Toast.fire({
            icon: ikon,
            title: pesan
          });
        },
        keluar() {
          localStorage.clear()
          window.open('<?= base_url(); ?>administrator/login', '_self')
        },
        async refresh() {
          await axios.get('<?= base_url() ?>api/admin-refresh', this.config)
            .then((res) => {
              localStorage.setItem('admin-token', res.data.new_token)
            })
            .catch()
        },
        async getWilayah() {
          await axios.get('<?= base_url(); ?>api/admin/wilayah', this.config)
            .then((res) => {
              // console.log(res.data);
              this.refresh()
              this.listWilayah = res.data.map((el) => {
                const dorong = {
                  id: el.id,
                  kode: el.kode,
                  keterangan: el.keterangan,
                  status: el.aktif == '1' ? true : false,
                  aktif: el.aktif == '1' ? 'Aktif' : 'Nonaktif',
                  warna: el.aktif == '1' ? 'success' : 'error'
                }
                return dorong
              })
            })
            .catch((err) => {
              if(err.response.status == 401){
                this.keluar()
              }
              console.log('getlist infq ', err);
            })
        },
        loadDialogWilayahBaru() {
          this.wilayah.kode = null
          this.wilayah.keterangan = null
          this.isEdit = false
          this.titileDialog = 'Tambah Wilayah Baru'
          this.dialogWilayah = true
        },
        async addWilayah() {
          const param = {
            kode: this.wilayah.kode,
            keterangan: this.wilayah.keterangan
          }
          await axios.post('<?= base_url() ?>api/admin/wilayah', param, this.config)
            .then((res) => {
              // console.log(res.data);
              this.refresh()
              this.toast('success', 'Tambah data wilayah '+res.data.kode+ ' berhasil disimpan.')
              this.dialogWilayah = false
              this.isEdit = true
            })
            .catch((err) => {
              if(err.response.status == 401){
                this.keluar()
              }
              if(err.response.status === 409) {
                const errKode = err.response.data.messages.kode ? err.response.data.messages.kode : ''
                const errKeterangan = err.response.data.messages.keterangan ? err.response.data.messages.keterangan : ''
                const pesan = 
                this.toast('error', errKode +'\n' +errKeterangan)
                return false
              }
              if(err.response.status === 402) {
                this.toast('error', err.response.data.messages.error)
              }
              this.toast('error', JSON.stringify(err.response.data));
            })
        },
        async loadDialogWilayahEdit(id) {
          await axios.get('<?= base_url() ?>api/admin/wilayah/' + id, this.config)
            .then((res) => {
              // console.log(res.data);
              this.refresh()
              this.wilayah.id = parseInt(res.data.id)
              this.wilayah.kode = res.data.kode
              this.wilayah.keterangan = res.data.keterangan
              this.wilayah.aktif = res.data.aktif
              this.isEdit = true
              this.titileDialog = 'Edit Data Wilayah'
              this.dialogWilayah = true
              console.log(this.wilayah);
              
            })
            .catch((err) => {
              if(err.response.status == 401){
                this.keluar()
              }
              console.log(err.response.data);
            })
        },
        async updateDataWilayah() {
          const param = {
            keterangan: this.wilayah.keterangan,
            aktif: this.wilayah.aktif
          }
          await axios.put('<?= base_url() ?>api/admin/wilayah/' + this.wilayah.id, param, this.config)
            .then((res) => {
              // console.log(res.data);
              this.refresh()
              this.toast('success', 'Data wilayah '+this.wilayah.kode+ ' berhasil diperbarui.')
              this.getWilayah()
              this.dialogWilayah = false
            })
            .catch((err) => {
              if(err.response.status == 401){
                this.keluar()
              }
              if(err.response.status == 409) {
                const errKode = err.response.data.messages.kode ? err.response.data.messages.kode : ''
                const errKeterangan = err.response.data.messages.keterangan ? err.response.data.messages.keterangan : ''
                const pesan = 
                this.toast('error', errKode +'\n' +errKeterangan)
                return false
              }
              if(err.response.status == 402) {
                this.toast('error', err.response.data.messages.error)
              } else {
                this.toast('error', JSON.stringify(err.response.data));
              }
              console.log('status error : ',err.response.status);
            })
        },
      }
    })
  </script>
</body>

</html>