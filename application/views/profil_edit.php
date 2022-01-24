 <div class="row">
   <div class="col-md-12">
    <div class="panel-body bio-graph-info">
      <h1 class="font-weight-bold"> Profil Anda</h1>
    </div>
    <div class="card">
      <div class="card-header">
        Ubah Profil
      </div>
      <div class="card-body">
        <?php
        if ($this->session->userdata('success')) {
          ?>
          <div class="alert alert-success" role="alert">
            <strong>Sukses!</strong> <?= $this->session->userdata('success') ?>
          </div>
          <?php
        }
        ?>
        
        <form class="form-horizontal" role="form" action="<?= base_url('profil/save') ?>" method="post" enctype="multipart/form-data">
          <div class="form-group">
            <label  class="col-lg-2 control-label">Nama Lengkap</label>
            <div class="col-lg-12">
              <input type="text" name="nama_lengkap" class="form-control" id="f-name" placeholder="Nama Lengkap" value="<?= ($get_profil[0]['nama_lengkap']) ? $get_profil[0]['nama_lengkap'] : '' ?>">
            </div>
          </div>
          <div class="form-group">
            <label  class="col-lg-2 control-label">Tanggal Lahir</label>
            <div class="col-lg-12">
              <input type="text" placeholder="dd/mm/yyyy" data-mask="99/99/9999" class="form-control" name="tanggal_lahir" value="<?= ($get_profil[0]['tanggal_lahir']!='0000/00/00') ? date('d/m/Y',strtotime($get_profil[0]['tanggal_lahir'])) : '' ?>">
              <span class="help-inline">Format : dd/mm/yyyy</span>
            </div>
          </div>
          <div class="form-group">
            <label  class="col-lg-2 control-label">No. Ponsel</label>
            <div class="col-lg-12">
              <input type="text" class="form-control" id="c-name" placeholder="No. Ponsel" name="no_whatsapp" value="<?= ($get_profil[0]['no_whatsapp']) ? $get_profil[0]['no_whatsapp'] : '' ?>">
            </div>
          </div>
          <div class="form-group">
            <label  class="col-lg-2 control-label">Alamat Rumah</label>
            <div class="col-lg-12">
              <textarea name="alamat_rumah" id="" class="form-control" cols="30" rows="3" placeholder="Alamat Rumah"><?= ($get_profil[0]['alamat_rumah']) ? $get_profil[0]['alamat_rumah'] : '' ?></textarea>
            </div>
          </div>
          <div class="form-group">
            <label  class="col-lg-2 control-label">Sekolah</label>
            <div class="col-lg-12">
              <input type="text"  class="form-control" id="occupation" placeholder="Nama Sekolah" name="instansi" value="<?= ($get_profil[0]['instansi']) ? $get_profil[0]['instansi'] : '' ?>">
            </div>
          </div>
          <div class="form-group">
            <label  class="col-lg-2 control-label">Telp. Sekolah</label>
            <div class="col-lg-12">
              <input type="text" class="form-control" id="occupation" placeholder="Telp. Instansi" name="telp_instansi" value="<?= ($get_profil[0]['telp_instansi']) ? $get_profil[0]['telp_instansi'] : '' ?>">
            </div>
          </div>
          <div class="form-group">
            <label  class="col-lg-2 control-label">Alamat Sekolah</label>
            <div class="col-lg-12">
              <textarea name="alamat_instansi" id="" class="form-control" cols="30" rows="3" placeholder="Alamat Sekolah"><?= ($get_profil[0]['alamat_instansi']) ? $get_profil[0]['alamat_instansi'] : '' ?></textarea>
            </div>
          </div>
          <div class="form-group">
            <label  class="col-lg-2 control-label">Motto Hidup</label>
            <div class="col-lg-12">
              <input type="text" class="form-control" id="c-name" placeholder="Motto Hidup" name="motto_hidup" value="<?= ($get_profil[0]['motto_hidup']) ? $get_profil[0]['motto_hidup'] : '' ?>">
            </div>
          </div>

          <div class="form-group">
            <div class="col-lg-offset-2 col-lg-10">
              <button type="submit" class="btn btn-success">Save</button>
              <button type="button" class="btn btn-default">Cancel</button>
            </div>
          </div>
        </form>
      </div>
    </div>