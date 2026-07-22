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
        var result = input;
        result = result.replace(/<\!--[\s\S]*?-->/g, '');
        result = result.replace(/\/\*[\s\S]*?\*\//g, '');
        result = result.replace(/\/\/.*$/gm, '');
        result = result.replace(/^[\s]*#.*$/gm, '');
        return result.trim();
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