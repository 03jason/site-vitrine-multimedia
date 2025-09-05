<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'navBar.php';

$message_status = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = htmlspecialchars($_POST['name'] ?? '');
    $email = htmlspecialchars($_POST['email'] ?? '');
    $subject = htmlspecialchars($_POST['subject'] ?? '');
    $message = htmlspecialchars($_POST['message'] ?? '');

    $_SESSION['contact_success_message'] = "Votre requête a été envoyée avec succès (simulation).";

    header('Location: index.php');
    exit();
}

if (isset($_SESSION['contact_success_message'])) {
    $message_status = '<div class="success-message">' . $_SESSION['contact_success_message'] . '</div>';
    unset($_SESSION['contact_success_message']); // Supprimer le message après l'affichage
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contactez-nous</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .contact-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .contact-container h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
            font-size: 2.2em;
        }

        .contact-form .form-group {
            margin-bottom: 20px;
        }

        .contact-form label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
            font-size: 1.05em;
        }

        .contact-form input[type="text"],
        .contact-form input[type="email"],
        .contact-form textarea {
            width: calc(100% - 22px);
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1em;
            box-sizing: border-box;
        }

        .contact-form textarea {
            resize: vertical;
            min-height: 120px;
        }

        .contact-form button {
            background-color: #007bff;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            font-size: 1.1em;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.2s ease-in-out;
        }

        .contact-form button:hover {
            background-color: #0056b3;
        }

        .success-message {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #badbcc;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: bold;
        }

        @media (max-width: 600px) {
            .contact-container {
                margin: 20px;
                padding: 20px;
            }

            .contact-container h1 {
                font-size: 1.8em;
            }

            .contact-form button {
                padding: 10px 20px;
                font-size: 1em;
            }
        }
    </style>
</head>
<body>

<div class="contact-container">
    <h1>Contactez-nous</h1>
    <?= $message_status ?>
    <p>Avez-vous une question, un commentaire ou un problème ? N'hésitez pas à nous envoyer un message !</p>

    <form action="contactUS.php" method="POST" class="contact-form">
        <div class="form-group">
            <label for="name">Votre Nom :</label>
            <input type="text" id="name" name="name" required>
        </div>
        <div class="form-group">
            <label for="email">Votre Email :</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="subject">Sujet :</label>
            <input type="text" id="subject" name="subject" required>
        </div>
        <div class="form-group">
            <label for="message">Votre Message :</label>
            <textarea id="message" name="message" required></textarea>
        </div>
        <button type="submit">Envoyer la requête</button>
    </form>
</div>

<?php include 'footer.php'; ?> </body>
</html>