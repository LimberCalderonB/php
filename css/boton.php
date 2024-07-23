
    <style>
    html, body {
    height: 50%;
    }

    body {
    text-align: center;
    }

    body:before {
    content: '';
    height: 50%;
    display: inline-block;
    vertical-align: middle;
    }

    .animated-btn {
    background: #1AAB8A;
    color: #fff;
    border: none;
    position: relative;
    height: 45px;
    font-size: 1.6em;
    padding: 0 2em;
    cursor: pointer;
    transition: 800ms ease all;
    outline: none;
    display: inline-block;
    text-decoration: none; /* Elimina el subrayado del enlace */
    }

    .animated-btn:hover {
    background: #fff;
    color: #1AAB8A;
    }

    .animated-btn:before, .animated-btn:after {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    height: 2px;
    width: 0;
    background: #1AAB8A;
    transition: 400ms ease all;
    }

    .animated-btn:after {
    right: inherit;
    top: inherit;
    left: 0;
    bottom: 0;
    }

    .animated-btn:hover:before, .animated-btn:hover:after {
    width: 100%;
    transition: 800ms ease all;
    }
    </style>