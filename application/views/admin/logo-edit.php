<div class="row">
    <div class="col-md-12">
        <div class="panel-body bio-graph-info">
            <h1 class="font-weight-bold">Manajemen Logo</h1>
        </div>
        <div class="card">
            <div class="card-header">
                Edit Logo
            </div>
            <div class="card-body">
                <form action="<?= base_url() ?>logo/edit/<?= $get_data[0]['id'] ?>" enctype="multipart/form-data" method="POST">
                    <div class="mb-3">
                        <label for="" class="form-label">Nama daerah</label>
                        <input type="text" class="form-control" name="nama" placeholder="Provinsi DKI Jakarta" value="<?= $get_data[0]['nama'] ?>">
                    </div>
                    <div class="mb-3">
                        <img src="<?= base_url() ?>uploads/logo/<?= $get_data[0]['path'] ?>" alt="Logo" width="100px" height="100px">
                    </div>
                    <div class="mb-3">
                        <label for="" class="form-label">Logo Daerah</label>
                        <input type="file" class="form-control" name="uploaded_img" placeholder="">
                    </div>
                    <button type="submit" class="btn btn-primary container-fluid">Tambah Logo</button>
                </form>
            </div>
        </div>
    </div>
</div>