<div class="row">
	<div class="col-md-12">
		<div class="panel-body bio-graph-info">
			<h1 class="font-weight-bold"> Daftar Narasumber</h1>
		</div>
		<div class="card">
			<div class="card-header">
				Daftar Narasumber
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
				<form class="form-horizontal" role="form" action="<?= base_url('admin/pesertadidik') ?>" method="get">

					<div class="input-group mb-3">
						<select class="form-control" name="tahun_ajaran" id="tahun_ajaran">
							<option value="" selected>Lihat seluruh tahun ajaran</option>
							<?php
							foreach ($tahun_ajaran as $value) {
								if (!empty($value['tahun_ajaran'])) {
							?>

									<option value="<?= $value['tahun_ajaran'] ?>" <?= isset($_GET['tahun_ajaran']) && $value['tahun_ajaran'] == $_GET['tahun_ajaran']  ? 'selected' : '' ?>><?= $value['tahun_ajaran'] ?></option>
							<?php
								}
							}
							?>
						</select>
						<select class="form-control" name="sekolah" id="sekolah">
							<option selected>Lihat Seluruh Sekolah</option>
						</select>
						<div class="input-group-append">
							<button class="btn btn-primary" type="submit">Filter</button>
						</div>
					</div>
				</form>
				<a id="download_all" href="<?= base_url('admin/download_narasumber?tahun_ajaran=') ?><?= isset($_GET['tahun_ajaran']) ? $_GET['tahun_ajaran'] : '' ?>&sekolah=<?= isset($_GET['sekolah']) ? $_GET['sekolah'] : '' ?>" class="btn btn-sm float-right ml-2 btn-primary"><i class="fa fa-download"></i> Download Daftar Narasumber PDF</a>

				<a id="download_all" href="<?= base_url('admin/export_excel_narasumber?tahun_ajaran=') ?><?= isset($_GET['tahun_ajaran']) ? $_GET['tahun_ajaran'] : '' ?>&sekolah=<?= isset($_GET['sekolah']) ? $_GET['sekolah'] : '' ?>" class="btn btn-sm float-right ml-2 btn-success"><i class="fa fa-download"></i> Download Daftar Narasumber Excel</a>
				<div class="adv-table table-responsive">
					<table id="mytable" class="display table table-bordered table-striped dynamic-table">
						<thead style="vertical-align : middle;text-align:center;">
							<tr>
								<th>No</th>
								<th>NIS</th>
								<th>Nama</th>
								<th>Jenis Kelamin</th>
								<th>Tanggal Lahir</th>
								<th>Email</th>
								<th>Nomor Whatsapp</th>
								<th>Instansi - Kelas</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$i =  1;
							foreach ($siswa as $key => $value) {
								if ($value['jk'] == 'Laki-laki' || $value['jk'] == 'L') {
									$jeniskelamin = 'L';
								} else {
									$jeniskelamin = 'P';
								}

								$get_kelas = $this->db->query("SELECT DISTINCT * FROM kelas JOIN user_info ON kelas.user_id = user_info.user_id WHERE kelas.id = " . $value['id_kelas'])->result_array();

								if (!empty($get_kelas)) {
									$sekolah = $get_kelas[0]['instansi'];
									$kelas = $get_kelas[0]['kelas'];
								} else {
									$sekolah = 'No Data';
									$kelas = 'Found';
								}

							?>
								<tr class="gradeX">
									<td><?= $i ?></td>
									<td><?= $value['nis'] ?></td>
									<td><?= $value['nama'] ?></td>
									<td><?= $jeniskelamin ?></td>
									<td><?= $value['tgl_lahir'] ?></td>
									<td><?= $value['email'] ?></td>
									<td><?= $value['no_telepon'] ?></td>
									<td><?= $sekolah  ?> - <?= $kelas ?></td>
								</tr>
							<?php
								$i++;
							}
							?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	$(document).ready(function() {

		var tahun_ajaran = $("#tahun_ajaran").val();
		var sekolah = "<?= isset($_GET['sekolah']) ? $_GET['sekolah'] : '' ?>";

		$.ajax({
			url: '/admin/getsekolah',
			data: 'tahunajaran=' + tahun_ajaran + '&sekolah=' + sekolah,
			type: 'POST',
			dataType: 'html',
			success: function(msg) {
				$("#sekolah").html(msg);
			}
		});

		$("#tahun_ajaran").change(function() {
			var tahun_ajaran = $("#tahun_ajaran").val();
			console.log('Berubah');
			$.ajax({
				url: '/admin/getsekolah',
				data: 'tahunajaran=' + tahun_ajaran,
				type: 'POST',
				dataType: 'html',
				success: function(msg) {
					$("#sekolah").html(msg);
				}
			});
		});
	});
</script>
