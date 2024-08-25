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
                <v-col cols="7">
                  <v-text-field
                    label="Cari Data Infaq"
                    v-model="cari"></v-text-field>
                </v-col>
                <v-col cols="5">
                  <v-select
                    :items="lunasataubelum"
                    v-model="isLunas"
                    label="Status"
                    @change="getList()"
                  ></v-select>
                </v-col>
                <!-- <v-col cols="4">
                  <v-btn class="mt-4" color="info" small depressed @click="loadDialogBaru()">
                    <v-icon>mdi-plus</v-icon>Infaq
                  </v-btn>
                </v-col> -->
              </v-row>
              <v-data-table
                :headers="header"
                :items="list"
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
                  <v-btn
                    color="warning"
                    depressed
                    rounded
                    small
                    dark
                    @click="loadGenerate(item)">
                    Generate
                  </v-btn>
                  <!-- <v-btn color="error" small rounded depressed dark @click="hapusAnggota(item.id)">hapus</v-btn> -->
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
                  label="Kode"
                  v-model="infaq.kode"
                  :disabled="isEdit"
                  v-if="isEdit"></v-text-field>
                <v-text-field
                  label="Acara"
                  v-model="infaq.acara"
                  :disabled="isEdit"></v-text-field>
                <v-textarea
                  rows="2"
                  label="Keterangan"
                  v-model="infaq.keterangan"></v-textarea>
                <v-dialog
                  ref="dialog"
                  v-model="modal"
                  :return-value.sync="infaq.tanggal_acara"
                  persistent
                  width="290px">
                  <template v-slot:activator="{ on, attrs }">
                    <v-text-field
                      v-model="infaq.tanggal_acara"
                      label="Tanggal Acara"
                      readonly
                      v-bind="attrs"
                      v-on="on"></v-text-field>
                  </template>
                  <v-date-picker
                    v-model="infaq.tanggal_acara"
                    scrollable>
                    <v-spacer></v-spacer>
                    <v-btn
                      text
                      color="primary"
                      @click="modal = false">
                      Cancel
                    </v-btn>
                    <v-btn
                      text
                      color="primary"
                      @click="$refs.dialog.save(infaq.tanggal_acara)">
                      OK
                    </v-btn>
                  </v-date-picker>
                </v-dialog>
                <!-- <v-text-field
                  label="Tanggal"
                  v-model="infaq.tanggal_acara"></v-text-field> -->
                <v-text-field
                  type="number"
                  pattern="[0-9\s]{13,19}"
                  label="nominal"
                  v-model="infaq.nominal"></v-text-field>

                <v-radio-group
                  v-model="infaq.rutin"
                  row>
                  <v-radio
                    label="Rutin"
                    value="1"></v-radio>
                  <v-radio
                    label="Insidentil"
                    value="0"></v-radio>
                </v-radio-group>

                <v-radio-group
                  v-if="isEdit"
                  v-model="infaq.aktif"
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
                <v-toolbar-title>Data Infa {{ infaq.acara }}</v-toolbar-title>
                <v-spacer></v-spacer>
                <v-btn outlined @click="dialogDetail = false"><v-icon>mdi-close</v-icon></v-btn>
              </v-toolbar>
              <v-card-text class="py-8">
                <!-- <v-card color="teal" flat class="text-center py-5">
                  <div class="mx-auto">
                    <img :src="anggota.foto" width="100%" height="150" alt="alt">
                  </div>
                </v-card> -->
                <v-card-text class="px-5">
                  <v-row class="mt-2">
                    <v-col cols="4">
                      <div>Kode</div>
                    </v-col>
                    <v-col cols="8">
                      <div class="font-weight-bold text-right">{{infaq.kode}}</div>
                    </v-col>
                  </v-row>
                  <v-divider></v-divider>
                  <v-row class="mt-2">
                    <v-col cols="4">
                      <div>Acara</div>
                    </v-col>
                    <v-col cols="8">
                      <div class="font-weight-bold text-right">{{infaq.acara}}</div>
                    </v-col>
                  </v-row>
                  <v-divider></v-divider>
                  <v-row class="mt-2">
                    <v-col cols="4">
                      <div>keterangan</div>
                    </v-col>
                    <v-col cols="8">
                      <div class="font-weight-bold text-right">{{ infaq.keterangan }}</div>
                    </v-col>
                  </v-row>
                  <v-divider></v-divider>

                  <v-row class="mt-2">
                    <v-col cols="4">
                      <div>Tgl. Acara</div>
                    </v-col>
                    <v-col cols="8">
                      <div class="font-weight-bold text-right">{{ infaq.tanggal_acara }}</div>
                    </v-col>
                  </v-row>
                  <v-divider></v-divider>

                  <v-row class="mt-2">
                    <v-col cols="4">
                      <div>Nominal</div>
                    </v-col>
                    <v-col cols="8">
                      <div class="font-weight-bold text-right">{{ infaq.nominal}}</div>
                    </v-col>
                  </v-row>
                  <v-divider></v-divider>

                  <v-row class="mt-2">
                    <v-col cols="6">
                      <div>Status</div>
                    </v-col>
                    <v-col cols="6">
                      <div class="font-weight-bold text-right">{{ infaq.aktif }}</div>
                    </v-col>
                  </v-row>

                </v-card-text>
            </v-card>
          </v-dialog>

          <v-dialog
            v-model="dialogGenerate">
            <v-card flat max-width="500" class="mx-auto">
              <v-toolbar color="primary" flat dark>
                <v-toolbar-title>Generate Infaq</v-toolbar-title>
                <v-spacer></v-spacer>
                <v-btn outlined @click="dialogGenerate = false"><v-icon>mdi-close</v-icon></v-btn>
              </v-toolbar>
              <v-card-text class="py-8">
                <!-- <v-card color="teal" flat class="text-center py-5">
                  <div class="mx-auto">
                    <img :src="anggota.foto" width="100%" height="150" alt="alt">
                  </div>
                </v-card> -->
                <v-card-text class="px-5">
                  <v-row class="mt-2">
                    <v-col cols="4">
                      <div>Kode</div>
                    </v-col>
                    <v-col cols="8">
                      <div class="font-weight-bold text-right">{{infaq.kode}}</div>
                    </v-col>
                  </v-row>
                  <v-divider></v-divider>
                  <v-row class="mt-2">
                    <v-col cols="4">
                      <div>Acara</div>
                    </v-col>
                    <v-col cols="8">
                      <div class="font-weight-bold text-right">{{infaq.acara}}</div>
                    </v-col>
                  </v-row>
                  <v-divider></v-divider>
                  <v-row class="mt-2">
                    <v-col cols="4">
                      <div>keterangan</div>
                    </v-col>
                    <v-col cols="8">
                      <div class="font-weight-bold text-right">{{ infaq.keterangan }}</div>
                    </v-col>
                  </v-row>
                  <v-divider></v-divider>

                  <v-row class="mt-2">
                    <v-col cols="4">
                      <div>Tgl. Acara</div>
                    </v-col>
                    <v-col cols="8">
                      <div class="font-weight-bold text-right">{{ infaq.tanggal_acara }}</div>
                    </v-col>
                  </v-row>
                  <v-divider></v-divider>

                  <v-row class="mt-2">
                    <v-col cols="4">
                      <div>Nominal</div>
                    </v-col>
                    <v-col cols="8">
                      <div class="font-weight-bold text-right">{{ infaq.nominal}}</div>
                    </v-col>
                  </v-row>
                  <v-divider></v-divider>

                  <v-row class="mt-2">
                    <v-col cols="6">
                      <div>Status</div>
                    </v-col>
                    <v-col cols="6">
                      <div class="font-weight-bold text-right">{{ infaq.aktif }}</div>
                    </v-col>
                  </v-row>
                  <v-divider></v-divider>

                  <v-row>
                    <v-col cols="12">
                      <v-switch
                        v-model="isSemua"
                        :label="`Semua Wilayah`"></v-switch>
                    </v-col>
                    <v-col cols="12" v-if="isSemua">
                      <v-btn block depressed color="success" small @click="semuaWilayah()">Generate ke Semua Wilayah</v-btn>
                    </v-col>

                    <v-col cols="12" v-else>
                      <v-row>
                        <v-col cols="12">
                          <v-autocomplete
                            label="Pilih Wilayah"
                            :items="listWilayah"
                            item-text="kode"
                            item-value="kode"
                            v-model="selectedWilayah"></v-autocomplete>
                        </v-col>
                        <v-col cols="12">
                          <v-btn block depressed color="success" small @click="perwilayah()">Generate Wilayah {{ selectedWilayah }}</v-btn>
                        </v-col>
                      </v-row>
                    </v-col>

                    <v-col cols="12">
                      <v-btn block depressed color="error" small @click="dialogGenerate = false">Batal</v-btn>
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
    const swal = (judul, pesan, ikon) => {
      Swal.fire({
        title: judul,
        text: pesan,
        icon: ikon
      });
    }
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
        title: "PENERIMAAN INFAQ",
        config: null,
        token: null,
        header: [{
            text: 'Kode Infaq',
            align: 'start', // sortable: false,
            value: 'kode_infaq',
          },
          {
            text: 'Acara',
            value: 'acara'
          },
          {
            text: 'Nomor Pembayaran',
            value: 'nomor_pembayaran'
          },
          {
            text: 'Nama',
            value: 'nama'
          },
          {
            text: 'Bayar',
            value: 'bayar',
            align: 'end',
          },
          
          {
            text: 'Tgl. Pembayaran',
            value: 'tanggal_bayar'
          },
          {
            text: 'Penerima',
            value: 'validator'
          },
          // {
          //   text: 'Status',
          //   value: 'aktif',
          // },
          {
            text: 'Aksi',
            value: 'aksi',
          },
        ],
        list: [],
        dialogDetail: false,
        cari: null,
        dialog: false,
        titileDialog: null,
        infaq: {
          id: null,
          tanggal: null,
          nomor_pembayaran: null,
          kode_infaq: null,
          acara: null,
          rutin: null,
          nia: null,
          nama: null,
          bayar: null,
          bukti_bayar: null,
          tanggal_bayar: null,
          validator: null,
          tanggal_validasi: null,
        },
        isEdit: false,
        isLunas: 'pending',
        lunasataubelum: ['baru', 'pending','lunas'],
        /////
        modal: false,
        dialogGenerate: false,
        listWilayah: [],
        selectedWilayah: null,
        isSemua: false,
      },
      watch: {},
      created() {
        this.token = localStorage.getItem('admin-token')
        this.config = {
          headers: {
            Authorization: `Bearer ${this.token}`
          }
        }
        this.getList()
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
        async getList() {
          await axios.get('<?= base_url(); ?>api/admin/daftar-bayar-infaq/' + this.isLunas, this.config)
            .then((res) => {
              console.log(res.data);
              this.refresh()
              this.list = res.data.map((el) => {
                const dorong = {
                  id: el.id,
                  tanggal: el.tanggal,
                  nomor_pembayaran: el.nomor_pembayaran,
                  kode_infaq: el.kode_infaq,
                  acara: el.acara,
                  rutin: el.rutin,
                  nia: el.nia,
                  nama: el.nama,
                  bayar: parseInt(el.bayar).toLocaleString('ID-id'),
                  bukti_bayar: el.bukti_bayar,
                  tanggal_bayar: el.tanggal_bayar,
                  validator: el.validator,
                  tanggal_validasi: el.tanggal_validasi,
                  status: el.aktif == '1' ? true : false,
                  aktif: el.aktif == '1' ? 'Aktif' : 'Nonaktif',
                  warna: el.aktif == '1' ? 'success' : 'error'
                }
                return dorong
              })
            })
            .catch((err) => {
              if (err.response.status === 401) {
                this.keluar()
              }
              console.log('getlist infq ', err.response);
            })
        },
        async lihatDetail(id) {
          await axios.get('<?= base_url(); ?>api/admin/infaq/' + id, this.config)
            .then((res) => {
              this.refresh()
              console.log('detail ', res.data);
              this.infaq.id = res.data.id
              this.infaq.tanggal = res.data.tanggal
              this.infaq.kode = res.data.kode
              this.infaq.acara = res.data.acara
              this.infaq.tanggal_acara = res.data.tanggal_acara
              this.infaq.keterangan = res.data.keterangan
              this.infaq.nominal = parseInt(res.data.nominal).toLocaleString('ID-id')
              this.infaq.rutin = res.data.rutin
              this.infaq.aktif = res.data.aktif == '1' ? 'Aktif' : 'Nonaktif'
              this.dialogDetail = true
            })
            .catch((err) => {
              console.log(err.response.data);

            })
        },
        async loadDialogBaru() {
          await axios.get('<?= base_url(); ?>api/admin/infaq/new', this.config)
            .then((res) => {
              console.log(res.data);
              this.refresh()
              this.infaq.acara = null
              this.infaq.tanggal_acara = (new Date(Date.now() - (new Date()).getTimezoneOffset() * 60000)).toISOString().substr(0, 10)
              this.infaq.keterangan = null
              this.infaq.nominal = null
              this.infaq.rutin = '1'

              this.isEdit = false
              this.titileDialog = 'Tambah Infaq Baru'
              this.dialog = true
            })
            .catch((err) => {
              this.toast('error', JSON.stringify(err.response.data));
            })

        },
        async simpan() {
          const param = {
            acara: this.infaq.acara,
            tanggal_acara: this.infaq.tanggal_acara,
            keterangan: this.infaq.keterangan,
            nominal: this.infaq.nominal,
            rutin: this.infaq.rutin,
          }
          await axios.post('<?= base_url() ?>api/admin/infaq', param, this.config)
            .then((res) => {
              this.refresh()
              console.log(res.data);
              this.toast('success', 'Data infaq baru berhasil disimpan.')
              this.dialog = false
              this.isEdit = true
              this.getList()
            })
            .catch((err) => {
              if (err.response.status === 409) {
                const errAcara = err.response.data.messages.acara ? err.response.data.messages.acara + '\n' : ''
                const errKeterangan = err.response.data.messages.keterangan ? err.response.data.messages.keterangan + '\n' : ''
                const errNominal = err.response.data.messages.nominal ? err.response.data.messages.nominal + '\n' : ''

                const pesan = errAcara + errKeterangan + errNominal
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
          await axios.get('<?= base_url() ?>api/admin/infaq/edit/' + id, this.config)
            .then((res) => {
              console.log(res.data);
              this.refresh()

              this.infaq.id = res.data.data.id
              this.infaq.tanggal = res.data.data.tanggal
              this.infaq.kode = res.data.data.kode
              this.infaq.acara = res.data.data.acara
              this.infaq.tanggal_acara = res.data.data.tanggal_acara
              this.infaq.keterangan = res.data.data.keterangan
              this.infaq.nominal = res.data.data.nominal
              this.infaq.rutin = res.data.data.rutin
              this.infaq.aktif = res.data.data.aktif

              this.isEdit = true
              this.titileDialog = 'Edit Data Infaq'
              this.dialog = true

              console.log('anggota ', this.anggota);

            })
            .catch((err) => {
              console.log(err.response.data);
            })
        },
        async update() {
          const param = {
            kode: this.infaq.kode,
            acara: this.infaq.acara,
            keterangan: this.infaq.keterangan,
            nominal: this.infaq.nominal,
            rutin: this.infaq.rutin,
            tanggal_acara: this.infaq.tanggal_acara
          }
          await axios.put('<?= base_url() ?>api/admin/infaq/' + this.infaq.id, param, this.config)
            .then((res) => {
              console.log(res.data);
              this.refresh()
              this.toast('success', 'Data Acara berhasil diperbarui.')
              this.getList()
              this.dialog = false
            })
            .catch((err) => {
              if (err.response.status == 409) {
                const errAcara = err.response.data.messages.acara ? err.response.data.messages.acara + '\n' : ''
                const errKeterangan = err.response.data.messages.keterangan ? err.response.data.messages.keterangan + '\n' : ''
                const errNominal = err.response.data.messages.nominal ? err.response.data.messages.nominal + '\n' : ''

                const pesan = errAcara + errKeterangan + errNominal
                return false
              }
              if (err.response.status == 402) {
                this.toast('error', err.response.data.messages.error)
              } else {
                this.toast('error', JSON.stringify(err.response.data));
              }
              console.log('status error : ', err.response.data);
            })
        },

        async loadGenerate(data) {
          await axios.get('<?= base_url() ?>api/admin/wilayah', this.config)
            .then((res) => {
              this.refresh()
              console.log(res.data);
              this.infaq.id = data.id
              this.infaq.tanggal = data.tanggal
              this.infaq.kode = data.kode
              this.infaq.acara = data.acara
              this.infaq.tanggal_acara = data.tanggal_acara
              this.infaq.keterangan = data.keterangan
              this.infaq.nominal = data.nominal
              this.infaq.rutin = data.rutin
              this.infaq.aktif = data.aktif
              this.dialogGenerate = true
              this.selectedWilayah = null
              this.isSemua = true
              this.listWilayah = res.data.map((el) => {
                if (el.aktif == '1') {
                  const dorong = {
                    kode: el.kode
                  }
                  return dorong
                }
              })
            })
            .catch((err) => {
              if (err.response.status == 401) {
                this.keluar()
              }
            })
        },
        async perwilayah() {
          const param = {
            kode_infaq: this.infaq.kode,
            wilayah: this.selectedWilayah
          }
          axios.post('<?= base_url(); ?>api/admin/infaq-generate/', param, this.config)
            .then((res) => {
              console.log();
              this.refresh()
              this.dialogGenerate = false
              swal('Sukses!', 'Kode infaq berhasil digenerate ke wilayah ' + this.selectedWilayah, 'success')
            })
            .catch((err) => {
              console.log(err.response.data);
              if (err.response.status == 409) {
                swal('Gagal!', err.response.data.messages.wilayah, 'error')
              }
              if (err.response.status == 402) {
                swal('Gagal!', err.response.data.messages.error, 'error')
              }
              if (err.response.status == 401) {
                this.keluar()
              }

            })
        },
        async semuaWilayah() {
          axios.get('<?= base_url(); ?>api/admin/infaq-generate-all/' + this.infaq.kode, this.config)
            .then((res) => {
              console.log(res.data);
              this.refresh()
              this.dialogGenerate = false
              swal('Sukses!', 'Kode infaq berhasil digenerate ke semua wilayah.', 'success')
            })
            .catch((err) => {
              if (err.response.status == 401) {
                this.keluar()
              }
              if (err.response.status == 402) {
                swal('Gagal!', err.response.data.messages.error, 'error')
              }
              console.log(err.response.data);

            })
        },
      }
    })
  </script>
</body>

</html>