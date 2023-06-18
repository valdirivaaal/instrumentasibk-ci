<?php
$params = [
	'judul' => '',
	'id_pertanyaan' => '',
	'jumlah_pertanyaan' => 0,
	'jumlah_pilihan' => '',
	'bobot_penilaian' => [],
	'url' => '',
];

// printA($config);

if ($codeSettled) {
	// Set variable
	$params['judul'] = $codeSettled[0]['judul'];
	$params['id_pertanyaan'] = unserialize($codeSettled[0]['id_pertanyaan']);
	$params['jumlah_pertanyaan'] = count($params['id_pertanyaan']);
	$params['jumlah_pilihan'] = $codeSettled[0]['jumlah_pilihan'];
	$params['bobot_penilaian'] = unserialize($codeSettled[0]['bobot_penilaian']);
	$params['url'] = $codeSettled[0]['url'];
}
?>
<div class="row">
	<div class="col-md-12">
		<div class="panel panel-body bio-graph-info">
			<h1 class="font-weight-bold">Setting Form Penyebaran</h1>
		</div>
		<div class="alert alert-info" role="alert">
			<strong>Kode ini akan menjadi link untuk disebarkan ke anak-anak Bapak/Ibu. Bapak/Ibu bisa menggunakan kode yang kami sediakan atau mengubah kode ini sesuai dengan keinginan Bapak/Ibu.</strong>
		</div>
		<div class="card">
			<div class="card-header">Form Penyebaran</div>
			<div class="card-body">
				<?php
				if ($this->session->flashdata('error')) {
				?>
					<div class="alert alert-danger" role="alert">
						<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
						<strong>Gagal!</strong><?php echo $this->session->flashdata('error'); ?>
					</div>
				<?php
				}
				?>
				<form action="<?php echo base_url('sosiometri/codeSave'); ?>" method="post">
					<?php
					if ($codeSettled) {
					?>
						<input type="hidden" name="id" value="<?php echo $codeSettled[0]['id']; ?>">
					<?php
					}
					?>
					<div class="form-group">
						<label for="" class="col-lg-12 control-label">Judul</label>
						<div class="col-md-12">
							<input type="text" class="form-control" name="judul" value="<?php echo $params['judul']; ?>" required>
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-lg-12 control-label">Jumlah Pertanyaan</label>
						<div class="col-md-12">
							<?php
							if ($config) {
								echo '<select name="jumlah_pertanyaan" id="jumlah_pertanyaan" class="form-control" required>';
								for ($i = 1; $i <= $config[0]['jumlah_pertanyaan']; $i++) {
									if ($params['jumlah_pertanyaan'] != 0) {
										$selected = $params['jumlah_pertanyaan'] == $i ? 'selected' : '';
									} else {
										$selected = $i == 1 ? 'selected' : '';
									}

									echo '<option ' . $selected . ' value="' . $i . '">' . $i . '</option>';
								}
								echo '</select>';
							}
							?>
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-lg-12 control-label">Pertanyaan</label>
						<div class="col-md-12" id="questionGroup">
							<!-- <small class="w-100">Pertanyaan ke-1</small> -->
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-lg-12 control-label">Jumlah Pilihan</label>
						<div class="col-md-12">
							<input type="text" class="form-control" name="jumlah_pilihan" value="<?php echo $params['jumlah_pilihan']; ?>" required>
						</div>
					</div>
					<div class="form-group d-none" id="bobot-nilai-wrap">
						<label for="" class="col-lg-12 control-label">Bobot Penilaian</label>
						<div class="bobot-nilai-input">
							<?php
							if ($params['bobot_penilaian']) {
								foreach ($params['bobot_penilaian'] as $bobot) {
							?>
									<div class="col-md-12 mb-3">
										<input value="<?php echo $bobot; ?>" type="text" class="form-control" name="bobot_penilaian[]" required>
									</div>
							<?php
								}
							}
							?>
						</div>
					</div>
					<div class="form-group">
						<label class="col-lg-12 control-label">URL</label>
						<div class="col-md-12">
							<div class="input-group mb-3">
								<div class="input-group-prepend">
									<span class="input-group-text" id="basic-addon3">https://instrumentasibk.com/sosiometri/siswa/</span>
								</div>
								<input value="<?php echo $codeSettled ? $params['url'] : random_string('alnum', 5); ?>" type="text" class="form-control" name="url" id="basic-url" aria-describedby="basic-addon3" placeholder="Masukkan kode singkat anda disini">
							</div>
						</div>
					</div>
					<div class="form-group d-none" id="button-wrap">
						<div class="col-lg-offset-2 col-lg-10">
							<button type="submit" class="btn btn-success">Simpan</button>
							<button type="button" class="btn btn-default" onclick="history.back()">Batal</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<script>
	$(document).ready(function() {
		let arrQuestion = [];
		let questionChosen = [];
		var bobot_penilaian = <?php echo $params['bobot_penilaian'] ? 'true' : 'false'; ?>;
		console.log('Bobot', bobot_penilaian)

		if (bobot_penilaian) {
			$('#bobot-nilai-wrap').removeClass('d-none')
			$('#button-wrap').removeClass('d-none')
		} else {
			$('#bobot-nilai-wrap').addClass('d-none')
			$('#button-wrap').addClass('d-none')
		}

		$('input[name="jumlah_pilihan"]').on('keyup', function() {
			var nums = $(this).val()

			// Reset bobot-nilai-wrap
			$('.bobot-nilai-input').html('')

			if (nums != '') {
				$('#bobot-nilai-wrap').removeClass('d-none')
				$('#button-wrap').removeClass('d-none')
			} else {
				$('#bobot-nilai-wrap').addClass('d-none')
				$('#button-wrap').addClass('d-none')
			}

			for (let i = 0; i < nums; i++) {
				$('.bobot-nilai-input').append('\
					<div class="col-md-12 mb-3">\
						<input type="text" class="form-control" name="bobot_penilaian[]" required>\
					</div>\
				')
			}
		})

		$("#jumlah_pertanyaan").on('change', function() {
			var nums = $(this).val();

			setQuestion(nums)
		})

		function setQuestion(amount) {
			$.ajax({
				type: 'GET',
				data: {
					id_sosiometri: "<?php echo $codeSettled ? $codeSettled[0]['id'] : ''; ?>"
				},
				url: "<?php echo base_url('Sosiometri/getQuestions'); ?>",
				cache: false,
				success: function(r) {
					console.log('Set Question response', r)
					if (r.success) {
						let el = '';
						let counter = 0;
						for (let i = 1; i <= amount; i++) {
							el += '<select name="id_pertanyaan[]" id="pertanyaan' + counter + '" class="form-control mt-2" required><option value="">-- Pilih Pertanyaan --</option>';

							let selected = '';
							$.each(r.data, function(index, v) {

								if (r.sosiometri.length > 0) {
									selected = v.id == r.sosiometri[0].id_pertanyaan[counter] ? 'selected' : '';
								} else {
									selected = '';
								}

								el += '<option ' + selected + ' value=' + v.id + '>' + v.pertanyaan + '</option>'
							})

							el += '</select>';
							counter++;
						}

						$("#questionGroup").html(el);

						// Reset
						doReset()

						$('select[name="id_pertanyaan[]"]').each(function() {
							arrQuestion.push($(this).attr('id'))
						})

						console.log('Questions ID', arrQuestion)

						$.each(arrQuestion, function(i, v) {
							selectOnChange(v)

							return false;
						})
					}
				},
				error: function(jqXHR, textStatus, errorThrown) {
					console.error(jqXHR, textStatus, errorThrown)
				}
			})
		}

		function selectOnChange(selector) {
			$("#" + selector).on("change", function() {
				let val = $(this).val()
				let selectorChange = arrQuestion.indexOf(selector) + 1

				if (questionChosen[selectorChange] !== undefined) {
					questionChosen[selectorChange] = val
				} else {
					questionChosen.push(val)
				}

				console.log('Question chosen', questionChosen)

				getQuestionNotIn(arrQuestion[selectorChange])
				selectOnChange(arrQuestion[selectorChange])
			})
		}

		function getQuestionNotIn(selector) {
			$.ajax({
				type: 'GET',
				data: {
					id: questionChosen,
					id_sosiometri: "<?php echo $codeSettled ? $codeSettled[0]['id'] : ''; ?>"
				},
				url: "<?php echo base_url('Sosiometri/getQuestionNotIn'); ?>",
				cache: false,
				success: function(r) {

					if (r.success) {
						console.log('Current selector', selector)
						console.log('Question not in response', r)
						let opt = '<option value="">-- Pilih Pertanyaan --</option>'

						$.each(r.data, function(i, v) {
							opt += '<option value="' + v.id + '">' + v.pertanyaan + '</option>'
						})

						$("#" + selector).html(opt)
					}
				},
				error: function(jqXHR, textStatus, errorThrown) {
					console.error(jqXHR, textStatus, errorThrown)
				}
			})
		}

		function doReset() {
			arrQuestion = [];
			questionChosen = [];
		}

		$("#jumlah_pertanyaan").change();
	})
</script>