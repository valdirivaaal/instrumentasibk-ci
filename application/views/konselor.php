<div class="row">
 <div class="col-md-12">
  <div class="panel-body bio-graph-info">
    <h1 class="font-weight-bold"> Daftar Guru</h1>
  </div>
  <div class="card">
    <div class="card-header">
      Data Guru
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
      <a href="<?= base_url('konselor/tambah') ?>" class="btn btn-primary float-right"><i class="fa fa-plus"></i> Tambah Guru BK</a>
      <div class="adv-table">
        <table  class="display table table-bordered table-striped dynamic-table">
          <thead> 
            <tr>
              <th>No</th>
              <th>Nama Lengkap</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $i =  1;
            foreach ($get_konselor as $key => $value) {
              ?>
              <tr class="gradeX">
                <td><?= $i++ ?></td>
                <td><?= $value['nama_lengkap'] ?></td>
                <td><a href="<?= base_url('konselor/tambah/'.$value['id']) ?>" class="btn btn-sm btn-primary"><i class="fa fa-pencil"></i> Ubah</a><button type="button" class="btn btn-sm btn-danger ml-2 delete-alert<?= $value['id'] ?>" data-id="<?= $value['id'] ?>" onclick="deletealert(this)"><i class="fa fa-trash-o"></i> Hapus</button></td>
              </tr> 
              <?php
            }
            ?>
          </tbody>
          <tfoot>
            <tr>
              <th>No</th>
              <th>Kelas</th>
              <th>Action</th>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
  function deletealert(data){
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
                    url: "<?= base_url() ?>konselor/hapus/"+id,  
                    cache: false,
                    success: function(response) {
                        swal.fire({
                            title : 'Terhapus!',
                            text : 'Tunggu beberapa detik atau klik ok.',
                            type : 'success',
                            timer : 3000
                        }, function(){
                            window.location.reload();
                        });
                        setTimeout(function() {
                            window.location.reload();
                        }, 3000);
                    }
                }
                )
            }
        })
    }
</script>