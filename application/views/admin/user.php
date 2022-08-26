<div class="row">
	<div class="col-md-12">
		<div class="panel-body bio-graph-info">
			<h1 class="font-weight-bold"> Daftar Pengguna</h1>
		</div>
		<div class="card">
			<div class="card-header">
				Data Pengguna
			</div>
			<div class="card-body">
				<?php
				if ($this->session->flashdata('success')) {
				?>
					<div class="alert alert-success" role="alert">
						<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
						<strong>Berhasil!</strong> Data <?= $this->session->userdata('success') ?> berhasil disimpan.
					</div>
				<?php
				}
				?>
				<?php
				if ($this->session->flashdata('error')) {
				?>
					<div class="alert alert-danger" role="alert">
						<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
						<strong>Sukses!</strong> <?= $this->session->userdata('error') ?> .
					</div>
				<?php
				}
				?>
				<div class="adv-table">
					<table class="display table table-bordered table-striped dynamic-table">
						<thead>
							<tr>
								<th>No</th>
								<th>Nama Pengguna</th>
								<th>Email</th>
								<th>Password</th>
								<th>Nomor Whatsapp</th>
								<th>Jenjang</th>
								<th>Instansi</th>
								<th>Event Key</th>
								<th>Sisa Hari</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$i =  1;
							foreach ($get_user as $key => $value) {
								$get_data = $this->GetModel->getLastTicket($value['user_id']);
								// $get_data = $get_data[0];

							?>
								<tr class="gradeX">
									<td><?= $i++ ?></td>
									<td><?= $value['nama_lengkap'] ?></td>
									<td><?= $value['email'] ?></td>
									<td><?= $value['password'] ?></td>
									<td><?= $value['no_whatsapp'] ?></td>
									<td><?= $value['jenjang'] ?></td>
									<td><?= $value['instansi'] ?></td>
									<td><?= !empty($get_data) ? $get_data[0]['event_key']  : '-' ?></td>
									<td><?= !empty($get_data) ? ceil((strtotime($get_data[0]['tgl_kadaluarsa']) - time()) / (60 * 60 * 24)) . ' Hari' : 'Tidak Aktif'; ?></td>
									<td>
										<a href="<?= base_url('admin/user/edit/' . $value['user_id']) ?>" class="btn btn-info">Edit</a>
										<button onclick="deletealert(this)" data-id="<?= $value['user_id'] ?>" class="btn btn-danger delete-alert<?= $value['user_id'] ?>">Hapus</button>
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
					url: "<?= base_url('hapus/user_admin/') ?>" + id,
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
