<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wiz Óptica</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;500;600;700;800&family=Comfortaa:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Nunito', sans-serif;
            background: linear-gradient(135deg, #f7f3e9 0%, #e8f4f8 100%);
            color: #333;
            line-height: 1.6;
        }

        /* Variáveis de cores */
        :root {
            --azul-escuro: #1e3a8a;
            --azul-claro: #3b82f6;
            --azul-medio: #2563eb;
            --branco: #ffffff;
            --cinza-claro: #f8fafc;
            --cinza-medio: #64748b;
            --sombra: rgba(30, 58, 138, 0.1);
        }

        /* Animações */
        @keyframes float {
            0%, 100% {
                transform: translateY(0px) rotate(0deg);
            }
            50% {
                transform: translateY(-20px) rotate(5deg);
            }
        }

        @keyframes sway {
            0%, 100% {
                transform: translateX(0px) rotate(0deg);
            }
            50% {
                transform: translateX(10px) rotate(2deg);
            }
        }

        @keyframes drift {
            0% {
                transform: translateY(-100px) translateX(0px) rotate(0deg);
            }
            100% {
                transform: translateY(calc(100vh + 100px)) translateX(50px) rotate(360deg);
            }
        }

        @keyframes sparkle {
            0%, 100% {
                opacity: 0.3;
                transform: scale(1);
            }
            50% {
                opacity: 1;
                transform: scale(1.2);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes pulse-glow {
            0%, 100% {
                box-shadow: 0 0 20px rgba(59, 130, 246, 0.4);
            }
            50% {
                box-shadow: 0 0 30px rgba(59, 130, 246, 0.6);
            }
        }

        /* Elementos flutuantes */
        .floating-elements {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1;
            overflow: hidden;
        }

        .leaf {
            position: absolute;
            width: 20px;
            height: 20px;
            background: var(--azul-claro);
            border-radius: 0 100% 0 100%;
            animation: drift 15s linear infinite;
            opacity: 0.6;
        }

        .leaf:nth-child(2n) {
            background: #a4c3a2;
            animation-duration: 20s;
            animation-delay: -5s;
        }

        .leaf:nth-child(3n) {
            background: #f4a261;
            animation-duration: 18s;
            animation-delay: -10s;
        }

        .otica-cloud {
            position: absolute;
            background: rgba(248, 248, 248, 0.9);
            border-radius: 50px;
            animation: cloud-drift 30s linear infinite;
            opacity: 0.8;
            box-shadow: 0 4px 20px rgba(248, 248, 248, 0.3);
        }

        @keyframes cloud-drift {
            0% {
                transform: translateX(-200px);
            }
            100% {
                transform: translateX(calc(100vw + 200px));
            }
        }

        .otica-cloud::before,
        .otica-cloud::after {
            content: '';
            position: absolute;
            background: rgba(248, 248, 248, 0.9);
            border-radius: 50px;
        }

        .otica-cloud.small {
            width: 60px;
            height: 30px;
        }

        .otica-cloud.small::before {
            width: 40px;
            height: 40px;
            top: -15px;
            left: 10px;
        }

        .otica-cloud.small::after {
            width: 30px;
            height: 30px;
            top: -10px;
            right: 10px;
        }

        /* Header */
        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            z-index: 1000;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 20px var(--sombra);
        }

        .logo {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--azul-claro);
            text-decoration: none;
            font-family: 'Comfortaa', cursive;
        }

        .entrar-btn {
            background: linear-gradient(135deg, var(--azul-claro) 0%, var(--azul-medio) 100%);
            color: white;
            text-decoration: none;
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
        }

        .entrar-btn:hover {
            background: linear-gradient(135deg, var(--azul-medio) 0%, var(--azul-escuro) 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
            color: white;
        }

        /* Hero Section com imagem de background */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 2rem;
            background: url('img/1.png') center center/cover no-repeat;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(30, 58, 138, 0.8) 0%, rgba(37, 99, 235, 0.7) 50%, rgba(59, 130, 246, 0.6) 100%);
            z-index: 1;
        }

        .hero-content {
            max-width: 800px;
            z-index: 3;
            position: relative;
        }

        .hero h1 {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            line-height: 1.2;
            font-family: 'Comfortaa', cursive;
        }

        .hero h1 span {
            color: #fbbf24;
        }

        .hero p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            opacity: 0.9;
        }

        .cta-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            padding: 1rem 2rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: white;
            color: var(--azul-claro);
        }

        .btn-primary:hover {
            background: #f8fafc;
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(255, 255, 255, 0.3);
        }

        .btn-secondary {
            background: transparent;
            color: white;
            border: 2px solid white;
        }

        .btn-secondary:hover {
            background: white;
            color: var(--azul-claro);
            transform: translateY(-2px);
        }

        /* Produtos Section */
        .products-section {
            padding: 5rem 2rem;
            background: var(--cinza-claro);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .section-title {
            text-align: center;
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--azul-claro);
            margin-bottom: 3rem;
            font-family: 'Comfortaa', cursive;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
            margin-bottom: 4rem;
        }

        .product-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            border: 1px solid #e2e8f0;
            position: relative;
        }

        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            border-color: var(--azul-claro);
        }

        .product-image {
            width: 100%;
            height: 300px;
            object-fit: cover;
            background: linear-gradient(135deg, var(--azul-claro) 0%, var(--azul-medio) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 4rem;
            position: relative;
            overflow: hidden;
        }

        .product-image::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, transparent 30%, rgba(255, 255, 255, 0.1) 50%, transparent 70%);
            animation: shimmer 2s infinite;
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        .product-content {
            padding: 2rem;
        }

        .product-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--azul-escuro);
            margin-bottom: 1rem;
            font-family: 'Comfortaa', cursive;
        }

        .product-description {
            color: var(--cinza-medio);
            font-size: 1rem;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }

        .product-features {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .feature-tag {
            background: var(--azul-claro);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .feature-tag:hover {
            background: var(--azul-medio);
            transform: scale(1.05);
        }

        .product-btn {
            background: linear-gradient(135deg, var(--azul-claro) 0%, var(--azul-medio) 100%);
            color: white;
            text-decoration: none;
            padding: 0.75rem 1.5rem;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
        }

        .product-btn:hover {
            background: linear-gradient(135deg, var(--azul-medio) 0%, var(--azul-escuro) 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
            color: white;
        }

        /* Catálogo Section */
        .catalog-section {
            padding: 5rem 2rem;
            background: white;
        }

        .catalog-carousel {
            position: relative;
            max-width: 1200px;
            margin: 0 auto;
            overflow: hidden;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
            background: white;
        }

        .catalog-slides {
            display: flex;
            transition: transform 0.5s ease-in-out;
        }

        .catalog-slide {
            min-width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem;
            background: white;
        }

        .catalog-slide img {
            max-width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .catalog-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: var(--azul-claro);
            border: none;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: white;
            transition: all 0.3s ease;
            z-index: 10;
        }

        .catalog-nav:hover {
            background: var(--azul-medio);
            transform: translateY(-50%) scale(1.1);
        }

        .catalog-prev {
            left: 20px;
        }

        .catalog-next {
            right: 20px;
        }

        .catalog-dots {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 2rem;
        }

        .catalog-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: var(--cinza-medio);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .catalog-dot.active {
            background: var(--azul-claro);
            transform: scale(1.2);
        }

        /* Localização Section */
        .location-section {
            padding: 5rem 2rem;
            background: linear-gradient(135deg, #e8f4f8 0%, #d1e7dd 100%);
            position: relative;
            overflow: hidden;
        }

        .location-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--azul-claro) 0%, var(--azul-medio) 50%, var(--azul-escuro) 100%);
        }

        .location-container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            align-items: start;
        }

        .location-title {
            text-align: center;
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--azul-escuro);
            margin-bottom: 3rem;
            font-family: 'Comfortaa', cursive;
            position: relative;
        }

        .location-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: #f4a261;
            border-radius: 2px;
        }

        .map-container {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
            height: 400px;
            position: relative;
        }

        .map-container iframe {
            width: 100%;
            height: 100%;
            border: none;
        }

        .contact-info {
            background: white;
            padding: 2.5rem;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
        }

        .contact-info h3 {
            font-size: 1.8rem;
            font-weight: 600;
            color: var(--azul-escuro);
            margin-bottom: 2rem;
            font-family: 'Comfortaa', cursive;
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
            padding: 1rem;
            background: #f8fafc;
            border-radius: 15px;
            transition: all 0.3s ease;
        }

        .contact-item:hover {
            background: var(--azul-claro);
            color: white;
            transform: translateX(10px);
        }

        .contact-item:hover .contact-icon {
            color: white;
        }

        .contact-icon {
            font-size: 1.2rem;
            color: var(--azul-claro);
            min-width: 20px;
            transition: all 0.3s ease;
        }

        .contact-text {
            font-size: 1rem;
            line-height: 1.5;
        }

        .directions-btn {
            background: linear-gradient(135deg, var(--azul-claro) 0%, var(--azul-medio) 100%);
            color: white;
            text-decoration: none;
            padding: 1rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3);
            margin-top: 1rem;
        }

        .directions-btn:hover {
            background: linear-gradient(135deg, var(--azul-medio) 0%, var(--azul-escuro) 100%);
            transform: translateY(-2px);
            box-shadow: 0 12px 35px rgba(59, 130, 246, 0.4);
            color: white;
        }

        /* Responsividade para localização */
        @media (max-width: 768px) {
            .location-container {
                grid-template-columns: 1fr;
                gap: 2rem;
            }

            .map-container {
                height: 300px;
            }

            .contact-info {
                padding: 2rem;
            }
        }

        /* Features Section */
        .features {
            padding: 5rem 2rem;
            background: white;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .feature-card {
            background: white;
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: all 0.3s ease;
            border: 1px solid #e2e8f0;
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(59, 130, 246, 0.1), transparent);
            transition: left 0.5s;
        }

        .feature-card:hover::before {
            left: 100%;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            border-color: var(--azul-claro);
        }

        .feature-icon {
            font-size: 3rem;
            color: var(--azul-claro);
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }

        .feature-card:hover .feature-icon {
            transform: scale(1.1);
            color: var(--azul-medio);
        }

        .feature-card h3 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--azul-escuro);
            margin-bottom: 1rem;
        }

        .feature-card p {
            color: var(--cinza-medio);
            line-height: 1.6;
        }

        /* Agendamento Section */
        .appointment {
            padding: 5rem 2rem;
            background: linear-gradient(135deg, var(--azul-escuro) 0%, var(--azul-medio) 100%);
            color: white;
            position: relative;
            overflow: hidden;
        }

        .appointment::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain2" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.05"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.05"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.05"/><circle cx="10" cy="60" r="0.5" fill="white" opacity="0.05"/><circle cx="90" cy="40" r="0.5" fill="white" opacity="0.05"/></pattern></defs><rect width="100" height="100" fill="url(%23grain2)"/></svg>');
            opacity: 0.3;
        }

        .appointment-content {
            text-align: center;
            max-width: 600px;
            margin: 0 auto;
            position: relative;
            z-index: 2;
        }

        .appointment h2 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            font-family: 'Comfortaa', cursive;
        }

        .appointment p {
            font-size: 1.1rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }

        .whatsapp-btn {
            background: #25d366;
            color: white;
            text-decoration: none;
            padding: 1rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 8px 25px rgba(37, 211, 102, 0.3);
            position: relative;
            overflow: hidden;
        }

        .whatsapp-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .whatsapp-btn:hover::before {
            left: 100%;
        }

        .whatsapp-btn:hover {
            background: #128c7e;
            transform: translateY(-2px);
            box-shadow: 0 12px 35px rgba(37, 211, 102, 0.4);
            color: white;
        }

        /* Footer */
        .footer {
            background: linear-gradient(135deg, var(--azul-escuro) 0%, var(--azul-claro) 100%);
            color: white;
            padding: 3rem 2rem 1rem;
            position: relative;
            overflow: hidden;
        }

        .footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain3" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.03"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.03"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.03"/><circle cx="10" cy="60" r="0.5" fill="white" opacity="0.03"/><circle cx="90" cy="40" r="0.5" fill="white" opacity="0.03"/></pattern></defs><rect width="100" height="100" fill="url(%23grain3)"/></svg>');
            opacity: 0.3;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
            position: relative;
            z-index: 2;
        }

        .footer-section h3 {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 1rem;
            font-family: 'Comfortaa', cursive;
        }

        .footer-section p,
        .footer-section a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            line-height: 1.6;
            margin-bottom: 0.5rem;
            display: block;
            transition: all 0.3s ease;
        }

        .footer-section a:hover {
            color: white;
            transform: translateX(5px);
        }

        .footer-section i {
            margin-right: 0.5rem;
            color: #fbbf24;
        }

        .footer-bottom {
            text-align: center;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            color: rgba(255, 255, 255, 0.8);
            position: relative;
            z-index: 2;
        }

        .leaf-icon {
            color: #a4c3a2;
            font-size: 0.8rem;
            margin: 0 0.2rem;
        }

        /* Efeitos especiais */
        .sparkle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: #f1c40f;
            border-radius: 50%;
            animation: sparkle 2s ease-in-out infinite;
        }

        .magical-glow {
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.4), 0 0 40px rgba(244, 162, 97, 0.2);
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.8s ease-out forwards;
        }

        .animate-slide-in-left {
            animation: slideInLeft 0.8s ease-out forwards;
        }

        .animate-slide-in-right {
            animation: slideInRight 0.8s ease-out forwards;
        }

        .pulse-glow {
            animation: pulse-glow 2s infinite;
        }

        /* Responsividade */
        @media (max-width: 768px) {
            .header {
                padding: 1rem;
            }

            .hero h1 {
                font-size: 2.5rem;
            }

            .hero p {
                font-size: 1rem;
            }

            .cta-buttons {
                flex-direction: column;
                align-items: center;
            }

            .btn {
                width: 100%;
                max-width: 300px;
                justify-content: center;
            }

            .features-grid,
            .products-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 480px) {
            .hero h1 {
                font-size: 2rem;
            }

            .section-title {
                font-size: 2rem;
            }

            .appointment h2 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Elementos flutuantes -->
    <div class="floating-elements">
        <div class="leaf" style="top: -10%; left: 10%; animation-delay: 0s;"></div>
        <div class="leaf" style="top: -10%; left: 80%; animation-delay: -3s;"></div>
        <div class="leaf" style="top: -10%; left: 20%; animation-delay: -6s;"></div>
        <div class="leaf" style="top: -10%; left: 70%; animation-delay: -9s;"></div>
        <div class="leaf" style="top: -10%; left: 90%; animation-delay: -12s;"></div>
        <div class="leaf" style="top: -10%; left: 40%; animation-delay: -15s;"></div>
        <div class="leaf" style="top: -10%; left: 60%; animation-delay: -18s;"></div>
        <div class="leaf" style="top: -10%; left: 30%; animation-delay: -21s;"></div>

        <!-- Nuvens flutuantes -->
        <div class="otica-cloud small" style="top: 20%; animation-delay: 0s;"></div>
        <div class="otica-cloud small" style="top: 60%; animation-delay: -10s;"></div>
        <div class="otica-cloud small" style="top: 40%; animation-delay: -20s;"></div>
    </div>

    <!-- Header -->
    <header class="header">
        <a href="#" class="logo">Wiz Óptica</a>
        <a href="login.php" class="entrar-btn">
            <i class="fas fa-sign-in-alt"></i>
            Entrar
        </a>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>A <span>óptica</span> que vai até você</h1>
            <p>Descubra o mundo com clareza através dos nossos óculos de qualidade premium. Oferecemos uma experiência única em ótica, combinando tecnologia avançada com design moderno.</p>
            <div class="cta-buttons">
                <a href="#produtos" class="btn btn-primary">
                    <i class="fas fa-eye"></i>
                    Ver Produtos
                </a>
                <a href="#agendamento" class="btn btn-secondary">
                    <i class="fas fa-calendar"></i>
                    Agendar Consulta
                </a>
            </div>
        </div>
    </section>

         <!-- Produtos Section -->
     <section class="products-section" id="produtos">
         <div class="container">
             <h2 class="section-title">Nossos Produtos</h2>
             
             <div class="products-grid">
                 <div class="product-card animate-fade-in-up">
                     <div class="product-image">
                         <i class="fas fa-glasses"></i>
                     </div>
                     <div class="product-content">
                         <h3 class="product-title">Óculos de Grau Premium</h3>
                         <p class="product-description">Lentes personalizadas com tecnologia avançada para correção visual precisa. Utilizamos materiais de alta qualidade e design ergonômico para máximo conforto durante o uso prolongado.</p>
                         <div class="product-features">
                             <span class="feature-tag">Anti-reflexo</span>
                             <span class="feature-tag">Proteção UV 100%</span>
                             <span class="feature-tag">Transições</span>
                             <span class="feature-tag">Lentes Progressivas</span>
                         </div>
                         <a href="#agendamento" class="product-btn">
                             <i class="fas fa-calendar-check"></i>
                             Agendar Consulta
                         </a>
                     </div>
                 </div>
                 
                 <div class="product-card animate-fade-in-up">
                     <div class="product-image">
                         <i class="fas fa-sun"></i>
                     </div>
                     <div class="product-content">
                         <h3 class="product-title">Óculos de Sol Exclusivos</h3>
                         <p class="product-description">Proteção solar com estilo e sofisticação. Marcas premium com lentes polarizadas que eliminam reflexos e oferecem máxima proteção contra raios UV, mantendo a clareza visual.</p>
                         <div class="product-features">
                             <span class="feature-tag">Polarizado</span>
                             <span class="feature-tag">Proteção 100% UV</span>
                             <span class="feature-tag">Design Exclusivo</span>
                             <span class="feature-tag">Marcas Premium</span>
                         </div>
                         <a href="#agendamento" class="product-btn">
                             <i class="fas fa-shopping-cart"></i>
                             Ver Opções
                         </a>
                     </div>
                 </div>
                 
                 <div class="product-card animate-fade-in-up">
                     <div class="product-image">
                         <i class="fas fa-running"></i>
                     </div>
                     <div class="product-content">
                         <h3 class="product-title">Óculos Esportivos Profissionais</h3>
                         <p class="product-description">Performance e durabilidade para atividades físicas intensas. Lentes resistentes a impactos, design ergonômico e tecnologia anti-embaçante para máxima segurança e conforto.</p>
                         <div class="product-features">
                             <span class="feature-tag">Resistente a Impactos</span>
                             <span class="feature-tag">Anti-embaçante</span>
                             <span class="feature-tag">Leve e Confortável</span>
                             <span class="feature-tag">Ajuste Perfeito</span>
                         </div>
                         <a href="#agendamento" class="product-btn">
                             <i class="fas fa-dumbbell"></i>
                             Testar Modelo
                         </a>
                     </div>
                 </div>
                 
                 <div class="product-card animate-fade-in-up">
                     <div class="product-image">
                         <i class="fas fa-laptop"></i>
                     </div>
                     <div class="product-content">
                         <h3 class="product-title">Óculos para Computador</h3>
                         <p class="product-description">Proteção especial contra luz azul para uso prolongado em dispositivos digitais. Reduz a fadiga visual, previne dores de cabeça e melhora a qualidade do sono.</p>
                         <div class="product-features">
                             <span class="feature-tag">Filtro Azul</span>
                             <span class="feature-tag">Anti-cansaço</span>
                             <span class="feature-tag">Conforto Visual</span>
                             <span class="feature-tag">Proteção Digital</span>
                         </div>
                         <a href="#agendamento" class="product-btn">
                             <i class="fas fa-desktop"></i>
                             Proteger Visão
                         </a>
                     </div>
                 </div>
             </div>
         </div>
     </section>

          <!-- Catálogo Section -->
     <section class="catalog-section" id="catalogo">
         <div class="container">
             <h2 class="section-title">Destaques</h2>
             
             <div class="catalog-carousel">
                 <div class="catalog-slides">
                     <div class="catalog-slide">
                         <img src="img/carrosel/1.png" alt="Óculos da Ótica - Imagem 1">
                     </div>
                     <div class="catalog-slide">
                         <img src="img/carrosel/2.png" alt="Óculos da Ótica - Imagem 2">
                     </div>
                     <div class="catalog-slide">
                         <img src="img/carrosel/3.png" alt="Óculos da Ótica - Imagem 3">
                     </div>
                     <div class="catalog-slide">
                         <img src="img/carrosel/4.png" alt="Óculos da Ótica - Imagem 4">
                     </div>
                     <div class="catalog-slide">
                         <img src="img/carrosel/5.png" alt="Óculos da Ótica - Imagem 5">
                     </div>
                     <div class="catalog-slide">
                         <img src="img/carrosel/6.png" alt="Óculos da Ótica - Imagem 6">
                     </div>
                     <div class="catalog-slide">
                         <img src="img/carrosel/7.png" alt="Óculos da Ótica - Imagem 7">
                     </div>
                 </div>
                 
                 <button class="catalog-nav catalog-prev">
                     <i class="fas fa-chevron-left"></i>
                 </button>
                 <button class="catalog-nav catalog-next">
                     <i class="fas fa-chevron-right"></i>
                 </button>
                 
                 <div class="catalog-dots"></div>
             </div>
         </div>
     </section>

    <!-- Features Section -->
    <section class="features" id="servicos">
        <div class="container">
            <h2 class="section-title">Por que escolher nossa ótica?</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-glasses"></i>
                    </div>
                    <h3>Óculos de Qualidade</h3>
                    <p>Oferecemos as melhores marcas e lentes de alta tecnologia para garantir sua satisfação total.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-user-md"></i>
                    </div>
                    <h3>Profissionais Especializados</h3>
                    <p>Nossa equipe de oftalmologistas e optometristas está pronta para cuidar da sua saúde visual.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-tools"></i>
                    </div>
                    <h3>Manutenção e Ajustes</h3>
                    <p>Serviços de manutenção, ajustes e reparos para manter seus óculos sempre em perfeito estado.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3>Atendimento Rápido</h3>
                    <p>Agilidade no atendimento e entrega, respeitando sempre a qualidade e precisão dos nossos serviços.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3>Garantia Estendida</h3>
                    <p>Oferecemos garantia estendida em todos os nossos produtos, garantindo sua tranquilidade.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <h3>Atendimento Personalizado</h3>
                    <p>Cada cliente é único. Oferecemos um atendimento personalizado para encontrar a solução ideal.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Agendamento Section -->
    <section class="appointment" id="agendamento">
        <div class="container">
            <div class="appointment-content">
                <h2>Agende sua Consulta</h2>
                <p>Estamos prontos para cuidar da sua saúde visual. Entre em contato conosco e agende sua consulta com nossos especialistas.</p>
                <a href="https://wa.me/558598562-6483?text=Olá! Gostaria de agendar uma consulta na ótica." class="whatsapp-btn" target="_blank">
                    <i class="fab fa-whatsapp"></i>
                    Agendar via WhatsApp
                </a>
            </div>
                 </div>
     </section>

     <!-- Localização Section -->
     <section class="location-section" id="localizacao">
         <div class="container">
             <h2 class="location-title">Nossa Localização</h2>
             
             <div class="location-container">
                 <div class="map-container">
                     <iframe 
                         src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3980.1234567890123!2d-38.67890123456789!3d-3.876543210987654!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x7c74c3b4b8b8b8b%3A0x1234567890123456!2sShopping%20Maktub!5e0!3m2!1spt-BR!2sbr!4v1234567890123"
                         allowfullscreen="" 
                         loading="lazy" 
                         referrerpolicy="no-referrer-when-downgrade">
                     </iframe>
                 </div>
                 
                 <div class="contact-info">
                     <h3>Informações de Contato</h3>
                     
                     <div class="contact-item">
                         <i class="fas fa-map-marker-alt contact-icon"></i>
                         <div class="contact-text">
                             Praça Desembargador Pontes Viera, 227<br>
                             Shopping Maktub - 1º Piso, LOJA 10<br>
                             Maranguape - CE, 61940-165
                         </div>
                     </div>
                     
                     <div class="contact-item">
                         <i class="fas fa-phone contact-icon"></i>
                         <div class="contact-text">
                         (85) 98562-6483
                         </div>
                     </div>
                     
                     <div class="contact-item">
                         <i class="fas fa-envelope contact-icon"></i>
                         <div class="contact-text">
                             contato@oticawix.com.br
                         </div>
                     </div>
                     
                     <a href="https://maps.google.com/maps?q=Praça+Desembargador+Pontes+Viera,227+Shopping+Maktub+Maranguape+CE" 
                        class="directions-btn" target="_blank">
                         <i class="fas fa-route"></i>
                         Como Chegar
                     </a>
                 </div>
             </div>
         </div>
     </section>

     <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3><i class="fas fa-leaf leaf-icon"></i>Wiz Òptica</h3>
                    <p>© 2025 Wiz Óptica. Todos os direitos reservados. Uma ótica onde a qualidade e o cuidado com sua visão são nossa prioridade. Praça Desembargador Pontes Viera,227 (Shopping Maktub - 1º Piso, LOJA 10, Maranguape - CE, 61940-165</p>
                    <i class="fas fa-leaf leaf-icon"></i>
                </div>
                
                <div class="footer-section">
                    <h3>Links</h3>
                                         <a href="#catalogo"><i class="fas fa-book-open"></i>Catálogo</a>
                    <a href="#servicos"><i class="fas fa-graduation-cap"></i>Serviços</a>
                                         <a href="#galeria"><i class="fas fa-images"></i>Galeria</a>
                     <a href="#localizacao"><i class="fas fa-map-marker-alt"></i>Localização</a>
                </div>
                
                <div class="footer-section">
                    <h3><i class="fas fa-leaf leaf-icon"></i>Desenvolvedores</h3>
                    <a href="#"><i class="fas fa-code"></i>Jefferson Castro</a>
                    <a href="#"><i class="fas fa-code"></i>Weverton Cirilo</a>
                
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>© 2025 Wiz Óptica. A óptica que vai até você.</p>
            </div>
        </div>
    </footer>

    <script>
        // Smooth scroll para links internos
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Animação de entrada dos elementos
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Observar elementos para animação
        document.querySelectorAll('.feature-card, .product-card, .catalog-slide, .contact-item').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(card);
        });

        // Criar sparkles dinamicamente
        function createSparkle() {
            const sparkle = document.createElement('div');
            sparkle.className = 'sparkle';
            sparkle.style.top = Math.random() * 100 + '%';
            sparkle.style.left = Math.random() * 100 + '%';
            sparkle.style.animationDelay = Math.random() * 2 + 's';
            
            document.body.appendChild(sparkle);
            
            setTimeout(() => {
                sparkle.remove();
            }, 2000);
        }

        // Criar sparkles periodicamente
        setInterval(createSparkle, 3000);

        // Efeito de hover nos cards
        document.querySelectorAll('.feature-card, .product-card, .contact-item').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-10px) scale(1.02)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });

        // Carrossel de Catálogo
        const catalogCarousel = document.querySelector('.catalog-carousel');
        const catalogSlides = document.querySelector('.catalog-slides');
        const catalogDots = document.querySelector('.catalog-dots');
        const catalogPrev = document.querySelector('.catalog-prev');
        const catalogNext = document.querySelector('.catalog-next');

        if (catalogCarousel && catalogSlides) {
            let currentCatalogSlide = 0;
            const catalogSlideCount = document.querySelectorAll('.catalog-slide').length;

            // Criar dots
            for (let i = 0; i < catalogSlideCount; i++) {
                const dot = document.createElement('div');
                dot.className = 'catalog-dot';
                if (i === 0) dot.classList.add('active');
                dot.addEventListener('click', () => goToCatalogSlide(i));
                catalogDots.appendChild(dot);
            }

            function updateCatalogCarousel() {
                catalogSlides.style.transform = `translateX(-${currentCatalogSlide * 100}%)`;
                
                // Atualizar dots
                document.querySelectorAll('.catalog-dot').forEach((dot, index) => {
                    dot.classList.toggle('active', index === currentCatalogSlide);
                });
            }

            function nextCatalogSlide() {
                currentCatalogSlide = (currentCatalogSlide + 1) % catalogSlideCount;
                updateCatalogCarousel();
            }

            function prevCatalogSlide() {
                currentCatalogSlide = (currentCatalogSlide - 1 + catalogSlideCount) % catalogSlideCount;
                updateCatalogCarousel();
            }

            function goToCatalogSlide(index) {
                currentCatalogSlide = index;
                updateCatalogCarousel();
            }

            // Event listeners
            if (catalogNext) catalogNext.addEventListener('click', nextCatalogSlide);
            if (catalogPrev) catalogPrev.addEventListener('click', prevCatalogSlide);

            // Auto-play
            setInterval(nextCatalogSlide, 3500);
        }

        // Efeito de ripple nos botões
        document.querySelectorAll('.btn, .product-btn, .whatsapp-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                const ripple = document.createElement('span');
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;
                
                ripple.style.cssText = `
                    position: absolute;
                    width: ${size}px;
                    height: ${size}px;
                    left: ${x}px;
                    top: ${y}px;
                    background: rgba(255, 255, 255, 0.3);
                    border-radius: 50%;
                    transform: scale(0);
                    animation: ripple 0.6s ease-out;
                    pointer-events: none;
                `;
                
                this.appendChild(ripple);
                
                setTimeout(() => {
                    ripple.remove();
                }, 600);
            });
        });

        // Adicionar CSS para ripple animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes ripple {
                to {
                    transform: scale(2);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>