(function() {
    function loadGitHubSettings() {
        var token = localStorage.getItem('f7p_gh_token_9x7k2m');
        var repo = localStorage.getItem('f7p_gh_repo_9x7k2m');
        var branch = localStorage.getItem('f7p_gh_branch_9x7k2m');
        var serverPath = localStorage.getItem('f7p_gh_server_path_9x7k2m');
        var githubPath = localStorage.getItem('f7p_gh_path_9x7k2m');
        var dirMode = localStorage.getItem('f7p_dir_mode_9x7k2m') || 'single';
        var frontendPath = localStorage.getItem('f7p_frontend_path_9x7k2m') || '';
        var backendPath = localStorage.getItem('f7p_backend_path_9x7k2m') || '';
        
        var tokenInput = document.getElementById('github_token');
        if (token && tokenInput) {
            tokenInput.value = token;
            tokenInput.type = 'text';
        }
        
        var repoInput = document.getElementById('github_repo');
        if (repo && repoInput) {
            repoInput.value = repo;
        }
        
        var branchInput = document.getElementById('github_branch');
        if (branch && branchInput) {
            branchInput.value = branch;
        }
        
        var serverInput = document.getElementById('github_server_path');
        if (serverPath && serverInput) {
            serverInput.value = serverPath;
        }
        
        var githubInput = document.getElementById('github_path');
        if (githubPath && githubInput) {
            githubInput.value = githubPath;
        }
        
        var frontendInput = document.getElementById('frontend_path');
        if (frontendPath && frontendInput) {
            frontendInput.value = frontendPath;
        }
        
        var backendInput = document.getElementById('backend_path');
        if (backendPath && backendInput) {
            backendInput.value = backendPath;
        }
        
        var dirModeRadios = document.querySelectorAll('input[name="dir_mode"]');
        if (dirModeRadios.length > 0) {
            dirModeRadios.forEach(function(radio) {
                if (radio.value === dirMode) {
                    radio.checked = true;
                }
            });
            toggleMultiDir(dirMode === 'multi');
        }
        
        var configDisplay = document.getElementById('current_config');
        if (configDisplay) {
            var configHtml = '';
            if (token) configHtml += 'Token: ' + token.substring(0, 10) + '... ';
            if (repo) configHtml += '| Repo: ' + repo + ' ';
            if (branch) configHtml += '| Branch: ' + branch + ' ';
            configHtml += dirMode === 'multi' ? '| Mode: Multi' : '| Mode: Single';
            if (dirMode === 'multi') {
                configHtml += ' (FE: ' + (frontendPath || 'not set') + ', BE: ' + (backendPath || 'not set') + ')';
            }
            configDisplay.innerHTML = configHtml || 'No configuration found';
        }
    }
    
    window.toggleMultiDir = function(isMulti) {
        var singleRow = document.getElementById('singleDirRow');
        var multiRow = document.getElementById('multiDirRow');
        
        if (singleRow && multiRow) {
            singleRow.style.display = isMulti ? 'none' : 'table-row';
            multiRow.style.display = isMulti ? 'table-row' : 'none';
        }
    };
    
    if (document.readyState === 'complete') {
        loadGitHubSettings();
    } else {
        document.addEventListener('DOMContentLoaded', loadGitHubSettings);
    }
    
    window.loadGitHubSettings = loadGitHubSettings;
})();