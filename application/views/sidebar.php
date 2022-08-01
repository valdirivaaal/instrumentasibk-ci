  <aside>
    <div id="sidebar"  class="nav-collapse ">
      <!-- sidebar menu start-->
      <ul class="sidebar-menu" id="nav-accordion">
        <li>
          <a class="<?= (getController()=='dashboard') ? 'active' : '' ?>" href="<?= base_url('dashboard') ?>">
            <i class="fa fa-dashboard"></i>
            <span>Dashboard</span>
          </a>
        </li>

        <li>
          <a class="<?= (getController()=='profil') ? 'active' : '' ?>" href="<?= base_url('profil') ?>">
            <i class="fa fa-user"></i>
            <span>Profil</span>
          </a>
        </li>

        <?php
        if (getField('user_info','status',array('user_id'=>$this->session->userdata('id')))=='Guru BK') {
          ?>
          <li>
            <a class="<?= (getController()=='konselor') ? 'active' : '' ?>" href="<?= base_url('konselor') ?>">
              <i class="fa fa-chalkboard-teacher"></i>
              <span>Daftar Guru</span>
            </a>
          </li>
          <?php
        }
        ?>

        <li>
          <a class="<?= (getController()=='kelas') ? 'active' : '' ?>" href="<?= base_url('kelas') ?>">
            <i class="fa fa-th"></i>
            <span>Daftar <?= (getField('user_info','status',array('user_id'=>$this->session->userdata('id')))=='Guru BK') ? 'Kelas' : 'Kelompok' ?></span>
          </a>
        </li>

        <?php
        if (getField('user_info','status',array('user_id'=>$this->session->userdata('id')))=='Guru BK') {
          ?>
          <li>
            <a class="<?= (getController()=='kelompok') ? 'active' : '' ?>" href="<?= base_url('kelompok') ?>">
              <i class="fa fa-users"></i>
              <span>Daftar Kelompok</span>
            </a>
          </li>
          <?php
        }
        ?>

        <?php
        if (getField('user_info','status',array('user_id'=>$this->session->userdata('id')))=='Guru BK') {
          ?>
          <li class="sub-menu">
            <a href="javascript:;" class="<?= (getController()=='aum' || getController()=='ptsdl' || getController()=='auap' || getController()=='dcm' || getController()=='sosiometri') ? 'active' : '' ?>" >
              <i class="fa fa-laptop"></i>
              <span>Instrumen</span>
            </a>
            <ul class="sub">
              <li class="<?= (getController()=='aum') ? 'active' : '' ?>"><a href="<?= base_url('aum') ?>">AUM Umum</a></li>
              <li class="<?= (getController()=='ptsdl') ? 'active' : '' ?>"><a href="<?= base_url('ptsdl') ?>">AUM PTSDL</a></li>
              <li class="<?= (getController()=='auap') ? 'active' : '' ?>"><a href="<?= base_url('auap') ?>">AU-AP</a></li>
              <li class="<?= (getController()=='dcm') ? 'active' : '' ?>"><a href="<?= base_url('dcm') ?>">DCM</a></li>
              <li class="<?= (getController()=='sosiometri') ? 'active' : '' ?>"><a href="<?= base_url('sosiometri') ?>">Sosiometri</a></li>
            </ul>
          </li>
          <?php
        } else {
          ?>
          <li class="sub-menu">
            <a href="javascript:;" class="<?= (getController()=='aum') ? 'active' : '' ?>" >
              <i class="fa fa-archive"></i>
              <span>AUM Umum</span>
            </a>
            <ul class="sub">
              <li class="<?= (isset($jenjang) && $jenjang=='SD') ? 'active' : '' ?>"><a href="<?= base_url('aum/index/SD') ?>">SD</a></li>
              <li class="<?= (isset($jenjang) && $jenjang=='SMP') ? 'active' : '' ?>"><a href="<?= base_url('aum/index/SMP') ?>">SMP</a></li>
              <li class="<?= (isset($jenjang) && $jenjang=='SMA') ? 'active' : '' ?>"><a href="<?= base_url('aum/index/SMA') ?>">SMA</a></li>
              <li class="<?= (isset($jenjang) && $jenjang=='PT') ? 'active' : '' ?>"><a href="<?= base_url('aum/index/PT') ?>">PT</a><li>
                <li class="<?= (isset($jenjang) && $jenjang=='Umum') ? 'active' : '' ?>"><a href="<?= base_url('aum/index/Umum') ?>">Masyarakat</a></li>
              </ul>
            </li>
            <li class="sub-menu">
              <a href="javascript:;" class="<?= (getController()=='ptsdl') ? 'active' : '' ?>" >
                <i class="fa fa-book"></i>
                <span>AUM PTSDL</span>
              </a>
              <ul class="sub">
                <li class="<?= (isset($jenjang) && $jenjang=='SD') ? 'active' : '' ?>"><a href="<?= base_url('ptsdl/index/SD') ?>">SD</a></li>
                <li class="<?= (isset($jenjang) && $jenjang=='SMP') ? 'active' : '' ?>"><a href="<?= base_url('ptsdl/index/SMP') ?>">SMP</a></li>
                <li class="<?= (isset($jenjang) && $jenjang=='SMA') ? 'active' : '' ?>"><a href="<?= base_url('ptsdl/index/SMA') ?>">SMA</a></li>
                <li class="<?= (isset($jenjang) && $jenjang=='PT') ? 'active' : '' ?>"><a href="<?= base_url('ptsdl/index/PT') ?>">PT</a><li>
                </ul>
              </li>
              <li class="sub-menu">
                <a href="javascript:;" class="<?= (getController()=='auap') ? 'active' : '' ?>" >
                  <i class="fa fa-briefcase"></i>
                  <span>AUAP</span>
                </a>
                <ul class="sub">
                  <li class="<?= (isset($jenjang) && $jenjang=='SMP') ? 'active' : '' ?>"><a href="<?= base_url('auap/index/SMP') ?>">SMP</a></li>
                  <li class="<?= (isset($jenjang) && $jenjang=='SMA') ? 'active' : '' ?>"><a href="<?= base_url('auap/index/SMA') ?>">SMA</a></li>
                </ul>
              </li>
               <li class="sub-menu">
                <a href="javascript:;" class="<?= (getController()=='dcm') ? 'active' : '' ?>" >
                  <i class="fa fa-briefcase"></i>
                  <span>DCM</span>
                </a>
                <ul class="sub">
                  <li class="<?= (isset($jenjang) && $jenjang=='SMP') ? 'active' : '' ?>"><a href="<?= base_url('dcm/index/SMP') ?>">SMP</a></li>
                  <li class="<?= (isset($jenjang) && $jenjang=='SMA') ? 'active' : '' ?>"><a href="<?= base_url('dcm/index/SMA') ?>">SMA</a></li>
                </ul>
              </li>
              <?php
            }
            ?>
          </ul>
          <!-- sidebar menu end-->
        </div>
      </aside>
