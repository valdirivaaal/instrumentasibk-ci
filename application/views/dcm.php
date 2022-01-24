<div class="row">
 <div class="col-md-12">
  <div class="panel-body bio-graph-info">
    <h1 class="font-weight-bold">Daftar Cek Masalah</h1>
  </div>
  <?php
  if ($get_dcm) {
    ?>
    <div class="alert alert-info" role="alert">
      <a href="<?= base_url('dcm/kode') ?>" class="btn btn-sm btn-info float-right"><i class="fa fa-pencil"></i> Atur Kode</a>
      <strong>Halo, <?= $get_profil[0]['nama_lengkap'] ?>!</strong> <br>Kamu dapat menginstruksikan peserta didik untuk membuka Alat Ungkap Masalah Umum di <a href="<?= base_url('isidcm/'.$get_kode[0]['kode_singkat']) ?>"><?= base_url('dcm/'.$get_kode[0]['kode_singkat']) ?></a>
    </div>
    <?php
  } else {
    ?>
    <div class="alert alert-danger" role="alert">
      <strong>Halo, <?= $get_profil[0]['nama_lengkap'] ?>!</strong> <br>Kamu belum mengatur kode unik untuk Daftar Cek Masalah. Silahkan atur kodenya <a href="<?= base_url('dcm/kode/'.(isset($get_profil[0]['jenjang']) ? $get_profil[0]['jenjang'] : '')) ?>">disini</a></a>
    </div>
    <?php
  }
  ?>

  <div class="card">
    <div class="card-header">
      Data Daftar Cek Masalah
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
      <?php
      if (getField('user_info','status',array('user_id'=>$this->session->userdata('id')))=='Guru BK')
      {
        ?>
        <div class="dropdown">
          <button class="btn btn-primary dropdown-toggle float-right" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Laporan Kelompok
          </button>
          <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
            <?php
            foreach ($get_kelompok as $key => $value) {
              ?>
              <a class="dropdown-item" href="<?= base_url('dcm/laporan_kelompok/'.$value['id']) ?>"><?= $value['nama_kelompok'] ?></a>
              <?php
            }
            ?>
          </div>
        </div>
        <?php
      }
      ?>
      <div class="adv-table">
        <table  class="display table table-bordered table-striped" id="dynamic-table">
          <thead>
            <tr>
              <th>No</th>
              <th>Terakhir Diisi</th>
              <th>Jumlah Siswa</th>
              <th>Kelas</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
           <?php
           $i = 1;
           foreach ($kelas as $key => $value_kelas) {
            $total_dcm = $this->Main_model->get_where('instrumen_jawaban',array('instrumen_id'=>@$get_dcm[0]['id'],'kelas'=>$value_kelas['id']));
            ?>
            <tr class="gradeX">
              <td class="center"><?= $i++ ?></td>
              <td><?= isset($total_dcm[0]['date_created']) ? date('d-m-Y',strtotime($total_dcm[0]['date_created'])) : 'Belum diisi' ?></td>
              <td><?= $value_kelas['jumlah_siswa'] ?> Siswa (<?= count($total_dcm). " Siswa" ?>)</td>
              <td><?= $value_kelas['kelas'] ?></td>
              <td><a class="btn btn-sm btn-primary ml-2" href="<?= base_url('dcm/view/'.$value_kelas['id']) ?>"><i class="fa fa-eye"></i> Buka</a><button type="button" class="btn btn-sm btn-danger ml-2"><i class="fa fa-trash-o"></i> Hapus</button></td>
            </tr>
            <?php
          }
          ?>          
        </tbody>
        <tfoot>
         <tr>
          <th>No</th>
          <th>Terakhir Diisi</th>
          <th>Jumlah Siswa</th>
          <th>Kelas</th>
          <th>Aksi</th>
        </tr>
      </tfoot>
    </table>
  </div>
</div>
</div>
</div>
</div>