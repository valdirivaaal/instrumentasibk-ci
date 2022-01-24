<div class="row">
 <div class="col-md-12">
  <div class="panel-body bio-graph-info">
    <h1 class="font-weight-bold"> Daftar Transaksi</h1>
  </div>
  <div class="card">
    <div class="card-header">
      Data Transaksi
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
      <div class="row">
        <div class="col-sm-3">
          <span>Pilih Bulan</span>
          <select class="form-control" onchange="this.options[this.selectedIndex].value && (window.location = this.options[this.selectedIndex].value);">
            <option value="01" <?= ($m=='01') ? 'selected' : '' ?>>Januari</option>
            <option value="02" <?= ($m=='02') ? 'selected' : '' ?>>Februari</option>
            <option value="03" <?= ($m=='03') ? 'selected' : '' ?>>Maret</option>
            <option value="04" <?= ($m=='04') ? 'selected' : '' ?>>April</option>
            <option value="05" <?= ($m=='05') ? 'selected' : '' ?>>Mei</option>
            <option value="06" <?= ($m=='06') ? 'selected' : '' ?>>Juni</option>
            <option value="07" <?= ($m=='07') ? 'selected' : '' ?>>Juli</option>
            <option value="08" <?= ($m=='08') ? 'selected' : '' ?>>Agustus</option>
            <option value="09" <?= ($m=='09') ? 'selected' : '' ?>>September</option>
            <option value="10" <?= ($m=='10') ? 'selected' : '' ?>>Oktober</option>
            <option value="11" <?= ($m=='11') ? 'selected' : '' ?>>November</option>
            <option value="12" <?= ($m=='12') ? 'selected' : '' ?>>Desember</option>
          </select>
        </div>
        <div class="col-sm-9">
          <a href="<?= base_url('admin/transaksi_add') ?>" class="btn btn-primary float-right"><i class="fa fa-plus"></i> Tambah Transaksi</a>
        </div>
      </div>
      <div class="adv-table">
        <table  class="display table table-bordered table-striped dynamic-table">
          <thead> 
            <tr>
              <th>No</th>
              <th>Nama Pembeli</th>
              <th>Intansi Pembeli</th>
              <th>Event Key</th>
              <th>Jenis Akun</th>
              <th>Tgl Transaksi</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $i =  1;
            foreach ($get_transaksi as $key => $value) {
              ?>
              <tr class="gradeX">
               <td><?= $i++ ?></td>
               <td><?= $value['nama_pembeli'] ?></td>
               <td><?= $value['instansi_pembeli'] ?></td>
               <td><?= $value['event_key'] ?></td>
               <td><?= $value['jenis_akun'] ?></td>
               <td><?= date('d-m-Y',strtotime($value['tanggal_transaksi'])) ?></td>
               <td><a href="<?= base_url('admin/transaksi_add/'.$value['id']) ?>" class="btn btn-sm btn-primary"><i class="fa fa-pencil"></i> Ubah</a><button type="button" class="btn btn-sm btn-danger ml-2 delete-alert<?= $value['id'] ?>" data-id="<?= $value['id'] ?>" onclick="deletealert(this)"><i class="fa fa-trash-o"></i> Hapus</button></td>
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
          url: "<?= base_url() ?>admin/transaksi_hapus/"+id,  
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