<?php
/**
 * API Client for Caminho Anglicano Calendar API
 *
 * @package Estevao_Liturgical_Calendar
 */

if (!defined('ABSPATH')) {
    exit;
}

class Estevao_Liturgical_API_Client {

    /**
     * API base URL
     */
    const API_BASE_URL = 'https://api.caminhoanglicano.com.br/api/v1/';

    /**
     * Cache duration in seconds (1 hour)
     */
    const CACHE_DURATION = 3600;

    /**
     * Get calendar data for a specific date
     *
     * @param string $date Date in Y-m-d format
     * @return array|WP_Error Calendar data or error
     */
    public function get_calendar($date) {
        $prayer_book = get_option('estevao_liturgical_prayer_book_code', 'loc_2015');
        $bible_version = get_option('estevao_liturgical_bible_version', 'nvi');

        $cache_key = 'estevao_liturgical_cal_' . $date . '_' . $prayer_book . '_' . $bible_version;
        $cached = get_transient($cache_key);

        if (false !== $cached) {
            return $cached;
        }

        $date_parts = explode('-', $date);
        if (count($date_parts) !== 3) {
            return new WP_Error('invalid_date', __('Formato de data inválido', 'estevao-liturgical-calendar'));
        }

        $preferences = wp_json_encode(array(
            'prayer_book_code' => $prayer_book,
            'bible_version' => $bible_version,
        ));

        $url = self::API_BASE_URL . 'calendar/' . $date_parts[0] . '/' . $date_parts[1] . '/' . $date_parts[2];
        $url = add_query_arg('preferences', $preferences, $url);

        $response = $this->make_request($url);

        if (!is_wp_error($response)) {
            set_transient($cache_key, $response, self::CACHE_DURATION);
        }

        return $response;
    }

    /**
     * Get list of prayer books
     *
     * @return array|WP_Error Prayer books data or error
     */
    public function get_prayer_books() {
        $cache_key = 'estevao_liturgical_prayer_books';
        $cached = get_transient($cache_key);

        if (false !== $cached) {
            return $cached;
        }

        $url = self::API_BASE_URL . 'prayer_books';
        $response = $this->make_request($url);

        if (!is_wp_error($response) && isset($response['data'])) {
            set_transient($cache_key, $response['data'], self::CACHE_DURATION * 24);
            return $response['data'];
        }

        return is_wp_error($response) ? $response : array();
    }

    /**
     * Get list of bible versions
     *
     * @return array|WP_Error Bible versions data or error
     */
    public function get_bible_versions() {
        $cache_key = 'estevao_liturgical_bible_versions';
        $cached = get_transient($cache_key);

        if (false !== $cached) {
            return $cached;
        }

        $url = self::API_BASE_URL . 'bible_versions';
        $response = $this->make_request($url);

        if (!is_wp_error($response) && isset($response['data'])) {
            set_transient($cache_key, $response['data'], self::CACHE_DURATION * 24);
            return $response['data'];
        }

        return is_wp_error($response) ? $response : array();
    }

    /**
     * Make HTTP request to the API
     *
     * @param string $url API URL
     * @return array|WP_Error Response data or error
     */
    private function make_request($url) {
        $args = array(
            'timeout' => 15,
            'headers' => array(
                'Accept' => 'application/json',
            ),
        );

        $response = wp_remote_get($url, $args);

        if (is_wp_error($response)) {
            return $response;
        }

        $status_code = wp_remote_retrieve_response_code($response);
        if ($status_code !== 200) {
            return new WP_Error(
                'api_error',
                sprintf(__('Erro na API: código %d', 'estevao-liturgical-calendar'), $status_code)
            );
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return new WP_Error('json_error', __('Erro ao processar resposta da API', 'estevao-liturgical-calendar'));
        }

        return $data;
    }

    /**
     * Clear all cached data
     */
    public function clear_cache() {
        global $wpdb;
        $wpdb->query(
            "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_estevao_liturgical_%' OR option_name LIKE '_transient_timeout_estevao_liturgical_%'"
        );
    }

    /**
     * Calculate date for last Sunday
     *
     * @return string Date in Y-m-d format
     */
    public function get_last_sunday_date() {
        $today = new DateTime('now', wp_timezone());
        $day_of_week = (int) $today->format('w');

        if ($day_of_week === 0) {
            // Today is Sunday, get previous Sunday
            $today->modify('-7 days');
        } else {
            // Get last Sunday
            $today->modify('last Sunday');
        }

        return $today->format('Y-m-d');
    }

    /**
     * Calculate date for next Sunday
     *
     * @return string Date in Y-m-d format
     */
    public function get_next_sunday_date() {
        $today = new DateTime('now', wp_timezone());
        $day_of_week = (int) $today->format('w');

        if ($day_of_week === 0) {
            // Today is Sunday, get next Sunday
            $today->modify('+7 days');
        } else {
            // Get next Sunday
            $today->modify('next Sunday');
        }

        return $today->format('Y-m-d');
    }

    /**
     * Get today's date
     *
     * @return string Date in Y-m-d format
     */
    public function get_today_date() {
        $today = new DateTime('now', wp_timezone());
        return $today->format('Y-m-d');
    }
}
