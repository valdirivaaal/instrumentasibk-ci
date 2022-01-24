 <div class="row">
 	<div class="col-md-12">
 		<div class="panel-body bio-graph-info">
 			<h1 class="font-weight-bold"> Kelompok</h1>
 		</div>
 		<div class="card">
 			<div class="card-header">
 				Sunting Kelompok
 			</div>
 			<div class="card-body">
 				<form class="form-horizontal" role="form" action="<?= base_url('kelompok/save') ?>" method="post">
 					<input type="hidden" name="id" value="<?= (isset($get_kelompok[0]['id'])) ? $get_kelompok[0]['id'] : '' ?>">
 					<div class="form-group">
 						<label  class="col-lg-12 control-label">Nama Kelompok</label>
 						<div class="col-md-12">
 							<div class="form-group">
 								<input type="text" class="form-control" name="nama_kelompok" placeholder="Nama Kelompok" value="<?= (isset($get_kelompok[0]['nama_kelompok'])) ? $get_kelompok[0]['nama_kelompok'] : '' ?>" required>
 							</div>
 						</div>
 					</div>
 					<div class="form-group">
 						<label  class="col-lg-12 control-label">Kelas</label>
 						<div class="col-md-12">
 							<div class="form-group">
 								<select class="form-control js-example-basic-multiple" name="kelas[]" required multiple="multiple">
 									<?php
 									foreach ($get_kelas as $key => $value) {
 										if (isset($get_kelompok[0]['kelas'])) {
 											?>
 											<option value="<?= $value['id'] ?>" <?= (in_array($value['id'],explode(",",$get_kelompok[0]['kelas']))) ? 'selected' : '' ?>><?= $value['kelas'] ?></option>
 											<?php
 										} else {
 											?>
 											<option value="<?= $value['id'] ?>"><?= $value['kelas'] ?></option>
 											<?php
 										}
 									}
 									?>
 								</select>
 							</div>
 						</div>
 					</div>

 					<div class="form-group">
 						<div class="col-lg-offset-2 col-lg-10">
 							<button type="submit" class="btn btn-success">Simpan</button>
 							<button type="button" class="btn btn-default">Batal</button>
 						</div>
 					</div>
 				</form>
 			</div>
 		</div>
 	</div>
 </div>

 <script type="text/javascript">
 	$(document).ready(function() {
 		$('.js-example-basic-multiple').select2({
 			placeholder: "Pilih kelas yang ingin dimasukkan dalam kelompok",
 			allowClear: true
 		});
 	});
 </script>