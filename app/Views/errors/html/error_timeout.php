<?php

use CodeIgniter\CodeIgniter;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Timeout</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css'); ?>">
    <style>
        body {
            height: 100vh;
            display: flex;
            align-items: center;
            background-color: #f8f9fa;
        }
        .timeout-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        .timeout-icon {
            font-size: 3rem;
            color: #dc3545;
            margin-bottom: 1rem;
        }
        .timeout-heading {
            font-weight: 600;
            margin-bottom: 1rem;
        }
        .retry-button {
            margin-top: 1.5rem;
        }
        .timeout-info {
            margin-top: 2rem;
            font-size: 0.9rem;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="timeout-container text-center">
            <div class="timeout-icon">
                <i class="ri-time-line"></i>
            </div>
            <h2 class="timeout-heading">Request Timeout</h2>
            <p class="lead">The server took too long to process your request.</p>
            <p>This may be due to high server load or a slow network connection.</p>
            
            <div class="retry-button">
                <button class="btn btn-primary" id="retry-btn">Try Again</button>
                <a href="<?= site_url('/') ?>" class="btn btn-outline-secondary ms-2">Go to Home</a>
            </div>
            
            <div class="timeout-info">
                <p>
                    <strong>Error Details:</strong><br>
                    Maximum execution time of 60 seconds exceeded
                </p>
                
                <?php if (ENVIRONMENT === 'development'): ?>
                <p>
                    <strong>File:</strong> <?= isset($file) ? esc($file) : 'CURLRequest.php' ?><br>
                    <strong>Line:</strong> <?= isset($line) ? esc($line) : '672' ?>
                </p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script>
        document.getElementById('retry-btn').addEventListener('click', function() {
            window.location.reload();
        });
    </script>
    <script src="<?= base_url('assets/js/bootstrap.bundle.min.js'); ?>"></script>
</body>
</html>
