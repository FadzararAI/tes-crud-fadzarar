# Tailwind browser compiler

`tailwind-browser.js` is the unmodified @tailwindcss/browser 4.1.18 distribution from:

https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4.1.18/dist/index.global.js

Tailwind CSS is MIT licensed: https://github.com/tailwindlabs/tailwindcss/blob/v4.1.18/LICENSE

It compiles the Tailwind styles in the browser for this local assessment without an npm/build setup. The PHP header inlines `assets/style.css` as `text/tailwindcss`. Keep this vendor file unchanged; edit templates and style.css instead.
