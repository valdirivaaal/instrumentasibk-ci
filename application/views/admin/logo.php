<div class="row">
    <div class="col-md-12">
        <div class="panel-body bio-graph-info">
            <h1 class="font-weight-bold">Manajemen Logo</h1>
        </div>
        <div class="card">
            <div class="card-header">
                Manajemen Logo
            </div>
            <div class="card-body">

                <?php
                if ($this->session->flashdata('success')) {
                ?>
                    <div class="alert alert-success" role="alert">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        <strong>Sukses!</strong> Data <?= $this->session->userdata('success') ?> berhasil ditambahkan.
                        <?php unset($_SESSION['success']) ?>
                    </div>
                <?php
                }
                ?>
                <?php
                if ($this->session->flashdata('error')) {
                ?>
                    <div class="alert alert-danger" role="alert">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        <strong>Error!</strong><?= $this->session->userdata('error') ?>
                        <?php unset($_SESSION['error']) ?>
                    </div>
                <?php
                }
                ?>

                <a href="<?= base_url('logo/tambah') ?>" class="btn btn-primary float-right"><i class="fa fa-plus"></i> Tambah Logo</a>
                <div class="adv-table">
                    <table class="display table table-bordered table-striped dynamic-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Logo</th>
                                <th>Nama daerah</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i =  1;
                            foreach ($get_logo as $key => $value) {
                            ?>
                                <tr class="gradeX">
                                    <td style="width: 5%;"><?= $i++ ?></td>
                                    <td><img src="<?= base_url() ?>uploads/logo/<?= $value['path'] ?>" alt="Logo" width="70px" height="70px"></td>
                                    <td style="width: 40%;"><?= $value['nama'] ?></td>
                                    <td style="width: 15%;"><a href="<?= base_url() ?>logo/edit/<?= $value['id'] ?>" class="btn btn-info">Edit</a>
                                        <button onclick="deletealert(this)" data-id="<?= $value['id'] ?>" class="btn btn-danger delete-alert<?= $value['id'] ?>">Hapus</button>
                                    </td>
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

<script type="text/javascript">
    function deletealert(data) {
        var id = data.getAttribute("data-id");
        swal.fire({
            title: 'Apakah kamu yakin?',
            text: "Kamu tidak bisa mengembalikan data setelah terhapus!",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    type: "POST",
                    url: "<?= base_url() ?>logo/hapus/" + id,
                    cache: false,
                    success: function(response) {
                        swal.fire({
                            title: 'Terhapus!',
                            text: 'Tunggu beberapa detik atau klik ok.',
                            type: 'success',
                            timer: 3000
                        }, function() {
                            window.location.reload();
                        });
                        setTimeout(function() {
                            window.location.reload();
                        }, 3000);
                    }
                })
            }
        })
    }
</script>