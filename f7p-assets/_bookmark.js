(function() {
    var BOOKMARK_KEY = 'f7p_bookmarks_9x7k2m';
    var isBookmarking = false;
    
   
    function getBookmarks() {
        try {
            var data = localStorage.getItem(BOOKMARK_KEY);
            return data ? JSON.parse(data) : [];
        } catch(e) {
            return [];
        }
    }
    
   
    function saveBookmarks(bookmarks) {
        try {
            localStorage.setItem(BOOKMARK_KEY, JSON.stringify(bookmarks));
        } catch(e) {}
    }
    
    function getCurrentPath() {
        var path = '';
        var breadcrumb = document.querySelector('#breadcrumb');
        if (breadcrumb) {
            var links = breadcrumb.querySelectorAll('a');
            if (links.length > 0) {
                var lastLink = links[links.length - 1];
                var href = lastLink.getAttribute('href');
                if (href) {
                    var match = href.match(/y=([^&]+)/);
                    if (match) {
                        path = decodeURIComponent(match[1]);
                    }
                }
            }
        }
        
       
        if (!path && window.F7P_CONFIG) {
            path = window.F7P_CONFIG.currentPath;
        }
        
       
        if (!path) {
            var params = new URLSearchParams(window.location.search);
            path = params.get('y') || '/';
        }
        
        return path;
    }

    function renderBookmarks() {
        var list = document.getElementById('bookmarkList');
        if (!list) return;
        
        var bookmarks = getBookmarks();
        
        if (bookmarks.length === 0) {
            list.innerHTML = '<div class="bookmark-empty">No bookmarks yet</div>';
            return;
        }
        
        var html = '';
        bookmarks.forEach(function(path, index) {
            var displayPath = path;
            var lastPart = '';
            
            var parts = path.split(/[\/\\]/);
            if (parts.length > 0) {
                lastPart = parts[parts.length - 1];
                if (lastPart === '') {
                    lastPart = parts[parts.length - 2] || '';
                }
            }
            
            if (displayPath.length > 20) {
                displayPath = '...' + displayPath.substring(displayPath.length - 17);
            }
            
            if (lastPart && displayPath.indexOf(lastPart) !== -1) {
                displayPath = displayPath.replace(lastPart, '<strong style="font-size:16px;color:#0066cc;">' + lastPart + '</strong>');
            }
            
            html += '<div class="bookmark-item" data-index="' + index + '">';
            html += '<span class="path" title="' + path.replace(/"/g, '&quot;') + '">' + displayPath + '</span>';
            html += '<span class="delete-bookmark" data-index="' + index + '" title="Remove bookmark">✕</span>';
            html += '</div>';
        });
        
        list.innerHTML = html;
        
        list.querySelectorAll('.bookmark-item').forEach(function(item) {
            var index = parseInt(item.dataset.index);
            var bookmarks = getBookmarks();
            var path = bookmarks[index];
            
            if (!path) return;
            
            item.addEventListener('click', function(e) {
                if (e.target.classList.contains('delete-bookmark')) return;
                window.location.href = '?y=' + encodeURIComponent(path);
            });
            
            var deleteBtn = item.querySelector('.delete-bookmark');
            if (deleteBtn) {
                deleteBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    var idx = parseInt(this.dataset.index);
                    var bookmarks = getBookmarks();
                    bookmarks.splice(idx, 1);
                    saveBookmarks(bookmarks);
                    renderBookmarks();
                });
            }
        });
    }
    
function toggleDropdown() {
    var dropdown = document.getElementById('bookmarkDropdown');
    var toggle = document.getElementById('bookmarkToggle');
    if (dropdown) {
        dropdown.classList.toggle('open');
        toggle.classList.toggle('transparent');
        if (dropdown.classList.contains('open')) {
            renderBookmarks();
        }
    }
}

function closeDropdown() {
    var dropdown = document.getElementById('bookmarkDropdown');
    var toggle = document.getElementById('bookmarkToggle');
    if (dropdown) {
        dropdown.classList.remove('open');
        toggle.classList.remove('transparent');
    }
}
    
    function bookmarkThis() {
        var currentPath = getCurrentPath();
        if (!currentPath) {
            return;
        }
        
        var bookmarks = getBookmarks();
        if (bookmarks.includes(currentPath)) {
            var header = document.getElementById('bookmarkThis');
            if (header) {
                var originalText = header.innerHTML;
                header.innerHTML = '✅ Already bookmarked';
                header.style.color = '#28a745';
                setTimeout(function() {
                    header.innerHTML = originalText;
                    header.style.color = '';
                }, 1500);
            }
            return;
        }
        
        if (navigator.vibrate) {
            navigator.vibrate(50);
        }
        
        bookmarks.push(currentPath);
        saveBookmarks(bookmarks);
        renderBookmarks();
        
        var header = document.getElementById('bookmarkThis');
        if (header) {
            var originalText = header.innerHTML;
            header.innerHTML = '✅ Bookmarked!';
            header.style.color = '#28a745';
            isBookmarking = true;
            setTimeout(function() {
                header.innerHTML = originalText;
                header.style.color = '';
                isBookmarking = false;
                closeDropdown();
            }, 1500);
        }
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        var toggle = document.getElementById('bookmarkToggle');
        var bookmarkThisBtn = document.getElementById('bookmarkThis');
        
        if (toggle) {
            toggle.addEventListener('click', function(e) {
                e.stopPropagation();
                toggleDropdown();
            });
        }
        
        if (bookmarkThisBtn) {
            bookmarkThisBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                bookmarkThis();
            });
        }
        
        document.addEventListener('click', function(e) {
    var container = document.querySelector('.bookmark-overlay');
    var toggle = document.getElementById('bookmarkToggle');
    if (!container) return;
    if (container.contains(e.target)) return;
    if (e.target === toggle || toggle.contains(e.target)) return;
    closeDropdown();
});
        
        var observer = new MutationObserver(function() {
            renderBookmarks();
        });
        
        var content = document.getElementById('content');
        if (content) {
            observer.observe(content, {
                childList: true,
                subtree: true
            });
        }
    });
    
   
    window.bookmarkThis = bookmarkThis;
    window.renderBookmarks = renderBookmarks;
    window.getBookmarks = getBookmarks;
    window.saveBookmarks = saveBookmarks;
})();