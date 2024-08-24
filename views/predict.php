<div class="page-inner">
    <div class="page-content">
        <div class="card">
            <div class="card-header">
                <div class="card-title">Prediction Page</div>
            </div>
            <div class="card-body">
                <form id="predictForm" method="post" action="predict.php">
                    <div class="form-group">
                        <label for="inputData">Input Data:</label>
                        <textarea class="form-control" id="inputData" name="inputData" rows="5"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Predict</button>
                </form>
                <div id="predictionResult"></div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#predictForm').on('submit', function(e) {
            e.preventDefault();
            var inputData = $('#inputData').val();

            $.ajax({
                url: 'predisi.php',
                type: 'POST',
                data: {
                    inputData: inputData
                },
                success: function(response) {
                    let data = JSON.parse(response);

                    if (data.status == 'success') {
                        let html = '<div class="alert alert-success">' + data.message + '</div>';
                        html += '<h3><strong>Prediction Result:</strong> ' + data.prediction + '</h3>';

                        $('#predictionResult').html(html);
                    } else {
                        $('#predictionResult').html('<div class="alert alert-danger">' + data.message + '</div>');
                    }
                },
                error: function() {
                    $('#predictionResult').html('<div class="alert alert-danger">There was an error processing your request.</div>');
                }
            });
        });
    });
</script>