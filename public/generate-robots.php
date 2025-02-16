<?php
// public/generate-robots.php

// Get current hostname
$hostname = $_SERVER['HTTP_HOST'] ?? 'localhost:8080';

// Create robots.txt content
$content = "User-agent: *\nDisallow:\n\nSitemap: https://{$hostname}/sitemap.xml";

// Set content type
header('Content-Type: text/plain');

// Output robots.txt content
echo $content;