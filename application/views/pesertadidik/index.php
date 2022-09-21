<div class="row">
	<div class="col-md-12">
		<div class="panel-body bio-graph-info">
			<h1 class="font-weight-bold"> Daftar Peserta Didik</h1>
		</div>
		<div class="card">
			<div class="card-header">
				Kelas dan Tahun ajaran
			</div>
			<div class="card-body">
				<form class="form-horizontal" role="form" action="<?= base_url('pesertadidik/view') ?>" method="get">
					<div class="form-group">
						<label class="col-lg-12 control-label">Tahun Ajaran</label>
						<div class="col-md-12">
							<div class="form-group">
								<select class="form-control" name="tahun_ajaran" id="tahun_ajaran" required>
									<option value="">Pilih Tahun Ajaran</option>
									<?php
									foreach ($get_tahun_ajaran as $value) {
										if (!empty($value['tahun_ajaran'])) {
									?>

											<option value="<?= $value['tahun_ajaran'] ?>"><?= $value['tahun_ajaran'] ?></option>
									<?php
										}
									}
									?>
								</select>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-lg-12 control-label">Pilih Kelas</label>
						<div class="col-md-12">
							<div class="form-group">
								<select class="form-control" name="kelas" id="kelas" required>
									<option value="">Pilih Kelas</option>
								</select>
							</div>
						</div>
					</div>

					<div class="form-group-append">
					</div>
					<div class="form-group">
						<div class="col-lg-offset-2 col-lg-10">
							<button type="submit" class="btn btn-success">Cari data</button>
						</div>
					</div>
				</form>
			</div>
		</div>
		<script type="text/javascript">
			$(document).ready(function() {

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
