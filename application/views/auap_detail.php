<div class="row">
 <div class="col-md-12">
  <div class="panel-body bio-graph-info">
    <h1 class="font-weight-bold">Alat Ungkap Arah Peminatan</h1>
  </div>
  <div class="card">
    <div class="card-header">
      Data AUAP Kelas <?= getField('kelas','kelas',array('id'=>$id)) ?>
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
      <a class="btn btn-primary btn-sm float-right ml-2" href="<?= base_url('auap/laporan_kelas/'.$id) ?>"><i class="fa fa-book"></i> Cetak Laporan Kelas</a>
      <div class="adv-table">
        <table  class="display table table-bordered table-striped" id="dynamic-table">
          <thead>
            <tr>
              <th>No</th>
              <th>Tanggal Pengisian</th>
              <th>Nama Siswa</th>
              <th>Jenis Kelamin</th>
              <th>Tangal Lahir</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $i = 1;
            foreach ($get_jawaban as $value) {
              ?>
              <tr>
                <td class="center"><?= $i++ ?></td>
                <td><?= date('d-m-Y',strtotime($value['date_created'])) ?></td>
                <td><?= $value['nama_lengkap'] ?></td>
                <td><?= $value['jenis_kelamin'] ?></td>
                <td><?= date('d-m-Y',strtotime($value['tanggal_lahir'])) ?></td>
                <td><a class="btn btn-sm btn-primary" href="<?= base_url('auap/laporan_individu/'.$value['id']) ?>"><i class="fa fa-file"></i> Cetak Laporan</a><button type="button" class="btn btn-sm btn-danger ml-2"><i class="fa fa-trash-o"></i> Hapus</button></td>
              </tr>
              <?php
            }
            ?>
          </tbody>
          <tfoot>
           <tr>
              <th>No</th>
              <th>Tanggal Pengisian</th>
              <th>Nama Siswa</th>
              <th>Jenis Kelamin</th>
              <th>Tangal Lahir</th>
              <th>Aksi</th>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
</div>
</div>
</div>