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
    var dirMode = localStorage.getItem('f7p_dir_mode_9x7k2m') || 'single';
    var frontendPath = localStorage.getItem('f7p_frontend_path_9x7k2m') || '';
    var backendPath = localStorage.getItem('f7p_backend_path_9x7k2m') || '';
    var githubPath = localStorage.getItem('f7p_gh_path_9x7k2m') || '';
    
    var fileInput = document.querySelector('input[name="saveas"]');
    var githubFullPath = document.getElementById('github_full_path');
    var btn = document.getElementById('pushToGitBtn');
    
    if (!githubFullPath) return;
    
    var fullPath = '';
    var isValid = false;
    
    if (!token || !repo || !serverPath) {
        fullPath = '⚠️ Setup GitHub API first (⋮ → GitHub API)';
        isValid = false;
    } else if (!fileInput || !fileInput.value) {
        fullPath = '❌ No file selected';
        isValid = false;
    } else {
        var filePath = fileInput.value;
        var fileName = filePath.split('/').pop();
        var ext = fileName.split('.').pop().toLowerCase();
        
        var backendExts = ['php', 'phtml', 'php3', 'php4', 'php5', 'php7', 'phps', 'php8', 
                          'htaccess', 'htpasswd', 'env', 'ini', 'conf', 'config', 'cfg',
                          'yaml', 'yml', 'sql', 'db', 'py', 'rb', 'pl', 'cgi', 'sh', 'bash', 'zsh'];
        var isBackend = backendExts.includes(ext);
        
        var githubPathToUse = '';
        if (dirMode === 'multi') {
            githubPathToUse = isBackend ? backendPath : frontendPath;
        } else {
            githubPathToUse = githubPath;
        }
        
        if (!githubPathToUse) {
            fullPath = '⚠️ No GitHub directory set for this file type';
            isValid = false;
        } else if (filePath.startsWith(serverPath)) {
            var relativePath = filePath.substring(serverPath.length);
            if (relativePath.startsWith('/') || relativePath.startsWith('\\')) {
                relativePath = relativePath.substring(1);
            }
            
            var finalPath = githubPathToUse;
            if (!finalPath.endsWith('/')) finalPath += '/';
            finalPath += relativePath;
            
            var typeLabel = dirMode === 'multi' ? ' [' + (isBackend ? 'BACKEND' : 'FRONTEND') + ']' : '';
            
            fullPath = 'github.com/' + repo + '/' + finalPath + typeLabel;
            isValid = true;
        } else {
            fullPath = '🚫 File outside Server Path';
            isValid = false;
        }
    }
    
    githubFullPath.value = fullPath;
    githubFullPath.style.color = isValid ? '#0066cc' : '#dc3545';
    githubFullPath.dataset.isValid = isValid ? 'true' : 'false';
    
    function doScroll() {
        if (githubFullPath) {
            githubFullPath.scrollLeft = githubFullPath.scrollWidth;
        }
    }
    
    doScroll();
    setTimeout(doScroll, 10);
    setTimeout(doScroll, 50);
    setTimeout(doScroll, 100);
    
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

function pushToGitHub() {
    var githubFullPath = document.getElementById('github_full_path');
    var fileInput = document.querySelector('input[name="saveas"]');
    var contentTextarea = document.querySelector('textarea[name="content_plain"]');
    
    if (!githubFullPath || !fileInput || !contentTextarea) {
        alert('Elements not found!');
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
    var fileName = filePath.split('/').pop();
    var ext = fileName.split('.').pop().toLowerCase();
    var isImage = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg', 'ico'].includes(ext);
    
    var fullPath = githubFullPath.value;
    var githubPath = fullPath.replace('github.com/' + repo + '/', '');
    githubPath = githubPath.replace(/ \[.*\]$/, '');
    
    var btn = document.getElementById('pushToGitBtn');
    if (!btn) return;
    
    var originalText = btn.textContent;
    
    function updateStatus(msg) {
        var statusDiv = document.getElementById('saveStatus');
        if (statusDiv) {
            statusDiv.innerHTML = msg;
            statusDiv.style.display = 'block';
        } else {
            var div = document.querySelector('#editForm div[style*="text-align:right"]');
            if (div) {
                div.innerHTML = msg;
            }
        }
    }
    
    btn.disabled = true;
    btn.value = 'Pushing...';
    btn.style.opacity = '0.7';
    
   
    function pushContent(base64Content, sha) {
        var apiUrl = 'https://api.github.com/repos/' + repo + '/contents/' + githubPath;
        var payload = {
            message: 'Update ' + fileName + ' via F7P',
            content: base64Content,
            branch: branch
        };
        if (sha) payload.sha = sha;
        
        fetch(apiUrl, {
            method: 'PUT',
            headers: {
                'Authorization': 'token ' + token,
                'Content-Type': 'application/json',
                'Accept': 'application/vnd.github.v3+json'
            },
            body: JSON.stringify(payload)
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
            var now = new Date();
            var h = now.getHours();
            var m = String(now.getMinutes()).padStart(2, '0');
            var s = String(now.getSeconds()).padStart(2, '0');
            var ampm = h >= 12 ? 'PM' : 'AM';
            h = h % 12 || 12;
            var timeStr = h + ':' + m + ':' + s + ' ' + ampm;
            
            var repoUrl = 'https://github.com/' + repo + '/blob/' + branch + '/' + githubPath;
            var linkHtml = '✅<a href="' + repoUrl + '" target="_blank" style="color:#0066cc;text-decoration:underline dotted #0066cc;font-size:12px;display:inline-block;max-width:330px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;vertical-align:middle;">' + repoUrl + '</a>';
            
            updateStatus(linkHtml + ' ' + timeStr);
            btn.value = 'Push to Git';
            btn.style.background = '#2b3137';
            btn.style.opacity = '1';
            btn.disabled = false;
        })
        .catch(function(error) {
            btn.value = 'Push to Git';
            btn.style.background = '#dc3545';
            btn.style.opacity = '1';
            btn.disabled = false;
            var errorMsg = error.message;
            if (errorMsg.includes('403')) {
                errorMsg = 'Permission denied. Check your token permissions (need "repo" scope)';
            } else if (errorMsg.includes('404')) {
                errorMsg = 'Repository not found. Check repo name format: username/repo';
            }
            updateStatus('❌ Push failed: ' + errorMsg);
            setTimeout(function() {
                btn.style.background = '#2b3137';
            }, 3000);
        });
    }
    
   
    if (isImage) {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', '?img_direct=' + encodeURIComponent(filePath), true);
        xhr.responseType = 'arraybuffer';
        xhr.onload = function() {
            if (xhr.status === 200) {
                var arrayBuffer = xhr.response;
                var uint8Array = new Uint8Array(arrayBuffer);
                var binary = '';
                for (var i = 0; i < uint8Array.length; i++) {
                    binary += String.fromCharCode(uint8Array[i]);
                }
                var base64Content = btoa(binary);
                
               
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
                    pushContent(base64Content, data.sha || null);
                })
                .catch(function(error) {
                    btn.value = 'Push to Git';
                    btn.disabled = false;
                    btn.style.opacity = '1';
                    updateStatus('❌ ' + error.message);
                });
            } else {
                btn.value = 'Push to Git';
                btn.disabled = false;
                btn.style.opacity = '1';
                updateStatus('❌ Failed to read image file');
            }
        };
        xhr.onerror = function() {
            btn.value = 'Push to Git';
            btn.disabled = false;
            btn.style.opacity = '1';
            updateStatus('❌ Network error reading image');
        };
        xhr.send();
        return;
    }
    
   
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
        pushContent(encodedContent, data.sha || null);
    })
    .catch(function(error) {
        btn.value = 'Push to Git';
        btn.disabled = false;
        btn.style.opacity = '1';
        updateStatus('❌ ' + error.message);
    });
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
    var fileName = prompt('Enter new file name:', 'index.html');
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