 <div class="row">
   <div class="col-md-12">
    <div class="panel-body bio-graph-info">
      <h1 class="font-weight-bold"> Daftar Guru BK</h1>
    </div>
    <div class="card">
      <div class="card-header">
        Tambah Guru BK
      </div>
      <div class="card-body">
        <form class="form-horizontal" role="form" action="<?= base_url('konselor/save') ?>" method="post">
          <input type="hidden" name="id" value="<?= (isset($get_konselor[0]['id'])) ? $get_konselor[0]['id'] : '' ?>">
          <div class="form-group">
            <label  class="col-lg-2 control-label">Nama Lengkap</label>
            <div class="col-lg-12">
              <input type="text" name="nama_lengkap" class="form-control" id="f-name" placeholder="Nama Lengkap" value="<?= (isset($get_konselor[0]['nama_lengkap'])) ? $get_konselor[0]['nama_lengkap'] : '' ?>">
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