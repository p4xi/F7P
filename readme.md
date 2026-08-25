# F7P - File 7ransfer Protocol

**F7P** is a powerful web-based file manager and remote administration tool. It provides a clean, mobile-friendly interface for managing files, executing commands, and performing various system operations through a web browser.

## 🚀 Features
- **Responsive Design**: Works on desktop and mobile devices
- **File Management**: Browse, upload, download, rename, delete files and directories
- **Code Editor**: Edit files directly with copy and paste button functionality
- **GitHub Integration**: Push files directly to GitHub repositories with one click
- **Command Execution**: Run system commands with a built-in terminal
- **MySQL Management**: Connect to databases, execute queries, and view tables
- **File Viewing**: Preview images and view file contents
- **Backdoor Tools**: Network testing utilities (bind shells, reverse connections)
- **PHP Eval**: Execute PHP code directly
- **PHP Mail**: Send emails directly from the server via PHP!
- **Comment Remover**: Remove all comments from code with a single click
- **Multi Repository**: Push to frontend or backend repo separately with auto-detection and customizable folder names
- **Bookmarks**: Save and jump to any file or directory in one click
- **Breadcrumb Navigation**: Easy directory traversal
- **GitHub Page Navigation**: Navigate directly to the current file/directory on GitHub
- **Version History**: Restore any of the last 9 versions locally
- **Auto Logout**: Automatic session expiration after 7 days of inactivity

## 📋 Requirements

- PHP 5.6 or higher
- MySQL extension (optional, for MySQL features)
- File system write permissions for file operations
- `gd` extension for image handling (recommended)

## 🔐 Default Login

| Credential | Value |
|------------|-------|
| Username | `admin` |
| Password | `password123` |

**⚠️ IMPORTANT**: Change the default password immediately after deployment!

## 📦 Installation

1. Upload `f7p.php` to your web server
2. Access the file via browser
3. Login with default credentials
4. Change the admin password (see Security section below)

## 🔒 Security

### Changing Password

To change the admin password:

1. Generate a new bcrypt hash using: https://lain.lain.ch/password-hash/
2. Replace the `ADMIN_PASS_HASH` value in the file:

```php
define('ADMIN_PASS_HASH', '$2a$09$lF0dTQmb5Dhh2BG5DAS6NuzJ8/rOT9el9Nui2vZAZmWkkKKf4idCu');
```

### Security Recommendations

1. **Rename the file**: Change `f7p.php` to something less obvious
2. **Add .htaccess protection**: Restrict access by IP or require additional authentication
3. **Remove after use**: Delete the file when not needed
4. **Use HTTPS**: Always access over HTTPS to prevent credential interception
5. **Monitor logs**: Check server logs for unauthorized access attempts

### .htaccess Example

```apache
<Files "f7p.php">
    Order Deny,Allow
    Deny from all
    Allow from 192.168.1.100  # Your IP only
    AuthType Basic
    AuthName "Restricted Area"
    AuthUserFile /path/to/.htpasswd
    Require valid-user
</Files>
```

## 🎭 Screenshots
**File list**
![](https://res.cloudinary.com/dry7cujup/image/upload/v1784072294/1-file-list_uwg4w5.jpg)

**Editing**
![](https://res.cloudinary.com/dry7cujup/image/upload/v1784072389/2-editing_z5elyw.jpg)

**Deleting**
![](https://res.cloudinary.com/dry7cujup/image/upload/v1784072451/3-move-to-trash_gynibu.jpg)

**GitHub set**: Use **Classic token**, check **REPO** only
![](https://res.cloudinary.com/dry7cujup/image/upload/v1784072504/4-githubset_h2wtxl.jpg)

**PHP Info**
![](https://res.cloudinary.com/dry7cujup/image/upload/v1784072588/5-phpinfo_kdgx5j.jpg)

## ⚠️ Disclaimer

**F7P is a powerful administrative tool that should only be used on servers you own or have explicit permission to access. Unauthorized use may violate laws and terms of service. The developers assume no responsibility for misuse or damage caused by this tool.**

## 📝 Usage Notes

- The tool uses PHP sessions; ensure session handling is enabled
- Some features require specific PHP extensions (MySQL, etc.)
- File paths are automatically detected for Windows/Linux systems

## 🐛 Troubleshooting

### Common Issues

| Issue | Solution |
|-------|----------|
| Login doesn't work | Check PHP session configuration |
| File upload fails | Verify write permissions on target directory |
| MySQL can't connect | Ensure MySQL extension is enabled and credentials are correct |
| GitHub push fails | Verify token has `repo` scope and repository exists |
| Command execution doesn't work | Check `disable_functions` in php.ini |

### Required PHP Extensions

```ini
extension=mysqli      # For MySQL features
extension=gd          # For image handling
extension=openssl     # For secure connections
extension=curl        # For URL downloads
```

## 📄 License

This project is distributed for educational and administrative purposes. Use responsibly.

---

## 🔧 Configuration

### File Constants

```php
define('ADMIN_USER', 'admin');
define('ADMIN_PASS_HASH', '$2a$09$lF0dTQmb5Dhh2BG5DAS6NuzJ8/rOT9el9Nui2vZAZmWkkKKf4idCu');
```

## 📱 Mobile Support

F7P is fully responsive and works on:
- Android/iOS browsers
- Tablets
- Desktop browsers (Chrome, Firefox, Safari, Edge)

## 🛡️ Vulnerability Disclosure

If you discover a security vulnerability in F7P, please:
1. Use it responsibly
2. Report findings to the repository owner
3. Do not disclose publicly until patched

---

**Remember**: With great power comes great responsibility. Use F7P ethically and legally.
