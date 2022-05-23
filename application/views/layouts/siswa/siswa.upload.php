<div class="row">
	<div class="col-md-12">
		<div class="panel-body bio-graph-info">
			<h1 class="font-weight-bold">
				Upload Data Siswa
			</h1>
		</div>
		<div class="card">
			<div class="card-header">Import Data Siswa</div>
			<div class="card-body">
				<form method="post" action="<?php echo base_url('/kelas/siswa_upload/' . $id_kelas); ?>" enctype="multipart/form-data">
					<div class="form-group mb-1">
						<input type="file" name="file" id="" class="form-control">
					</div>

					<!-- <div class="custom-file mb-3">
						<input type="file" class="custom-file-input" id="validatedCustomFile" name="file">
						<label class="custom-file-label" for="validatedCustomFile">Choose file...</label>
						<div class="invalid-feedback">Example invalid custom file feedback</div>
					</div> -->
					<div class="form-group mb-5">
						<a href="<?php echo base_url('/excel/format.xlsx'); ?>" class="btn btn-danger"><i class="fa fa-download"></i> Download Template</a>
						<button type="submit" name="preview" class="btn btn-primary"><i class="fa fa-search"></i> Preview</button>
					</div>
				</form>

				<?php 
					if (isset($_POST['preview']))
					{
						if (isset($upload_error))
						{
							echo "<div style='color: red;'>".$upload_error."</div>";
							die;
						}

						?>
						<form action="<?php echo base_url('/kelas/siswa_import/' . $id_kelas); ?>" method="post">
							<div style="color: red;" id="kosong">Semua data belum diisi, Ada <span id="jumlah_kosong"></span> data yang belum diisi</div>

							<table class="table">
								<thead>
									<tr>
										<th colspan="5" style="text-align: center;">Preview Data</th>
									</tr>
									<tr>
										<th>NIS</th>
										<th>Nama</th>
										<th>Jenis Kelamin</th>
										<th>Alamat</th>
									</tr>
								</thead>
								<tbody>
									<?php 
										$numrow = 1;
										$kosong = 0;

										foreach ($sheet as $row) {
											$nis = $row['A'];
											$nama = $row['B'];
											$jk = $row['C'];
											$alamat = $row['D'];

											if ($nis == '' && $nama = '' && $jk == '' && $alamat == '')
												continue;

											if ($numrow > 1) {
												$nisRow = (!empty($nis)) ? '' : "style='background: #E07171;'";
												$namaRow = (!empty($nama)) ? '' : "style='background: #E07171;'";
												$jkRow = (!empty($jk)) ? '' : "style='background: #E07171;'";
												$alamatRow = (!empty($alamat)) ? '' : "style='background: #E07171;'";

												if ($nis == '' or $nama == '' or $jk == '' or $alamat == '')
													$kosong++;
												?>
													<tr>
														<td <?php echo $nisRow; ?>><?php echo $nis; ?></td>
														<td <?php echo $namaRow; ?>><?php echo $nama; ?></td>
														<td <?php echo $jkRow; ?>><?php echo $jk; ?></td>
														<td <?php echo $alamatRow; ?>><?php echo $alamat; ?></td>
													</tr>
												<?php
											}

											$numrow++;
										}
									?>
								</tbody>
							</table>
							<?php
								if ($kosong > 0) {
									?>
										<script>
											$(document).ready(function() {
												$("#jumlah_kosong").html('<?php echo $kosong; ?>');
												
												$("#kosong").show();
											});
										</script>
									<?php
								} else {
									?>
										<hr>
										<button type="submit" name="import" class="btn btn-info"><i class="fa fa-cloud-upload"></i> Import</button>
										<a href="<?php echo base_url('/kelas/detail/' . $id_kelas); ?>">Cancel</a>
									<?php
								}
							?>
						</form>
					<?php
					}
				?>
			</div>
		</div>
	</div>
</div>
<script>
	$(document).ready(function() {
		$("#kosong").hide();
	});
</script>
