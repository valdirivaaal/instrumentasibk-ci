 <div class="row">
 	<div class="col-md-12">
 		<div class="panel-body bio-graph-info">
 			<h1 class="font-weight-bold"> Kelas</h1>
 		</div>
 		<div class="card">
 			<div class="card-header">
 				Tambah Kelas
 			</div>
 			<div class="card-body">
 				<form class="form-horizontal" role="form" action="<?= base_url('kelas/save') ?>" method="post">
 					<div class="form-group">
 						<label  class="col-lg-12 control-label">Berapa jumlah kelas yang ingin dimasukkan?</label>
 						<div class="col-md-12">
 							<div id="spinner4">
 								<div class="input-group">
 									<div class="spinner-buttons input-group-btn">
 										<button type="button" class="btn spinner-up btn-warning">
 											<i class="fa fa-plus"></i>
 										</button>
 									</div>
 									<input type="text" class="spinner-input form-control mx-2 text-center" maxlength="3" readonly style="max-width:65px">
 									<div class="spinner-buttons input-group-btn">
 										<button type="button" class="btn spinner-down btn-danger">
 											<i class="fa fa-minus"></i>
 										</button>
 									</div>
 									<button id="tambah_kelas" type="button" class="btn btn-primary ml-3"><i class="fa fa-plus"></i> Tambah Kelas</button>
 								</div>
 							</div>
 						</div>
 					</div>
 					<div class="form-group-append">
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

 		<script type="text/javascript">
 			$(document).ready(function () {
 				$("#tambah_kelas").click(function(){
 					var spin_value = $(".spinner-input").val();
 					var current_val = $(".input-group-text").last().html();
 					var first_val = parseInt(current_val) + 1;
 					var total_val = parseInt(spin_value) + parseInt(current_val);
 					if (spin_value > 0) {
 						if (current_val == undefined) {
 							for (var i = 1; i <= spin_value; i++) {
 								$(".form-group-append").append('<div class="form-group form-'+i+'"><div class="col-lg-12"><div class="input-group"><div class="input-group-prepend"><span class="input-group-text" id="basic-addon1">'+ i +'</span></div><input type="text" name="kelas[]" class="form-control" placeholder="Kelas" required><select class="form-control"><option value="">Pilih guru BK</option>'+<?= '<option value="">Pilih guru BK</option>' ?>+'</select><input type="number" name="jumlah_siswa[]" class="form-control" placeholder="Jumlah Siswa" required><button type="button" class="btn btn-danger" onClick="$(this).deleteRow('+i+')"><i class="fa fa-times"></i> Hapus Data</button></div></div></div>');
 							}
 						} else {
 							for (var i = first_val; i <= total_val; i++) {
 								$(".form-group-append").append('<div class="form-group form-'+i+'"><div class="col-lg-12"><div class="input-group"><div class="input-group-prepend"><span class="input-group-text" id="basic-addon1">'+ i +'</span></div><input type="text" name="kelas[]" class="form-control" placeholder="Kelas" required><select class="form-control"><option value="">Pilih guru BK</option></select><input type="number" name="jumlah_siswa[]" class="form-control" placeholder="Jumlah Siswa" required><button type="button" class="btn btn-danger" onClick="$(this).deleteRow('+i+')"><i class="fa fa-times"></i> Hapus Data</button></div></div></div>');
 							}
 						}
 					} else {
 						alert('Anda belum 	memasukkan jumlah kelas');
 					}
 				});

 				(function($) {
 					$.fn.deleteRow = function(msg) {
 						alert(msg);
 					};
 				})(jQuery);


 			});
 		</script>