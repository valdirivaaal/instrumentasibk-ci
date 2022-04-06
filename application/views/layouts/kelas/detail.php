<div class="row">
	<div class="col-md-12">
		<div class="panel-body bio-graph-info">
			<h1 class="font-weight-bold">
				Daftar Siswa
			</h1>
		</div>
		<div class="card">
			<div class="card-header">
				<?php
					if ($data) {
						echo $data['kelas']['kelas'] ? $data['kelas']['kelas'] : "";
					}
				?>
				<?php //printA($data); ?>
			</div>
			<div class="card-body">
				<?php
					if ($this->session->flashdata('success')) {
						?>
						<div class="alert alert-success" role="alert">
							<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
							<strong>Sukses !</strong> Data <?php echo $this->session->userdata('success'); ?> berhasil disimpan.
						</div>
						<?php
					}
				?>

				<!-- LIST DATA SISWA -->
				<div class="adv-table">
					<table class="display table table-bordered table-striped" id="tableSiswa">
						<thead>
							<tr>
								<th>No</th>
								<th>NIS</th>
								<th>Nama</th>
								<th>Jenis Kelamin</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
							<?php 
								if ($data['siswa']) {
									$counter = 1;

									foreach ($data['siswa'] as $key => $val)
									{
										?>
											<tr>
												<td><?php echo $counter; ?></td>
												<td><?php echo $val['nis']; ?></td>
												<td><?php echo $val['nama']; ?></td>
												<td><?php echo $val['jk'] == 'L' ? 'Laki-laki' : 'Perempuan'; ?></td>
												<td width="18%" align="center">
													<a href="<?php echo base_url('siswa/edit/'.$val['id']); ?>" class="btn btn-sm btn-primary"><i class="fa fa-pencil"></i> Ubah</a>
													<button type="button" class="btn btn-sm btn-danger ml-2 delete-alert<?php echo $val['id']; ?>" data-id="<?php echo $val['id']; ?>" onclick="deleteAlert(this)"><i class="fa fa-trash-o"></i> Hapus</button>
												</td>
											</tr>
										<?php

										$counter++;
									}
								}
							?>
						</tbody>
						<tfoot>
							<tr>
								<th>No</th>
								<th>NIS</th>
								<th>Nama</th>
								<th>Jenis Kelamin</th>
								<th>Actions</th>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function() {

		$("#tableSiswa").DataTable()
	})

	function deleteAlert(data) {

		// Set variables
		let id = data.getAttribute("data-id")
		let url = "<?php echo base_url(); ?>"

		swal.fire({
			title: "Apakah kamu yakin?",
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
					url: url + "siswa/delete/" + id,
					cache: false,
					success: function (response) {
						swal.fire({
							title: "Terhapus!",
							text : 'Tunggu beberapa detik atau klik ok.',
							type : 'success',
							timer : 3000
						}, function() {
							window.location.reload()
						});

						setTimeout(function() {
							window.location.reload();
						}, 3000)
					}
				})
			}
		})
	}
</script>
