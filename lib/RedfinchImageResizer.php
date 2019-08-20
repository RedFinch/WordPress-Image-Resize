<?php

/**
 * Class RedfinchImageResizer
 */
class RedfinchImageResizer
{
    /**
     * @var int
     */
    private $attachment_id;

    /**
     * @var array|bool
     */
    private $attachment;

    /**
     * @var array
     */
    private $uploadDir;

    /**
     * @var int
     */
    private $width;

    /**
     * @var int
     */
    private $height;

    /**
     * @var bool
     */
    private $crop;

    /**
     * RedfinchImageResizer constructor.
     *
     * @param int $attachment_id
     * @param int $width
     * @param int $height
     * @param bool $crop
     */
    public function __construct(int $attachment_id, int $width, int $height, bool $crop = false)
    {
        $this->attachment_id = $attachment_id;
        $this->width = absint($width);
        $this->height = absint($height);
        $this->crop = $crop;

        $this->attachment = wp_get_attachment_metadata($attachment_id);
        $this->uploadDir = wp_upload_dir();

        if (!$this->attachment || !file_exists($this->getSourceFilePath())) {
            throw new \InvalidArgumentException(
                __('Invalid attachment ID', 'redfinch-image-resize')
            );
        }
    }

    /**
     * Returns a URL to the resized image, generating
     * a new version on the fly if it cannot be found
     *
     * @param bool $force - always generate a new image
     *
     * @return string
     * @throws \Exception
     */
    public function resize(bool $force = false): string
    {
        if (!file_exists($this->getResizedFilePath()) || $force) {
            $this->generateResizedImage();
        }

        return $this->getResizedUrl();
    }

    /**
     * Returns a path to the non-modified source file
     *
     * @return string
     */
    private function getSourceFilePath(): string
    {
        return $this->makePath($this->uploadDir['basedir'], $this->attachment['file']);
    }

    /**
     * Returns the path to the resized image
     *
     * @return string
     */
    private function getResizedFilePath(): string
    {
        $filename = preg_replace(
            '/(\.[^\.]+)$/',
            '-' . $this->getSizeKey() . '$1',
            basename($this->attachment['file'])
        );

        $subdirectory = preg_replace('/\/?[^\/]+\z/', '', $this->attachment['file']);

        $path = $this->makePath($this->uploadDir['basedir'], $subdirectory, $filename);

        return apply_filters('redfinch_image_resize_get_path', $path);
    }

    /**
     * Returns the URL to the resized image
     *
     * @return string
     */
    private function getResizedUrl(): string
    {
        $url = str_replace(
            [$this->uploadDir['basedir'], DIRECTORY_SEPARATOR],
            [$this->uploadDir['baseurl'], '/'],
            $this->getResizedFilePath()
        );

        return apply_filters('redfinch_image_resize_get_url', $url);
    }

    /**
     * Uses the WordPress image editing API to generate
     * a new resized image and update the sizes array
     * of the source attachment
     *
     * @throws \Exception
     */
    private function generateResizedImage(): void
    {
        $editor = wp_get_image_editor(
            $this->getSourceFilePath()
        );

        if (is_wp_error($editor)) {
            throw new \Exception($editor->get_error_message());
        }

        do_action('redfinch_image_resize_pre_generate_image', $editor);

        $result = $editor->resize($this->width, $this->height, $this->crop);

        do_action('redfinch_image_resize_post_generate_image', $editor);

        if (is_wp_error($result)) {
            throw new \Exception($editor->get_error_message());
        }

        $properties = $editor->save(
            $this->getResizedFilePath()
        );

        if (is_wp_error($properties)) {
            throw new \Exception($editor->get_error_message());
        }

        $this->attachment['sizes'][$this->getSizeKey()] = $properties;

        wp_update_attachment_metadata($this->attachment_id, $this->attachment);
    }

    /**
     * Returns a formatted string describing the size manipulations
     *
     * @return string
     */
    private function getSizeKey(): string
    {
        return $this->width . 'x' . $this->height . ($this->crop ? '-cropped' : '');
    }

    /**
     * Combines parameters into a single file path
     *
     * @param string ...$parts
     *
     * @return string
     */
    private function makePath(string ...$parts): string
    {
        return implode(DIRECTORY_SEPARATOR, $parts);
    }
}
