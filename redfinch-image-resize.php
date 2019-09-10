<?php

/*
Plugin Name: Image Resizer
Plugin URI: https://redfinch.dev
Description: Provides helper functions to dynamically resize attachments to arbitrary dimensions.
Version: 1.0.0
Author: James Wigger
Author URI: https://jameswigger.co.uk
License: MIT
License URI: https://opensource.org/licenses/MIT
*/

require __DIR__ . '/lib/RedfinchImageResizer.php';

/**
 * Resizes a given attachment id to fit within a width and height
 *
 * @param int $attachment_id
 * @param int $width
 * @param int $height
 *
 * @return string
 * @throws \Exception
 */
function redfinch_resize_image(int $attachment_id, int $width, int $height): ?string
{
    try {

        $imageResizer = new RedfinchImageResizer($attachment_id, $width, $height);

        return $imageResizer->resize();

    } catch (Exception $e) {

        return null;

    }
}

/**
 * Resizes the post thumbnail to fit within a width and height
 *
 * @param int $width
 * @param int $height
 * @param null $post
 *
 * @return string
 * @throws \Exception
 */
function redfinch_resize_post_thumbnail(int $width, int $height, $post = null): ?string
{
    $attachment_id = get_post_thumbnail_id($post);

    return redfinch_resize_image($attachment_id, $width, $height);
}

/**
 * Resizes and crops an image to exactly match a width and height
 *
 * @param int $attachment_id
 * @param int $width
 * @param int $height
 *
 * @return string
 * @throws \Exception
 */
function redfinch_crop_image(int $attachment_id, int $width, int $height): ?string
{
    try {

        $imageResizer = new RedfinchImageResizer($attachment_id, $width, $height, true);

        return $imageResizer->resize();

    } catch (Exception $e) {

        return null;

    }
}

/**
 * Resizes and crops the post thumbnail to exactly match a width and height
 *
 * @param int $width
 * @param int $height
 * @param null $post
 *
 * @return string
 * @throws \Exception
 */
function redfinch_crop_post_thumbnail(int $width, int $height, $post = null): ?string
{
    $attachment_id = get_post_thumbnail_id($post);

    return redfinch_crop_image($attachment_id, $width, $height);
}
