(function() {
    function loadGitHubSettings() {
        var token = localStorage.getItem('f7p_gh_token_9x7k2m');
        var repo = localStorage.getItem('f7p_gh_repo_9x7k2m');
        var branch = localStorage.getItem('f7p_gh_branch_9x7k2m');
        var serverPath = localStorage.getItem('f7p_gh_server_path_9x7k2m');
        var githubPath = localStorage.getItem('f7p_gh_path_9x7k2m');
        var currentDir = window.F7P_CONFIG ? window.F7P_CONFIG.currentPath : '';
        
        var configHtml = '';
        
        var tokenInput = document.getElementById('github_token');
        if (token) {
            configHtml += 'Token: ' + token.substring(0, 10) + '... ';
            if (tokenInput) {
                tokenInput.value = token;
                tokenInput.type = 'text';
            }
        } else {
            configHtml += 'Token not set ';
        }
        
        var repoInput = document.getElementById('github_repo');
        if (repo) {
            configHtml += '| Repo: ' + repo + ' ';
            if (repoInput) repoInput.value = repo;
        }
        
        var branchInput = document.getElementById('github_branch');
        if (branch) {
            configHtml += '| Branch: ' + branch + ' ';
            if (branchInput) branchInput.value = branch;
        }
        
        var serverInput = document.getElementById('github_server_path');
        if (serverPath) {
            configHtml += '| Server: ' + serverPath;
            if (serverInput) serverInput.value = serverPath;
        }
        
        var githubInput = document.getElementById('github_path');
        if (githubPath) {
            configHtml += '| GitHub: ' + githubPath;
            if (githubInput) githubInput.value = githubPath;
        }
        
        var configDisplay = document.getElementById('current_config');
        if (configDisplay) {
            configDisplay.innerHTML = configHtml || 'No configuration found';
        }
        
        if (!serverPath && currentDir && serverInput) {
            serverInput.placeholder = 'Suggested: ' + currentDir;
        }
        
        setTimeout(function() {
            if (typeof updatePaths === 'function') updatePaths();
            if (typeof initPushButton === 'function') initPushButton();
        }, 300);
    }
    
   
    if (document.readyState === 'complete') {
        loadGitHubSettings();
    } else {
        document.addEventListener('DOMContentLoaded', loadGitHubSettings);
    }
    
   
    window.loadGitHubSettings = loadGitHubSettings;
})();