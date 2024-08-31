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
        <nav-bar :title="title"></nav-bar>

        <v-container>
          <v-card class="mx-auto justify-center mt-5 pb-7" max-width="800" flat>
            <v-toolbar color="white" flat class="text-center mx-auto" dark>
              <!-- <v-toolbar-title>Daftar Infaq Umum</v-toolbar-title> -->
              <v-spacer></v-spacer>
              <v-btn color="primary" depressed @click="loadDialogInfaq()"><v-icon>mdi-plus</v-icon> infaq</v-btn>
            </v-toolbar>
            <v-card-text class="px-2">
              <v-data-table
                :headers="headerInfaq"
                :items="listInfaq"
                class="elevation-0">
                <template v-slot:item.lunas="{ item }">
                  <v-chip
                    :color="item.warna_lunas"
                    depressed
                    rounded
                    small
                    dark>
                    {{ item.lunas }}
                  </v-chip>
                </template>
                <template v-slot:item.detail="{ item }">
                  <v-btn
                    color="info"
                    depressed
                    rounded
                    small
                    dark
                    @click="getInfaqDetail(item.kode)">
                    Detail
                  </v-btn>
                </template>
              </v-data-table>
            </v-card-text>
          </v-card>

          <v-dialog
            v-model="dialogInfaq">
            <v-card flat max-width="500" class="mx-auto">
              <v-toolbar color="primary" flat dark>
                <v-toolbar-title>Pembayaran Infaq Umum</v-toolbar-title>
              </v-toolbar>
              <v-card-text class="py-8">
                <v-row>
                  <v-col cols="12">
                    <v-text-field
                      class="text-right"
                      outlined
                      v-model="infaq.nominal"
                      type="number"
                      pattern="[0-9\s]{13,19}"
                      label="Nominal Infaq"></v-text-field>
                  </v-col>
                  <v-col cols="12">
                    <v-text-field
                      class="text-right"
                      outlined
                      v-model="infaq.keterangan"
                      label="Keterangan"></v-text-field>
                  </v-col>
                  <v-col cols="12">

                    <v-file-input
                      label="Unggah bukti infaq"
                      outlined
                      dense
                      show-size
                      hint="Harus berupa file jpg atau jpeg dan ukuran maximal 2mb"
                      @change="upload">
                    </v-file-input>
                  </v-col>
                  <v-col cols="12">
                    <v-btn color="success" block depressed @click="buatInfaq()">Simpan</v-btn>
                  </v-col>
                  <v-col cols="12">
                    <v-btn color="error" block depressed @click="dialogInfaq = false">Batal</v-btn>
                  </v-col>
                </v-row>
              </v-card-text>
            </v-card>
          </v-dialog>

          
          <v-dialog
            v-model="dialogDetail">
            <v-card flat max-width="500" class="mx-auto">
              <v-toolbar color="primary" flat dark>
                <v-toolbar-title>Detail Data Infaq Umum</v-toolbar-title>
                <v-spacer></v-spacer>
                <v-btn outlined small @click="dialogDetail = false"><v-icon>mdi-close</v-icon></v-btn>
              </v-toolbar>
              <v-card-text class="py-8">
                <v-row>
                  <v-col cols="12">
                    <v-simple-table>
                      <template v-slot:default>
                        <tbody>
                          <tr>
                            <td>Kode</td>
                            <td class="text-right font-weight-bold">{{ detail.kode }}</td>
                          </tr>
                          <tr>
                            <td>Tanggal Infaq</td>
                            <td class="text-right font-weight-bold">{{ detail.tanggal }}</td>
                          </tr>
                          <tr>
                            <td>Nominal</td>
                            <td class="text-right font-weight-bold">{{ detail.nominal }}</td>
                          </tr>
                          <tr>
                            <td>Keterangan</td>
                            <td class="text-right font-weight-bold">{{ detail.keterangan }}</td>
                          </tr>
                          <tr>
                            <td>Penerima</td>
                            <td class="text-right font-weight-bold">{{ detail.validator }}</td>
                          </tr>
                          <tr>
                            <td>Waktu Terima</td>
                            <td class="text-right font-weight-bold">{{ detail.tanggal_validasi }} {{detail.jam_validasi}}</td>
                          </tr>
                        </tbody>
                      </template>
                    </v-simple-table>
                  </v-col>
                  <v-col cols="12">
                    <v-img :src="detail.bukti"></v-img>
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
  <script src="<?= base_url(); ?>api/render/js/dash.js"></script>
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
        title: "DAFTAR INFAQ UMUM",
        config: null,
        token: null,
        nia: null,
        headerInfaq: [{
            text: 'Tanggal',
            align: 'start', // sortable: false,
            value: 'tanggal',
          },
          {
            text: 'Keterangan',
            value: 'keterangan'
          },
          {
            text: 'nominal',
            value: 'nominal',
            align: 'end'
          },
          {
            text: 'Status',
            value: 'lunas'
          },
          {
            text: 'Penerima',
            value: 'validator'
          },
          {
            text: 'Detail',
            value: 'detail'
          },
        ],
        listInfaq: [],
        dialogInfaq: false,
        infaq: {
          keterangan: null,
          nominal: null,
        },
        attFile: null,
        detail: {
          bukti: null,
          jam_validasi: null,
          keterangan: null,
          kode: null,
          nominal: null,
          tanggal: null,
          tanggal_validasi: null,
          jam_validasi: null,
          validator: null
        },
        dialogDetail: false
      },
      watch: {},
      created() {
        const localData = JSON.parse(localStorage.getItem("user"));
        this.nia = localData.nia
        this.token = localStorage.getItem('token')
        this.config = {
          headers: {
            Authorization: `Bearer ${this.token}`
          }
        }
        this.getListInfaq()
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
          window.open('<?= base_url(); ?>login', '_self')
        },
        async refresh() {
          await axios.get('<?= base_url() ?>api/user/refresh-token', this.config)
            .then((res) => {
              localStorage.setItem('token', res.data.new_token)
            })
            .catch()
        },
        async getListInfaq() {
          await axios.get('<?= base_url(); ?>api/user/home/infaq-umum', this.config)
            .then((res) => {
              // console.log('res ', res.data);
              this.refresh()
              this.listInfaq = res.data.map((val) => {
                const tgl = String(val.tanggal).split(' ')
                const spTanggal = String(tgl[0]).split('-')
                const isLunas = val.validator == null ? false : true
                const dorong = {
                  kode: val.kode,
                  keterangan: val.keterangan,
                  tanggal: `${spTanggal[2]}-${spTanggal[1]}-${spTanggal[0]}`,
                  nominal: parseInt(val.nominal).toLocaleString('ID-id'),
                  lunas: isLunas === true ? 'Diterima' : 'Pending',
                  warna_lunas: isLunas === false ? 'warning' : 'success',
                  status_lunas: isLunas,
                  validator: val.validator

                };
                return dorong
              })
              console.log(this.listInfaq);
            })
            .catch((err) => {
              if(err.response.status == 401 ){
                this.keluar()
              }
              console.log('getlist infq ', err);

            })
        },
        async getInfaqDetail(kode) {
          await axios.get('<?= base_url(); ?>api/user/infaq-umum/id/' + kode, this.config)
            .then((res) => {
              // console.log('detail ', res.data);
              this.refresh()
              this.detail.bukti = res.data.bukti
              this.detail.jam_validasi = res.data.jam_validasi
          this.detail.keterangan = res.data.keterangan
          this.detail.kode = res.data.kode
          this.detail.nominal = parseInt(res.data.nominal).toLocaleString('ID-id')
          this.detail.tanggal = res.data.tanggal
          this.detail.tanggal_validasi = res.data.tanggal_validasi
          this.detail.jam_validasi = res.data.jam_validasi
          this.detail.validator = res.data.validator

          this.dialogDetail = true

            })
            .catch((err) => {
              if(err.response.status == 401 ){
                this.keluar()
              }
              console.log('getlist infq ', err);

            })
        },
        async loadDialogInfaq() {
          await axios.get('<?= base_url(); ?>api/user/infaq-umum/new', this.config)
            .then((res) => {
              // console.log(res.data);
              this.refresh()
              this.infaq.nominal = null
              this.infaq.keterangan = null
              this.attFile = null;
              this.dialogInfaq = true
            })
            .catch((err) => {
              if(err.response.status == 401 ){
                this.keluar()
              }
              console.log(err.response);

            })
        },
        async buatInfaq() {
          if (this.infaq.nominal == 0 || this.infaq.nominal == '' || this.infaq.nominal == null) {
            this.toast('error', 'Anda belum memasukkan nominal infaq')
            return false
          }
          if (this.infaq.keterangan == '' || this.infaq.keterangan == null) {
            this.toast('error', 'Anda belum memasukkan keterangan infaq')
            return false
          }
          if (this.attFile == null) {
            this.toast('error', 'Silahkan sertakan bukti infaq Anda.')
            return false
          }
          const param = {
            nominal: this.infaq.nominal,
            keterangan: this.infaq.keterangan
          }
          await axios.post('<?= base_url(); ?>/api/user/infaq-umum/add', param, this.config)
            .then((res) => {
              // console.log(res.data);
              this.refresh()
              this.uploadBuktiBayar(res.data.kode)
            })
            .catch((err) => {
              if(err.response.status == 401 ){
                this.keluar()
              }
              if (err.response.status === 402) {
                this.toast('error', err.response.data.messages.error)
              }
              console.log(err.response.data);

            })
        },
        upload(event) {
          console.log("nama", event);
          console.log("type", event.type);
          console.log("ukuran", event.size);

          if (event.type != "image/jpeg") {
            this.toast('error', "Silakan upload file yang dengan ekstensi .jpeg atau.jpg");
            // this.linkFoto = "";
            this.attFile = null;
            return false;
          }
          if (event.size > 4000000) {
            this.toast('error', "Ukuran bukti infaq melebihi 4mb.");
            this.attFile = null;
            return false;
          }
          this.attFile = event;
          console.log(this.attFile);
          
          // this.linkFoto = URL.createObjectURL(event);
        },
        async uploadBuktiBayar(kode) {
          if (this.attFile == null) {
            this.toast('error', "Anda belum memilih foto");
            return false;
          }
          let fdata = new FormData();
          fdata.append("bukti", this.attFile);
          await axios
            .post('<?= base_url(); ?>api/user/infaq-umum-bukti/' + kode, fdata, this.config)
            .then((res) => {
              console.log(res.data);
              this.toast('success', 'Silahkan hubungi admin untuk menerima infaq umum Anda.')
              this.getListInfaq()
              this.dialogInfaq = false
              this.attFile = null
              fdata.delete("bukti")
            })
            .catch((err) => {
              if (err.response.status > 400) {
                this.hapusInfaqUmum(kode)
                this.toast('error', 'Data infaq umum Anda gagal disimpan.')
              }
              console.log('bukti ', err.response);
            });
        },

        async hapusInfaqUmum(kode) {
          await axios
            .delete('<?= base_url(); ?>api/user/infaq-umum/delete/' + kode, this.config)
            .then((res) => {
              console.log(res.data);
            })
            .catch((err) => {
              console.log('hapus ', err.response);
            });
        },

      }
    })
  </script>
</body>

</html>