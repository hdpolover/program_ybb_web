<?php

namespace App\Controllers\landing;

use App\Controllers\BaseController;

class Announcements extends BaseController
{
    public function index()
    {
        // Get query parameters for pagination
        $page = (int)($this->request->getGet('page') ?? 1);
        $perPage = 8; // Number of items per page

        // Build API URL with parameters (without pagination since API doesn't support it)
        $apiUrl = '/landing/announcements?web_url=' . $this->currentUrl;

        // Get all announcements data from API
        $announcementsData = $this->makeGetRequest($apiUrl, [], false);
        $allAnnouncements = $announcementsData['announcements'] ?? [];

        // Default sorting by newest first
        if ((!isset($announcementsData['sorted']) || !$announcementsData['sorted'])) {
            usort($allAnnouncements, function ($a, $b) {
                return strtotime($b['created_at'] ?? 0) - strtotime($a['created_at'] ?? 0);
            });
        }
        // Get total count for pagination
        $total = count($allAnnouncements);

        // Manual pagination
        $offset = ($page - 1) * $perPage;
        $paginatedAnnouncements = array_slice($allAnnouncements, $offset, $perPage);

        $data = [
            'title' => 'Announcements',
            'category' => $announcementsData['category'] ?? [],
            'announcements' => $paginatedAnnouncements,
            'total' => $total,
            'currentPage' => $page,
            'perPage' => $perPage,
            'totalPages' => ceil($total / $perPage),
        ];

        return $this->render('landing/announcements', $data);
    }

    public function detail($identifier)
    {
        $announcementData = $this->makeGetRequest($this->buildDetailEndpoint($identifier));
        $announcement = $announcementData['announcement'] ?? [];

        if (empty($announcement)) {
            return redirect()->to('/announcements')->with('error', 'Announcement not found');
        }

        log_message('info', 'Retrieved landing announcement details for identifier: ' . $identifier);

        $data = [
            'title' => $announcement['title'] ?? 'Announcement Detail',
            'category' => $announcementData['category'] ?? [],
            'announcement' => $announcement,
        ];

        return $this->render('landing/announcement-detail', $data);
    }

    private function buildDetailEndpoint($identifier): string
    {
        $query = http_build_query(['web_url' => $this->currentUrl]);

        if (is_numeric($identifier)) {
            return '/landing/announcements/' . $identifier . '?' . $query;
        }

        return '/landing/announcement-by-slug/' . rawurlencode((string) $identifier) . '?' . $query;
    }
}
