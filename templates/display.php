<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KCC Digital Signage</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: black;
            color: white;
            overflow: hidden;
        }

        .content-container {
            width: 100vw;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        img, video {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .error {
            font-family: Arial, sans-serif;
            font-size: 24px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="content-container">
        <?php if ($display->getCurrentContent()): ?>
            <?php if ($display->isVideo()): ?>
                <video autoplay loop muted playsinline>
                    <source src="<?= htmlspecialchars($display->getContentPath()) ?>" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            <?php else: ?>
                <img src="<?= htmlspecialchars($display->getContentPath()) ?>" alt="Display Content">
            <?php endif; ?>
        <?php else: ?>
            <div class="error">No content available</div>
        <?php endif; ?>
    </div>

    <script>
        // Refresh the page periodically
        const REFRESH_INTERVAL = <?= $_ENV['DISPLAY_REFRESH_INTERVAL'] ?? 30 ?> * 1000;
        setInterval(() => {
            window.location.reload();
        }, REFRESH_INTERVAL);
    </script>
</body>
</html> 