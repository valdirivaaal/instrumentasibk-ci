<div class="row">
    <div class="col-md-12">
        <div class="panel-body bio-graph-info">
            <h1 class="font-weight-bold">Manajemen Key</h1>
        </div>
        <div class="card">
            <div class="card-header">
                Edit Logo
            </div>
            <div class="card-body">
                <form action="<?= base_url() ?>admin/key<?= $get_data[0]['id'] ?>" enctype="multipart/form-data" method="POST">
                    <div class="mb-3">
                        <label for="" class="form-label">Event Key</label>
                        <input type="text" class="form-control" name="event_key" placeholder="Provinsi DKI Jakarta" value="<?= $get_data[0]['key_code'] ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="" class="form-label">Pemilik</label>
                        <input type="text" class="form-control" name="nama_lengkap" placeholder="Provinsi DKI Jakarta" value="<?= $get_data[0]['nama_lengkap'] ? $get_data[0]['nama_lengkap'] : '-' ?>" disabled>
                    </div>
                    <?php if ($get_data[0]['key_status'] == 'Inactive') { ?>
                        <div class="mb-3">
                            <label for="" class="form-label">Masa Berlaku (Hari)</label>
                            <input type="text" class="form-control" name="masa_berlaku" placeholder="Provinsi DKI Jakarta" value="<?= $get_data[0]['masa_berlaku'] ?>">
                        </div>
                    <?php } ?>
                    <?php if ($get_data[0]['key_status'] == 'Inactive') { ?>
                        <div class="mb-3">
                            <label for="" class="form-label">Tipe Key</label>
                            <select class="form-control" name="tipe">
                                <option value="1" <?= $get_data[0]['tipe'] == 1 ? 'selected' : '' ?>>Guru BK</option>
                                <option value="2" <?= $get_data[0]['tipe'] == 2 ? 'selected' : '' ?>>Konselor</option>
                                <option value="3" <?= $get_data[0]['tipe'] == 3 ? 'selected' : '' ?>>DCM</option>
                            </select>
                        </div>
                    <?php } ?>


                    <button type="submit" class="btn btn-primary container-fluid">Edit Key</button>
                </form>
            </div>
        </div>
    </div>
</div>