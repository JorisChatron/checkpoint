<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Page non trouvée - 404</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500&display=swap" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #1E1E2F 80%, #7F39FB 100%);
            font-family: 'Orbitron', Arial, sans-serif;
            color: #E0F7FA;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .error-container {
            background: rgba(31, 27, 46, 0.97);
            border-radius: 18px;
            box-shadow: 0 0 32px #7F39FB55;
            padding: 3.5rem 2.5rem 2.5rem 2.5rem;
            text-align: center;
            max-width: 420px;
            width: 95vw;
            margin: 2rem auto;
        }
        .error-code {
            font-size: 5rem;
            color: #7F39FB;
            margin-bottom: 0.5rem;
            text-shadow: 0 2px 16px #7F39FB44;
        }
        .error-title {
            font-size: 2rem;
            color: #9B5DE5;
            margin-bottom: 1.2rem;
        }
        .error-message {
            color: #E0F7FA;
            font-size: 1.1rem;
            margin-bottom: 2.2rem;
        }
        .btn-home {
            background: #7F39FB;
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 0.9rem 2.2rem;
            font-size: 1.1rem;
            font-family: 'Orbitron', sans-serif;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 2px 12px #7F39FB33;
            transition: background 0.2s, color 0.2s, transform 0.2s;
        }
        .btn-home:hover {
            background: #9B5DE5;
            color: #00E5FF;
            transform: scale(1.05);
        }
        @media (max-width: 600px) {
            .error-container {
                padding: 1.5rem 0.5rem;
            }
            .error-code {
                font-size: 3.2rem;
            }
            .error-title {
                font-size: 1.2rem;
            }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-code">404</div>
        <div class="error-title">Page non trouvée</div>
        <div class="error-message">
            <?php if (ENVIRONMENT !== 'production') : ?>
                <?= nl2br(esc($message)) ?>
            <?php else : ?>
                Désolé, la page que vous cherchez n'existe pas ou a été déplacée.
            <?php endif; ?>
        </div>
        <a href="/" class="btn-home">Retour à l'accueil</a>
    </div>
</body>
</html>
