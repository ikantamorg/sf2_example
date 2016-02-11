require([
  'jquery',
  'underscore',
  'session-record-flash-player'
], function ($, _) {


  $(document).ready(function(){

    $(".accordion h3").click(function(){
      var $this = $(this);
      $this.next(".accordion-box:not(.stick)").slideToggle("slow").siblings(".accordion-box:not(.stick):visible").slideUp("slow");
      $this.toggleClass("active");
      $this.siblings("h3").removeClass("active");
    });

  });



});
