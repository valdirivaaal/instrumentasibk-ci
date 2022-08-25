<div class="row">
	<div class="col-md-12">
		<div class="panel-body bio-graph-info">
			<h1 class="font-weight-bold"> <?= (getField('user_info', 'status', array('user_id' => $this->session->userdata('id'))) == 'Guru BK') ? 'Kelas' : 'Kelompok' ?> Anda</h1>
		</div>
		<div class="alert alert-info" role="alert">
			<strong>Halo, <?php echo $get_profil[0]['nama_lengkap']; ?>!</strong><br>Kamu dapat menginstruksikan peserta didik untuk melengkapi biodata diri di <a href="<?php echo base_url('siswa/siswa_biodata/' . $this->session->userdata('id')); ?>"><?php echo base_url('siswa/siswa_biodata/' . $this->session->userdata('id')); ?></a>
		</div>
		<div class="card">
			<div class="card-header">
				Data <?= (getField('user_info', 'status', array('user_id' => $this->session->userdata('id'))) == 'Guru BK') ? 'Kelas' : 'Kelompok' ?>
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
				<a href="<?= base_url('kelas/tambah') ?>" class="btn btn-primary float-right"><i class="fa fa-plus"></i> Tambah <?= (getField('user_info', 'status', array('user_id' => $this->session->userdata('id'))) == 'Guru BK') ? 'Kelas' : 'Kelompok' ?></a>
				<div class="adv-table">
					<table class="display table table-bordered table-striped dynamic-table">
						<thead>
							<tr>
								<th>No</th>
								<th><?= (getField('user_info', 'status', array('user_id' => $this->session->userdata('id'))) == 'Guru BK') ? 'Kelas' : 'Kelompok' ?></th>
								<th>Jumlah Siswa</th>
								<th><?= ($get_profil[0]['status'] == 'Guru BK') ? 'Guru BK' : 'Konselor' ?></th>
								<?php if ($get_profil[0]['status'] == 'Konselor') { ?>
									<th>Jenjangr</th>
								<?php } ?>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$i =  1;
							foreach ($get_kelas as $key => $value) {
							?>
								<tr class="gradeX">
									<td><?= $i++ ?></td>
									<td><?= $value['kelas'] ?></td>
									<td><?= $value['jumlah_siswa'] ?></td>
									<td><?= $value['nama_lengkap'] ?></td>
									<?php if ($get_profil[0]['status'] == 'Konselor') { ?>
										<td><?= $value['jenjang'] ?></th>
										<?php } ?>
										<td>
											<a href="<?= base_url('kelas/sunting/' . $value['id']) ?>" class="btn btn-sm btn-primary"><i class="fa fa-pencil"></i> Ubah</a>
											<button type="button" class="btn btn-sm btn-danger ml-2 delete-alert<?= $value['id'] ?>" data-id="<?= $value['id'] ?>" onclick="deletealert(this)"><i class="fa fa-trash-o"></i> Hapus</button>
											<a href="<?php echo base_url('kelas/detail/' . $value['id']); ?>" class="btn btn-sm btn-info"><i class="fa fa-file"></i> Daftar Siswa</a>
										</td>
								</tr>
							<?php
							}
							?>
						</tbody>
						<tfoot>
							<tr>
								<th>No</th>
								<th><?= (getField('user_info', 'status', array('user_id' => $this->session->userdata('id'))) == 'Guru BK') ? 'Kelas' : 'Kelompok' ?></th>
								<th>Jumlah Siswa</th>
								<th><?= ($get_profil[0]['status'] == 'Guru BK') ? 'Guru BK' : 'Jenjang' ?></th>
								<th>Action</th>
							</tr>
						</tfoot>
					</table>
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
						url: "<?= base_url() ?>kelas/hapus/" + id,
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
