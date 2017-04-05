<div class="clear"></div>

<div class="sponsors">
  <div class="container">
    <h3 class="title">Partnerzy i sponsorzy</h3>
    <div class="flex-marquee">
      <div class="partner">
        <a href="http://weedcs.pl/"><img class="partner" src="img/partners/partner1.png" alt="partner1"></a>
      </div>
      <div class="partner">
        <a href="http://ts3-palarnia.pl/"><img src="img/partners/partner2.png" alt="2" class="partner"></a>
      </div>
      <div class="partner">
        <a href="http://www.adversa.pl/"><img src="img/partners/partner3.png" alt="3" class="partner"></a>
      </div>
      <div class="partner">
        <a href="https://www.youtube.com/user/MrKOKOSfly"><img src="img/partners/partner4.png" alt="4" class="partner"></a>
      </div>
      <div class="partner">
        <a href="http://ts3-palarnia.pl/"><img src="img/partners/partner2.png" alt="2" class="partner"></a>
      </div>
      <div class="partner">
        <a href="http://www.adversa.pl/"><img src="img/partners/partner3.png" alt="3" class="partner"></a>
      </div>
    </div>
  </div>
</div>



<footer id="footer">
  <div class="container">
    <img src="img/logo-foot.png" alt="logo KFSE" />
    <p>Krajowa Federacja Sportów Elektronicznych</p>
    <p>Copyright &copy; 2017 Krajowa Federacja Sportów Elektronicznych</p>
    <p>All rights reserved.</p>
    <span id="loadTime" style="font-size:10px;"></span>
  </div>
</footer>
  

  <!-- <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.min.css"> -->
  <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
  <script>
    $( function() { $( "#tabs" ).tabs(); } );
  </script>

  <script src="//cdn.jsdelivr.net/jquery.marquee/1.4.0/jquery.marquee.min.js" type="text/javascript"></script>
  <script>
    $(function() {
      $('.flex-marquee').marquee({
        duration: 10000,
        startVisible: true,
        gap: 0,
        duplicated: true,
        pauseOnHover: true
      });
    });

  </script>
</body>
</html>
<?php
  $endTime = getTime();

  echo '<script>$("#loadTime").html("' . round($endTime - $startTime, 3) . ' sek, ' . date('H:i:s') . '");</script>';
  ob_end_flush();  