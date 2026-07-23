(function() {
    'use strict';
    
    var DB_NAME = 'F7P_EditorDB';
    var DB_VERSION = 1;
    var STORE_NAME = 'files';
    var db = null;
    
    function openDB() {
        return new Promise(function(resolve, reject) {
            if (db) {
                resolve(db);
                return;
            }
            var request = indexedDB.open(DB_NAME, DB_VERSION);
            request.onerror = function(event) {
                reject('IndexedDB error: ' + event.target.error);
            };
            request.onsuccess = function(event) {
                db = event.target.result;
                resolve(db);
            };
            request.onupgradeneeded = function(event) {
                var db = event.target.result;
                if (!db.objectStoreNames.contains(STORE_NAME)) {
                    db.createObjectStore(STORE_NAME, { keyPath: 'path' });
                }
            };
        });
    }
    
    function saveToDB(path, content) {
        return new Promise(function(resolve, reject) {
            openDB().then(function(db) {
                var transaction = db.transaction([STORE_NAME], 'readwrite');
                var store = transaction.objectStore(STORE_NAME);
                var request = store.put({
                    path: path,
                    content: content,
                    timestamp: Date.now()
                });
                request.onsuccess = function() {
                    resolve();
                };
                request.onerror = function() {
                    reject('Failed to save to IndexedDB');
                };
            }).catch(reject);
        });
    }
    
    function getFromDB(path) {
        return new Promise(function(resolve, reject) {
            openDB().then(function(db) {
                var transaction = db.transaction([STORE_NAME], 'readonly');
                var store = transaction.objectStore(STORE_NAME);
                var request = store.get(path);
                request.onsuccess = function() {
                    if (request.result) {
                        resolve(request.result.content);
                    } else {
                        resolve(null);
                    }
                };
                request.onerror = function() {
                    reject('Failed to read from IndexedDB');
                };
            }).catch(reject);
        });
    }
    
    function deleteFromDB(path) {
        return new Promise(function(resolve, reject) {
            openDB().then(function(db) {
                var transaction = db.transaction([STORE_NAME], 'readwrite');
                var store = transaction.objectStore(STORE_NAME);
                var request = store.delete(path);
                request.onsuccess = function() {
                    resolve();
                };
                request.onerror = function() {
                    reject('Failed to delete from IndexedDB');
                };
            }).catch(reject);
        });
    }
    
    function uploadFile(filePath, content) {
        return new Promise(function(resolve, reject) {
            var formData = new FormData();
            var blob = new Blob([content], { type: 'application/octet-stream' });
            var fileName = filePath.split('/').pop();
            formData.append('file_upload', blob, fileName);
            formData.append('file_path', filePath);
            formData.append('overwrite', '1');
            var xhr = new XMLHttpRequest();
            xhr.open('POST', window.location.href + '&x=upload_ajax', true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    try {
                        var response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            resolve(response);
                        } else {
                            reject(response.message || 'Upload failed');
                        }
                    } catch(e) {
                        reject('Invalid response');
                    }
                } else {
                    reject('HTTP error: ' + xhr.status);
                }
            };
            xhr.onerror = function() {
                reject('Network error');
            };
            xhr.send(formData);
        });
    }
    
    window.saveFileViaIndexedDB = function(filePath, content) {
        return new Promise(function(resolve, reject) {
            saveToDB(filePath, content).then(function() {
                return uploadFile(filePath, content);
            }).then(function(response) {
                deleteFromDB(filePath).catch(function() {});
                resolve(response);
            }).catch(function(error) {
                reject(error);
            });
        });
    };
    
    window.restoreFromIndexedDB = function(filePath) {
        return getFromDB(filePath);
    };
    
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
        setTimeout(function() {
            if (input) {
                input.scrollLeft = input.scrollWidth;
            }
        }, 50);
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
    var result = input;
    result = result.replace(/<!--[\s\S]*?-->/g, '');
    result = result.replace(/\/\*[\s\S]*?\*\//g, '');
    result = result.replace(/(^|\s)\/\/.*$/gm, '');
    result = result.replace(/^[\s]*#.*$/gm, '');
    return result.trim();
}
var removeCommentsBtn = document.getElementById('removeCommentsBtn');
if (removeCommentsBtn && editorContent) {
    removeCommentsBtn.addEventListener('click', function() {
        var btn = this;
        var originalText = btn.value;
        var content = editorContent.value;
        

        var hasComments = /<!--[\s\S]*?-->/.test(content) || 
                          /\/\*[\s\S]*?\*\//.test(content) || 
                          /(^|\s)\/\/.*$/gm.test(content) || 
                          /^[\s]*#.*$/gm.test(content);
        
        if (!hasComments) {
            btn.value = 'No comments';
            btn.style.background = '#ffc107';
            btn.style.color = '#333';
            setTimeout(function() {
                btn.value = originalText;
                btn.style.background = '#d29922';
                btn.style.color = 'white';
            }, 1500);
            return;
        }
       
        btn.value = 'Removing...';
        btn.disabled = true;
        btn.style.opacity = '0.7';
        
        setTimeout(function() {
            var r = content;
            r = r.replace(/<!--[\s\S]*?-->/g, '');
            r = r.replace(/\/\*[\s\S]*?\*\//g, '');
            r = r.replace(/(^|\s)\/\/.*$/gm, '');
            r = r.replace(/^[\s]*#.*$/gm, '');
            editorContent.value = r.trim();
            editorContent.dispatchEvent(new Event('input'));
            
            btn.value = '✅ Removed!';
            btn.style.background = '#28a745';
            btn.style.color = 'white';
            btn.disabled = false;
            btn.style.opacity = '1';
            
            setTimeout(function() {
                btn.value = originalText;
                btn.style.background = '#d29922';
                btn.style.color = 'white';
            }, 1500);
        }, 100);
    });
}
    
    if (pasteSaveBtn && editorContent && editForm) {
        pasteSaveBtn.addEventListener('click', function() {
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
                navigator.clipboard.readText().then(function(text) {
                    if (text && text.trim() !== '') {
                        editorContent.value = text;
                        editorContent.dispatchEvent(new Event('input'));
                        btn.value = 'Saving...';
                        var saveBtn = document.getElementById('saveBtn');
                        if (saveBtn) {
                            saveBtn.click();
                        } else {
                            var submitBtn = editForm.querySelector('input[name="save"]');
                            if (submitBtn) {
                                submitBtn.click();
                            } else {
                                editForm.submit();
                            }
                        }
                    } else {
                        alert('Clipboard kosong');
                        btn.value = originalText;
                        btn.disabled = false;
                        btn.style.opacity = '1';
                    }
                }).catch(function(err) {
                    alert('Gagal membaca clipboard: ' + err.message);
                    btn.value = originalText;
                    btn.disabled = false;
                    btn.style.opacity = '1';
                });
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
    
    var saveasInput = document.getElementById('saveas_input');
    if (saveasInput) {
        saveasInput.addEventListener('change', function() {
            setTimeout(function() {
                if (typeof updatePaths === 'function') updatePaths();
            }, 100);
        });
    }
    
    setTimeout(updatePushButton, 200);
})();

var copyBtn = document.getElementById('copyContentBtn');
if (copyBtn && document.getElementById('editorContent')) {
    var editorContent = document.getElementById('editorContent');
    copyBtn.addEventListener('click', function() {
        var btn = this;
        var originalText = btn.value;
        var content = editorContent.value;
        if (!content || content.trim() === '') {
            alert('Textarea kosong, tidak ada yang bisa disalin');
            return;
        }
        if (!navigator.clipboard) {
            var textarea = document.createElement('textarea');
            textarea.value = content;
            document.body.appendChild(textarea);
            textarea.select();
            try {
                document.execCommand('copy');
                btn.value = 'Copied';
            } catch(err) {
                alert('Failed to copy: ' + err.message);
                return;
            }
            document.body.removeChild(textarea);
        } else {
            navigator.clipboard.writeText(content).then(function() {
                btn.value = 'Copied';
            }).catch(function(err) {
                alert('Failed to copy: ' + err.message);
                return;
            });
        }
        btn.disabled = true;
        btn.style.opacity = '0.7';
        setTimeout(function() {
            btn.value = originalText;
            btn.disabled = false;
            btn.style.opacity = '1';
        }, 1500);
    });
}