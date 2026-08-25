(function() {
    'use strict';
    
    if (!document.getElementById('editorContent')) {
        return;
    }
    
    var VERSION_DB_NAME = 'F7P_VersionDB';
    var VERSION_DB_VERSION = 1;
    var VERSION_STORE_NAME = 'versions';
    var MAX_VERSIONS = 9;
    var db = null;
    
    function openVersionDB() {
        return new Promise(function(resolve, reject) {
            if (db) {
                resolve(db);
                return;
            }
            try {
                var request = indexedDB.open(VERSION_DB_NAME, VERSION_DB_VERSION);
                request.onerror = function(event) {
                    reject('IndexedDB error: ' + event.target.error);
                };
                request.onsuccess = function(event) {
                    db = event.target.result;
                    resolve(db);
                };
                request.onupgradeneeded = function(event) {
                    var dbase = event.target.result;
                    if (!dbase.objectStoreNames.contains(VERSION_STORE_NAME)) {
                        var store = dbase.createObjectStore(VERSION_STORE_NAME, { keyPath: 'id' });
                        store.createIndex('filePath', 'filePath', { unique: false });
                        store.createIndex('timestamp', 'timestamp', { unique: false });
                    }
                };
            } catch(e) {
                reject('IndexedDB not supported');
            }
        });
    }
    
    function saveVersion(filePath, content, fileName) {
        return new Promise(function(resolve, reject) {
            openVersionDB().then(function(dbase) {
                var transaction = dbase.transaction([VERSION_STORE_NAME], 'readwrite');
                var store = transaction.objectStore(VERSION_STORE_NAME);
                var request = store.getAll();
                request.onsuccess = function() {
                    var allVersions = request.result || [];
                    allVersions.sort(function(a, b) {
                        return b.timestamp - a.timestamp;
                    });
                    while (allVersions.length >= MAX_VERSIONS) {
                        var oldest = allVersions.pop();
                        store.delete(oldest.id);
                    }
                    var newVersion = {
                        id: Date.now() + '_' + Math.random().toString(36).substr(2, 9),
                        filePath: filePath,
                        fileName: fileName || filePath.split('/').pop(),
                        content: content,
                        timestamp: Date.now(),
                        timestampStr: new Date().toLocaleString()
                    };
                    store.put(newVersion);
                    resolve(newVersion);
                };
                request.onerror = function() {
                    reject('Failed to get all versions');
                };
            }).catch(reject);
        });
    }
    
    function getAllVersions() {
        return new Promise(function(resolve, reject) {
            openVersionDB().then(function(dbase) {
                var transaction = dbase.transaction([VERSION_STORE_NAME], 'readonly');
                var store = transaction.objectStore(VERSION_STORE_NAME);
                var request = store.getAll();
                request.onsuccess = function() {
                    var versions = request.result || [];
                    versions.sort(function(a, b) {
                        return b.timestamp - a.timestamp;
                    });
                    resolve(versions);
                };
                request.onerror = function() {
                    reject('Failed to get all versions');
                };
            }).catch(reject);
        });
    }
    
    function deleteVersion(id) {
        return new Promise(function(resolve, reject) {
            openVersionDB().then(function(dbase) {
                var transaction = dbase.transaction([VERSION_STORE_NAME], 'readwrite');
                var store = transaction.objectStore(VERSION_STORE_NAME);
                var request = store.delete(id);
                request.onsuccess = function() {
                    resolve();
                };
                request.onerror = function() {
                    reject('Failed to delete version');
                };
            }).catch(reject);
        });
    }
    
    function clearAllVersions() {
        return new Promise(function(resolve, reject) {
            openVersionDB().then(function(dbase) {
                var transaction = dbase.transaction([VERSION_STORE_NAME], 'readwrite');
                var store = transaction.objectStore(VERSION_STORE_NAME);
                var request = store.clear();
                request.onsuccess = function() {
                    resolve();
                };
                request.onerror = function() {
                    reject('Failed to clear versions');
                };
            }).catch(reject);
        });
    }
    
    function getVersionContent(filePath, versionId) {
        return new Promise(function(resolve, reject) {
            openVersionDB().then(function(dbase) {
                var transaction = dbase.transaction([VERSION_STORE_NAME], 'readonly');
                var store = transaction.objectStore(VERSION_STORE_NAME);
                var request = store.get(versionId);
                request.onsuccess = function() {
                    resolve(request.result ? request.result.content : null);
                };
                request.onerror = function() {
                    reject('Failed to get version content');
                };
            }).catch(reject);
        });
    }
    
    window.saveVersion = saveVersion;
    window.getAllVersions = getAllVersions;
    window.deleteVersion = deleteVersion;
    window.clearAllVersions = clearAllVersions;
    window.getVersionContent = getVersionContent;
    
    window.showVersionHistory = function() {
        getAllVersions().then(function(versions) {
            if (versions.length === 0) {
                alert('No versions saved yet.\n\nAutomatically when you click Save or Paste+Save.');
                return;
            }
            var html = '<div style="position:fixed;top:0;left:0;right:0;bottom:0;z-index:9999;background:rgba(0,0,0,0.5);display:flex;justify-content:center;align-items:center;padding:20px;" id="versionModal">';
            html += '<div style="background:#fff;border-radius:12px;max-width:700px;width:100%;max-height:90vh;overflow:hidden;display:flex;flex-direction:column;box-shadow:0 20px 60px rgba(0,0,0,0.3);">';
            html += '<div style="padding:16px 20px;border-bottom:2px solid #0066cc;display:flex;justify-content:space-between;align-items:center;flex-shrink:0;">';
            html += '<h3 style="margin:0;color:#0066cc;">Saved Version (' + versions.length + '/' + MAX_VERSIONS + ')</h3>';
            html += '<button onclick="document.getElementById(\'versionModal\').remove();" style="background:none;border:none;font-size:24px;cursor:pointer;color:#999;">✕</button>';
            html += '</div>';
            html += '<div style="flex:1;overflow-y:auto;padding:12px 16px;">';
            var grouped = {};
            versions.forEach(function(v) {
                if (!grouped[v.filePath]) {
                    grouped[v.filePath] = [];
                }
                grouped[v.filePath].push(v);
            });
            var filePaths = Object.keys(grouped);
            filePaths.forEach(function(filePath) {
                var fileVersions = grouped[filePath];
                var fileName = fileVersions[0].fileName || filePath.split('/').pop();
                html += '<div style="margin-bottom:16px;border-bottom:1px solid #eee;padding-bottom:12px;">';
                html += '<div style="font-weight:bold;color:#0066cc;font-size:14px;margin-bottom:8px;word-break:break-all;">' + escapeHtml(fileName) + '</div>';
                fileVersions.forEach(function(v, index) {
                    var isLatest = index === 0;
                    var size = v.content ? v.content.length : 0;
                    var sizeStr = size > 1024 ? (size/1024).toFixed(1) + 'KB' : size + 'B';
                    html += '<div style="display:flex;align-items:center;gap:8px;padding:6px 8px;border-radius:4px;margin-bottom:2px;' + (isLatest ? 'background:#e8f4fd;' : '') + '">';
                    html += '<span style="font-size:12px;color:#666;min-width:30px;">#' + (fileVersions.length - index) + '</span>';
                    html += '<span style="font-size:12px;color:#888;min-width:70px;">' + v.timestampStr + '</span>';
                    html += '<span style="font-size:11px;color:#999;min-width:50px;">' + sizeStr + '</span>';
                    html += '<button onclick="restoreVersion(\'' + v.id + '\', \'' + escapeHtml(v.filePath) + '\')" style="padding:2px 10px;background:#0066cc;color:#fff;border:none;border-radius:4px;font-size:12px;cursor:pointer;">Restore</button>';
                    html += '<button onclick="deleteVersionEntry(\'' + v.id + '\')" style="padding:2px 8px;background:#dc3545;color:#fff;border:none;border-radius:4px;font-size:11px;cursor:pointer;">✕</button>';
                    if (isLatest) {
                        html += '<span style="font-size:10px;color:#0066cc;font-weight:bold;margin-left:auto;">Latest</span>';
                    }
                    html += '</div>';
                });
                html += '</div>';
            });
            html += '</div>';
            html += '<div style="padding:12px 20px;border-top:1px solid #eee;display:flex;gap:10px;flex-shrink:0;flex-wrap:wrap;">';
            html += '<button onclick="clearAllVersionHistory()" style="padding:6px 16px;background:#dc3545;color:#fff;border:none;border-radius:6px;cursor:pointer;font-size:13px;">Clear All</button>';
            html += '<span style="font-size:12px;color:#888;display:flex;align-items:center;">Max ' + MAX_VERSIONS + '</span>';
            html += '</div>';
            html += '</div></div>';
            var modalContainer = document.createElement('div');
            modalContainer.innerHTML = html;
            var modalElement = modalContainer.firstElementChild;
            document.body.appendChild(modalElement);
            modalElement.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.remove();
                }
            });
        }).catch(function(err) {});
    };
    
    window.restoreVersion = function(versionId, filePath) {
        getVersionContent(filePath, versionId).then(function(content) {
            if (content === null) {
                alert('Version not found');
                return;
            }
            var editor = document.getElementById('editorContent');
            if (editor) {
                editor.value = content;
                editor.dispatchEvent(new Event('input'));
                alert('Version restored! Click Save to keep changes.');
            } else {
                alert('Editor not found. Please open a file first.');
            }
            var modal = document.getElementById('versionModal');
            if (modal) modal.remove();
        }).catch(function(err) {
            alert('Error restoring version: ' + err);
        });
    };
    
    window.deleteVersionEntry = function(versionId) {
        if (!confirm('Delete this version?')) return;
        deleteVersion(versionId).then(function() {
            showVersionHistory();
        }).catch(function(err) {
            alert('Error deleting version: ' + err);
        });
    };
    
    window.clearAllVersionHistory = function() {
        if (!confirm('Delete ALL version history?')) return;
        clearAllVersions().then(function() {
            var modal = document.getElementById('versionModal');
            if (modal) modal.remove();
        }).catch(function(err) {
            alert('Error clearing versions: ' + err);
        });
    };
    
    function escapeHtml(str) {
        if (!str) return '';
        return String(str).replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    }

    function hookToExistingSave() {
        var saveBtn = document.getElementById('saveBtn');
        if (!saveBtn) {
            setTimeout(hookToExistingSave, 500);
            return;
        }
        var originalHandler = saveBtn.onclick;
        saveBtn.onclick = function(e) {
            var editor = document.getElementById('editorContent');
            var filePath = document.querySelector('input[name="saveas"]');
            if (editor && filePath) {
                var content = editor.value;
                var path = filePath.value;
                var fileName = path.split('/').pop();
                saveVersion(path, content, fileName).catch(function() {});
            }
            if (typeof originalHandler === 'function') {
                originalHandler.call(saveBtn, e);
            }
        };
    }
    
    if (document.readyState === 'complete') {
        setTimeout(hookToExistingSave, 100);
    } else {
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(hookToExistingSave, 100);
        });
    }
    
})();