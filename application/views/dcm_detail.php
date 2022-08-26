<div class="row">
	<div class="col-md-12">
		<div class="panel-body bio-graph-info">
			<h1 class="font-weight-bold">Daftar Cek Masalah</h1>
		</div>
		<div class="card">
			<div class="card-header">
				Data DCM Kelas <?= getField('kelas', 'kelas', array('id' => $id)) ?>
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
				<a class="btn btn-primary btn-sm float-right ml-2" href="<?= base_url('dcm/laporan_kelas/' . $id) ?>"><i class="fa fa-book"></i> Cetak Laporan Kelas</a>
				<div class="adv-table">
					<table class="display table table-bordered table-striped" id="dynamic-table">
						<thead>
							<tr>
								<th>No</th>
								<th>Tanggal Pengisian</th>
								<th>Nama Siswa</th>
								<th>Jenis Kelamin</th>
								<th>Tangal Lahir</th>
								<th>Aksi</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$i = 1;
							foreach ($get_jawaban as $value) {
							?>
								<tr>
									<td class="center"><?= $i++ ?></td>
									<td><?= date('d-m-Y', strtotime($value['date_created'])) ?></td>
									<td><?= $value['nama_lengkap'] ?></td>
									<td><?= $value['jenis_kelamin'] ?></td>
									<td><?= date('d-m-Y', strtotime($value['tanggal_lahir'])) ?></td>
									<td>
										<a class="btn btn-sm btn-primary" href="<?= base_url('dcm/laporan_individu/' . $value['id']) ?>"><i class="fa fa-file"></i> Cetak Laporan</a>
										<button type="button" class="btn btn-sm btn-danger ml-2 delete-alert<?= $value['id'] ?>" data-id="<?= $value['id'] ?>" onclick="deletealert(this)"><i class="fa fa-trash-o"></i> Hapus Laporan</button>
									</td>
								</tr>
							<?php
							}
							?>
						</tbody>
						<tfoot>
							<tr>
								<th>No</th>
								<th>Tanggal Pengisian</th>
								<th>Nama Siswa</th>
								<th>Jenis Kelamin</th>
								<th>Tangal Lahir</th>
								<th>Aksi</th>
							</tr>
						</tfoot>
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
			text: "Dengan mengklik Yes Anda akan menghapus Jawaban / Laporan tersebut.",
			type: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Yes, delete it!'
		}).then((result) => {
			if (result.value) {
				$.ajax({
					type: "POST",
					url: "<?= base_url() ?>hapus/aum/<?= $id ?>/" + id,
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
					},
					error: function(response) {
						swal.fire({
							title: 'Gagal',
							text: 'Tunggu beberapa detik atau klik ok.',
							type: 'error',
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
