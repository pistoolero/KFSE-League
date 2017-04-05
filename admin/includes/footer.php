    </div>
  </main>

  <footer class="page-footer">
    <div class="container">
      <div class="row">
        <div class="col l6 s12">
          <h5 class="white-text">Footer Content</h5>
          <p class="grey-text text-lighten-4">Lorem ipsum dolor sit amet, consectetur adipisicing elit.</p>
        </div>
        <div class="col l4 offset-l2 s12">
          <h5 class="white-text">Links</h5>
          <ul>
            <li style="height:24px;line-height:24px;"><a class="grey-text text-lighten-3" href="../"><i class="material-icons left clear">forum</i>KFSE</a></li>
            <li style="height:24px;line-height:24px;"><a class="grey-text text-lighten-3" href="https://www.youtube.com/user/MrKOKOSfly"><i class="material-icons left clear">aspect_ratio</i>MrKOKOSfly</a></li>
            <li style="height:24px;line-height:24px;"><a class="grey-text text-lighten-3" href="https://www.youtube.com/user/MakerioDG"><i class="material-icons left clear">aspect_ratio</i>Makerio</a></li>
            <li style="height:24px;line-height:24px;"><a class="grey-text text-lighten-3" href="mailto:anszlus12@gmail.com"><i class="material-icons left clear">code</i>Anszlus</a></li>
          </ul>
        </div>
      </div>
    </div>
    <div class="footer-copyright">
      <div class="container">
        All rights reserved.<br />
        Copyright &copy; 2017 Krajowa Federacja Sportów Elektronicznych
        <a class="grey-text text-lighten-4 right" href="../">KFSE</a>
      </div>
    </div>
  </footer>

  <!-- Import jQuery before materialize.js -->
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.98.0/js/materialize.min.js"></script>
  
  <script>
    $('select.select').material_select();
    $(".button-collapse").sideNav();
    $('.collapsible').collapsible();
    $('.materialboxed').materialbox();
    $('textarea').trigger('autoresize')

    $(window).resize(function(){
      if (typeof drawChart == "function") drawChart();
    });
  </script>

</body>
</html>
<?php
  ob_end_flush();