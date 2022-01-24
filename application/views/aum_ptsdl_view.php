<div class="row mt-5">
 <div class="col-md-12">
  <div class="panel-body bio-graph-info">
  </div>
  <div class="card">
    <div class="card-header text-center">
      <h1 class="font-weight-bold"> Alat Ungkap Masalah PTSDL</h1>
    </div>
    <div class="card-body">
      <form class="form-horizontal" role="form" action="<?= base_url('instrumen/aum_ptsdl_view_advanced') ?>" method="get">
        <div class="form-group">
          <label  class="col-lg-12 control-label">Nama Lengkap</label>
          <div class="col-lg-12">
            <input type="text" class="form-control" placeholder="Nama Lengkap" name="nama_lengkap" required> 
          </div>
        </div>

        <div class="form-group">
          <label  class="col-lg-12 control-label">Jenis Kelamin</label>
          <div class="col-lg-12">
            <div class="custom-control custom-radio custom-control-inline">
              <input type="radio" id="customRadioInline1" name="customRadioInline1" class="custom-control-input" value="1"  required>
              <label class="custom-control-label" for="customRadioInline1">Laki-laki</label>
            </div>
            <div class="custom-control custom-radio custom-control-inline">
              <input type="radio" id="customRadioInline2" name="customRadioInline1" class="custom-control-input" value="2" required>
              <label class="custom-control-label" for="customRadioInline2">Perempuan</label>
            </div>
          </div>
        </div>

         <div class="form-group">
          <label  class="col-lg-12 control-label">NIS</label>
          <div class="col-lg-12">
            <input type="text" class="form-control" placeholder="NIS" name="nis"> 
          </div>
        </div>

        <div class="form-group">
          <label  class="col-lg-12 control-label">Kelas</label>
          <div class="col-lg-12">
            <select class="js-example-basic-single" name="kelas">
              <option></option>
              <option value="AL">10 MIPA 1</option>
              <option value="WY">10 MIPA 2</option>
            </select>
          </div>
        </div>

        <div class="form-group">
          <label  class="col-lg-12 control-label">Tanggal Lahir</label>
          <div class="col-lg-12">
            <input type="text" class="form-control date-picker-input" placeholder="Tanggal Lahir" name="tanggal_lahir" required> 
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
</div>