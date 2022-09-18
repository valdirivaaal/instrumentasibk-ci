<div class="row">
	<div class="col-md-12">
		<div class="panel-body bio-graph-info">
			<h1 class="font-weight-bold">Alat Ungkap Masalah Kegiatan Belajar</h1>
		</div>
		<div class="card">
			<div class="card-header">
				Data AUM PTSDL Kelas <?= getField('kelas', 'kelas', array('id' => $id)) ?>
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
				<a class="btn btn-primary btn-sm float-right ml-2" href="<?= base_url('ptsdl/laporan_kelas/' . $id) ?>"><i class="fa fa-book"></i> Cetak Laporan Kelas</a>
				<a onclick='downloadAll()' id="download_all" href="#" class="btn btn-sm float-right ml-2 btn-primary"><i class="fa fa-download"></i> Unduh Semua Laporan Individu</a>
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
										<a class="btn btn-sm btn-primary individu-download" data-nama="<?= $value['nama_lengkap'] ?>" target="_blank" href="<?= base_url('ptsdl/laporan_individu/' . $value['id']) ?>"><i class="fa fa-file"></i> Cetak Laporan</a>
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
					url: "<?= base_url() ?>hapus/ptsdl/<?= $id ?>/" + id,
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
						});
					}
				})
			}
		})
	}
</script>
<script>
	var download;

	function downloadAll() {
		var zip = new JSZip();
		var a = document.querySelector("#download_all");
		$("#download_all").text("Sedang mendownload file");
		$("#download_all").removeClass("btn-success");
		$("#download_all").addClass("btn-danger");
		$("#download_all").attr("disabled", "disabled");
		// $("#download_all").hide();
		var kelas = "<?= $get_kelas[0]['kelas'] ?>";
		var tahun_ajaran = "<?= $get_kelas[0]['tahun_ajaran'] ?>";
		var sekolah = "<?= $get_profil[0]['instansi'] ?>";
		var urls = [];
		$(".individu-download").each(function() {
			var url = $(this).attr('href');
			var name = $(this).attr('data-nama');
			urls.push({
				url: url,
				name: name
			});
			urls[url = name];
		});

		function request(url, name) {
			return new Promise(function(resolve) {
				var httpRequest = new XMLHttpRequest();
				httpRequest.open("GET", url);
				httpRequest.responseType = "blob";
				httpRequest.onload = function() {
					var blob = new Blob([this.response], {
						type: 'application/pdf'
					});
					var filename = kelas + "/" + name + ".pdf";
					zip.file(filename, blob);
					resolve()
				}
				httpRequest.send()
			})
		}

		Promise.all(urls.map(function(data) {
				var url = data['url'] + ".pdf";
				var name = data['name'];
				return request(url, name)
			}))
			.then(function() {
				console.log(zip);
				zip.generateAsync({
						type: "blob"
					})
					.then(function(content) {
						$("#download_all").attr("href", URL.createObjectURL(content));
						$("#download_all").attr("download", "Laporan Individu Kelas " + kelas + "- Tahun Ajaran " + tahun_ajaran + " - " + sekolah);
						$("#download_all").removeAttr("onclick");
						$("#download_all").removeAttr("disabled");
						$("#download_all").removeClass("btn-danger");
						$("#download_all").addClass("btn-primary");
						$("#download_all").html("<i class='fa fa-download'></i> Unduh Semua Laporan Individu");
						saveAs(content, "Laporan Individu Kelas " + kelas + "- Tahun Ajaran " + tahun_ajaran + " - " + sekolah);
					});
			})
	}
</script>
