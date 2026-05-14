<?php

namespace App\Services;

use CodeIgniter\Cache\CacheInterface;

class CacheService
{
    protected $cache;
    protected $defaultTTL = 900; // 15 minutes default

    public function __construct()
    {
        $this->cache = \Config\Services::cache();
    }

    /**
     * Get cached data or fetch from API if not cached
     */
    public function remember(string $key, callable $callback, int $ttl = null): mixed
    {
        $ttl = $ttl ?? $this->defaultTTL;
        
        // Try to get from cache first
        $cached = $this->cache->get($key);
        if ($cached !== null) {
            log_message('debug', "Cache HIT for key: {$key}");
            return $cached;
        }

        // Not in cache, execute callback and cache result
        log_message('debug', "Cache MISS for key: {$key}");
        $result = $callback();
        
        if ($result !== null) {
            $this->cache->save($key, $result, $ttl);
        }
        
        return $result;
    }

    /**
     * Cache key generators for different data types
     */
    public function getWebSettingsKey(string $domain): string
    {
        $safeDomain = str_replace(['.', ':', '/', '\\', '@', '-'], '_', $domain);
        return "web_settings_{$safeDomain}_v1";
    }

    public function getProgramsCategoryKey(int $categoryId): string
    {
        return "programs_category_{$categoryId}_v1";
    }

    public function getParticipantPaymentsKey(int $participantId): string
    {
        return "payments_participant_{$participantId}_v1";
    }

    public function getParticipantStatusKey(int $participantId): string
    {
        return "participant_status_{$participantId}_v1";
    }

    public function getUserParticipantsKey(int $userId): string
    {
        return "participant_user_{$userId}_v1";
    }

    public function getProgramDetailsKey(int $programId): string
    {
        return "program_details_{$programId}_v1";
    }

    public function getLandingDataKey(string $page, string $domain): string
    {
        $safeDomain = str_replace(['.', ':', '/', '\\', '@', '-'], '_', $domain);
        $safePage = str_replace(['.', ':', '/', '\\', '@', '-'], '_', $page);
        return "landing_{$safePage}_{$safeDomain}_v1";
    }

    /**
     * Clear specific cache patterns
     */
    public function clearUserCache(int $userId): void
    {
        $this->cache->delete($this->getUserParticipantsKey($userId));
        // Clear all participant-specific caches for this user
        // You might need to track participant IDs for this user
    }

    public function clearParticipantCache(int $participantId): void
    {
        $this->cache->delete($this->getParticipantPaymentsKey($participantId));
        $this->cache->delete($this->getParticipantStatusKey($participantId));
    }

    public function clearProgramCache(int $programId): void
    {
        $this->cache->delete($this->getProgramDetailsKey($programId));
    }

    public function clearCategoryCache(int $categoryId): void
    {
        $this->cache->delete($this->getProgramsCategoryKey($categoryId));
    }

    /**
     * Get cache statistics
     */
    public function getStats(): array
    {
        // This would depend on your cache driver
        // For Redis, you could get more detailed stats
        return [
            'driver' => get_class($this->cache),
            'timestamp' => time()
        ];
    }
}
