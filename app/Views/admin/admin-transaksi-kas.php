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
        <admin-nav-bar :title="title"></admin-nav-bar>

        <v-container>
          <v-card class="mx-auto justify-center mt-5 pb-7" max-width="800" flat>
            <v-toolbar color="teal" flat class="text-center mx-auto" dark>
              <!-- <v-toolbar-title>Mutasi KAS </v-toolbar-title> -->
              <v-spacer></v-spacer>
              <v-btn class="mr-2" text outlined @click="loadDialog()"><v-icon>mdi-plus-box</v-icon>Transaksi</v-btn>
              <v-btn text outlined @click="lapPdf()"><v-icon>mdi-file-pdf-box</v-icon>PDF</v-btn>
            </v-toolbar>
            <v-card-text class="px-2">
              <v-row justify-end>
                <v-col cols="4">
                  <v-dialog
                    ref="dialogAwal"
                    v-model="dialogTglAwal"
                    :return-value.sync="dateAwal"
                    persistent
                    width="290px">
                    <template v-slot:activator="{ on, attrs }">
                      <v-text-field
                        v-model="dateAwal"
                        label="Dari Tanggal"
                        readonly
                        v-bind="attrs"
                        v-on="on"
                        @change="getLaporanKas()"></v-text-field>
                    </template>
                    <v-date-picker
                      v-model="dateAwal"
                      scrollable>
                      <v-spacer></v-spacer>
                      <v-btn
                        text
                        color="primary"
                        @click="dialogTglAwal = false">
                        Cancel
                      </v-btn>
                      <v-btn
                        text
                        color="primary"
                        @click="$refs.dialogAwal.save(dateAwal)">
                        OK
                      </v-btn>
                    </v-date-picker>
                  </v-dialog>
                </v-col>
                <v-col cols="4">
                  <v-dialog
                    ref="dialogAkhir"
                    v-model="dialogTglAkhir"
                    :return-value.sync="dateAkhir"
                    persistent
                    width="290px">
                    <template v-slot:activator="{ on, attrs }">
                      <v-text-field
                        v-model="dateAkhir"
                        label="Sampai Tanggal"
                        readonly
                        v-bind="attrs"
                        v-on="on"
                        @change="getLaporanKas()"></v-text-field>
                    </template>
                    <v-date-picker
                      v-model="dateAkhir"
                      scrollable>
                      <v-spacer></v-spacer>
                      <v-btn
                        text
                        color="primary"
                        @click="dialogTglAkhir = false">
                        Cancel
                      </v-btn>
                      <v-btn
                        text
                        color="primary"
                        @click="$refs.dialogAkhir.save(dateAkhir)">
                        OK
                      </v-btn>
                    </v-date-picker>
                  </v-dialog>
                </v-col>
                <v-col cols="2">
                  <v-btn class="mt-3" color="info" small depressed @click="getList()"><v-icon>mdi-gesture-tap-box</v-icon></v-btn>
                </v-col>
              </v-row>

              <template>
                <!-- <v-simple-table>
                  <template v-slot:default>
                    <thead>
                      <tr>
                        <th class="text-left">
                          Tanggal
                        </th>
                        <th class="text-right">
                          Debet
                        </th>
                        <th class="text-right">
                          Kredit
                        </th>
                        <th class="text-right">
                          Saldo
                        </th>
                        <th>Aksi</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="text-right">{{ saldoAwal }}</td>
                        <td></td>
                      </tr>
                      <tr
                        v-for="item in listMutasi"
                        :key="item.urut">
                        <td >{{ item.tanggal }}</td>
                        <td class="text-right">{{ item.masuk }}</td>
                        <td class="text-right">{{ item.keluar }}</td>
                        <td class="text-right">{{ item.saldo }}</td>
                        <td>
                          <v-btn color="info" x-small depressed >Detail</v-btn>
                        </td>
                      </tr>
                    </tbody>
                  </template>
                </v-simple-table> -->
                <v-data-table
                  :headers="headerList"
                  :items="listMutasi"
                  class="elevation-0"
                  :hide-default-footer="true">
                  <template v-slot:item.aksi="{ item }">
                    <v-btn
                      color="info"
                      depressed
                      rounded
                      small
                      dark
                      @click="lihatDetail(item.nomor)">
                      Detail
                    </v-btn>
                  </template>
                </v-data-table>

              </template>

            </v-card-text>
          </v-card>

          <v-dialog
            v-model="dialog">
            <v-card flat max-width="500" class="mx-auto">
              <v-toolbar color="primary" flat dark>
                <v-toolbar-title>{{ titleDialog }}</v-toolbar-title>
              </v-toolbar>
              <v-card-text class="py-8">
                <v-text-field
                  type="number"
                  pattern="[0-9\s]{13,19}"
                  label="nominal"
                  v-model="mutasi.nominal"></v-text-field>
                <v-textarea
                  label="Keterangan"
                  v-model="mutasi.keterangan"
                  rows="2"></v-textarea>

                <v-radio-group
                  v-model="mutasi.jenis"
                  row>
                  <v-radio
                    label="Masuk"
                    value="D"></v-radio>
                  <v-radio
                    label="Keluar"
                    value="K"></v-radio>
                </v-radio-group>

                <v-row>
                  <v-col cols="12">
                    <v-btn color="success" block depressed @click="simpan()">Simpan</v-btn>
                  </v-col>
                  <v-col cols="12">
                    <v-btn color="error" block depressed @click="dialog = false">Batal</v-btn>
                  </v-col>
                </v-row>
              </v-card-text>
            </v-card>
          </v-dialog>

          <v-dialog
            v-model="dialogDetail"
          >
            <v-card class="mx-auto" max-width="600">
              <v-toolbar color="primary" flat dark>
                <v-toolbart-tiltle>Detail Mutasi</v-toolbart-tiltle>
                <v-spacer></v-spacer>
                <v-btn color="error" text @click="dialogDetail = false"><v-icon>mdi-close</v-icon></v-btn>
              </v-toolbar>
              <v-card-text>
                <table>
                  <tbody>
                    <tr>
                      <td width="35%">Tanggal</td>
                      <td width="65%"> <div class="font-weight-bold"> : {{ detail.tanggal }}</div></td>
                    </tr>
                    <tr>
                      <td>Jenis</td>
                      <td> <div class="font-weight-bold"> : {{ detail.jenis }}</div></td>
                    </tr>
                    <tr>
                      <td>Nominal</td>
                      <td> <div class="font-weight-bold"> : {{ detail.nominal }}</div></td>
                    </tr>
                      <tr>
                      <td>Keterangan</td>
                      <td> <div class="font-weight-bold"> : {{ detail.keterangan }}</div></td>
                    </tr>
                    <tr>
                      <td>Admin</td>
                      <td> <div class="font-weight-bold"> : {{ detail.admin }}</div></td>
                    </tr>
                  </tbody>
                </table>
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
    const swal = (judul, pesan, ikon) => {
      Swal.fire({
        title: judul,
        text: pesan,
        icon: ikon
      });
    }
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
        title: "TRANSAKSI KAS",
        config: null,
        token: null,
        dialogTglAwal: false,
        dialogTglAkhir: false,
        dateAkhir: (new Date(Date.now() - (new Date()).getTimezoneOffset() * 60000)).toISOString().substr(0, 10),
        dateAwal: null,
        saldoAwal: 0,
        saldoAkhir: 0,
        headerList: [{
            text: 'Tanggal',
            value: 'tanggal'
          },
          {
            text: 'Masuk',
            value: 'masuk'
          },
          {
            text: 'Keluar',
            value: 'keluar'
          },
          {
            text: 'Saldo',
            value: 'saldo'
          },
          {
            text: 'Aksi',
            value: 'aksi',
          },
        ],
        listMutasi: [],
        mutasi: {
          keterangan: null,
          nominal: null,
          jenis: null,
        },
        listJenis: [{
            value: 'D',
            text: 'Masuk'
          },
          {
            value: 'K',
            text: 'Keluar'
          },
        ],
        dialog: false,
        titleDialog: null,
        dialogDetail: false,
        detail: {
          admin: null,
          jenis: null,
          keterangan: null,
          nama: null,
          nominal: null,
          nomor_mutasi: null,
          tanggal: null
        }

      },
      watch: {},
      created() {
        this.token = localStorage.getItem('admin-token')
        this.config = {
          headers: {
            Authorization: `Bearer ${this.token}`
          }
        }
        this.dateAwal = (new Date(Date.parse(this.dateAkhir) - (1000 * 60 * 60 * 24 * 30))).toISOString().substr(0, 10);
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
          await axios.get('<?= base_url(); ?>api/admin/mutasi-tanggal/' + this.dateAwal + '/' + this.dateAkhir, this.config)
            .then((res) => {
              console.log('res ', res.data);
              this.saldoAwal = parseInt(res.data.saldo_awal)
              let saldo = this.saldoAwal
              let urut = 0
              this.listMutasi = res.data.mutasi.map((val) => {
                const tgl = String(val.tanggal).split(' ')
                const spTanggal = String(tgl[0]).split('-')
                const masuk = val.debet // == '0' ? null : parseInt(val.debet)
                const keluar = val.kredit // == '0' ? null : parseInt(val.kredit)
                saldo = saldo + masuk - keluar
                const dorong = {
                  tanggal: `${spTanggal[2]}-${spTanggal[1]}-${spTanggal[0]}`,
                  masuk: masuk != '0' ? masuk.toLocaleString('ID-id') : null,
                  keluar: keluar > 0 ? keluar.toLocaleString('ID-id') : null,
                  saldo: saldo.toLocaleString('ID-id'),
                  nomor: val.nomor,
                  keterangan: val.keterangan,
                  admin: val.admin

                };
                return dorong
              })
              console.log(this.listMutasi);
            })
            .catch((err) => {
              if (err.response.status == 401) {
                this.keluar()
                return false
              }
              console.log('getlist infq ', err);

            })
        },
        async lihatDetail(nomor) {
          await axios.get('<?= base_url(); ?>api/admin/mutasi-detail/' + nomor, this.config)
            .then((res) => {
              console.log(res.data);
              this.detail.admin = res.data.admin
              this.detail.jenis = res.data.jenis == 'D' ? 'Kas masuk' : 'Kas keluar'
              this.detail.keterangan = res.data.keterangan
              this.detail.nama = res.data.nama
              this.detail.nominal = parseInt(res.data.nominal).toLocaleString("ID-id")
              this.detail.nomor_mutasi = res.data.nomor_mutasi
              this.detail.tanggal = res.data.tanggal

              this.dialogDetail = true
              this.refresh()
            })
            .catch((err) => {
              console.log(err.response);
            })
        },
        loadDialog() {
          this.mutasi.nominal = null
          this.mutasi.keterangan = null
          this.mutasi.jenis = null
          this.dialog = true
          this.titleDialog = 'Form Transaksi Kas'
        },
        async simpan() {
          const param = {
            nominal: this.mutasi.nominal,
            keterangan: this.mutasi.keterangan,
            jenis: this.mutasi.jenis
          }
          await axios.post('<?= base_url() ?>api/admin/mutasi', param, this.config)
            .then((res) => {
              console.log(res.data);
              this.toast('success', res.data.pesan)
              this.dialog = false
              this.getList()
              this.refresh()
            })
            .catch((err) => {
              console.log(err.response);
              if (err.response.status == 401) {
                this.keluar
              } else if (err.response.status == 409) {
                const errNominal = err.response.data.messages.nominal ? err.response.data.messages.nominal + '\n' : ''
                const errKeterangan = err.response.data.messages.keterangan ? err.response.data.messages.keterangan + '\n' : ''
                const errJenis = err.response.data.messages.jenis ? err.response.data.messages.jenis : ''

                swal('Gagal!', errNominal + errKeterangan + errJenis, 'error')
              } else if (err.response.status == 402) {
                swal('Gagal!', err.response.data.messages.error, 'error')
              } else {
                swal('Gagal!', JSON.stringify(err.response.data), 'error')
              }
            })
        },

        async lapPdf() {
          await window.open('<?= base_url(); ?>api/pdf/mutasi/' + this.dateAwal + '/' + this.dateAkhir, '_blank')
        },

      }
    })
  </script>
</body>

</html>