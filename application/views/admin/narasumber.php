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
								<th>Nama Narasumber</th>
								<th>Email</th>
								<th>Asal Sekolah</th>
								<th>Kelas</th>
								<th>Tanggal Lahir</th>
								<th>Nomor Whatsapp</th>
								<th>Instrumen</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$i =  1;
							foreach ($get_narsum as $key => $value) {
								// $get_data = $get_data[0];

								if ($value['nickname'] == 'AUM Umum') {
									$instrum = 'aum';
								} else if ($value['nickname'] == 'AUAP') {
									$instrum = 'auap';
								} else if ($value['nickname'] == 'AUM PTSDL') {
									$instrum = 'ptsdl';
								} else if ($value['nickname'] == 'DCM') {
									$instrum = 'dcm';
								} else {
									$instrum = '';
								}
							?>
								<tr class="gradeX">
									<td><?= $i++ ?></td>
									<td><?= $value['nama_narasumber'] ?></td>
									<td><?= $value['email_narasumber'] ?></td>
									<td><?= $value['instansi'] ?></td>
									<td><?= $value['kelas'] ?></td>
									<td><?= $value['tanggal_lahir'] ?></td>
									<td><?= $value['whatsapp'] ?></td>
									<td><?= $value['nickname'] ?> - <?= $value['jenjang_instrumen'] ?></td>
									<td><?php if (!empty($instrum)) { ?>
											<a href="<?= base_url($instrum . '/laporan_individu/' . $value['id_narasumber']) ?>" class="btn btn-info">Download Laporan</a>
										<?php } else { ?>
											<a href="<?= base_url($instrum . '/laporan_individu/' . $value['id_narasumber']) ?>" class="btn btn-info" role="button" aria-disabled="true">Laporan tidak tersedia</a>
										<?php } ?>
										<button onclick="deletealert(this)" data-id="<?= $value['id_narasumber'] ?>" class="btn btn-danger delete-alert<?= $value['id_narasumber'] ?>">Hapus</button>
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
			text: "Kamu juga akan menghapus jawaban Narasumber Tersebut,Data yang sudah dihapus tidak dapat dikembalikan",
			type: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085d6',
			cancelButtonColor: '#d33',
			confirmButtonText: 'Yes, delete it!'
		}).then((result) => {
			if (result.value) {
				$.ajax({
					type: "POST",
					url: "<?= base_url('hapus/narasumber/') ?>" + id,
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
