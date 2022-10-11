# GDPR compliant simple video nodetype for Neos CMS

This plugin will provide a YouTube video element which will not embed the video, but instead 
will automatically retrieve the thumbnail for the video and show it with a play button.
Pressing the button will open a new tab or window with the YouTube video.

## Features

* GDPR-compliant - no external data is loaded in the frontend
* Video thumbnails are retrieved when a video id is changed and stored as asset
* Video thumbnails can be replaced by custom images via the inspector
* Currently only YouTube is supported
* Has no additional dependencies besides Neos itself
* Auto-includes the required CSS file

## How to use custom style

If you want to use a custom style for the video element, you can do so by disabling 
the included styles in your site package.

```neosfusion
prototype(Neos.Neos:Page) {
    head.stylesheets.shelNeosVideo >
}
```

Then you can copy the included CSS file to your own styles. 
You can find them in [Resources/Public/video.css](./Resources/Public/video.css). 

## License

See [License](LICENSE.txt)
