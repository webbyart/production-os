<?php
/**
 * Helper Functions
 * Common utility functions
 */

/**
 * Escape output for XSS protection
 */
function escape($string)
{
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Get base URL
 */
function baseUrl($path = '')
{
    return BASE_URL . $path;
}

/**
 * Redirect to URL
 */
function redirect($path = '')
{
    header('Location: ' . baseUrl($path));
    exit;
}

/**
 * Set flash message
 */
function setFlash($type, $message)
{
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Get and clear flash message
 */
function getFlash()
{
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Format currency
 */
function formatCurrency($amount)
{
    return '$' . number_format($amount, 2);
}

/**
 * Format date
 */
function formatDate($date, $format = 'd/m/Y')
{
    return date($format, strtotime($date));
}

/**
 * Format datetime
 */
function formatDateTime($datetime, $format = 'd/m/Y H:i')
{
    return date($format, strtotime($datetime));
}

/**
 * Get time ago
 */
function timeAgo($datetime)
{
    $timestamp = strtotime($datetime);
    $currentTime = time();
    $diff = $currentTime - $timestamp;

    if ($diff < 60) {
        return $diff . ' seconds ago';
    } elseif ($diff < 3600) {
        $minutes = floor($diff / 60);
        return $minutes . ' minute' . ($minutes > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    } else {
        $weeks = floor($diff / 604800);
        return $weeks . ' week' . ($weeks > 1 ? 's' : '') . ' ago';
    }
}

/**
 * Generate unique code
 */
function generateUniqueCode($prefix = '', $length = 8)
{
    $code = bin2hex(random_bytes($length / 2));
    return strtoupper($prefix . substr($code, 0, $length));
}

/**
 * Validate email
 */
function isValidEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Sanitize input
 */
function sanitizeInput($input)
{
    return trim(strip_tags($input));
}

/**
 * Get status badge
 */
function getStatusBadge($status)
{
    $badges = [
        'active' => '<span class="badge bg-success">Active</span>',
        'inactive' => '<span class="badge bg-danger">Inactive</span>',
        'draft' => '<span class="badge bg-secondary">Draft</span>',
        'pending' => '<span class="badge bg-warning">Pending</span>',
        'production' => '<span class="badge bg-info">Production</span>',
        'qc' => '<span class="badge bg-primary">QC</span>',
        'finished' => '<span class="badge bg-success">Finished</span>',
        'closed' => '<span class="badge bg-dark">Closed</span>',
        'pass' => '<span class="badge bg-success">Pass</span>',
        'fail' => '<span class="badge bg-danger">Fail</span>',
        'hold' => '<span class="badge bg-warning">Hold</span>',
    ];

    return $badges[$status] ?? '<span class="badge bg-secondary">' . $status . '</span>';
}

/**
 * Get priority badge
 */
function getPriorityBadge($priority)
{
    $badges = [
        'low' => '<span class="badge bg-info">Low</span>',
        'medium' => '<span class="badge bg-warning">Medium</span>',
        'high' => '<span class="badge bg-danger">High</span>',
        'urgent' => '<span class="badge bg-danger" style="animation: pulse 1s infinite;">Urgent</span>',
    ];

    return $badges[$priority] ?? '<span class="badge bg-secondary">' . $priority . '</span>';
}

/**
 * Check if array is associative
 */
function isAssociativeArray($array)
{
    return array_keys($array) !== range(0, count($array) - 1);
}

/**
 * Convert array to query string
 */
function arrayToQueryString($array)
{
    return http_build_query($array);
}

/**
 * Log activity
 */
function logActivity($action, $description, $module, $relatedId = null)
{
    $auth = new Auth();
    if (!$auth->isLoggedIn()) {
        return false;
    }

    $activityLog = new ActivityLog();
    return $activityLog->insert([
        'user_id' => $auth->getUserId(),
        'action' => $action,
        'description' => $description,
        'related_module' => $module,
        'related_id' => $relatedId
    ]);
}

/**
 * Get file extension
 */
function getFileExtension($filename)
{
    return pathinfo($filename, PATHINFO_EXTENSION);
}

/**
 * Check if file is image
 */
function isImageFile($filename)
{
    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $extension = strtolower(getFileExtension($filename));
    return in_array($extension, $imageExtensions);
}

/**
 * Generate random color
 */
function randomColor()
{
    return '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
}

/**
 * Truncate string
 */
function truncate($string, $length = 100, $suffix = '...')
{
    if (strlen($string) <= $length) {
        return $string;
    }
    return substr($string, 0, $length) . $suffix;
}
