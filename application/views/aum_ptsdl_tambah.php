<div class="row">
 <div class="col-md-12">
  <div class="panel-body bio-graph-info">
    <h1 class="font-weight-bold"> AUM Umum</h1>
  </div>
  <div class="card">
    <div class="card-header">
      Tambah AUM Umum
    </div>
    <div class="card-body">
      <form class="form-horizontal" role="form" action="<?= base_url('instrumen/aum_ptsdl_save') ?>" method="get">
        <div class="form-group">
          <label  class="col-lg-12 control-label">Tanggal Penyebaran Instrumen</label>
          <div class="col-lg-12">
            <input type="text" class="form-control date-picker-input" placeholder="Tanggal Penyebaran Instrumen"> 
          </div>
        </div>
        <div class="form-group">
          <label  class="col-lg-12 control-label">Batas Pengisian Instrumen</label>
          <div class="col-lg-12">
            <input type="text" class="form-control date-picker-input" placeholder="Batas Pengisian Instrumen"> 
          </div>
        </div>
        <div class="form-group">
          <label  class="col-lg-12 control-label">Kelas</label>
          <div class="col-lg-12">
            <select class="js-example-basic-single">
              <option></option>
              <option value="AL">10 MIPA 1</option>
              <option value="WY">10 MIPA 2</option>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label  class="col-lg-12 control-label">Tahun Ajaran</label>
          <div class="col-lg-12">
            <input type="text" class="form-control" placeholder="Contoh : 2018/2019"> 
          </div>
        </div>
        <div class="form-group">
          <label  class="col-lg-12 control-label">Jumlah Siswa</label>
          <div class="col-lg-12">
            <div id="spinner4">
              <div class="input-group">
                <div class="spinner-buttons input-group-btn">
                  <button type="button" class="btn spinner-up btn-warning">
                    <i class="fa fa-plus"></i>
                  </button>
                </div>
                <input type="text" class="spinner-input form-control mx-2 text-center" maxlength="3" style="max-width:65px">
                <div class="spinner-buttons input-group-btn">
                  <button type="button" class="btn spinner-down btn-danger">
                    <i class="fa fa-minus"></i>
                  </button>
                </div>
              </div>
            </div>
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