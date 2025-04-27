<html>
    <head>
        <meta charset="utf-8">
        <title>Furs Data</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

        <style>
            body {
                background-color: #f8f9fa;
            }
            table {
                margin: 20px auto;
                width: 80%;
                border-collapse: collapse;
            }
            th, td {
                padding: 10px;
                text-align: left;
                border-bottom: 1px solid #ddd;
            }
            th {
                background-color: #007bff;
                color: white;
            }
            </style>
    </head>
<body>
<div class="container">
    <h1 class='text-center'>Furs data file - top 1000 rows</h1>
<hr>
<?php if($this->onReloadMode): ?>
    <div class="alert alert-warning" role="alert">
        Data is older than 24 hours or missing. waiting for data reload.
    </div>
    <p>
        <span>
        <div class="spinner-border text-success" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        Loading...
        </span>
    </p>

    <div class="card">
        <div class="card-header">
            Data update log
        </div>
        <div class="card-body">
            <?=$this->log->readLog();?>
        </div>
    </div>

    <script>
        setTimeout(function() {
            window.location.reload();
        }, 5000); // Reload every 5 seconds
    </script>

    <?php else: ?>
    <?php if(count($this->data->getData())==0){?>
        <div class='alert alert-danger' role='alert'>No data available</div>
        <div class="card">
        
        <div class="card-header">
            Data update log
        </div>

        <div class="card-body">
            <?=$this->log->readLog();?>
        </div>
    </div>
        
    <?php }else{ ?>
    <table>
        <thead>
        <tr>
            <?php foreach(explode(",",$this->env['COLUMNS_TITLES']) as $key => $value): ?>
                <th><?php echo $value; ?></th>
            <?php endforeach; ?>
        </tr>
        </thead>
        <tbody>
            <?php foreach($this->data->getData() as $row): ?>
                <tr>
                    <?php foreach($row as $key => $value): ?>
                        <td><?php echo $value; ?></td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
    <?php } ?>
    <?php endif; ?>
    </div>
</body> 
</html>