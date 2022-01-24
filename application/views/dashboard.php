 <div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        Dashboard Instrumentasi BK
      </div>
      <div class="card-body">
        <img class="border img-thumbnail rounded mx-auto d-block mb-3" src="<?= base_url('assets/member/') ?>img/prayitno.jpg" width="300">
        <h4 class="text-center font-weight-bold">Prof. Dr. Prayitno, M.Sc. Ed.</h4>
        <blockquote class="blockquote text-center">
          <p class="mb-0">Halo, selamat datang di Instrumentasi BK.</p>
        </blockquote>
        <hr>
        <div class="row">
          <div class="col-md-3">
            <h2 class="h4 font-weight-bold text-center">Progress Pendaftaran</h2>
            <!-- Progress bar 1 -->
            <div class="progress mx-auto mt-3" data-value='<?= $persentase ?>'>
              <span class="progress-left">
                <span class="progress-bar border-primary"></span>
              </span>
              <span class="progress-right">
                <span class="progress-bar border-primary"></span>
              </span>
              <div class="progress-value w-100 h-100 rounded-circle d-flex align-items-center justify-content-center">
                <div class="h2 font-weight-bold"><?= $skor ?>/<?= getField('user_info','status',array('user_id'=>$this->session->userdata('id')))=='Guru BK' ? '6' : '4' ?></div>
              </div>
            </div>
          </div>
          <div class="col-md-9">
            <div class="row container">
              <div class="col-md-4 mt-4">
                <div class="row no-gutters container">
                  <div class="col-lg-6">
                    <div class="icon-flex">
                     <?php
                     if (emptyElementExists($get_profil[0])==FALSE) {
                      ?>
                      <div class="icon-wrapper" style="border-color:#2ecc71 !important"><i class="fas fa-check fa-xs" style="color:#2ecc71 !important"></i></div>
                      <?php
                    } else {
                      ?>
                      <a href="<?= base_url('profil') ?>"><div class="icon-wrapper"><i class="fas fa-user fa-xs"></i></div></a>
                      <?php
                    }
                    ?>
                  </div>
                </div>
                <div class="col-lg-6">
                  <span class="align-middle">Lengkapi Profil</span>
                </div>
              </div>
            </div>
            <div class="col-md-4 mt-4">
              <div class="row no-gutters container">
                <div class="col-md-6">
                  <div class="icon-flex">
                    <?php
                    unset($get_kopsurat[0]['baris_kelima']);
                    if (@emptyElementExists($get_kopsurat[0])==FALSE) {
                      ?>
                      <div class="icon-wrapper" style="border-color:#2ecc71 !important"><i class="fas fa-check fa-xs" style="color:#2ecc71 !important"></i></div>
                      <?php
                    } else {
                      ?>
                      <a href="<?= base_url('profil/kop_surat') ?>"><div class="icon-wrapper"><i class="fas fa-envelope fa-xs"></i></div></a>
                      <?php
                    }
                    ?>
                  </div>
                </div>
                <div class="col-md-6">
                  <span>Lengkapi Kop Surat</span>
                </div>
              </div>
            </div>
            <?php
            if (getField('user_info','status',array('user_id'=>$this->session->userdata('id')))=='Guru BK'){
              ?>
              <div class="col-md-4 mt-4">
                <div class="row no-gutters container">
                  <div class="col-md-6">
                    <div class="icon-flex">
                     <?php
                     if (@emptyElementExists($get_konselor[0])==FALSE) {
                      ?>
                      <div class="icon-wrapper" style="border-color:#2ecc71 !important"><i class="fas fa-check fa-xs" style="color:#2ecc71 !important"></i></div>
                      <?php
                    } else {
                      ?>
                      <a href="<?= base_url('konselor') ?>"><div class="icon-wrapper"><i class="fas fa-chalkboard-teacher fa-xs"></i></div></a>
                      <?php
                    }
                    ?>
                  </div>
                </div>
                <div class="col-md-6">
                  <span>Isi Data Guru BK</span>
                </div>
              </div>
            </div>
            <?php
          }
          ?>
          <div class="col-md-4 mt-4">
            <div class="row no-gutters container">
              <div class="col-md-6">
                <div class="icon-flex">
                 <?php
                 if (@emptyElementExists($get_kelas[0])==FALSE) {
                  ?>
                  <div class="icon-wrapper" style="border-color:#2ecc71 !important"><i class="fas fa-check fa-xs" style="color:#2ecc71 !important"></i></div>
                  <?php
                } else {
                  ?>
                  <a href="<?= base_url('kelas') ?>"><div class="icon-wrapper"><i class="fas fa-th fa-xs"></i></div></a>
                  <?php
                }
                ?>
              </div>
            </div>
            <div class="col-md-6 pt-2">
              <span>Isi Data <?= getField('user_info','status',array('user_id'=>$this->session->userdata('id')))=='Guru BK' ? 'Kelas' : 'Kelompok' ?></span>
            </div>
          </div>
        </div>
        <?php
        if (getField('user_info','status',array('user_id'=>$this->session->userdata('id')))=='Guru BK'){
          ?>
          <div class="col-md-4 mt-4">
            <div class="row no-gutters container">
              <div class="col-md-6">
                <div class="icon-flex">
                 <?php
                 if (@emptyElementExists($get_kelompok[0])==FALSE) {
                  ?>
                  <div class="icon-wrapper" style="border-color:#2ecc71 !important"><i class="fas fa-check fa-xs" style="color:#2ecc71 !important"></i></div>
                  <?php
                } else {
                  ?>
                  <a href="<?= base_url('kelompok') ?>"><div class="icon-wrapper"><i class="fas fa-users fa-xs"></i></div></a>
                  <?php
                }
                ?>
              </div>
            </div>
            <div class="col-md-6">
              <span>Isi Data Kelompok</span>
            </div>
          </div>
        </div>
        <?php
      }
      ?>
      <div class="col-md-4 mt-4">
        <div class="row no-gutters container">
          <div class="col-md-6">
            <div class="icon-flex">
              <?php
              if (@emptyElementExists($get_ticket[0])==FALSE) {
                ?>
                <div class="icon-wrapper" style="border-color:#2ecc71 !important"><i class="fas fa-check fa-xs" style="color:#2ecc71 !important"></i></div>
                <?php
              } else {
                ?>
                <a href="<?= base_url('aum') ?>"><div class="icon-wrapper"><i class="fas fa-key fa-xs"></i></div></a>
                <?php
              }
              ?>
            </div>
          </div>
          <div class="col-md-6">
            <span>Gunakan Event Key</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</div>
</div>
</div>
</div>

<script type="text/javascript">
  $(function() {

    $(".progress").each(function() {

      var value = $(this).attr('data-value');
      var left = $(this).find('.progress-left .progress-bar');
      var right = $(this).find('.progress-right .progress-bar');

      if (value > 0) {
        if (value <= 50) {
          right.css('transform', 'rotate(' + percentageToDegrees(value) + 'deg)')
        } else {
          right.css('transform', 'rotate(180deg)')
          left.css('transform', 'rotate(' + percentageToDegrees(value - 50) + 'deg)')
        }
      }

    })

    function percentageToDegrees(percentage) {

      return percentage / 100 * 360

    }

  });

</script>