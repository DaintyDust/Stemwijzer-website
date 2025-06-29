 <?php
    $footer_links = [
        "TelefoonNummer" => "+31 85 125 9832",
        "E-mailadres" => "Info@NeutraalKiesLab.nl"
    ];
    $footer_socials = [
        "Facebook" => "https://www.facebook.com",
        "LinkedIn" => "https://www.linkedin.com"
    ];
    ?>

 <footer>
     <div class="footer-links">
         <?php foreach ($footer_links as $name => $link): ?>
             <div>
                 <span><?= $name ?>:</span>
                 <?php if (strpos($link, '@') !== false): ?>
                     <a href="mailto:<?= $link ?>"><?= $link ?></a>
                 <?php else: ?>
                     <span><?= $link ?></span>
                 <?php endif; ?>
             </div>
         <?php endforeach; ?>
     </div>
     <div class="footer-socials">
         <?php foreach ($footer_socials as $name => $link): ?>
             <a id="<?= strtolower($name) ?>-link" class="footer-social" href="<?= $link ?>" target="_blank">
                 <img alt="<?= $name ?> logo">
             </a>
         <?php endforeach; ?>
     </div>
 </footer>