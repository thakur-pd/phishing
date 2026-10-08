# ProLink - Advanced Data Collection System

A powerful PHP-based system for creating customizable data collection links with configurable microphone recording duration, image capture count, and optional link preview images.

## Features

✨ **Configurable Settings**
- Adjust microphone recording duration (1-60 seconds)
- Set number of camera images to capture (1-20)
- Add optional preview images to links

📱 **Multi-Tool Data Collection**
- Camera capture with multiple photos
- Microphone audio recording
- GPS location tracking
- Device information collection
- Network details
- Battery status

🔐 **Secure & Private**
- User authentication system
- Link-based data isolation
- Admin dashboard for management
- Automatic IP geolocation

🎨 **Modern UI**
- Clean, responsive design
- Mobile-friendly interface
- Real-time status updates
- Easy link management

## Installation on InfinityFree

### Step 1: Prepare Your Account
1. Create an account at [InfinityFree.net](https://www.infinityfree.net)
2. Create a new website and note your FTP credentials
3. Choose PHP 7.4+ as your PHP version

### Step 2: Upload Files via FTP

1. Download and install an FTP client (e.g., FileZilla)
2. Connect using your InfinityFree FTP credentials
3. Navigate to the `htdocs` folder (or your public_html directory)
4. Upload all files from the ProLink project:
   - All PHP files
   - `style.css`
   - `.htaccess`
   - Create `data/` folder (for SQLite database)
   - Create `uploads/` folder (for captured files)
   - Create `link_images/` folder (for link preview images)

### Step 3: Set Folder Permissions

Via FTP, right-click on these folders and set permissions to **777**:
- `data/`
- `uploads/`
- `link_images/`

### Step 4: Access Your Installation

Visit: `https://yourdomain.infinityfree.net/`

### Step 5: Create Admin Account

1. Click "Get Started"
2. Register your first account (this will be admin)
3. Log in and start creating links

## Database

The system uses **SQLite** by default, which requires no additional setup. The database is automatically created in the `data/` folder.

### Database Tables

- **users** - User accounts and authentication
- **links** - Data collection links with settings
- **captured_data** - Collected data from links

## Configuration

### Microphone Duration
- Minimum: 1 second
- Maximum: 60 seconds
- Default: 5 seconds

### Image Count
- Minimum: 1 image
- Maximum: 20 images
- Default: 5 images

### Link Image
- Supported formats: JPG, PNG, GIF, WebP
- Max size: 50MB (configurable via .htaccess)
- Optional - leave blank to skip

## File Structure

```
prolink/
├── index.php              # Main application file
├── style.css              # Stylesheet
├── .htaccess              # URL rewriting rules
├── config/
│   ├── database.php       # Database configuration
│   └── auth.php           # Authentication functions
├── pages/
│   ├── home.php           # Landing page
│   ├── login.php          # Login page
│   ├── register.php       # Registration page
│   ├── dashboard.php      # User dashboard
│   ├── link.php           # Data collection page
│   ├── view_data.php      # View captured data
│   └── admin.php          # Admin panel
├── data/                  # SQLite database (auto-created)
├── uploads/               # Captured files (auto-created)
└── link_images/           # Link preview images (auto-created)
```

## Usage

### Creating a Link

1. Log in to your dashboard
2. Fill in the link details:
   - **Data Collection Tool**: Choose what to collect (Camera, Microphone, Location, or All)
   - **Redirect URL**: Where users go after data collection
   - **Link Title**: Social media preview title
   - **Description**: Social media preview description
   - **Mic Duration**: How long to record audio (seconds)
   - **Image Count**: How many photos to capture
   - **Link Image**: Optional preview image

3. Click "Generate Link"
4. Copy the generated link and share it

### Viewing Collected Data

1. Go to your dashboard
2. Click "View" (📊) on any link card
3. Browse all captured data:
   - Photos
   - Audio recordings
   - Location data
   - Device information
   - Network details

### Admin Features

1. Log in with your admin account
2. Click "Admin" in the navigation
3. View system statistics
4. Manage users

## Security Notes

⚠️ **Important Considerations**

- This system collects sensitive user data (camera, microphone, location)
- Always comply with local privacy laws and regulations
- Obtain explicit user consent before collecting data
- Use HTTPS in production (InfinityFree provides free SSL)
- Regularly backup your database
- Keep the `config/` and `data/` folders protected from direct access

## Troubleshooting

### Database Errors
- Ensure `data/` folder exists and has write permissions (777)
- Check that PHP PDO extension is enabled

### Upload Failures
- Verify `uploads/` and `link_images/` folders exist
- Check folder permissions (should be 777)
- Verify upload limits in .htaccess

### Links Not Working
- Ensure `.htaccess` is uploaded and mod_rewrite is enabled
- Check that `index.php` is in the root directory
- Verify database connection in `config/database.php`

### Permission Issues on InfinityFree
1. Use FTP to navigate to the folder
2. Right-click and select "File Permissions"
3. Set to 777 (read, write, execute for all)

## API Endpoints

### Capture Data
```
POST /index.php?api=capture
Parameters:
- link_code: Link code
- data_type: Type of data (device_info, location, etc.)
- data_content: Data content
- latitude, longitude, accuracy: Optional GPS data
```

### Upload File
```
POST /index.php?api=upload
Parameters:
- link_code: Link code
- file: File to upload
- data_type: Type of data (camera_photo, microphone_audio, etc.)
```

## Support & Issues

For issues or questions:
1. Check the troubleshooting section above
2. Verify file permissions
3. Check InfinityFree control panel for errors
4. Review PHP error logs

## License

This project is provided as-is for educational and authorized use only.

## Disclaimer

This tool is designed for legitimate data collection purposes with proper user consent. Unauthorized data collection may violate privacy laws and regulations. Users are responsible for compliance with all applicable laws.
