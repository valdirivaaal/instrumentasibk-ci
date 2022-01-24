<form class="form-signin" action="<?= base_url('auth/authentication') ?>" method="post">
  <h2 class="form-signin-heading">Masuk ke akun anda</h2>
  <?php
  if ($this->session->flashdata('success')) {
    ?>
    <div class="alert alert-success" role="alert">
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        <span class="sr-only">Close</span>
      </button>
      <strong>Registrasi berhasil!</strong> Silahkan login menggunakan akun yang sudah kamu daftarkan.
    </div>
    <?php
  }
  ?>
  <?php
  if ($this->session->flashdata('error')) {
    ?>
    <div class="alert alert-danger" role="alert">
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
        <span class="sr-only">Close</span>
      </button>
      <?= $this->session->flashdata('error') ?>
    </div>
    <?php
  }
  ?>
  <div class="login-wrap">
    <div class="form-group">
      <input type="text" class="form-control" placeholder="Email" name="e-mail" autofocus>
    </div>
    <div class="form-group">
      <input type="password" class="form-control" placeholder="Kata Sandi" name="password">
    </div>
    <div class="form-group">
      <a href="<?= base_url('auth/register') ?>"> Daftar?</a> |
      <a data-toggle="modal" href="#myModal"> Lupa kata sandi?</a>
    </div>
    <button class="btn btn-lg btn-login btn-block" type="submit">Masuk</button>
  </div>

  <!-- Modal -->
  <div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="myModal" class="modal fade">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Forgot Password ?</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p>Enter your e-mail address below to reset your password.</p>
          <input type="text" name="email" placeholder="Email" autocomplete="off" class="form-control placeholder-no-fix">

        </div>
        <div class="modal-footer">
          <button data-dismiss="modal" class="btn btn-default" type="button">Cancel</button>
          <button class="btn btn-success" type="button">Submit</button>
        </div>
      </div>
    </div>
  </div>
  <!-- modal -->
</form>