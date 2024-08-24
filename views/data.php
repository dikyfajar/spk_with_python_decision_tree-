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
                <div class="card-title">Decision Tree Plot</div>
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
                        <!-- Prediction inputs will be dynamically added here -->
                    </div>
                    <button type="submit" class="btn btn-primary">Predict</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $("#mndata").addClass("active");

    $(document).ready(function() {
        let columnCount = 0; // Initialize column count
        $('#uploadForm').on('submit', function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            Swal.showLoading();
            $.ajax({
                url: 'upload.php',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    let data = JSON.parse(response);
                    let output = JSON.parse(data.output);
                    if (data.status == 'success') {
                        // Swal.hideLoading();
                        Swal.fire({
                            title: 'BERHASIL',
                            text: 'Data Berhasil Diunggah!',
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
                                html += '<th colspan=7>' + col + '</th>';
                            } else {
                                html += '<th rowspan=2>' + col + '</th>';
                            }
                        });
                        html += '</tr><tr><td>Riwayat Sebelum SMA/MA</td><td>Sekolah Asal</td><td>Status</td><td>Jarak Tempuh</td><td>Rata-Rata Nilai</td><td>Alasan Masuk Ponpes</td><td>Beasiswa</td></tr></thead><tbody>';

                        data.trainData.forEach(function(row) {
                            html += '<tr>';
                            row.forEach(function(item, i) {
                                if (i != 7 || i != 8) {
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
                                html += '<th colspan=7>' + col + '</th>';
                            } else {
                                html += '<th rowspan=2>' + col + '</th>';
                            }
                        });
                        html += '</tr><tr><td>Riwayat Sebelum SMA/MA</td><td>Sekolah Asal</td><td>Status</td><td>Jarak Tempuh</td><td>Rata-Rata Nilai</td><td>Alasan Masuk Ponpes</td><td>Beasiswa</td></tr></thead><tbody>';

                        data.testData.forEach(function(row) {
                            html += '<tr>';
                            row.forEach(function(item, i) {
                                if (i != 7 || i != 8) {
                                    html += '<td>' + row[i] + '</td>'; // Actual Label
                                } else {
                                    html += '<td colspan=2>' + row[i] + '</td>'; // Actual Label
                                }
                            });
                            html += '</tr>';
                        });

                        html += '</tbody></table></div>';
                        html += '<br />';

                        html += '<h3><strong>Train Accuracy:</strong> ' + (output[0] * 100).toFixed(2) + '%</h3>';
                        html += '<h3><strong>Test Accuracy:</strong> ' + (output[1] * 100).toFixed(2) + '%</h3>';

                        $('#viewData').html(html);
                        $('#trainDataTable').DataTable();
                        $('#testDataTable').DataTable();

                        // Display the decision tree plot
                        $('#plotImage').attr('src', data.plotFile).show();


                        // Set column count and create prediction inputs
                        columnCount = data.samples[0].length;
                        let predictInputs = $('#predictInputs');
                        predictInputs.empty();

                        // Add dropdown inputs for predictions
                        predictInputs.append(`
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
                                    <option value="SMP">SMP</option>
                                    <option value="MTSN">MTSN</option>
                                    <option value="SMPN">SMPN</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="input3">Status:</label>
                                <select class="form-control" id="input3" name="input3" required>
                                    <option selected>PILIH...</option>
                                    <option value="SWASTA">SWASTA</option>
                                    <option value="NEGERI">NEGERI</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="input4">Jarak Tempuh:</label>
                                <select class="form-control" id="input4" name="input4" required>
                                    <option selected>PILIH...</option>
                                    <option value="DEKAT">DEKAT</option>
                                    <option value="SEDANG">SEDANG</option>
                                    <option value="JAUH">JAUH</option>
                                    <option value="SANGAT JAUH">SANGAT JAUH</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="input5">Rata-Rata Nilai:</label>
                                <select class="form-control" id="input5" name="input5" required>
                                    <option selected>PILIH...</option>
                                    <option value="A">A</option>
                                    <option value="B">B</option>
                                    <option value="C">C</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="input6">Alasan Masuk Ponpes:</label>
                                <select class="form-control" id="input6" name="input6" required>
                                    <option selected>PILIH...</option>
                                    <option value="KEINGINAN PRIBADI">KEINGINAN PRIBADI</option>
                                    <option value="KEINGINAN ORANG TUA">KEINGINAN ORANG TUA</option>
                                    <option value="IKUT TEMAN">IKUT TEMAN</option>
                                    <option value="REKOMENDASI PENGASUH">REKOMENDASI PENGASUH</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="input7">Beasiswa:</label>
                                <select class="form-control" id="input7" name="input7" required>
                                    <option selected>PILIH...</option>
                                    <option value="YA">YA</option>
                                    <option value="TIDAK">TIDAK</option>
                                </select>
                            </div>
                        `);
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
            for (let i = 1; i <= columnCount; i++) {
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

        // $('#uploadForm').on('submit', function(e) {
        //     e.preventDefault();
        //     var formData = new FormData(this);
        //     Swal.showLoading();
        //     $.ajax({
        //         url: 'upload.php',
        //         type: 'POST',
        //         data: formData,
        //         contentType: false,
        //         processData: false,
        //         success: function(response) {
        //             let data = JSON.parse(response);
        //             let output = JSON.parse(data.output);
        //             if (data.status == 'success') {
        //                 // Swal.hideLoading();
        //                 Swal.fire({
        //                     title: 'BERHASIL',
        //                     text: 'Data Berhasil Diunggah!',
        //                     icon: 'success'
        //                 });
        //                 let html = '<div class="alert alert-success">' + data.message + '</div>';
        //                 html += '<br/>'

        //                 // Training Data Table
        //                 html += '<h3>Training Data</h3>';
        //                 html += '<div class="table-responsive">';
        //                 html += '<table id="trainDataTable" class="table table-bordered"><thead><tr>';
        //                 let columns = ['Sample', 'Actual Label', 'Predicted Label'];
        //                 columns.forEach(function(col, index) {
        //                     if (index == 0) {
        //                         html += '<th colspan=7>' + col + '</th>';
        //                     } else {
        //                         html += '<th rowspan=2>' + col + '</th>';
        //                     }
        //                 });
        //                 html += '</tr><tr><td>Riwayat Sebelum SMA/MA</td><td>Sekolah Asal</td><td>Status</td><td>Jarak Tempuh</td><td>Rata-Rata Nilai</td><td>Alasan Masuk Ponpes</td><td>Beasiswa</td></tr></thead><tbody>';

        //                 data.trainData.forEach(function(row) {
        //                     html += '<tr>';
        //                     row.forEach(function(item, i) {
        //                         if (i != 7 || i != 8) {
        //                             html += '<td>' + row[i] + '</td>'; // Actual Label
        //                         } else {
        //                             html += '<td colspan=2>' + row[i] + '</td>'; // Actual Label
        //                         }
        //                     });
        //                     html += '</tr>';
        //                 });

        //                 html += '</tbody></table></div>';
        //                 html += '<br />';

        //                 // Testing Data Table
        //                 html += '<h3>Testing Data</h3>';
        //                 html += '<div class="table-responsive">';
        //                 html += '<table id="testDataTable" class="table table-bordered"><thead><tr>';
        //                 columns.forEach(function(col, index) {
        //                     if (index == 0) {
        //                         html += '<th colspan=7>' + col + '</th>';
        //                     } else {
        //                         html += '<th rowspan=2>' + col + '</th>';
        //                     }
        //                 });
        //                 html += '</tr><tr><td>Riwayat Sebelum SMA/MA</td><td>Sekolah Asal</td><td>Status</td><td>Jarak Tempuh</td><td>Rata-Rata Nilai</td><td>Alasan Masuk Ponpes</td><td>Beasiswa</td></tr></thead><tbody>';

        //                 data.testData.forEach(function(row) {
        //                     html += '<tr>';
        //                     row.forEach(function(item, i) {
        //                         if (i != 7 || i != 8) {
        //                             html += '<td>' + row[i] + '</td>'; // Actual Label
        //                         } else {
        //                             html += '<td colspan=2>' + row[i] + '</td>'; // Actual Label
        //                         }
        //                     });
        //                     html += '</tr>';
        //                 });

        //                 html += '</tbody></table></div>';
        //                 html += '<br />';

        //                 html += '<h3><strong>Train Accuracy:</strong> ' + (output[0] * 100).toFixed(2) + '%</h3>';
        //                 html += '<h3><strong>Test Accuracy:</strong> ' + (output[1] * 100).toFixed(2) + '%</h3>';

        //                 $('#viewData').html(html);
        //                 $('#trainDataTable').DataTable();
        //                 $('#testDataTable').DataTable();

        //                 // Display the decision tree plot
        //                 $('#plotImage').attr('src', data.plotFile).show();


        //                 // Set column count and create prediction inputs
        //                 columnCount = data.samples[0].length;
        //                 let predictInputs = $('#predictInputs');
        //                 predictInputs.empty();

        //                 // Add dropdown inputs for predictions
        //                 predictInputs.append(`
        //                     <div class="form-group">
        //                         <label for="input0">Riwayat Sebelum SMA/MA:</label>
        //                         <select class="form-control" id="input0" name="input0" required>
        //                             <option selected>PILIH...</option>
        //                             <option value="BERSAMA ORANG TUA">BERSAMA ORANG TUA</option>
        //                             <option value="DI PESANTREN">DI PESANTREN</option>
        //                         </select>
        //                     </div>
        //                     <div class="form-group">
        //                         <label for="input1">Sekolah Asal:</label>
        //                         <select class="form-control" id="input1" name="input1" required>
        //                             <option selected>PILIH...</option>
        //                             <option value="MTS">MTS</option>
        //                             <option value="MTSN">MTSN</option>
        //                             <option value="SMP">SMP</option>
        //                             <option value="SMPN">SMPN</option>
        //                         </select>
        //                     </div>
        //                     <div class="form-group">
        //                         <label for="input2">Status:</label>
        //                         <select class="form-control" id="input2" name="input2" required>
        //                             <option selected>PILIH...</option>
        //                             <option value="NEGERI">NEGERI</option>
        //                             <option value="SWASTA">SWASTA</option>
        //                         </select>
        //                     </div>
        //                     <div class="form-group">
        //                         <label for="input3">Jarak Tempuh:</label>
        //                         <select class="form-control" id="input3" name="input3" required>
        //                             <option selected>PILIH...</option>
        //                             <option value="DEKAT">DEKAT</option>
        //                             <option value="SEDANG">SEDANG</option>
        //                             <option value="JAUH">JAUH</option>
        //                             <option value="SANGAT JAUH">SANGAT JAUH</option>
        //                         </select>
        //                     </div>
        //                     <div class="form-group">
        //                         <label for="input4">Rata-Rata Nilai:</label>
        //                         <select class="form-control" id="input4" name="input4" required>
        //                             <option selected>PILIH...</option>
        //                             <option value="A">A</option>
        //                             <option value="B">B</option>
        //                             <option value="C">C</option>
        //                         </select>
        //                     </div>
        //                     <div class="form-group">
        //                         <label for="input5">Alasan Masuk Ponpes:</label>
        //                         <select class="form-control" id="input5" name="input5" required>
        //                             <option selected>PILIH...</option>
        //                             <option value="KEINGINAN PRIBADI">KEINGINAN PRIBADI</option>
        //                             <option value="KEINGINAN ORANG TUA">KEINGINAN ORANG TUA</option>
        //                             <option value="IKUT TEMAN">IKUT TEMAN</option>
        //                             <option value="REKOMENDASI PENGASUH">REKOMENDASI PENGASUH</option>
        //                         </select>
        //                     </div>
        //                     <div class="form-group">
        //                         <label for="input6">Beasiswa:</label>
        //                         <select class="form-control" id="input6" name="input6" required>
        //                             <option selected>PILIH...</option>
        //                             <option value="YA">YA</option>
        //                             <option value="TIDAK">TIDAK</option>
        //                         </select>
        //                     </div>
        //                 `);
        //             } else {
        //                 $('#viewData').html('<div class="alert alert-danger">' + data.message + '</div><pre>' + data.output + '</pre>');
        //             }
        //         },
        //         error: function() {
        //             $('#viewData').html('<div class="alert alert-danger">There was an error processing your request.</div>');
        //         }
        //     });
        // });

        // // Handle prediction form submission
        // $('#predictForm').on('submit', function(e) {
        //     e.preventDefault();
        //     let sample = [];
        //     for (let i = 0; i < columnCount; i++) {
        //         sample.push($('#input' + i).val());
        //     }

        //     $.ajax({
        //         url: 'predict.php',
        //         type: 'POST',
        //         data: {
        //             sample: sample
        //             // input1: sample[0],
        //             // input2: sample[1],
        //             // input3: sample[2],
        //             // input4: sample[3],
        //             // input5: sample[4],
        //             // input6: sample[5],
        //             // input7: sample[6],
        //         },
        //         success: function(response) {
        //             let data = JSON.parse(response);
        //             if (data.prediction) {
        //                 Swal.fire('Hasil Prediksi: ' + data.prediction);
        //             } else {
        //                 Swal.fire('Error: ' + data.message);
        //             }
        //         },
        //         error: function() {
        //             Swal.fire('There was an error processing your request.');
        //         }
        //     });
        // });
    });
</script>