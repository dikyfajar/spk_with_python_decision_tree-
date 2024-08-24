<div class="page-inner">
    <!-- Alert for successful login -->
    <div class="alert alert-success" id="login-success" style="display: none;">
        <strong>Success!</strong> Anda berhasil login.
    </div>
    <div class="page-header">
        <h1 class="page-title">Selamat Datang,<br />di SISTEM PENDUKUNG KEPUTUSAN<br />PEMILIHAN SEKOLAH MENENGAH ATAS<br />PONDOK PESANTREN BAHRUL ULUM</h1>
    </div>
    <div class="page-category">Untuk memenuhi Tugas Akhir Universitas KH. A. Wahab Hasbullah Tambakberas Jombang<br />Muhammad Diky Fajar Romadloni<br />Sistem Informasi B</div>
</div>

<script>
    $(document).ready(function() {
        $("#mnhome").addClass("active");

        // Simulate successful login (replace with actual login success condition)
        let loginSuccess = true;

        if (loginSuccess) {
            $("#login-success").show();
        }
    });
</script>