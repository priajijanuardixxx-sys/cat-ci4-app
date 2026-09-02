<?= $this->extend('layout/admin_layout') ?>
<?= $this->section('content') ?>

<div class="container-fluid">
  <h4 class="mb-3">Pengaturan Ujian</h4>

  <div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
      <span><i class="bi bi-gear"></i> Daftar Jenis Ujian</span>
      <button class="btn btn-primary btn-sm" id="btnAddExamType">
        <i class="bi bi-plus-circle"></i> Tambah Jenis Ujian
      </button>
    </div>
    <div class="card-body">
      <div id="examTypeTable"></div>
    </div>
  </div>
</div>

<!-- ===========================
     Modal Tambah/Edit Jenis Ujian
     =========================== -->
     <div class="modal fade" id="examTypeModal" tabindex="-1" aria-labelledby="examTypeModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <form id="formExamType" class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="examTypeModalLabel">Tambah Jenis Ujian</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="id" id="exam-id">

            <div class="mb-3">
              <label for="name" class="form-label">Nama Jenis Ujian</label>
              <input type="text" class="form-control" name="name" id="exam-name" required>
            </div>

            <div class="mb-3">
              <label for="description" class="form-label">Deskripsi</label>
              <textarea class="form-control" name="description" id="exam-description" rows="2"></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-success">Simpan</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          </div>
        </form>
      </div>
    </div>

<!-- ===========================
     Modal Pengaturan Detail
     =========================== -->
     <div class="modal fade" id="examSettingModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Pengaturan Ujian</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body" id="examSettingContent">
            <div class="text-center py-5">
              <div class="spinner-border text-primary" role="status"></div>
            </div>
          </div>
        </div>
      </div>
    </div>


    <!-- Modal Daftar Peserta -->
    <div class="modal fade" id="participantsModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Daftar Peserta</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body" id="participantsContent">
            <div class="text-center py-5">
              <div class="spinner-border text-primary" role="status"></div>
            </div>
          </div>
        </div>
      </div>
    </div>


    <?= $this->endSection() ?>
    <?= $this->section('scripts') ?>
    <script>
      $(document).ready(function() {

  // =============================
  // 1️⃣ Load daftar jenis ujian
  // =============================
  function loadExamTypes() {
    $.get('<?= base_url('admin/exams/load') ?>', function(res) {
      $('#examTypeTable').html(res);
    });
  }
  loadExamTypes();


  // =============================
  // 2️⃣ Tambah/Edit Jenis Ujian (exam_types)
  // =============================
  $('#btnAddExamType').on('click', function() {
    $('#formExamType')[0].reset();
    $('#exam-id').val('');
    $('#examTypeModalLabel').text('Tambah Jenis Ujian');
    $('#examTypeModal').modal('show');
  });

  $(document).on('click', '.btn-edit', function() {
    $('#exam-id').val($(this).data('type-id'));
    $('#exam-name').val($(this).data('name'));
    $('#exam-description').val($(this).data('description'));
    $('#examTypeModalLabel').text('Edit Jenis Ujian');
    $('#examTypeModal').modal('show');
  });

  $(document).on('click', '.btn-participants', function() {
    const typeId = $(this).data('type-id');

    $('#participantsModal').modal('show');
    $('#participantsContent').html('<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>');

    $.get(`<?= base_url('admin/peserta/exam') ?>/${typeId}`, function(res) {
      if(res.status === 'success') {
        $('#participantsContent').html(res.html);
      } else {
        $('#participantsContent').html('<div class="text-danger text-center">' + res.message + '</div>');
      }
    }).fail(function() {
      $('#participantsContent').html('<div class="text-danger text-center">Gagal memuat peserta.</div>');
    });
  });


  $('#formExamType').on('submit', function(e) {
    e.preventDefault();
    $.ajax({
      url: '<?= base_url('admin/exams/save') ?>',
      method: 'POST',
      data: $(this).serialize(),
      dataType: 'json',
      success: function(res) {
        if (res.status === 'success') {
          Swal.fire('Berhasil', res.message, 'success');
          $('#examTypeModal').modal('hide');
          loadExamTypes();
        } else {
          Swal.fire('Error', res.message, 'error');
        }
      },
      error: function(xhr) {
        console.error(xhr.responseText);
        Swal.fire('Gagal', 'Terjadi kesalahan server.', 'error');
      }
    });
  });


  // =============================
  // 3️⃣ Hapus Jenis Ujian (exam_types)
  // =============================
  $(document).on('click', '.btn-delete', function() {
    const typeId = $(this).data('type-id');
    Swal.fire({
      title: 'Yakin ingin hapus?',
      text: 'Data tidak bisa dikembalikan.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Ya, hapus!'
    }).then((result) => {
      if (result.isConfirmed) {
        $.get(`<?= base_url('admin/exams/delete') ?>/${typeId}`, function(res) {
          if (res.status === 'success') {
            Swal.fire('Terhapus', res.message, 'success');
            loadExamTypes();
          } else {
            Swal.fire('Gagal', res.message, 'error');
          }
        });
      }
    });
  });
  
  $(document).on('click', '.btnReset', function() {
    const settingId = $(this).data('setting-id');

    Swal.fire({
      title: 'Reset ujian?',
      text: 'Waktu ujian akan diulang dari awal.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Ya, reset!'
    }).then((result) => {
      if (result.isConfirmed) {
        $.post(`<?= base_url('admin/exams-session/reset') ?>/${settingId}`, { 
          <?= csrf_token() ?>: '<?= csrf_hash() ?>'
        }, function(res) {
          if (res.status === 'success') {
            Swal.fire('Direset!', res.message, 'success');
            updateExamStatus(settingId);
          } else {
            Swal.fire('Gagal!', res.message, 'error');
          }
        }, 'json');
      }
    });
  });



  // =============================
  // 4️⃣ Modal Setting Ujian (exam_settings)
  // =============================
  $(document).on('click', '.btn-setting', function() {
    const typeId = $(this).data('type-id'); // ambil dari exam_types
    $('#examSettingModal').modal('show');
    $('#examSettingContent').html('<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>');
    $.get(`<?= base_url('admin/exams/setting') ?>/${typeId}`, function(res) {
      $('#examSettingContent').html(res);
    });
  });
  function updateExamStatus(settingId) {
    $.get(`<?= base_url('admin/exams-session/status') ?>/${settingId}`, function(res) {
      if (res.status === 'success') {
        const statusCell = $(`#status-${settingId}`);
        const badgeMap = {
          'scheduled': '<span class="badge bg-secondary">Belum Mulai</span>',
          'started': '<span class="badge bg-success">Berlangsung</span>',
          'paused': '<span class="badge bg-warning text-dark">Dijeda</span>',
          'finished': '<span class="badge bg-dark">Selesai</span>'
        };

        let htmlStatus = badgeMap[res.exam_status] || badgeMap['scheduled'];

      // Tambahkan sisa waktu jika started atau paused
      if (['started', 'paused'].includes(res.exam_status) && res.remaining_seconds != null) {
        const h = Math.floor(res.remaining_seconds / 3600);
        const m = Math.floor((res.remaining_seconds % 3600) / 60);
        const s = res.remaining_seconds % 60;
        htmlStatus += `<div class="small text-muted mt-1">Sisa waktu: ${h}j ${m}m ${s}s</div>`;
      }

      statusCell.html(htmlStatus);

      // tombol aksi
      renderActionButtons(settingId, res.exam_status, res.exam_type_id);

      if (res.exam_status === 'started' && res.remaining_seconds > 0) {
        startCountdown(settingId, res.remaining_seconds);
      }
    }
  });
  }

  function renderActionButtons(settingId, status, examTypeId) {
    let html = '';
    if (status === 'scheduled') {
      html += `<button class="btn btn-success btn-sm btnStart" data-setting-id="${settingId}"><i class="bi bi-play-circle"></i> </button>`;
    } else if (status === 'started') {
      html += `<button class="btn btn-warning btn-sm btnPause" data-setting-id="${settingId}"><i class="bi bi-pause-circle"></i> </button>`;
    } else if (status === 'paused') {
      html += `<button class="btn btn-info btn-sm btnResume" data-setting-id="${settingId}"><i class="bi bi-play-btn"></i> </button>`;

      html += ` <button class="btn btn-danger btn-sm btnReset" data-setting-id="${settingId}">
      <i class="bi bi-arrow-clockwise"></i> 
      </button>`;
    }
     // Tombol setting tetap pakai settingId
    html += `<button class="btn btn-primary btn-sm btn-setting" data-type-id="${examTypeId}"><i class="bi bi-gear"></i></button>`;

    // Tombol peserta pakai examTypeId
    html += `<button class="btn btn-info btn-sm btn-participants" data-type-id="${examTypeId}"><i class="bi bi-people"></i></button>`;

    // Tombol delete pakai examTypeId
    html += `<button class="btn btn-danger btn-sm btn-delete" data-type-id="${examTypeId}"><i class="bi bi-trash"></i></button>`;

    $(`#status-${settingId}`).closest('tr').find('td:last').html(html);
  }

// Tombol Start / Pause / Resume
$(document).on('click', '.btnStart, .btnPause, .btnResume', function() {
  const settingId = $(this).data('setting-id');
  const action = $(this).hasClass('btnStart') ? 'start' : $(this).hasClass('btnPause') ? 'pause' : 'resume';
  $.get(`<?= base_url('admin/exams-session') ?>/${action}/${settingId}`, function(res) {
    if (res.status === 'success') {
      Swal.fire('Berhasil', res.message, 'success');
      updateExamStatus(settingId);
    } else {
      Swal.fire('Gagal', res.message, 'error');
    }
  });
});



  // =============================
  // 7️⃣ Fungsi Countdown Real-Time
  // =============================
  function startCountdown(settingId, seconds) {
    const countdownEl = $(`#status-${settingId} .countdown`);

    if (countdownEl.data('intervalId')) {
      clearInterval(countdownEl.data('intervalId'));
    }

    const timer = setInterval(() => {
      if (seconds <= 0) {
        clearInterval(timer);
        countdownEl.text('Waktu habis');
        updateExamStatus(settingId);
      } else {
        seconds--;
        const h = Math.floor(seconds / 3600);
        const m = Math.floor((seconds % 3600) / 60);
        const s = seconds % 60;
        countdownEl.text(`${h}j ${m}m ${s}d`);
      }
    }, 1000);

    countdownEl.data('intervalId', timer);
  }


  // =============================
  // 8️⃣ Auto-refresh status setiap 5 detik
  // =============================
  setInterval(function() {
    $('[id^="status-"]').each(function() {
      const settingId = parseInt($(this).attr('id').split('-')[1]);
    // 🔒 hanya update jika setting_id valid (> 0)
    if (settingId > 0) {
      updateExamStatus(settingId);
    }
  });
  }, 5000);


});
</script>
<?= $this->endSection() ?>
