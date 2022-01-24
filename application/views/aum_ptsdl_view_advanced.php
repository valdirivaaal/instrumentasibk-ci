<div class="row mt-5">
	<div class="col-md-12">
		<div class="panel-body bio-graph-info">
		</div>
		<div class="card">
			<div class="card-header text-center">
				<h1 class="font-weight-bold"> Alat Ungkap Masalah PTSDL</h1>
			</div>
			<div class="card-body">
				<form class="form-horizontal" role="form" action="#" method="get">
					<ul class="nav nav-tabs" id="myTab" role="tablist">
						<li class="nav-item">
							<a class="nav-link active" id="step-1" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Tahap 1</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" id="step-2" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Tahap 2</a>
						</li>
					</ul>
					<div class="tab-content" id="myTabContent">
						<div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
							<div class="col-md-12">
								<h5 class="font-weight-bold mt-3">Petunjuk Pengisian :</h5> 
								<p>1. Untuk setiap pernyataan disediakan lima kemungkinan jawaban</br>
								2. Pilihlah salah satu kemungkinan jawaban yang sesuai dengan apa yang terjadi/terdapat pada diri anda</p>
							</div>
							<hr>
							<div class="row container">
								<div class="col-lg-9 col-md-12">
									<h5>Hubungan dengan seseorang menjadi terganggu karena berita yang menyakitkan
									melalui handphone (HP) dan / atau media sosial</h5>
								</div>
								<div class="col-lg-3 text-center">
									<div id="checkboxes text-center">
										<div class="checkboxgroup">
											<label for="my_radio_button_id1">J</label>
											<input type="radio" name="radio" id="my_radio_button_id1" />
										</div>
										<div class="checkboxgroup">
											<label for="my_radio_button_id2">K</label>
											<input type="radio" name="radio" id="my_radio_button_id2" />
										</div>
										<div class="checkboxgroup">
											<label for="my_radio_button_id3">Sr</label>
											<input type="radio" name="radio" id="my_radio_button_id3" />
										</div>
										<div class="checkboxgroup">
											<label for="my_radio_button_id3">U</label>
											<input type="radio" name="radio" id="my_radio_button_id3" />
										</div>
										<div class="checkboxgroup">
											<label for="my_radio_button_id3">L</label>
											<input type="radio" name="radio" id="my_radio_button_id3" />
										</div>
									</div>
								</div>
							</div>
							<hr>
							<div class="row container">
								<div class="col-md-10">
									<h5>Hubungan dengan seseorang menjadi terganggu karena berita yang menyakitkan
									melalui handphone (HP) dan / atau media sosial</h5>
								</div>
								<div class="col-md-2 text-right">
									<select class="form-control">
										<option selected>Jarang</option>
										<option value="1">Kadang-Kadang</option>
										<option value="2">Sering</option>
										<option value="3">Pada Umumnya</option>
										<option value="3">Selalu</option>
									</select>
								</div>
							</div>
							<hr>
							<div class="form-group text-center col-lg-10 offset-1">
								<button type="submit" class="btn btn-success btnStep2">Selanjutnya</button>
							</div>
						</div>
						<div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
							<div class="col-md-12">
								<h5 class="font-weight-bold mt-3">Petunjuk Pengisian :</h5> 
								<p>1. Untuk setiap pernyataan disediakan lima kemungkinan jawaban</br>
								2. Pilihlah salah satu kemungkinan jawaban yang sesuai dengan apa yang terjadi/terdapat pada diri anda</p>
							</div>
							<hr>
							<div class="row container">
								<div class="col-lg-9 col-md-12">
									<h5>Hubungan dengan seseorang menjadi terganggu karena berita yang menyakitkan
									melalui handphone (HP) dan / atau media sosial</h5>
								</div>
								<div class="col-lg-3 text-center">
									<div id="checkboxes text-center">
										<div class="checkboxgroup">
											<label for="my_radio_button_id1">J</label>
											<input type="radio" name="radio" id="my_radio_button_id1" />
										</div>
										<div class="checkboxgroup">
											<label for="my_radio_button_id2">K</label>
											<input type="radio" name="radio" id="my_radio_button_id2" />
										</div>
										<div class="checkboxgroup">
											<label for="my_radio_button_id3">Sr</label>
											<input type="radio" name="radio" id="my_radio_button_id3" />
										</div>
										<div class="checkboxgroup">
											<label for="my_radio_button_id3">U</label>
											<input type="radio" name="radio" id="my_radio_button_id3" />
										</div>
										<div class="checkboxgroup">
											<label for="my_radio_button_id3">L</label>
											<input type="radio" name="radio" id="my_radio_button_id3" />
										</div>
									</div>
								</div>
							</div>
							<hr>
							<div class="row container">
								<div class="col-md-10">
									<h5>Hubungan dengan seseorang menjadi terganggu karena berita yang menyakitkan
									melalui handphone (HP) dan / atau media sosial</h5>
								</div>
								<div class="col-md-2 text-right">
									<select class="form-control">
										<option selected>Jarang</option>
										<option value="1">Kadang-Kadang</option>
										<option value="2">Sering</option>
										<option value="3">Pada Umumnya</option>
										<option value="3">Selalu</option>
									</select>
								</div>
							</div>
							<hr>
							<div class="form-group text-center col-lg-10 offset-1">
								<button type="submit" class="btn btn-success btnStep2">Selanjutnya</button>
							</div>
						</div>
						
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$('.btnStep2').click(function(){
		$('.nav-tabs > .nav-item > .active').parent().next('li').find('a').trigger('click');
		jQuery('html,body').animate({scrollTop:0},0);
	});

	$('.make-switch').change(function(){
		if($("#question-cerita").is(":checked")==true){
			$("#cerita").attr("placeholder", "Yuk cerita yuk");
		} else {
			$("#cerita").attr("placeholder", "Males ah");
		}
	})
</script>