<?php
/**
 * OPPI Admin - Git Repository & Deployment Control
 * Enables administrators to view Git status, pull updates from GitHub,
 * and stage/commit/push changes directly from the OPPI Admin Panel.
 */

require_once(__DIR__ . '/../includes/config.php');
require_once(__DIR__ . '/../includes/security.php');
require_once(__DIR__ . '/../includes/function.php');

// Admin Authentication Check
$current_admin = check_admin_auth(true);

$repo_path = realpath(__DIR__ . '/..');

/**
 * Execute Git command safely inside the repository root
 */
function run_git($subcmd, $cwd) {
    $descriptors = [
        0 => ["pipe", "r"], // stdin
        1 => ["pipe", "w"], // stdout
        2 => ["pipe", "w"]  // stderr
    ];
    $cmd = "git $subcmd";
    $env = array_merge($_SERVER, [
        'SystemRoot' => getenv('SystemRoot') ?: 'C:\Windows',
        'USERPROFILE' => getenv('USERPROFILE') ?: 'C:/Users/kmrgv',
        'HOME' => getenv('USERPROFILE') ?: 'C:/Users/kmrgv',
        'GIT_TERMINAL_PROMPT' => '0'
    ]);
    $process = proc_open($cmd, $descriptors, $pipes, $cwd, $env);
    if (!is_resource($process)) {
        return ['code' => 1, 'output' => 'Failed to initialize Git process.', 'stdout' => '', 'stderr' => 'Process spawn failure'];
    }
    fclose($pipes[0]);
    $stdout = stream_get_contents($pipes[1]);
    $stderr = stream_get_contents($pipes[2]);
    fclose($pipes[1]);
    fclose($pipes[2]);
    $exit_code = proc_close($process);
    $combined = trim($stdout . ($stderr ? ($stdout ? "\n" : '') . $stderr : ''));
    return [
        'code' => $exit_code,
        'output' => $combined,
        'stdout' => trim($stdout),
        'stderr' => trim($stderr),
        'cmd' => $cmd
    ];
}

/**
 * Gather Git state metadata
 */
function get_git_state($repo_path) {
    // Current branch
    $branch_res = run_git('rev-parse --abbrev-ref HEAD', $repo_path);
    $branch = ($branch_res['code'] === 0 && !empty($branch_res['output'])) ? $branch_res['output'] : 'main';

    // Remote URL
    $remote_res = run_git('remote get-url origin', $repo_path);
    $remote_url = ($remote_res['code'] === 0) ? $remote_res['output'] : '';

    // Last commit details
    $last_commit_res = run_git('log -1 --pretty=format:"%h|%an|%ar|%s|%ci"', $repo_path);
    $last_commit_parts = explode('|', $last_commit_res['output'] ?? '');
    $last_commit = [
        'hash' => $last_commit_parts[0] ?? 'N/A',
        'author' => $last_commit_parts[1] ?? 'N/A',
        'time' => $last_commit_parts[2] ?? 'N/A',
        'message' => $last_commit_parts[3] ?? 'N/A',
        'date' => $last_commit_parts[4] ?? 'N/A'
    ];

    // Status porcelain (file list)
    $status_res = run_git('status --porcelain', $repo_path);
    $status_lines = array_filter(explode("\n", str_replace("\r", '', $status_res['output'] ?? '')));
    $changed_files = [];
    foreach ($status_lines as $line) {
        $line = trim($line);
        if (empty($line)) continue;
        $code = substr($line, 0, 2);
        $file = trim(substr($line, 2));
        $status_label = 'Modified';
        $status_class = 'badge-warning';

        if (strpos($code, '??') !== false) {
            $status_label = 'Untracked';
            $status_class = 'badge-secondary';
        } elseif (strpos($code, 'A') !== false) {
            $status_label = 'Added';
            $status_class = 'badge-success';
        } elseif (strpos($code, 'D') !== false) {
            $status_label = 'Deleted';
            $status_class = 'badge-danger';
        } elseif (strpos($code, 'M') !== false) {
            $status_label = 'Modified';
            $status_class = 'badge-warning';
        }

        $changed_files[] = [
            'code' => $code,
            'file' => $file,
            'label' => $status_label,
            'class' => $status_class
        ];
    }

    // Behind / Ahead counts
    $counts_res = run_git("rev-list --left-right --count origin/$branch...$branch", $repo_path);
    $behind = 0;
    $ahead = 0;
    if ($counts_res['code'] === 0 && preg_match('/^(\d+)\s+(\d+)$/', trim($counts_res['output']), $m)) {
        $behind = (int)$m[1];
        $ahead = (int)$m[2];
    }

    // Recent 10 commits
    $log_res = run_git('log -10 --pretty=format:"%h|%an|%ar|%s|%ci"', $repo_path);
    $recent_commits = [];
    $log_lines = array_filter(explode("\n", str_replace("\r", '', $log_res['output'] ?? '')));
    foreach ($log_lines as $l) {
        $parts = explode('|', $l);
        if (count($parts) >= 4) {
            $recent_commits[] = [
                'hash' => $parts[0],
                'author' => $parts[1],
                'time' => $parts[2],
                'message' => $parts[3],
                'date' => $parts[4] ?? ''
            ];
        }
    }

    return [
        'branch' => $branch,
        'remote_url' => $remote_url,
        'last_commit' => $last_commit,
        'changed_files' => $changed_files,
        'has_changes' => count($changed_files) > 0,
        'behind' => $behind,
        'ahead' => $ahead,
        'recent_commits' => $recent_commits
    ];
}

$action_result = null;
$action_command = null;

// ---------------------------------------------------------------------------
// Handle Git Actions (Pull, Push, Fetch)
// ---------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!validate_csrf_token($_POST['csrf_token'] ?? '', 'oppi_git')) {
        $action_result = [
            'code' => 1,
            'action' => 'security',
            'output' => 'Security token invalid or expired. Please reload the page.',
            'success' => false
        ];
    } else {
        $act = $_POST['action'];
        $git_state = get_git_state($repo_path);
        $branch = escapeshellarg($git_state['branch']);

        if ($act === 'pull') {
            // Git Pull
            $cmd = "pull origin $branch";
            $res = run_git($cmd, $repo_path);
            $action_command = "git pull origin " . $git_state['branch'];
            $action_result = [
                'code' => $res['code'],
                'action' => 'pull',
                'output' => $res['output'] ?: 'Repository already up-to-date.',
                'success' => ($res['code'] === 0)
            ];
        } elseif ($act === 'force_pull') {
            // Force reset to origin branch (discards local conflicts and aligns with GitHub)
            $log_trace = [];
            $res_fetch = run_git("fetch origin $branch", $repo_path);
            $log_trace[] = "$ git fetch origin " . $git_state['branch'] . "\n" . ($res_fetch['output'] ?: '(Remote fetched)');

            // Remove untracked conflict files if they exist
            $conflict_untracked = ['oppi/export_changes.php', 'oppi/manage_git.php'];
            foreach ($conflict_untracked as $cfile) {
                $full_cpath = $repo_path . '/' . $cfile;
                if (file_exists($full_cpath)) {
                    $chk = run_git("status --porcelain " . escapeshellarg($cfile), $repo_path);
                    if (strpos($chk['output'] ?? '', '??') !== false) {
                        @unlink($full_cpath);
                        $log_trace[] = "Removed untracked conflict file: $cfile";
                    }
                }
            }

            // Hard reset to origin branch
            $res_reset = run_git("reset --hard origin/" . $git_state['branch'], $repo_path);
            $log_trace[] = "$ git reset --hard origin/" . $git_state['branch'] . "\n" . ($res_reset['output'] ?: 'Reset executed.');

            $action_command = "git fetch & reset --hard origin/" . $git_state['branch'];
            $action_result = [
                'code' => $res_reset['code'],
                'action' => 'force_pull',
                'output' => implode("\n\n", $log_trace),
                'success' => ($res_reset['code'] === 0)
            ];
        } elseif ($act === 'push') {
            // Git Push
            $commit_msg = trim($_POST['commit_message'] ?? '');
            if (empty($commit_msg)) {
                $commit_msg = "Admin update from OPPI on " . date('Y-m-d H:i:s');
            }
            // Sanitize commit message to single line
            $commit_msg = preg_replace('/[\r\n]+/', ' ', $commit_msg);

            $stage_all = !empty($_POST['stage_all']);
            $log_trace = [];

            if ($stage_all) {
                $add_res = run_git("add -A", $repo_path);
                $log_trace[] = "$ git add -A\n" . ($add_res['output'] ?: '(Files staged)');
            }

            // Check if there are staged changes to commit
            $diff_cached = run_git("diff --cached --name-only", $repo_path);
            if (!empty(trim($diff_cached['output']))) {
                $commit_res = run_git("commit -m " . escapeshellarg($commit_msg), $repo_path);
                $log_trace[] = "$ git commit -m \"" . htmlspecialchars($commit_msg) . "\"\n" . $commit_res['output'];
            } else {
                $log_trace[] = "$ git commit: No new uncommitted changes staged.";
            }

            // Execute Push
            $push_res = run_git("push origin $branch", $repo_path);
            $log_trace[] = "$ git push origin " . $git_state['branch'] . "\n" . ($push_res['output'] ?: 'Push executed.');

            $action_command = "git commit & push origin " . $git_state['branch'];
            $action_result = [
                'code' => $push_res['code'],
                'action' => 'push',
                'output' => implode("\n\n", $log_trace),
                'success' => ($push_res['code'] === 0)
            ];
        } elseif ($act === 'fetch') {
            // Git Fetch
            $res = run_git("fetch origin", $repo_path);
            $action_command = "git fetch origin";
            $action_result = [
                'code' => $res['code'],
                'action' => 'fetch',
                'output' => $res['output'] ?: 'Remote references successfully fetched.',
                'success' => ($res['code'] === 0)
            ];
        }
    }

    // Return JSON if requested via AJAX
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'status' => ($action_result['success'] ?? false) ? 'success' : 'error',
            'command' => $action_command,
            'output' => $action_result['output'] ?? '',
            'state' => get_git_state($repo_path)
        ]);
        exit;
    }
}

// Fetch current Git state for UI display
$git = get_git_state($repo_path);
$csrf_token = generate_csrf_token('oppi_git');

// Derive web URL for commits (GitHub support)
$web_repo_url = preg_replace('/\.git$/', '', $git['remote_url']);
if (strpos($web_repo_url, 'git@github.com:') === 0) {
    $web_repo_url = str_replace('git@github.com:', 'https://github.com/', $web_repo_url);
}

require_once(__DIR__ . '/includes/header.php');
?>

<!-- Breadcrumbs & Header -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1" style="gap: 8px;">
            <a href="manage_data.php" class="text-muted small text-decoration-none">
                <i class="fas fa-database mr-1"></i> Master Data Hub
            </a>
            <span class="text-muted small">/</span>
            <span class="text-primary small font-weight-bold">Git Deployment Control</span>
        </div>
        <h1 class="h3 fw-bold mb-1" style="font-family: 'Outfit', sans-serif;">Git Deployment &amp; Repository Manager</h1>
        <p class="text-muted mb-0" style="font-size: 0.9rem;">
            Synchronize local server codebase with GitHub repository. Pull incoming code updates or stage, commit, and push local modifications.
        </p>
    </div>
    <div class="mt-3 mt-md-0 d-flex gap-2" style="gap: 8px;">
        <form method="POST" action="manage_git.php" class="d-inline">
            <input type="hidden" name="action" value="fetch">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
            <button type="submit" class="btn btn-outline-secondary font-weight-bold shadow-sm" style="border-radius: var(--radius-sm); font-size: 0.88rem;" title="Fetch latest branches &amp; tags from remote">
                <i class="fas fa-sync-alt mr-1"></i> Fetch Remote
            </button>
        </form>
        <a href="manage_git.php" class="btn btn-light border font-weight-bold shadow-sm" style="border-radius: var(--radius-sm); font-size: 0.88rem;" title="Refresh Git Status">
            <i class="fas fa-redo-alt mr-1"></i> Refresh
        </a>
    </div>
</div>

<!-- Command Execution Alert / Terminal Output (When Action Run) -->
<?php if ($action_result): ?>
<div class="section-card p-4 mb-4" style="background: #0f172a; border: 1.5px solid <?php echo $action_result['success'] ? '#10b981' : '#ef4444'; ?>; border-radius: 16px; color: #f8fafc;">
    <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2" style="border-color: rgba(255, 255, 255, 0.1) !important;">
        <div class="d-flex align-items-center">
            <span class="badge <?php echo $action_result['success'] ? 'badge-success' : 'badge-danger'; ?> px-2.5 py-1 mr-2 text-uppercase font-weight-bold" style="font-size: 11px;">
                <i class="<?php echo $action_result['success'] ? 'fas fa-check-circle' : 'fas fa-exclamation-triangle'; ?> mr-1"></i>
                <?php echo $action_result['success'] ? 'Command Executed Successfully' : 'Command Returned Error'; ?>
            </span>
            <span class="text-monospace small" style="color: #94a3b8;">
                $ <?php echo htmlspecialchars($action_command ?? 'git'); ?>
            </span>
        </div>
        <button type="button" class="btn btn-sm btn-link text-white-50 p-0 text-decoration-none" onclick="this.closest('.section-card').style.display='none'">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <pre class="mb-0 text-monospace p-3 rounded" style="background: rgba(0, 0, 0, 0.4); color: <?php echo $action_result['success'] ? '#86efac' : '#fca5a5'; ?>; font-size: 13px; line-height: 1.5; max-height: 250px; overflow-y: auto; white-space: pre-wrap; word-break: break-all;"><?php echo htmlspecialchars($action_result['output']); ?></pre>
</div>
<?php endif; ?>

<!-- 4 Top Status Metrics Cards -->
<div class="row mb-4">
    <!-- Card 1: Current Branch -->
    <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
        <div class="section-card p-3 h-100 d-flex flex-column justify-content-between">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small font-weight-bold text-uppercase">Branch</span>
                <div style="width: 36px; height: 36px; border-radius: 10px; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center; font-size: 16px;">
                    <i class="fas fa-code-branch"></i>
                </div>
            </div>
            <div>
                <h4 class="font-weight-bold text-dark mb-1 text-monospace" style="font-size: 1.3rem;">
                    <?php echo htmlspecialchars($git['branch']); ?>
                </h4>
                <div class="d-flex align-items-center">
                    <span class="badge badge-success px-2 py-0.5" style="font-size: 10px; border-radius: 4px;">
                        <i class="fas fa-check-circle mr-1"></i>Active Working Branch
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 2: Sync Status -->
    <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
        <div class="section-card p-3 h-100 d-flex flex-column justify-content-between">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small font-weight-bold text-uppercase">Sync with Remote</span>
                <div style="width: 36px; height: 36px; border-radius: 10px; background: #ecfdf5; color: #047857; display: flex; align-items: center; justify-content: center; font-size: 16px;">
                    <i class="fas fa-cloud"></i>
                </div>
            </div>
            <div>
                <?php if ($git['behind'] === 0 && $git['ahead'] === 0): ?>
                    <h5 class="font-weight-bold text-success mb-1">
                        <i class="fas fa-check-circle mr-1"></i> Up to Date
                    </h5>
                    <div class="text-muted small">Synchronized with origin/<?php echo htmlspecialchars($git['branch']); ?></div>
                <?php elseif ($git['behind'] > 0): ?>
                    <h5 class="font-weight-bold text-warning mb-1">
                        <i class="fas fa-arrow-down mr-1"></i> <?php echo $git['behind']; ?> Commits Behind
                    </h5>
                    <div class="text-muted small">Pull recommended from remote</div>
                <?php else: ?>
                    <h5 class="font-weight-bold text-primary mb-1">
                        <i class="fas fa-arrow-up mr-1"></i> <?php echo $git['ahead']; ?> Commits Ahead
                    </h5>
                    <div class="text-muted small">Ready to push to remote</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Card 3: Working Directory -->
    <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
        <div class="section-card p-3 h-100 d-flex flex-column justify-content-between">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small font-weight-bold text-uppercase">Working Tree</span>
                <div style="width: 36px; height: 36px; border-radius: 10px; background: #fef3c7; color: #b45309; display: flex; align-items: center; justify-content: center; font-size: 16px;">
                    <i class="fas fa-edit"></i>
                </div>
            </div>
            <div>
                <?php if (!$git['has_changes']): ?>
                    <h5 class="font-weight-bold text-success mb-1">
                        <i class="fas fa-check mr-1"></i> Tree Clean
                    </h5>
                    <div class="text-muted small">No uncommitted files</div>
                <?php else: ?>
                    <h5 class="font-weight-bold text-warning mb-1">
                        <?php echo count($git['changed_files']); ?> File(s) Changed
                    </h5>
                    <div class="text-muted small">Uncommitted modifications</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Card 4: Latest Commit -->
    <div class="col-lg-3 col-md-6">
        <div class="section-card p-3 h-100 d-flex flex-column justify-content-between">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-muted small font-weight-bold text-uppercase">Latest Commit</span>
                <div style="width: 36px; height: 36px; border-radius: 10px; background: #f3e8ff; color: #7e22ce; display: flex; align-items: center; justify-content: center; font-size: 16px;">
                    <i class="fas fa-history"></i>
                </div>
            </div>
            <div>
                <div class="d-flex align-items-center mb-1">
                    <span class="badge badge-light border text-monospace font-weight-bold text-dark mr-1.5" style="font-size: 11px;">
                        <?php echo htmlspecialchars($git['last_commit']['hash']); ?>
                    </span>
                    <span class="text-muted small text-truncate" title="<?php echo htmlspecialchars($git['last_commit']['message']); ?>">
                        <?php echo htmlspecialchars(substr($git['last_commit']['message'], 0, 24)) . (strlen($git['last_commit']['message']) > 24 ? '...' : ''); ?>
                    </span>
                </div>
                <div class="text-muted small">
                    by <strong><?php echo htmlspecialchars($git['last_commit']['author']); ?></strong> &bull; <?php echo htmlspecialchars($git['last_commit']['time']); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Dual Operations Card (Pull & Push) -->
<div class="row mb-4">
    <!-- Left Operation: Git Pull (Deploy Incoming Updates) -->
    <div class="col-lg-6 mb-4 mb-lg-0">
        <div class="section-card p-4 h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-3">
                    <div class="d-flex align-items-center">
                        <div style="width: 44px; height: 44px; border-radius: 12px; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center; font-size: 20px; margin-right: 12px;">
                            <i class="fas fa-cloud-download-alt"></i>
                        </div>
                        <div>
                            <h5 class="font-weight-bold mb-0">1. Git Pull (Update Code)</h5>
                            <p class="text-muted small mb-0">Download latest changes from remote repository.</p>
                        </div>
                    </div>
                    <span class="badge-premium badge-premium-primary">Pull</span>
                </div>

                <p class="text-muted small mb-3" style="line-height: 1.6;">
                    Fetches and merges the latest commits from <code>origin/<?php echo htmlspecialchars($git['branch']); ?></code> into this server's working copy. Use this when changes have been pushed from local machines or GitHub.
                </p>

                <div class="p-3 rounded mb-4" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                    <div class="d-flex justify-content-between small text-muted mb-1">
                        <span>Remote Target:</span>
                        <strong class="text-dark font-weight-bold">origin/<?php echo htmlspecialchars($git['branch']); ?></strong>
                    </div>
                    <div class="d-flex justify-content-between small text-muted mb-1">
                        <span>Repository URL:</span>
                        <span class="text-dark text-monospace" style="font-size: 11px;"><?php echo htmlspecialchars($git['remote_url']); ?></span>
                    </div>
                    <div class="d-flex justify-content-between small text-muted">
                        <span>Status:</span>
                        <?php if ($git['behind'] > 0): ?>
                            <span class="badge badge-warning"><?php echo $git['behind']; ?> new commit(s) ready to pull</span>
                        <?php else: ?>
                            <span class="badge badge-success"><i class="fas fa-check mr-1"></i>Current with remote</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <form method="POST" action="manage_git.php">
                <input type="hidden" name="action" value="pull">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                <button type="submit" class="btn btn-primary btn-block btn-lg font-weight-bold shadow-sm" style="border-radius: var(--radius-sm);" onclick="return confirm('Pull latest code updates from origin/<?php echo htmlspecialchars($git['branch']); ?>?');">
                    <i class="fas fa-cloud-download-alt mr-2"></i> Execute Git Pull
                </button>
            </form>
            <div class="mt-2 text-center">
                <form method="POST" action="manage_git.php" class="d-inline">
                    <input type="hidden" name="action" value="force_pull">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                    <button type="submit" class="btn btn-sm btn-link text-danger font-weight-bold p-0 text-decoration-none" onclick="return confirm('WARNING: This will forcefully reset server working files to match GitHub origin/<?php echo htmlspecialchars($git['branch']); ?> and resolve overwrite conflicts.\n\nYour database and .env are completely safe.\n\nProceed?');">
                        <i class="fas fa-exclamation-triangle mr-1"></i> Conflict? Force Reset &amp; Sync to GitHub
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Right Operation: Git Push (Publish Local Changes) -->
    <div class="col-lg-6">
        <div class="section-card p-4 h-100 d-flex flex-column justify-content-between">
            <div>
                <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-3">
                    <div class="d-flex align-items-center">
                        <div style="width: 44px; height: 44px; border-radius: 12px; background: #ecfdf5; color: #047857; display: flex; align-items: center; justify-content: center; font-size: 20px; margin-right: 12px;">
                            <i class="fas fa-cloud-upload-alt"></i>
                        </div>
                        <div>
                            <h5 class="font-weight-bold mb-0">2. Git Push (Publish Changes)</h5>
                            <p class="text-muted small mb-0">Commit and push modified files to GitHub.</p>
                        </div>
                    </div>
                    <span class="badge-premium badge-premium-success">Push</span>
                </div>

                <p class="text-muted small mb-3" style="line-height: 1.6;">
                    Stages local changes, creates a descriptive Git commit, and pushes directly to <code>origin/<?php echo htmlspecialchars($git['branch']); ?></code>.
                </p>

                <form method="POST" action="manage_git.php" id="gitPushForm">
                    <input type="hidden" name="action" value="push">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">

                    <div class="form-group mb-3">
                        <label for="commit_message" class="font-weight-bold text-dark small mb-1">
                            <i class="fas fa-comment-alt text-success mr-1"></i> Commit Message:
                        </label>
                        <input type="text" class="form-control font-weight-bold" id="commit_message" name="commit_message" placeholder="e.g., Update site settings and database records" style="border-radius: var(--radius-sm); font-size: 0.95rem;">
                        <small class="text-muted">A clear description of what was changed or updated.</small>
                    </div>

                    <div class="custom-control custom-checkbox mb-4">
                        <input type="checkbox" class="custom-control-input" id="stage_all" name="stage_all" value="1" checked>
                        <label class="custom-control-label font-weight-bold text-dark small" for="stage_all">
                            Stage all modified, newly added, and deleted files (<code>git add -A</code>)
                        </label>
                    </div>

                    <button type="submit" class="btn btn-success btn-block btn-lg font-weight-bold shadow-sm" style="border-radius: var(--radius-sm);" onclick="return confirm('Stage, commit, and push changes to origin/<?php echo htmlspecialchars($git['branch']); ?>?');">
                        <i class="fas fa-cloud-upload-alt mr-2"></i> Execute Git Push
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Working Directory Uncommitted Changes Table (If changes exist) -->
<div class="section-card p-4 mb-4">
    <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-3">
        <div class="d-flex align-items-center">
            <div style="width: 40px; height: 40px; border-radius: 10px; background: #fff7ed; color: #ea580c; display: flex; align-items: center; justify-content: center; font-size: 18px; margin-right: 12px;">
                <i class="fas fa-file-code"></i>
            </div>
            <div>
                <h5 class="font-weight-bold mb-0" style="font-size: 1.05rem;">Working Directory Changes</h5>
                <p class="text-muted small mb-0">Files modified locally that will be included in the next commit and push.</p>
            </div>
        </div>
        <span class="badge-premium <?php echo $git['has_changes'] ? 'badge-premium-warning' : 'badge-premium-success'; ?>">
            <?php echo count($git['changed_files']); ?> Files
        </span>
    </div>

    <?php if (!$git['has_changes']): ?>
        <div class="text-center py-4">
            <div class="mb-2" style="font-size: 36px; color: #10b981;">
                <i class="fas fa-check-circle"></i>
            </div>
            <h6 class="font-weight-bold text-dark mb-1">Working Directory is Clean</h6>
            <p class="text-muted small mb-0">There are no uncommitted or modified files on the server.</p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="thead-light">
                    <tr>
                        <th style="width: 15%;">Status</th>
                        <th style="width: 85%;">File Path</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($git['changed_files'] as $f): ?>
                    <tr>
                        <td>
                            <span class="badge <?php echo $f['class']; ?> px-2.5 py-1 text-uppercase font-weight-bold" style="font-size: 10.5px;">
                                <?php echo htmlspecialchars($f['label']); ?>
                            </span>
                        </td>
                        <td class="text-monospace text-dark font-weight-bold" style="font-size: 13px;">
                            <?php echo htmlspecialchars($f['file']); ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- Recent Commits Timeline Table -->
<div class="section-card p-4 mb-4">
    <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-3">
        <div class="d-flex align-items-center">
            <div style="width: 40px; height: 40px; border-radius: 10px; background: #f3e8ff; color: #7e22ce; display: flex; align-items: center; justify-content: center; font-size: 18px; margin-right: 12px;">
                <i class="fas fa-code-commit"></i>
            </div>
            <div>
                <h5 class="font-weight-bold mb-0" style="font-size: 1.05rem;">Recent Commit History</h5>
                <p class="text-muted small mb-0">Last 10 commits recorded in the repository.</p>
            </div>
        </div>
        <?php if (!empty($web_repo_url)): ?>
        <a href="<?php echo htmlspecialchars($web_repo_url); ?>/commits/<?php echo htmlspecialchars($git['branch']); ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-secondary font-weight-bold">
            <i class="fab fa-github mr-1"></i> View on GitHub
        </a>
        <?php endif; ?>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="thead-light">
                <tr>
                    <th style="width: 15%;">Commit</th>
                    <th style="width: 50%;">Commit Message</th>
                    <th style="width: 20%;">Author</th>
                    <th style="width: 15%;" class="text-right">Time</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($git['recent_commits'] as $c): ?>
                <tr>
                    <td>
                        <?php if (!empty($web_repo_url)): ?>
                            <a href="<?php echo htmlspecialchars($web_repo_url); ?>/commit/<?php echo htmlspecialchars($c['hash']); ?>" target="_blank" rel="noopener noreferrer" class="badge badge-light border text-monospace font-weight-bold text-primary text-decoration-none px-2 py-1" style="font-size: 11.5px;">
                                <i class="fas fa-code-branch mr-1" style="font-size: 9px;"></i><?php echo htmlspecialchars($c['hash']); ?>
                            </a>
                        <?php else: ?>
                            <span class="badge badge-light border text-monospace font-weight-bold text-dark px-2 py-1" style="font-size: 11.5px;">
                                <?php echo htmlspecialchars($c['hash']); ?>
                            </span>
                        <?php endif; ?>
                    </td>
                    <td class="font-weight-bold text-dark" style="font-size: 13.5px;">
                        <?php echo htmlspecialchars($c['message']); ?>
                    </td>
                    <td class="text-muted small">
                        <i class="fas fa-user-circle mr-1 text-secondary"></i><?php echo htmlspecialchars($c['author']); ?>
                    </td>
                    <td class="text-right text-muted small" title="<?php echo htmlspecialchars($c['date']); ?>">
                        <?php echo htmlspecialchars($c['time']); ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once(__DIR__ . '/includes/footer.php'); ?>
