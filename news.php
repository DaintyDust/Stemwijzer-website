<?php require 'php_require/autoload.php'; ?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Neutraal Kieslab - Nieuws</title>
    <?php require 'php_require/resources.php'; ?>
    <link rel="stylesheet" href="styles/news.css">
</head>

<body>
    <?php require 'php_require/header.php'; ?>
    <div id="maincontent">
        <?php
        $isView = isset($_GET['view']);
        $mode = $isView ? 'viewing' : 'normal';
        $conn = getConnection();
        if ($mode === 'normal') {
            $news = getNews($conn);
            echo "<div id='news-container'>";
            foreach ($news as $article) {
                $articleId = htmlspecialchars($article['id'] ?? '');
                $articleTitle = htmlspecialchars($article['titel'] ?? '');
                $articleContent = htmlspecialchars($article['inhoud'] ?? '');
                $articlePicture = htmlspecialchars($article['afbeelding'] ?? '');

                echo "<form class='news-item' method='post' action='?view' onclick='this.submit()'>";
                echo "<h2>$articleTitle</h2>";
                if (strpos($articlePicture, 'example.com') !== false) {
                    echo "<img src='images/template-news.png' alt='Nieuws Icon'>";
                } elseif (!empty($articlePicture)) {
                    echo "<img src='$articlePicture' alt='Nieuws Icon'>";
                }
                echo "<p>$articleContent</p>";
                echo "<input type='hidden' name='article_id' value='$articleId'>";
                echo "</form>";
            }
            echo "</div>";
        } elseif ($mode === 'viewing') {
            echo "<a href='news.php' id='back-button'><img src='images/arrow-right.svg' alt='Back Arrow'>Terug naar Nieuws</a>";
            $newsArticle = getNewsArticleInfo($conn, $_POST['article_id']);

            if (!$newsArticle) {
                echo "<div id='article'><h1>Artikel niet gevonden</h1><p>Het opgevraagde artikel bestaat niet of is niet meer beschikbaar.</p></div>";
            } else {
                $articleId = htmlspecialchars($newsArticle['id'] ?? '');
                $articleTitle = htmlspecialchars($newsArticle['titel'] ?? '');
                $articleContent = htmlspecialchars($newsArticle['inhoud'] ?? '');
                $articlePicture = htmlspecialchars($newsArticle['afbeelding'] ?? '');
                $articleCreationDate = htmlspecialchars($newsArticle['aangemaakt_op'] ?? '');
                $articleAuthorId = htmlspecialchars($newsArticle['auteur_id'] ?? '');
        ?>
                <div id='article'>
                    <div id="title">
                        <h1><?php echo $articleTitle; ?></h1>
                        <p class='article-meta'>Gepubliceerd op <?php echo $articleCreationDate; ?> door <?php
                                                                                                            $authorInfo = getUserInfo($conn, $articleAuthorId);
                                                                                                            echo htmlspecialchars($authorInfo['naam'] ?? 'Onbekende auteur');
                                                                                                            ?></p>
                    </div>
                    <?php
                    if (strpos($articlePicture, 'example.com') !== false) {
                        echo "<div id='image'>";
                        echo "<img src='images/template-news.png' alt='Nieuws Icon'>";
                        echo "</div>";
                    } elseif (!empty($articlePicture) && file_exists($articlePicture)) {
                        echo "<div id='image'>";
                        echo "<img src='$articlePicture' alt='Nieuws Icon'>";
                        echo "</div>";
                    }
                    ?>
                    <div id="content">
                        <p><?php echo nl2br($articleContent); ?></p>
                    </div>
                    <hr>
                    <div id="comments">
                        <h2>Reacties</h2>
                        <div id="comments-container">
                            <?php
                            $comments = getComments($conn, $articleId);
                            if (count($comments) > 0) {
                                foreach ($comments as $comment) {
                                    $commentText = htmlspecialchars($comment['comment_text'] ?? '');
                                    $commentAuthor = getUserInfo($conn, htmlspecialchars($comment['gebruiker_id'] ?? ''));
                                    $commentDate = htmlspecialchars($comment['created_at'] ?? '');
                                    $commentUpdatedDate = htmlspecialchars($comment['updated_at'] ?? '');

                                    if (!$commentAuthor) {
                                        continue; // Skip this comment if author info is not available
                                    }

                                    $commentClass = ($comment['gebruiker_id'] == $_SESSION['UserId']) ? 'comment yourcomment' : 'comment';
                                    echo "<div class='$commentClass' data-comment-id='{$comment['comment_id']}' data-author-id='{$comment['gebruiker_id']}'>";
                                    echo "<div class='author-comment-info'>";
                                    $profileImage = $commentAuthor['profielfoto'] ?? '';
                                    if (empty($profileImage) || str_contains($profileImage, 'example.com')) {
                                        $profileImage = 'images/DefaultProfile.svg';
                                    } else {
                                        $profileImage = 'pfpUploads/' . htmlspecialchars($profileImage);
                                    }
                                    echo "<img class='Profile-Picture' src='" . htmlspecialchars($profileImage) . "' alt='Gebruiker Icon'>";
                                    echo "<div>";
                                    echo "<p class='comment-author'>" . htmlspecialchars($commentAuthor['naam'] ?? 'Onbekende gebruiker') . "</p>";
                                    $dutchDays = ['Zondag', 'Maandag', 'Dinsdag', 'Woensdag', 'Donderdag', 'Vrijdag', 'Zaterdag'];
                                    $dutchMonths = ['Januari', 'Februari', 'Maart', 'April', 'Mei', 'Juni', 'Juli', 'Augustus', 'September', 'Oktober', 'November', 'December'];

                                    $commentDateFormatted = date('Y', strtotime($commentDate)) == date('Y')
                                        ? $dutchDays[date('w', strtotime($commentDate))] . ', ' . date('j', strtotime($commentDate)) . ' ' . $dutchMonths[date('n', strtotime($commentDate)) - 1] . ' om ' . date('H:i', strtotime($commentDate))
                                        : date('j', strtotime($commentDate)) . ' ' . $dutchMonths[date('n', strtotime($commentDate)) - 1] . ' ' . date('Y', strtotime($commentDate)) . ' om ' . date('H:i', strtotime($commentDate));
                                    echo "<p>$commentDateFormatted</p>";
                                    echo "</div>";
                                    if ($comment['gebruiker_id'] == $_SESSION['UserId']) {
                                        echo "<div class='comment-actions'>";
                                        echo "<button class='edit-comment'><img src='images/edit-pencil.png' alt='Bewerk Reactie'></button>";
                                        echo "<button class='delete-comment'><img src='images/trash.png' alt='Verwijder Reactie'></button>";
                                        echo "<button class='cancel-comment-edit'><img src='images/cancel.png' alt='Annuleren'></button>";
                                        echo "</div>";
                                    } else {
                                        echo "<div class='comment-actions reply'>";
                                        echo "<button class='reply-comment'><img src='images/reply-message.png' alt='Beantwoord Reactie'></button>";
                                        echo "</div>";
                                    }
                                    echo "</div>";
                                    $highlightClass = (isset($_SESSION['Username']) && strpos($commentText, '@' . $_SESSION['Username']) !== false) ? ' highlight' : '';
                                    echo "<p class='comment-text$highlightClass'>" . preg_replace('/(<br\s*\/?>\s*){4,}/', '<br><br><br>', nl2br($commentText)) . "</p>";
                                    echo "<div class='triangle right'></div>";
                                    echo "</div>";
                                }
                            } else {
                                echo "<p>Er zijn nog geen reacties op dit artikel.</p>";
                            }
                            ?>
                        </div>
                        <div id="place-comments-container">
                            <form id="comment-form">
                                <input type="hidden" name="articleId" value="<?php echo $articleId; ?>">
                                <textarea name="comment" placeholder="Plaats een reactie..." required></textarea>
                                <button type="submit"><img src="images/send-message.svg" alt="Verzend bericht"></button>
                            </form>
                        </div>
                    </div>
                </div>
        <?php }
        } ?>
        <!-- <form class="news-item" method="post" action="?view" onclick="this.submit()">
                <h2>Nieuws</h2>
                <img src="images/template-news.png" alt="Nieuws Icon">
                <p>Welkom bij het Neutraal Kieslab! Hier vind je het laatste nieuws over de verkiezingen, stemwijzers en meer.</p>
            </form> -->
    </div>
    <?php require 'php_require/footer.php'; ?>
    <script src="js/news.js"></script>
</body>

</html>