
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Loneed - Back Office</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: 'Inter', sans-serif;
                background: #000;
                color: #fff;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 2rem;
            }

            .container {
                text-align: center;
                max-width: 600px;
            }

            .logo {
                font-size: 4rem;
                font-weight: 700;
                letter-spacing: -0.05em;
                margin-bottom: 1rem;
                background: linear-gradient(135deg, #fff 0%, #999 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }

            .subtitle {
                font-size: 1.25rem;
                color: #666;
                margin-bottom: 3rem;
                font-weight: 400;
            }

            .btn {
                display: inline-block;
                padding: 1rem 3rem;
                background: #fff;
                color: #000;
                text-decoration: none;
                border-radius: 8px;
                font-weight: 600;
                font-size: 1rem;
                transition: all 0.3s;
                border: 2px solid #fff;
            }

            .btn:hover {
                background: transparent;
                color: #fff;
                transform: translateY(-3px);
            }

            .info {
                margin-top: 4rem;
                padding-top: 2rem;
                border-top: 1px solid #222;
            }

            .info-item {
                display: inline-block;
                margin: 0 1.5rem;
                color: #666;
                font-size: 0.9rem;
            }

            .info-item a {
                color: #999;
                text-decoration: none;
                transition: color 0.3s;
            }

            .info-item a:hover {
                color: #fff;
            }

            @media (max-width: 640px) {
                .logo {
                    font-size: 3rem;
                }

                .subtitle {
                    font-size: 1rem;
                }

                .info-item {
                    display: block;
                    margin: 0.5rem 0;
                }
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1 class="logo">LONEED</h1>
            <p class="subtitle">Panneau d'administration</p>
            
            <a href="{{ url('/admin/login') }}" class="btn">Accéder au Back Office →</a>

            <div class="info">
                <div class="info-item">
                    <a href="" target="_blank">Documentation API</a>
                </div>
                <div class="info-item">
                    {{ date('Y') }} © Loneed
                </div>
            </div>
        </div>
    </body>
</html>


