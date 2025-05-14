<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Footer LMS - EdaaLearning</title>
    <style>
        /* Police personnalisée (Google Fonts) */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap');

        /* Style général du footer */
        .footer-area {
            font-family: 'Poppins', sans-serif;
            background-color: #1a1a1a;
            color: #ffffff;
            padding-top: 100px;
            padding-bottom: 20px;
            min-height: 350px;
            max-height: 350px;
            box-sizing: border-box;
            overflow: hidden;
        }

        .footer__logo {
            max-width: 150px;
            max-height: 60px;
            width: auto;
            object-fit: contain;
        }

        .contact-list {
            list-style: none;
            padding-left: 0;
        }

        .contact-list li {
            margin-bottom: 10px;
            font-size: 1rem;
        }

        .contact-list a {
            color: #ffffff;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .contact-list a:hover {
            color: #EC5252;
        }

        .tagline {
            text-align: center;
        }

        .highlight-text {
            font-size: 2rem;
            font-weight: 700;
            color: #EC5252;
            text-transform: uppercase;
            line-height: 1.2;
            margin: 0;
        }

        .section-block {
            border-top: 1px solid #333333;
            margin: 20px 0;
        }

        .copy-desc {
            font-size: 0.9rem;
            margin: 0;
        }

        .copy-desc a {
            color: #EC5252;
            text-decoration: none;
        }

        .copy-desc a:hover {
            color: #ffffff;
        }

        .footer-links {
            list-style: none;
            padding-left: 0;
            margin: 0;
            display: flex;
            justify-content: flex-end;
        }

        .footer-links li {
            margin-left: 20px;
            font-size: 0.9rem;
        }

        .footer-links a {
            color: #ffffff;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-links a:hover {
            color: #EC5252;
        }

        /* Responsive */
        @media (max-width: 991px) {
            .footer-area {
                min-height: 400px;
                max-height: 400px;
            }

            .highlight-text {
                font-size: 1.5rem;
            }

            .footer-links {
                justify-content: center;
                flex-wrap: wrap;
            }

            .footer-links li {
                margin: 5px 10px;
            }

            .responsive-column-half {
                text-align: center;
                margin-bottom: 20px;
            }
        }

        @media (max-width: 767px) {
            .footer-area {
                min-height: 450px;
                max-height: 450px;
                padding-top: 50px;
            }

            .highlight-text {
                font-size: 1.2rem;
            }
        }
    </style>
</head>
<body>
    <section class="footer-area">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-4 responsive-column-half">
                    <div class="footer-item">
                        <a href="{{ url('/') }}" class="logo">
                            <img src="{{ $siteSettings->logo ? Storage::url($siteSettings->logo) : asset('images/default-logo.png') }}"
                                 alt="Logo" class="lazy footer__logo" loading="lazy"
                                 onerror="this.src='{{ asset('images/default-logo.png') }}'">
                        </a>
                        <h3 style="color: #EC5252;">CONTACT US</h3>
                        <ul class="contact-list pt-4">
                            <li><a href="tel:{{ preg_replace('/[^0-9+]/', '', $siteSettings->phone ?? '+1234567890') }}">{{ $siteSettings->phone ?? '+1234567890' }}</a></li>
                            <li><a href="mailto:{{ $siteSettings->email ?? 'contact@EdaaLearning.com' }}">{{ $siteSettings->email ?? 'contact@EdaaLearning.com' }}</a></li>
                            <li>Tunis, Tunisie</li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="footer-item tagline">
                        <p class="highlight-text">DÉCOUVREZ UNE EXPÉRIENCE D'APPRENTISSAGE EN LIGNE SIMPLE ET EFFICACE AVEC EdaaLearning</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="section-block"></div>
        <div class="copyright-content py-4">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <p class="copy-desc">{{ $siteSettings->copyright ?? '© 2025 EdaaLearning. All Rights Reserved. by lmspfee' }}</p>
                    </div>
                    <div class="col-lg-6">
                        <ul class="footer-links">
                            <li><a href="">Politique de confidentialité</a></li>
                            <li><a href="">Conditions d'utilisation</a></li>
                            <li><a href="">Contact</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>
</html>