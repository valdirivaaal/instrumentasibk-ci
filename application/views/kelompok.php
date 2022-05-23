<div class="row">
 <div class="col-md-12">
  <div class="panel-body bio-graph-info">
    <h1 class="font-weight-bold"> Daftar Kelompok</h1>
  </div>
  <div class="card">
    <div class="card-header">
      Data Kelompok
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
      <a href="<?= base_url('kelompok/tambah') ?>" class="btn btn-primary float-right"><i class="fa fa-plus"></i> Tambah Kelompok</a>
      <div class="adv-table">
        <table  class="display table table-bordered table-striped dynamic-table">
          <thead> 
            <tr>
              <th>No</th>
              <th>Nama Kelompok</th>
              <th>Kelas</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $i =  1;
            foreach ($get_kelompok as $key => $value) {
              $kelas = explode(",", $value['kelas']);
              $get_kelas = $this->Main_model->get_where_in('kelas','id',$kelas);
              $array_kelas = array();

              foreach ($get_kelas as $key_kelas => $value_kelas) {
                $array_kelas[] = $value_kelas['kelas'];
              }
              
              ?>
              <tr class="gradeX">
               <td><?= $i++ ?></td>
               <td><?= $value['nama_kelompok'] ?></td>
               <td><?= implode(", ", $array_kelas) ?></td>
               <td><a href="<?= base_url('kelompok/tambah/'.$value['id']) ?>" class="btn btn-sm btn-primary"><i class="fa fa-pencil"></i> Ubah</a><button type="button" class="btn btn-sm btn-danger ml-2 delete-alert<?= $value['id'] ?>" data-id="<?= $value['id'] ?>" onclick="deletealert(this)"><i class="fa fa-trash-o"></i> Hapus</button></td>
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
          url: "<?= base_url() ?>kelompok/hapus/"+id,  
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
