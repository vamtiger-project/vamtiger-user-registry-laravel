<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">

        <title>User Registry</title>

        <style>
            html,
            body
            {
                margin: 0;
                padding: 0;
            }

            body
            {
                width: 100vw;
                height: 100vh;
                position: relative;
            }

            vamtiger-user-registry
            {
                width: 100%;
                height: 100%;
                display: inline-block;
            }

            vamtiger-user-registry
            {
                opacity: 0;
            }

            vamtiger-user-registry[data-connected]
            {
                opacity: 1;
            }

            vamtiger-user-registry[data-connected] img
            {
                object-fit: cover;
            }
        </style>

        <script
            src="https://unpkg.com/vamtiger-browser-support@latest/build/vamtiger-browser-support.js"
            data-es2015-support-primary
            data-element-query-support
            data-web-component-support
            data-fetch-support
            data-load="https://cdn.jsdelivr.net/npm/vamtiger-user-registry@latest/build/vamtiger-user-registry.js"
        ></script>
    </head>

    <body>
        <vamtiger-user-registry
            data-test-mode
            data-get-users-url="/api/get-users"
            data-get-user-url="/api/get-user"
            data-add-new-user-url="/api/add-new-user"
            data-delete-user-url="/api/delete-user"
        >
            <img slot="header-image" src="image/Everlytic-Home-Page-HeroImage.jpg">
            <div slot="header-caption">
                User Registry
            </div>
        </vamtiger-user-registry>
    </body>
</html>