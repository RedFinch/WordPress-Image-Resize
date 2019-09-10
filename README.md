# WordPress Image Resize

WordPress can be difficult to work with when using images. Especially if you want to have multiple image sizes
available for templates, the result is either putting the resizing responsibility on end users or having an extremely
large uploads directory.

This plugin aims to solve this issue, simply and without any additional libraries. It will automatically add your custom
sizes to the Attachment meta data which means on deletion, all of the arbitrary sizes will be removed as well.

## Installation

If you are using `roots/bedrock` or a variant of this, the installation is simply to 
run `composer require redfinch/redfinch-image-resize`.

To install manually go to the releases page and download the latest version. Unzip the file into your WordPress plugins
directory.

## Using the plugin

The plugin is aimed at developers and therefore there is no admin interface. The plugin exposes four main functions:

### redfinch\_resize\_image

This resizes an image proportionally to fit X and Y values.

```php
<img src="<?php echo redfinch_resize_image($attachment_id, 320, 120); ?>" />
```

### redfinch\_resize\_post\_thumbnail

This is a helper function to resize the featured image of the current post. You can optionally pass through
a `WP_Post` or post ID value as the last parameter to display the image from a specific post or page.

```php
<img src="<?php echo redfinch_resize_post_thumbnail(320, 120); ?>" />
```

### redfinch\_crop\_image

This is very similar to the `resize()` function, however it will crop the image to fit ensuring that
the image is always going to be the given dimensions.

```php
<img src="<?php echo redfinch_crop_image($attachment_id, 450, 450); ?>" />
```

### redfinch\_crop\_post\_thumbnail

Identical to `resize()`, instead ensuring the featured image is always the given dimensions. You can optionally pass through
a `WP_Post` or post ID value as the last parameter to display the image from a specific post or page.

```php
<img src="<?php echo redfinch_resize_post_thumbnail(320, 120); ?>" />
```

## Hooks

### Filters

#### redfinch\_image\_resize\_get\_path

Returns the generated path to the resized image. The value passed through is a string and
it expects a string to be returned.

#### redfinch\_image\_resize\_get\_url

Returns the generated URL to the resized image. The value passed through is a string and
it expects a string to be returned.

### Actions

#### redfinch\_image\_resize\_pre\_generate\_image

This action triggers before the `resize()` method is called on the `WP_Image_Editor` object. The single parameter
is the Image Editor instance.

#### redfinch\_image\_resize\_post\_generate\_image

This action triggers after the `resize()` method is called on the `WP_Image_Editor` object. The single parameter
is the Image Editor instance.
