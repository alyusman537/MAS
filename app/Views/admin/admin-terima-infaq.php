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
                    @change="getList()"></v-select>
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
                    @click="lihatDetail(item.nomor_pembayaran)">Lihat dan terima</v-btn>
                  <!--v-btn
                    :color="item.warna"
                    depressed
                    rounded
                    small
                    dark
                    @click="loadDialogEdit(item.id)">
                    Edit
                  </v-btn>
                  <v-btn
                  v-if="isTerima"
                    color="warning"
                    depressed
                    rounded
                    small
                    dark
                    @click="">
                    Terima
                  </v-btn-->
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
                <v-toolbar-title>Data Infaq {{ infaq.acara }}</v-toolbar-title>
                <v-spacer></v-spacer>
                <v-btn outlined @click="dialogDetail = false"><v-icon>mdi-close</v-icon></v-btn>
              </v-toolbar>
              <v-card-text class="py-8">
                <!-- <v-card color="teal" flat class="text-center py-5">
                  <div class="mx-auto">
                    <img :src="anggota.foto" width="100%" height="150" alt="alt">
                  </div>
                </v-card> -->
                <!-- this.infaq.kode_infaq = res.data.infaq.kode
              this.infaq.acara = res.data.infaq.acara
              this.infaq.tanggal_acara = res.data.infaq.tanggal_acara
              this.infaq.nominal = parseInt(res.data.infaq.nominal).toLocaleString('ID-id')
              this.infaq.rutin = res.data.infaq.rutin
              this.infaq.nia = res.data.pembayaran.nia
              this.infaq.nama = res.data.anggota.nama
              this.infaq.bayar = parseInt(res.data.pembayaran.bayar).toLocaleString('ID-id')
              this.infaq.tanggal_bayar = res.data.pembayaran.tanggal_bayar
              this.infaq.validator = res.data.pembayaran.validator
              this.infaq.tanggal_validasi = res.data.pembayaran.tanggal_validasi -->
                <v-card-text class="px-5">
                  <v-row class="mt-2">
                    <v-col cols="4">
                      <div>Tujuan Infaq</div>
                    </v-col>
                    <v-col cols="8">
                      <div class="font-weight-bold text-right">{{infaq.acara}}</div>
                    </v-col>
                  </v-row>
                  <v-divider></v-divider>
                  <v-row class="mt-2">
                    <v-col cols="4">
                      <div>Tagihan Infaq</div>
                    </v-col>
                    <v-col cols="8">
                      <div class="font-weight-bold text-right">{{infaq.nominal}}</div>
                    </v-col>
                  </v-row>
                  <v-divider></v-divider>
                  <v-row class="mt-2">
                    <v-col cols="4">
                      <div>Batas Pembayaran</div>
                    </v-col>
                    <v-col cols="8">
                      <div class="font-weight-bold text-right">{{ infaq.tanggal_acara }}</div>
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
                      <div>Sifat Infaq</div>
                    </v-col>
                    <v-col cols="8">
                      <div class="font-weight-bold text-right">{{ infaq.rutin}}</div>
                    </v-col>
                  </v-row>
                  <v-divider></v-divider>

                  <v-row class="mt-2">
                    <v-col cols="6">
                      <div>Nomor Pembayaran</div>
                    </v-col>
                    <v-col cols="6">
                      <div class="font-weight-bold text-right">{{ infaq.nomor_pembayaran }}</div>
                    </v-col>
                  </v-row>
                  <v-divider></v-divider>

                  <v-row class="mt-2">
                    <v-col cols="6">
                      <div>NIA</div>
                    </v-col>
                    <v-col cols="6">
                      <div class="font-weight-bold text-right">{{ infaq.nia }}</div>
                    </v-col>
                  </v-row>
                  <v-divider></v-divider>

                  <v-row class="mt-2">
                    <v-col cols="6">
                      <div>Nama Anggota</div>
                    </v-col>
                    <v-col cols="6">
                      <div class="font-weight-bold text-right">{{ infaq.nama }}</div>
                    </v-col>
                  </v-row>
                  <v-divider></v-divider>

                  <v-row class="mt-2">
                    <v-col cols="6">
                      <div>Nominal Pembayaran</div>
                    </v-col>
                    <v-col cols="6">
                      <div class="font-weight-bold text-right">{{ infaq.bayar }}</div>
                    </v-col>
                  </v-row>
                  <v-divider></v-divider>

                  <v-row class="mt-2">
                    <v-col cols="6">
                      <div>Tanggal Pembayaran</div>
                    </v-col>
                    <v-col cols="6">
                      <div class="font-weight-bold text-right">{{ infaq.tanggal_bayar }}</div>
                    </v-col>
                  </v-row>
                  <v-divider></v-divider>

                  <v-row class="mt-2">
                    <v-col cols="6">
                      <div>Penerima</div>
                    </v-col>
                    <v-col cols="6">
                      <div class="font-weight-bold text-right">{{ infaq.validator }}</div>
                    </v-col>
                  </v-row>
                  <v-divider></v-divider>

                  <v-row class="mt-2">
                    <v-col cols="6">
                      <div>Tanggal Penerimaan</div>
                    </v-col>
                    <v-col cols="6">
                      <div class="font-weight-bold text-right">{{ infaq.tanggal_validasi }}</div>
                    </v-col>
                    <v-col cols="12">
                        <v-img :src="infaq.bukti_bayar" width="400" height="100%" @click="lihatBukti"></v-img>
                    </v-col>
                    <v-col cols="12" v-if="isTerima">
                      <v-btn color="success" depressed small block @click="terimaInfaq()">Terima Pembayaran</v-btn>
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
        header: [
          // {
          //   text: 'Kode Infaq',
          //   align: 'start', // sortable: false,
          //   value: 'kode_infaq',
          // },
          {
            text: 'Acara',
            value: 'acara'
          },
          {
            text: 'Tgl. Pembayaran max',
            value: 'tanggal_acara'
          },
          {
            text: 'Nomor Pembayaran',
            value: 'nomor_pembayaran'
          },
          {
            text: 'NIA',
            value: 'nia'
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
          kode_infaq: null,
          acara: null,
          tanggal_acara: null,
          nominal: null,
          rutin: null,
          nia: null,
          nama: null,
          nomor_pembayaran: null,
          bayar: null,
          bukti_bayar: null,
          tanggal_bayar: null,
          validator: null,
          tanggal_validasi: null,
        },
        isEdit: false,
        isLunas: 'pending',
        lunasataubelum: ['baru', 'pending', 'lunas'],
        isTerima: false,
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
                  tanggal_acara: el.tanggal_acara,
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
              if (this.isLunas == 'pending') {
                this.isTerima = true
              } else {
                this.isTerima = false
              }
              console.log(this.list);
            })
            .catch((err) => {
              if (err.response.status === 401) {
                this.keluar()
              }
              console.log('getlist infq ', err.response);
            })
        },
        async lihatDetail(kode_pembayaran) {
          await axios.get('<?= base_url(); ?>api/admin/detail-bayar-infaq/' + kode_pembayaran, this.config)
            .then((res) => {
              this.refresh()
              console.log('detail ', res.data);
              this.infaq.id = res.data.pembayaran.id
              this.infaq.kode_infaq = res.data.infaq.kode
              this.infaq.acara = res.data.infaq.acara
              this.infaq.tanggal_acara = res.data.infaq.tanggal_acara
              this.infaq.nominal = parseInt(res.data.infaq.nominal).toLocaleString('ID-id')
              this.infaq.rutin = res.data.infaq.rutin == '1' ? 'Rutin' : 'Insidential'
              this.infaq.nia = res.data.pembayaran.nia
              this.infaq.nama = res.data.anggota.nama
              this.infaq.nomor_pembayaran = res.data.pembayaran.nomor_pembayaran
              this.infaq.bayar = parseInt(res.data.pembayaran.bayar).toLocaleString('ID-id')
              this.infaq.bukti_bayar = res.data.pembayaran.bukti_bayar == null ? '<?= base_url() ?>No_Image_Available.jpg' : '<?= base_url() ?>api/render/bukti/' + res.data.pembayaran.bukti_bayar
              this.infaq.tanggal_bayar = res.data.pembayaran.tanggal_bayar
              this.infaq.validator = res.data.pembayaran.validator
              this.infaq.tanggal_validasi = res.data.pembayaran.tanggal_validasi

              this.dialogDetail = true
            })
            .catch((err) => {
              console.log(err.response.data);

            })
        },

        terimaInfaq() {
          Swal.fire({
            title: "Penerimaan infaq?",
            text: "Yakin akan melakukan penerimaan infaq nomor pembayaran " + this.infaq.nomor_pembayaran,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Terima"
          }).then((result) => {
            if (result.isConfirmed) {
              axios.get('<?= base_url(); ?>api/admin/terima-infaq/' + this.infaq.nomor_pembayaran, this.config)
                .then((res) => {
                  this.refresh()
                  console.log('detail ', res.data);
                  swal('Berhasil!', "Nomor pembayaran infaq "+this.infaq.nomor_pembayaran+" berhasil diterima.", 'success')

                  this.dialogDetail = false
                })
                .catch((err) => {
                  console.log(err.response.data);

                })
            }
          });
        },
        lihatBukti()
        {
          window.open(this.infaq.bukti_bayar, '_blank')
        },

      }
    })
  </script>
</body>

</html>