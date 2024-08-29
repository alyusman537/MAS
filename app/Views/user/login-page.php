<!DOCTYPE html>
<!-- Coding By CodingNepal - www.codingnepalweb.com -->
<html>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900" rel="stylesheet"> -->
  <title>ALWAFA</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body>
  <div id="app" class="wrapper">
      <form v-on:submit="goLogin">
        <!-- <h2>Login</h2> -->
        <div class="tengah">
          <img src="<?= base_url() ?>logo_alwafa_white.png" height="100%" width="150"></img>
        </div>
        <div class="input-field">
          <input type="text" v-model="nia">
          <label>ID Anggota</label>
        </div>
        <div class="input-field">
          <input v-model="password" id="passwordKu" type="password"><!--:type="showPassword ? 'text' : 'password'" required /-->
          <label>Password</label>
        </div>
        <div style="flex: 1;">
          <input type="checkbox" onClick="showHide" style="border-color: #fff;">
          <label style="color: #fff;">Lihat Password </label>
        </div>
        <br>
        <button  type="submit">Log In</button>
        <div class="forget">
          <a href="#">Admin Page</a>
          <a href="#">Lupa password?</a>
        </div>
</form>
    </div>

  <script src="https://cdn.jsdelivr.net/npm/vue@2.x/dist/vue.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

  <script>
    function showHide() {
      var inputan = document.getElementById("passwordKu");
      if (inputan.type === "password") {
        inputan.type = "text";
      } else {
        inputan.type = "password";
      }
    }
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
        logo: '<?= base_url(); ?>logo_alwafa_white.png',
        attFile: null,
      },
      methods: {
        lihatPassword() {
          var inputan = document.getElementById("passwordKu");
          if (inputan.type === "password") {
            inputan.type = "text";
          } else {
            inputan.type = "password";
          }
        },
        async goLogin(e) {
          e.preventDefault()
          if (this.nia == null || this.password == null) {
            // alert("Anda belum memilih foto");
            return false;
          }
          // let param = new FormData();
          // fdata.append("nia", this.nia);
          // fdata.append("password", this.password);
          // console.log(fdata);
          

          const param = {
            nia: this.nia,
            password: this.password
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
              if (err.response.status === 400) {
                Toast.fire({
                  icon: "error",
                  title: "Gagal Login"
                });
              }

            })
        },
      }
    })
  </script>

  <style>
    @import url("https://fonts.googleapis.com/css2?family=Open+Sans:wght@200;300;400;500;600;700&display=swap");

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: "Open Sans", sans-serif;
    }

    body {
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      width: 100%;
      padding: 0 10px;
    }

    body::before {
      content: "";
      position: absolute;
      width: 100%;
      height: 100%;
      background: url("<?= base_url(); ?>payung.jpg"), #000;
      background-position: center;
      background-size: cover;
    }

    .wrapper {
      width: 400px;
      border-radius: 8px;
      padding: 30px;
      text-align: center;
      border: 0.5px solid rgba(255, 255, 255, 0.5);
      backdrop-filter: blur(2px);
      -webkit-backdrop-filter: blur(8px);
    }

    form {
      display: flex;
      flex-direction: column;
    }

    h2 {
      font-size: 2rem;
      margin-bottom: 20px;
      color: #fff;
    }

    .input-field {
      position: relative;
      border-bottom: 2px solid #ccc;
      margin: 15px 0;
    }

    .input-field label {
      position: absolute;
      top: 50%;
      left: 0;
      transform: translateY(-50%);
      color: #fff;
      font-size: 16px;
      pointer-events: none;
      transition: 0.15s ease;
    }

    .input-field input {
      width: 100%;
      height: 40px;
      background: transparent;
      border: none;
      outline: none;
      font-size: 16px;
      color: #fff;
    }

    .input-field input:focus~label,
    .input-field input:valid~label {
      font-size: 0.8rem;
      top: 10px;
      transform: translateY(-120%);
    }

    .forget {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin: 25px 0 35px 0;
      color: #fff;
    }

    #remember {
      accent-color: #fff;
    }

    .forget label {
      display: flex;
      align-items: center;
    }

    .forget label p {
      margin-left: 8px;
    }

    .wrapper a {
      color: #efefef;
      text-decoration: none;
    }

    .wrapper a:hover {
      text-decoration: underline;
    }

    button {
      background: #11A39C;
      color: #fff;
      font-weight: 600;
      border: none;
      padding: 12px 20px;
      cursor: pointer;
      border-radius: 3px;
      font-size: 16px;
      border: 2px solid transparent;
      transition: 0.3s ease;
    }

    /* button:hover {
  color: #fff;
  border-color: #fff;
  background: rgba(255, 255, 255, 0.15);
} */

    .register {
      text-align: center;
      margin-top: 30px;
      color: #fff;
    }

    .tengah {
      display: block;
      margin-left: auto;
      margin-right: auto;
    }
  </style>
</body>

</html>