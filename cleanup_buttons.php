<?php
// Cleanup script to remove residual sidebar-toggle buttons
$phpDir = __DIR__ . '/php';

// Get all PHP files
$files = glob($phpDir . '/*.php');

foreach ($files as $filePath) {
    if (is_file($filePath)) {
        $content = file_get_contents($filePath);
        $original = $content;
        
        // Regex to match the sidebar toggle button block with various whitespace possibilities
        // Matches: <button class="sidebar-toggle" ...> ... </button>
        $pattern = '/\s*<button class="sidebar-toggle" id="sidebarToggle"[^>]*>[\s\S]*?<\/button>/';
        
        $content = preg_replace($pattern, '', $content);
        
        if ($content !== $original) {
            file_put_contents($filePath, $content);
            echo "Cleaned: " . basename($filePath) . "\n";
        }
    }
}
echo "Cleanup completed.\n";
?>
