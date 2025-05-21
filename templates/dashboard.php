<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KCC Digital Signage Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .content-preview {
            width: 200px;
            height: 150px;
            object-fit: contain;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .location-card {
            margin-bottom: 20px;
        }
        .upload-zone {
            border: 2px dashed #ddd;
            border-radius: 4px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        .upload-zone:hover {
            border-color: #0d6efd;
            background-color: #f8f9fa;
        }
        .upload-zone.dragover {
            border-color: #198754;
            background-color: #e8f5e9;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">KCC Digital Signage</a>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <h2>Content Management</h2>
                <hr>
            </div>
        </div>

        <?php foreach ($_ENV['LOCATIONS'] ?? [] as $location): ?>
        <div class="card location-card">
            <div class="card-header">
                <h3><?= htmlspecialchars($location) ?></h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php foreach ($_ENV['CONTENT_TYPES'] ?? [] as $type): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <?= htmlspecialchars(ucfirst($type)) ?>
                            </div>
                            <div class="card-body">
                                <div class="upload-zone" 
                                     data-location="<?= htmlspecialchars($location) ?>"
                                     data-type="<?= htmlspecialchars($type) ?>">
                                    <i class="fas fa-cloud-upload-alt fa-2x mb-2"></i>
                                    <p>Drag & Drop or Click to Upload</p>
                                </div>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" value="" id="vertical-<?= htmlspecialchars($location) ?>-<?= htmlspecialchars($type) ?>">
                                    <label class="form-check-label" for="vertical-<?= htmlspecialchars($location) ?>-<?= htmlspecialchars($type) ?>">
                                        Vertical Display
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelectorAll('.upload-zone').forEach(zone => {
            zone.addEventListener('dragover', (e) => {
                e.preventDefault();
                zone.classList.add('dragover');
            });

            zone.addEventListener('dragleave', () => {
                zone.classList.remove('dragover');
            });

            zone.addEventListener('drop', async (e) => {
                e.preventDefault();
                zone.classList.remove('dragover');
                
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    await uploadFile(files[0], zone);
                }
            });

            zone.addEventListener('click', () => {
                const input = document.createElement('input');
                input.type = 'file';
                input.accept = 'image/*,video/mp4';
                input.onchange = async (e) => {
                    if (e.target.files.length > 0) {
                        await uploadFile(e.target.files[0], zone);
                    }
                };
                input.click();
            });
        });

        async function uploadFile(file, zone) {
            const location = zone.dataset.location;
            const type = zone.dataset.type;
            const verticalCheckbox = document.getElementById(`vertical-${location}-${type}`);
            const orientation = verticalCheckbox.checked ? 'vertical' : 'horizontal';

            const formData = new FormData();
            formData.append('file', file);
            formData.append('location', location);
            formData.append('type', type);
            formData.append('orientation', orientation);

            try {
                const response = await fetch('/api/upload', {
                    method: 'POST',
                    body: formData
                });

                if (!response.ok) {
                    throw new Error('Upload failed');
                }

                alert('Upload successful!');
            } catch (error) {
                alert('Error uploading file: ' + error.message);
            }
        }
    </script>
</body>
</html> 