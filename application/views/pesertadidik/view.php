<div class="row">
	<div class="col-md-12">
		<div class="panel-body bio-graph-info">
			<h1 class="font-weight-bold"> Daftar Peserta Didik</h1>
		</div>
		<div class="card">
			<div class="card-header">
				Daftar Peserta Didik
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
				<form class="form-horizontal" role="form" action="<?= base_url('pesertadidik/view') ?>" method="get">

					<div class="input-group mb-3">
						<select class="form-control" name="tahun_ajaran" id="tahun_ajaran" required>
							<option selected>Pilih tahun Ajaran</option>
							<?php
							foreach ($tahun_ajaran as $value) {
								if (!empty($value['tahun_ajaran'])) {
							?>

									<option value="<?= $value['tahun_ajaran'] ?>" <?= $value['tahun_ajaran'] == $_GET['tahun_ajaran'] ? 'selected' : '' ?>><?= $value['tahun_ajaran'] ?></option>
							<?php
								}
							}
							?>
						</select>
						<select class="form-control" name="kelas" id="kelas" required>
							<option selected>Pilih Tahun ajaran terlebih dahulu</option>
						</select>
						<div class="input-group-append">
							<button class="btn btn-primary" type="submit">Filter</button>
						</div>
					</div>
				</form>
				<a onclick='downloadAll()' id="download_all" href="#" class="btn btn-sm float-right ml-2 btn-primary"><i class="fa fa-download"></i> Unduh Semua Laporan Individu</a>
				<a onclick='downloadKelas()' id="download_all" href="#" class="btn btn-sm float-right ml-2 btn-warning"><i class="fa fa-download"></i> Unduh Laporan Kelas</a>
				<div class="adv-table table-responsive">
					<table id="mytable" class="display table table-bordered table-striped dynamic-table">
						<thead style="vertical-align : middle;text-align:center;">
							<tr>
								<th rowspan="2" style="vertical-align : middle;text-align:center;">No</th>
								<th rowspan="2" style="vertical-align : middle;text-align:center;">NIS</th>
								<th rowspan="2" style="vertical-align : middle;text-align:center;">Nama</th>
								<th rowspan="2" style="vertical-align : middle;text-align:center; width: 10px">Jenis Kelamin</th>
								<th rowspan="2" style="vertical-align : middle;text-align:center;">Tanggal Lahir</th>
								<th rowspan="2" style="vertical-align : middle;text-align:center;">Email</th>
								<th rowspan="2" style="vertical-align : middle;text-align:center;">Nomor Whatsapp</th>
								<th colspan="4">Instrumen</th>
							</tr>
							<tr>
								<th>DCM</th>
								<th>AUM-UMUM</th>
								<th>AUM-PTSDL</th>
								<th>AU-AP</th>
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

								$dataInstrumen = $this->Main_model->join('instrumen_jawaban', 'instrumen_jawaban.id as id_narasumber,instrumen.nickname as nama_instrumen,instrumen_jawaban.email as email_narasumber,instrumen_jawaban.nama_lengkap as nama_narasumber,instrumen.jenjang as jenjang_instrumen', [[
									'table' => 'user_instrumen',
									'parameter' => 'instrumen_jawaban.instrumen_id = user_instrumen.id'
								], [
									'table' => 'instrumen',
									'parameter' => 'user_instrumen.instrumen_id = instrumen.id'
								], [
									'table' => 'kelas',
									'parameter' => 'instrumen_jawaban.kelas = kelas.id'
								], [
									'table' => 'user_info',
									'parameter' => 'kelas.user_id = user_info.user_id'
								], [
									'table' => 'user_konselor',
									'parameter' => 'kelas.konselor_id = user_konselor.id'
								]], [
									'instrumen_jawaban.nama_lengkap' => $value['nama'],
									'user_instrumen.user_id' => $this->session->userdata('id'),
									'kelas.id' => $_GET['kelas']
								]);

								$dcmExist = false;
								$aumExist = false;
								$ptsdlExist = false;
								$auapExist = false;

								foreach ($dataInstrumen as $valInstrum) {
									if ($valInstrum['nama_instrumen'] == 'DCM') {
										$dcmExist = true;
										$dcmId = $valInstrum['id_narasumber'];
										$dcmInstrum = 'dcm';
									} else if ($valInstrum['nama_instrumen'] == 'AUM Umum') {
										$aumExist = true;
										$aumId = $valInstrum['id_narasumber'];
										$aumInstrum = 'aum';
									} else if ($valInstrum['nama_instrumen'] == 'AUM PTSDL') {
										$ptsdlExist = true;
										$ptsdlId = $valInstrum['id_narasumber'];
										$ptsdlInstrum = 'ptsdl';
									} else if ($valInstrum['nama_instrumen'] == 'AUAP') {
										$auapExist = true;
										$auapId = $valInstrum['id_narasumber'];
										$auapInstrum = 'auap';
									}
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
									<?php if ($dcmExist) { ?>
										<td class="">
											<a class="btn btn-sm btn-primary container-fluid mb-2 individu-download " data-instrum="<?= $dcmInstrum ?>" data-nama="<?= $value['nama'] ?>" target="_blank" href="<?= base_url($dcmInstrum . '/laporan_individu/' . $dcmId) ?>"><i class="fa fa-file"></i> Cetak Laporan</a>
											<button type="button" class="btn btn-sm btn-danger container-fluid delete-alert<?= $dcmId ?>" data-id="<?= $dcmId ?>" data-instrum="<?= $dcmInstrum ?>" onclick="deletealert(this)"><i class="fa fa-trash-o"></i> Hapus Laporan</button>
										</td>
									<?php } else { ?>
										<td style="background-color: #e03c26; text-align:center;">X</td>
									<?php } ?>

									<?php if ($aumExist) { ?>
										<td class="">
											<a class="btn btn-sm btn-primary container-fluid mb-2 individu-download " data-instrum="<?= $aumInstrum ?>" data-nama="<?= $value['nama'] ?>" target="_blank" href="<?= base_url($aumInstrum . '/laporan_individu/' . $aumId) ?>"><i class="fa fa-file"></i> Cetak Laporan</a>
											<button type="button" class="btn btn-sm btn-danger container-fluid delete-alert<?= $aumId ?>" data-id="<?= $aumId ?>" data-instrum="<?= $aumInstrum ?>" onclick="deletealert(this)"><i class="fa fa-trash-o"></i> Hapus Laporan</button>
										</td>
									<?php } else { ?>
										<td style="background-color: #e03c26; text-align:center;">X</td>
									<?php } ?>

									<?php if ($ptsdlExist) { ?>
										<td class="">
											<a class="btn btn-sm btn-primary container-fluid mb-2 individu-download " data-instrum="<?= $ptsdlInstrum ?>" data-nama="<?= $value['nama'] ?>" target="_blank" href="<?= base_url($ptsdlInstrum . '/laporan_individu/' . $ptsdlId) ?>"><i class="fa fa-file"></i> Cetak Laporan</a>
											<button type="button" class="btn btn-sm btn-danger container-fluid delete-alert<?= $ptsdlId ?>" data-id="<?= $ptsdlId ?>" data-instrum="<?= $ptsdlInstrum ?>" onclick="deletealert(this)"><i class="fa fa-trash-o"></i> Hapus Laporan</button>
										</td>
									<?php } else { ?>
										<td style="background-color: #e03c26; text-align:center;">X</td>
									<?php } ?>

									<?php if ($auapExist) { ?>
										<td class="">
											<a class="btn btn-sm btn-primary container-fluid mb-2 individu-download " data-instrum="<?= $auapInstrum ?>" data-nama="<?= $value['nama'] ?>" target="_blank" href="<?= base_url($auapInstrum . '/laporan_individu/' . $auapId) ?>"><i class="fa fa-file"></i> Cetak Laporan</a>
											<button type="button" class="btn btn-sm btn-danger container-fluid delete-alert<?= $auapId ?>" data-id="<?= $auapId ?>" data-instrum="<?= $auapInstrum ?>" onclick="deletealert(this)"><i class="fa fa-trash-o"></i> Hapus Laporan</button>
										</td>
									<?php } else { ?>
										<td style="background-color: #e03c26; text-align:center;">X</td>
									<?php } ?>
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
	//Unduh Laporan Individu
	var download;

	function downloadAll() {
		var myTable = $("#mytable").DataTable();
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
		$("#mytable").DataTable().$(".individu-download").each(function() {
			var url = $(this).attr('href');
			var name = $(this).attr('data-nama');
			var instrumen = $(this).attr('data-instrum');
			urls.push({
				url: url,
				name: name,
				instrumen: instrumen
			});
			urls[url = name];
			console.log(urls);
		});

		function request(url, name, instrumen) {
			return new Promise(function(resolve) {
				var httpRequest = new XMLHttpRequest();
				httpRequest.open("GET", url);
				httpRequest.responseType = "blob";
				httpRequest.onload = function() {
					var blob = new Blob([this.response], {
						type: 'application/pdf'
					});
					var filename = kelas + "/" + instrumen + "/" + name + ".pdf";
					zip.file(filename, blob);
					resolve()
				}
				httpRequest.send()
			})
		}

		Promise.all(urls.map(function(data) {
				var url = data['url'] + ".pdf";
				var name = data['name'];
				var instrumen = data['instrumen'];
				return request(url, name, instrumen)
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

<script>
	//Unduh Laporan Kelas
	var download;

	function downloadKelas() {
		var myTable = $("#mytable").DataTable();
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
		urls.push({
			url: "<?= base_url('aum/laporan_kelas/' . $get_kelas[0]['id']) ?>",
			name: kelas,
			instrumen: "aum"
		}, {
			url: "<?= base_url('auap/laporan_kelas/' . $get_kelas[0]['id']) ?>",
			name: kelas,
			instrumen: "auap"
		}, {
			url: "<?= base_url('ptsdl/laporan_kelas/' . $get_kelas[0]['id']) ?>",
			name: kelas,
			instrumen: "ptsdl"
		}, {
			url: "<?= base_url('dcm/laporan_kelas/' . $get_kelas[0]['id']) ?>",
			name: kelas,
			instrumen: "dcm"
		});
		urls[url = name];
		console.log(urls)

		function request(url, name, instrumen) {
			return new Promise(function(resolve) {
				var httpRequest = new XMLHttpRequest();
				httpRequest.open("GET", url);
				httpRequest.responseType = "blob";
				httpRequest.onload = function() {
					var blob = new Blob([this.response], {
						type: 'application/pdf'
					});
					var filename = kelas + "/" + instrumen + "/" + name + ".pdf";
					zip.file(filename, blob);
					resolve()
				}
				httpRequest.send()
			})
		}

		Promise.all(urls.map(function(data) {
				var url = data['url'] + ".pdf";
				var name = data['name'];
				var instrumen = data['instrumen'];
				return request(url, name, instrumen)
			}))
			.then(function() {
				console.log(zip);
				zip.generateAsync({
						type: "blob"
					})
					.then(function(content) {
						$("#download_all").attr("href", URL.createObjectURL(content));
						$("#download_all").attr("download", "Laporan Kelas " + kelas + "- Tahun Ajaran " + tahun_ajaran + " - " + sekolah);
						$("#download_all").removeAttr("onclick");
						$("#download_all").removeAttr("disabled");
						$("#download_all").removeClass("btn-danger");
						$("#download_all").addClass("btn-primary");
						$("#download_all").html("<i class='fa fa-download'></i> Unduh Laporan Kelas");
						saveAs(content, "Laporan Kelas " + kelas + "- Tahun Ajaran " + tahun_ajaran + " - " + sekolah);
					});
			})
	}
</script>


<script type="text/javascript">
	function deletealert(data) {
		var id = data.getAttribute("data-id");
		var type = data.getAttribute("data-instrum");
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
					url: "<?= base_url() ?>hapus/" + type + "/<?= $_GET['kelas'] ?>/" + id,
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

<script>
	$(document).ready(function() {
		var tahun_ajaran = $("#tahun_ajaran").val();
		var kelas = <?= $_GET['kelas'] ?>;

		$.ajax({
			url: '/pesertadidik/getkelas',
			data: 'tahunajaran=' + tahun_ajaran + '&kelas=' + kelas,
			type: 'POST',
			dataType: 'html',
			success: function(msg) {
				$("#kelas").html(msg);
			}
		});

		$("#tahun_ajaran").change(function() {
			var tahun_ajaran = $("#tahun_ajaran").val();
			console.log('Berubah');
			$.ajax({
				url: '/pesertadidik/getkelas',
				data: 'tahunajaran=' + tahun_ajaran,
				type: 'POST',
				dataType: 'html',
				success: function(msg) {
					$("#kelas").html(msg);
				}
			});
		});
	});
</script>
