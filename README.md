# konkanant.auth

## Installation Instructions

1. Download and install [stakx](https://github.com/stakx-io/stakx)
1. Use npm to install the required front-end packages (make sure you have [a recent version of npm and node.js](https://www.digitalocean.com/community/tutorials/how-to-install-node-js-on-ubuntu-16-04)):

    ```bash
    npm install
    ```

1. (Optional) You can install gulp globally for easier access:
    ```bash
    npm install --global gulp-cli
    ```

## Generation Instructions
To generate the final HTML files, use the following commands:

1. `gulp sass`
2. `/path/to/bin/stakx build`

You can use the `watch` directive for either of these commands to automatically build your website.

To test your generated content, run a web server
(e.g. `php -S localhost:8000`) in the *_site* directory,
and browse [http://localhost:8000/konkanant](http://localhost:8000/konkanant).
