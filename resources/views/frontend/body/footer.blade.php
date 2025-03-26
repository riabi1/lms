<!DOCTYPE html>
<html lang="fr">

<head>
 
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Footer LMS - EasyLearning</title>
    <style>
        /* Police personnalisée (Google Fonts) */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap');

        /* Style général du footer */
        .footer-area {
            font-family: 'Poppins', sans-serif; /* Police moderne et élégante */
            background-color: #0e2552; /* Fond sombre pour contraste */
            color: #ffffff; /* Texte blanc pour lisibilité */
            padding-top: 100px;
        }

        .footer__logo {
            max-width: 150px; /* Ajustez selon la taille de votre logo */
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
            color: #d3442a; /* Couleur au survol */
        }

        .tagline {
            text-align: center; /* Centrer la phrase */
        }

        .highlight-text {
            font-size: 2.5rem; /* Taille grande pour attirer l'attention */
            font-weight: 700; /* Gras */
            color: #d3442a; /* Couleur vive (cyan) */
            text-transform: uppercase; /* Majuscules pour impact */
            line-height: 1.2;
            margin: 0;
        }

        .section-block {
            border-top: 1px solid soft blue; /* Séparateur subtil */
            margin: 20px 0;
        }

        .copy-desc {
            font-size: 0.9rem;
            margin: 0;
        }

        .copy-desc a {
            color: #d3442a;
            text-decoration: none;
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

        

        /* Responsive */
        @media (max-width: 991px) {
            .highlight-text {
                font-size: 1.5rem; /* Réduire la taille sur petits écrans */
            }
            .footer-links {
                justify-content: center !important; /* Centrer les liens en mobile */
                flex-wrap: wrap;
            }
            .footer-links li {
                margin: 5px 10px;
            }
        }
    </style>

  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Footer LMS - EasyLearning</title>
  <style>
    /* Police personnalisée (Google Fonts) */
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap');

    /* Style général du footer */
    .footer-area {
      font-family: 'Poppins', sans-serif;
      /* Police moderne et élégante */
      background-color: #1a1a1a;
      /* Fond sombre pour contraste */
      color: #ffffff;
      /* Texte blanc pour lisibilité */
      padding-top: 100px;
    }

    .footer__logo {
      max-width: 150px;
      /* Ajustez selon la taille de votre logo */
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
      color: #2d63a3;
      /* Couleur au survol */
    }

    .tagline {
      text-align: center;
      /* Centrer la phrase */
    }

    .highlight-text {
      font-size: 2rem;
      /* Taille grande pour attirer l'attention */
      font-weight: 700;
      /* Gras */
      color: #2d63a3;
      /* Couleur vive (cyan) */
      text-transform: uppercase;
      /* Majuscules pour impact */
      line-height: 1.2;
      margin: 0;
    }

    .section-block {
      border-top: 1px solid #333333;
      /* Séparateur subtil */
      margin: 20px 0;
    }

    .copy-desc {
      font-size: 0.9rem;
      margin: 0;
    }

    .copy-desc a {
      color: #2d63a3;
      text-decoration: none;
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
      color: #2d63a3;
    }

    /* Responsive */
    @media (max-width: 991px) {
      .highlight-text {
        font-size: 1.5rem;
        /* Réduire la taille sur petits écrans */
      }

      .footer-links {
        justify-content: center !important;
        /* Centrer les liens en mobile */
        flex-wrap: wrap;
      }

      .footer-links li {
        margin: 5px 10px;
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
            <a href="index.html">
              <img src="{{ asset('frontend/images/logo.png') }}" alt="footer logo" class="footer__logo">
            </a>
            <h3 style="color: #EC5252;">CONTACT US </h3>
            <ul class="contact-list pt-4">
              <li><a href="tel:+21628587753">+216 28-587-753</a></li>
              <li><a href="mailto:lmspfee@gmail.com">lmspfee@gmail.com</a></li>
              <li>Tunis, Tunisie</li>
            </ul>
          </div><!-- end footer-item -->
        </div><!-- end col-lg-4 -->
        <div class="col-lg-8">
          <div class="footer-item tagline">
            <p class="highlight-text" style="color: #EC5252;">DÉCOUVREZ UNE EXPÉRIENCE D'APPRENTISSAGE EN LIGNE SIMPLE ET EFFICACE AVEC EASYLEARNING</p>
          </div><!-- end footer-item -->
        </div><!-- end col-lg-8 -->
      </div><!-- end row -->
    </div><!-- end container -->
    <div class="section-block"></div>
    <div class="copyright-content py-4">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-lg-6">
            <p class="copy-desc">© 2025 EasyLearning. All Rights Reserved. by lmspfee</p>
          </div><!-- end col-lg-6 -->
        </div><!-- end row -->
      </div><!-- end container -->
    </div><!-- end copyright-content -->
  </section><!-- end footer-area -->
</body>

</html>