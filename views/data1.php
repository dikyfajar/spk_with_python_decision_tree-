<div class="page-inner">
    <div class="page-content">
        <div class="card">
            <div class="card-header">
                <div class="card-title">Form Upload Data</div>
            </div>
            <div class="card-body">
                <form id="uploadForm" enctype="multipart/form-data" method="post" action="upload.php">
                    <div class="form-group">
                        <label for="upload"><i>Upload Data (.csv)</i></label>
                        <div class="input-group">
                            <input type="file" class="form-control" id="upload" name="file" required>
                            <div class="input-group-prepend">
                                <button type="submit" class="btn btn-primary">Unggah</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <div class="card-title">View Data</div>
            </div>
            <div class="card-body" id="viewData">
                <!-- Data will be displayed here -->
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <div class="card-title">Gambar Pohon Keputusan</div>
            </div>
            <div class="card-body">
                <img id="plotImage" src="" alt="Decision Tree Plot" style="display: none; width: 100%;">
            </div>
        </div>
        <!-- Button to Open the Modal -->
        <button type="button" class="btn btn-primary btn-lg btn-block" data-toggle="modal" data-target="#predictModal">Buat Prediksi</button>
    </div>
</div>

<!-- Modal -->
<div id="predictModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Form Prediksi</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="predictForm">
                    <div id="predictInputs">
                        <div class="form-group">
                            <label for="input1">Riwayat Sebelum SMA/MA:</label>
                            <select class="form-control" id="input1" name="input1" required>
                                <option selected>PILIH...</option>
                                <option value="BERSAMA ORANG TUA">BERSAMA ORANG TUA</option>
                                <option value="DI PESANTREN">DI PESANTREN</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="input2">Sekolah Asal:</label>
                            <select class="form-control" id="input2" name="input2" required>
                                <option selected>PILIH...</option>
                                <option value="MTS">MTS</option>
                                <option value="MTSN">MTSN</option>
                                <option value="SMP">SMP</option>
                                <option value="SMPN">SMPN</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="input3">Status:</label>
                            <select class="form-control" id="input3" name="input3" required>
                                <option selected>PILIH...</option>
                                <option value="NEGERI">NEGERI</option>
                                <option value="SWASTA">SWASTA</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="input4">Akreditasi:</label>
                            <select class="form-control" id="input4" name="input4" required>
                                <option selected>PILIH...</option>
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="C">C</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="input5">Jarak Tempuh:</label>
                            <select class="form-control" id="input5" name="input5" required>
                                <option selected>PILIH...</option>
                                <option value="DEKAT">DEKAT</option>
                                <option value="SEDANG">SEDANG</option>
                                <option value="JAUH">JAUH</option>
                                <option value="SANGAT JAUH">SANGAT JAUH</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="input6">Rata-Rata Nilai:</label>
                            <input type="text" class="form-control" id="input6" name="input6" required>
                        </div>
                        <div class="form-group">
                            <label for="input7">Alasan Masuk Ponpes:</label>
                            <select class="form-control" id="input7" name="input7" required>
                                <option selected>PILIH...</option>
                                <option value="KEINGINAN PRIBADI">KEINGINAN PRIBADI</option>
                                <option value="KEINGINAN ORANG TUA">KEINGINAN ORANG TUA</option>
                                <option value="IKUT TEMAN">IKUT TEMAN</option>
                                <option value="REKOMENDASI PENGASUH">REKOMENDASI PENGASUH</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="input8">Beasiswa:</label>
                            <select class="form-control" id="input8" name="input8" required>
                                <option selected>PILIH...</option>
                                <option value="YA">YA</option>
                                <option value="TIDAK">TIDAK</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Prediksi</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $("#mndata").addClass("active");

    $(document).ready(function() {
        $('#uploadForm').on('submit', function(e) {
            e.preventDefault();
            var formData = new FormData(this);

            $.ajax({
                url: 'upload.php',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    let data = JSON.parse(response);

                    if (data.status == 'success') {
                        Swal.fire({
                            title: 'DATA BERHASIL DIUNGGAH',
                            icon: 'success'
                        });
                        let html = '<div class="alert alert-success">' + data.message + '</div>';
                        html += '<br/>'

                        // Training Data Table
                        html += '<h3>Training Data</h3>';
                        html += '<div class="table-responsive">';
                        html += '<table id="trainDataTable" class="table table-bordered"><thead><tr>';
                        let columns = ['Sample', 'Actual Label', 'Predicted Label'];
                        columns.forEach(function(col, index) {
                            if (index == 0) {
                                html += '<th colspan=8>' + col + '</th>';
                            } else {
                                html += '<th rowspan=2>' + col + '</th>';
                            }
                        });
                        html += '</tr><tr><td>Riwayat Sebelum SMA/MA</td><td>Sekolah Asal</td><td>Status</td><td>Akreditasi</td><td>Jarak Tempuh</td><td>Rata-Rata Nilai</td><td>Alasan Masuk Ponpes</td><td>Beasiswa</td></tr></thead><tbody>';

                        data.trainData.forEach(function(row) {
                            html += '<tr>';
                            row.forEach(function(item, i) {
                                if (i != 8 || i != 9) {
                                    html += '<td>' + row[i] + '</td>'; // Actual Label
                                } else {
                                    html += '<td colspan=2>' + row[i] + '</td>'; // Actual Label
                                }
                            });
                            html += '</tr>';
                        });

                        html += '</tbody></table></div>';
                        html += '<br />';

                        // Testing Data Table
                        html += '<h3>Testing Data</h3>';
                        html += '<div class="table-responsive">';
                        html += '<table id="testDataTable" class="table table-bordered"><thead><tr>';
                        columns.forEach(function(col, index) {
                            if (index == 0) {
                                html += '<th colspan=8>' + col + '</th>';
                            } else {
                                html += '<th rowspan=2>' + col + '</th>';
                            }
                        });
                        html += '</tr><tr><td>Riwayat Sebelum SMA/MA</td><td>Sekolah Asal</td><td>Status</td><td>Akreditasi</td><td>Jarak Tempuh</td><td>Rata-Rata Nilai</td><td>Alasan Masuk Ponpes</td><td>Beasiswa</td></tr></thead><tbody>';

                        data.testData.forEach(function(row) {
                            html += '<tr>';
                            row.forEach(function(item, i) {
                                if (i != 8 || i != 9) {
                                    html += '<td>' + row[i] + '</td>'; // Actual Label
                                } else {
                                    html += '<td colspan=2>' + row[i] + '</td>'; // Actual Label
                                }
                            });
                            html += '</tr>';
                        });

                        html += '</tbody></table></div>';
                        html += '<br />';

                        // Display accuracies
                        html += '<h3><strong>Train Accuracy:</strong> ' + (data.trainAccuracy * 100).toFixed(2) + '%</h3>';
                        html += '<h3><strong>Test Accuracy:</strong> ' + (data.testAccuracy * 100).toFixed(2) + '%</h3>';

                        $('#viewData').html(html);
                        $('#trainDataTable').DataTable();
                        $('#testDataTable').DataTable();

                        // Display the decision tree plot
                        $('#plotImage').attr('src', data.plotFile).show();
                    } else {
                        $('#viewData').html('<div class="alert alert-danger">' + data.message + '</div><pre>' + data.output + '</pre>');
                    }
                },
                error: function() {
                    $('#viewData').html('<div class="alert alert-danger">There was an error processing your request.</div>');
                }
            });
        });

        $('#predictForm').on('submit', function(e) {
            e.preventDefault();
            let sample = [];
            for (let i = 1; i <= 8; i++) {
                sample.push($('#input' + i).val());
            }

            $.ajax({
                url: 'prediksi.php',
                type: 'POST',
                data: {
                    sample: sample
                },
                success: function(response) {
                    let data = JSON.parse(response);
                    if (data.status == 'success') {
                        Swal.fire('Hasil Prediksi: ' + ' ' + data.prediction);
                        $('#predictForm')[0].reset(); // Reset the form after success
                    } else {
                        Swal.fire('Error: ' + ' ' + data.message);
                    }
                },
                error: function() {
                    Swal.fire('There was an error processing your request.');
                }
            });
        });
    });
</script>