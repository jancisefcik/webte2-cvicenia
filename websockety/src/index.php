<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatsockets</title>
    <style>
        html {
            max-width: 70ch;
            padding: 3em 1em;
            margin: auto;
            line-height: 1.75;
            font-size: 1.25em;
        }

        h1,h2,h3,h4,h5,h6 {
            margin: 3em 0 1em;
        }

        p,ul,ol {
            margin-bottom: 2em;
            color: #1d1d1d;
            font-family: sans-serif;
        }
    </style>
    <link rel="stylesheet" href="css/pico.min.css">
    <link rel="stylesheet" href="css/pico.colors.min.css">

</head>
<body>
    <header>
        <h1>Chat a.k.a. "Hello world" websocketov</h1>
    </header>

    <main> 
        <p>Tvoj alias je <span class="pico-color-cyan-500" id="alias"></span></p>
        <div id="msg-block">
        </div>
        <div>
            <textarea id="msg-text" name="bio" placeholder="Tu mozete pisat spravu..."></textarea>
            <button disabled="true" id="send" onclick="sendMessage()">Odosli spravu!</button>
        </div>
    </main>    

    <script src="main.js"></script>
</body>
</html>