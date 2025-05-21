# KCC Digital Signage System

A modern, scalable digital signage solution that integrates GitHub for content management with support for web-based displays and Raspberry Pi kiosk mode.

## Features

- 🖥️ Modern web-based dashboard for content management
- 📱 Responsive design for all screen orientations
- 🔄 Automated content synchronization
- 🎯 Location-based content targeting
- 🖼️ Support for images and videos
- 🔒 Secure content delivery
- 🍓 Raspberry Pi kiosk mode support

## System Requirements

### Core Dependencies
- PHP >= 7.4
- Apache >= 2.4
- Composer (Latest Version)

### PHP Extensions
- json
- fileinfo
- pdo
- mbstring
- xml
- curl

### Supported Operating Systems
- Windows 10/11
- Linux (Ubuntu/Debian recommended)
- Raspberry Pi OS (for display units)

## Quick Start

1. **Verify Dependencies**:
   ```powershell
   # Run the dependency verification script
   ./scripts/verify-deps.ps1  # Windows
   ./scripts/verify-deps.sh   # Linux/Mac
   ```

2. **Install Dependencies (Windows)**:
   ```powershell
   # Install PHP
   # Download from https://windows.php.net/download/
   # Extract to C:\php
   # Add to PATH: [Environment]::SetEnvironmentVariable("Path", $env:Path + ";C:\php", "User")
   
   # Install Composer
   # Download and run https://getcomposer.org/Composer-Setup.exe
   
   # Install Apache
   # Download from https://www.apachelounge.com/download/
   # Extract to C:\Apache24
   # Run: httpd.exe -k install
   ```

3. **Install Dependencies (Linux/Mac)**:
   ```bash
   # Ubuntu/Debian
   sudo apt update
   sudo apt install php8.1 apache2 composer
   
   # Mac (using Homebrew)
   brew install php apache composer
   ```

4. **Setup Project**:
   ```bash
   # Install PHP dependencies
   composer install

   # Configure environment
   cp .env.example .env
   # Edit .env with your settings

   # Set up Apache
   sudo cp config/apache.conf /etc/apache2/sites-available/kcc-signage.conf
   sudo a2ensite kcc-signage
   sudo systemctl restart apache2
   ```

## Directory Structure

```
.
├── config/                 # Configuration files
│   └── apache.conf        # Apache server configuration
├── content/               # Content storage
│   ├── {location}/       # Location-specific content
│   │   ├── horizontal/   # Horizontal display content
│   │   └── vertical/     # Vertical display content
├── public/               # Public web files
├── scripts/              # Utility scripts
│   ├── cleanup.ps1      # Content cleanup script
│   ├── sync.sh          # Content synchronization script
│   └── verify-deps.ps1  # Dependency verification script
├── src/                  # Application source code
│   ├── Core/            # Core functionality
│   └── app.php          # Main application file
├── templates/            # View templates
├── vendor/              # Composer dependencies
├── .env                 # Environment configuration
├── .env.example         # Environment configuration example
├── composer.json        # PHP dependency management
└── README.md           # This file
```

## Configuration

### Environment Variables (.env)
```ini
APP_ENV=development
APP_DEBUG=true
APP_URL=http://localhost

# Display Settings
DEFAULT_DISPLAY_DURATION=10
DISPLAY_REFRESH_INTERVAL=30

# Locations
LOCATIONS=Excelsior,Flower,Kearney

# Content Types
CONTENT_TYPES=flower,nonflower,other

# Git Settings
GIT_AUTO_PULL=true
GIT_PULL_INTERVAL=1800

# Logging
LOG_LEVEL=debug
LOG_PATH=/var/log/kcc-signage/
```

### Apache Virtual Host
The Apache configuration is provided in `config/apache.conf`. Key settings:
- Document root points to `/public`
- Content directory is protected
- Security headers are enabled
- Logging is configured

## Content Management

### Dashboard Access
Access the dashboard at `http://your-server/dashboard`

### Display URLs
Format: `http://your-server/display/{location}/{type}/{orientation}`

Example:
- `http://your-server/display/Kearney/flower/horizontal`
- `http://your-server/display/Excelsior/other/vertical`

### Content Guidelines
- **Images**: JPG, PNG (recommended resolution: 1920x1080)
- **Videos**: MP4 format
- **File Size**: Keep under 100MB for optimal performance
- **Naming**: Use descriptive names without spaces

## Maintenance

### Regular Tasks
1. **Monitor Logs**:
   - Apache logs: `/var/log/apache2/kcc-signage-*.log`
   - Sync logs: `/var/log/kcc-signage/sync.log`
   - PHP logs: `/var/log/php/error.log`

2. **Update Content**:
   - Use dashboard for content updates
   - Changes sync automatically every 30 minutes

3. **System Updates**:
   ```bash
   # Update dependencies
   composer update

   # Check for system updates
   ./scripts/verify-deps.ps1
   ```

### Troubleshooting

1. **Display Issues**:
   - Check network connectivity
   - Verify content exists in correct directory
   - Check display orientation setting

2. **Upload Problems**:
   - Verify file permissions
   - Check PHP upload limits in php.ini
   - Ensure sufficient disk space

3. **Sync Issues**:
   - Check Git credentials
   - Verify network connectivity
   - Review sync logs

## Security

- ✓ Content access is logged
- ✓ Directory listings are disabled
- ✓ Security headers enabled
- ✓ File type restrictions
- ✓ Regular updates recommended

## Support

For technical support:
1. Check the logs in `/var/log/kcc-signage/`
2. Review troubleshooting section
3. Contact system administrator
4. Submit GitHub issues for bugs

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Run tests (if available)
5. Submit a pull request

## License

This project is proprietary and confidential.

---

Last Updated: March 2024
