<?php

if (!function_exists('get_version_by_tag')) {
    function get_version_by_tag(string $tag): string
    {
        return str_replace('v', '', $tag);
    }
}
