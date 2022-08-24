<div class="row">
  <div class="col-md-12">
    <div class="panel-body bio-graph-info">
      <h1 class="font-weight-bold"> Daftar Pengguna</h1>
    </div>
    <div class="card">
      <div class="card-header">
        Data Pengguna
      </div>
      <div class="card-body">
        <?php
        if ($this->session->flashdata('success')) {
        ?>
          <div class="alert alert-success" role="alert">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <strong>Sukses!</strong> Data <?= $this->session->userdata('success') ?> berhasil disimpan.
          </div>
        <?php
        }
        ?>
        <div class="adv-table">
          <table class="display table table-bordered table-striped dynamic-table">
            <thead>
              <tr>
                <th>No</th>
                <th>Nama Pengguna</th>
                <th>Jenjang</th>
                <th>Instansi</th>
                <th>Event Key</th>
                <th>Tanggal Registrasi</th>
                <th>Sisa Hari</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $i =  1;
              foreach ($get_user as $key => $value) {
              ?>
                <tr class="gradeX">
                  <td><?= $i++ ?></td>
                  <td><?= $value['nama_lengkap'] ?></td>
                  <td><?= $value['jenjang'] ?></td>
                  <td><?= $value['instansi'] ?></td>
                  <td><?= $value['event_key'] ?></td>
                  <td><?= date('d-m-Y', strtotime($value['date_created'])) ?></td>
                  <td><?= ceil((strtotime($value['tgl_kadaluarsa']) - time()) / (60 * 60 * 24)); ?> Hari</td>
                </tr>
              <?php
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>