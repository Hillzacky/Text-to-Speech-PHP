/* ===================================================================

  Minimizing Menu Bar 

  =================================================================== */

  $(document).ready(function() {

    "use strict";
  
    $(window).on('scroll', function() {
  
      if($(document).scrollTop()>100) {
        $('#navbar-container').addClass('minimize-navbar');
      }
      else {
        $('#navbar-container').removeClass('minimize-navbar');
      }
  
    });
  
  });


/* ===================================================================

   SCROLL TO TOP BUTTON

   =================================================================== */
	$(window).on("scroll", function(e) {
    	if ($(this).scrollTop() > 0) {
            $('#back-to-top').fadeIn('slow');
        } else {
            $('#back-to-top').fadeOut('slow');
        }
    });
    $("#back-to-top").on("click", function(e){
        $("html, body").animate({
            scrollTop: 0
        }, 600);
        return false;
    });


/* ===================================================================

   NOFICATION ALERTS

   =================================================================== */
    window.setTimeout(function() {
		$(".alert").fadeTo(500, 0).slideUp(4000, function(){
			$(this).remove(); 
		});
	}, 7000);



/*===========================================================================
*
*  TTS Dashboard 
*
*============================================================================*/
let previous_language;
let previous_voice = '';
let previous_selection = 0;

$(document).ready(function(){

    "use strict";

    $('.avoid-clicks').on('click',false);

    $('#clear-text').on("click", function(e){
        e.preventDefault();
        $('textarea').val('');
    });

    let current = $(".ssml");
    $("#text-type input[type='radio']").on('change', function() {
        current.hide();
        current = $("." + $("#text-type input[type='radio']:checked").val() );
        current.show();
    });

    let language = document.getElementById("languages");
    previous_language = language.value;

    let voice = document.getElementById("voices");
    previous_voice = 'current-' + voice.value;

    let preview = document.getElementById(voice.value);
    let url = preview.getAttribute('data-url');
    let name = preview.getAttribute('data-voice');
    
    document.getElementById('preview').setAttribute("src", url);
    document.getElementById('preview-name').innerHTML = name;

})

function language_select(value){

    "use strict";

    for (let i = 0; i < previous_selection.length; i++){
        previous_selection[i].style.display = 'none';
    }

    let elements_old = document.getElementsByClassName(previous_language);

    for (let i = 0; i < elements_old.length; i++){			
        elements_old[i].style.display = 'none';
    }

    let elements = document.getElementsByClassName(value);

    for (let i = 0; i < elements.length; i++){			
        elements[i].style.display = 'block';
    }
    
    let current_value = document.getElementsByClassName('current_value');

    if (current_value[1]) {
        if (document.getElementById(previous_voice)) {
            document.getElementById(previous_voice).innerHTML = 'Choose your Voice:';
            document.getElementById(previous_voice).style.display = 'block';
        }        
    }		

    previous_selection = elements;		
}

function default_voice(value) {

    "use strict";

    previous_voice = 'current-' + value;
}


/*===========================================================================
*
*  Process Select Voices 
*
*============================================================================*/
function voice_select(value) {
    
    "use strict";

    previous_voice = 'current-' + value;

    let sample = document.getElementById(value);
    let url = sample.getAttribute('data-url');
    let name = sample.getAttribute('data-voice');
    
    document.getElementById('preview').setAttribute("src", url);
    document.getElementById('preview-name').innerHTML = name;
}

let playBtn = document.getElementById('playBtn');
let stopBtn = document.getElementById('stopBtn');
let forwardBtn = document.getElementById('forwardBtn');
let backwardBtn = document.getElementById('backwardBtn');
let wave = document.getElementById('waveform');

let wavesurfer = WaveSurfer.create({
    container: wave,
    waveColor: '#ff9d00',
    progressColor: '#1e1e2d',
    selectionColor: '#d0e9c6',
    backgroundColor: '#ffffff',
    barWidth: 2,
    barHeight: 4,
    barMinHeight: 1,
    height: 50,
    responsive: true,				
    barRadius: 1,
    fillParent: true,
    plugins: [
      WaveSurfer.timeline.create({
          container: "#wave-timeline",
          timeInterval: 1,
      }),
      WaveSurfer.cursor.create({
          showTime: true,
          opacity: 1,
          customShowTimeStyle: {
              'background-color': '#000',
              color: '#fff',
              padding: '2px',
              'font-size': '10px'
          }
      }),
    ]
});

playBtn.onclick = function(e) {
  e.preventDefault();

  wavesurfer.playPause();
  if (playBtn.innerHTML.includes('fa-play')) {
      playBtn.innerHTML = '<i class="fa fa-pause"></i>';
      playBtn.classList.add('isPlaying');
  } else {
      playBtn.innerHTML = '<i class="fa fa-play"></i>';
      playBtn.classList.remove('isPlaying');
  }
}

stopBtn.onclick = function(e) {
  e.preventDefault();

  wavesurfer.stop();	
  playBtn.innerHTML = '<i class="fa fa-play"></i>';
  playBtn.classList.remove('isPlaying');
}

forwardBtn.onclick = function(e) {
  e.preventDefault();
  
  wavesurfer.skipForward(3);	
}

backwardBtn.onclick = function(e) {
  e.preventDefault();

  wavesurfer.skipBackward(3);	
}

wavesurfer.on('finish', function() {
  playBtn.innerHTML = '<i class="fa fa-play"></i>';
  playBtn.classList.remove('isPlaying');
  wavesurfer.stop();	
});




/*************************************************
 *  Process Live Synthesize Listen Mode Frontend
 *************************************************/
 $('#frontend-listen-text').on('click', function(e) {

    "use strict";
    
    e.preventDefault()
    
    if (document.getElementById("textarea").value == '') {  
        Swal.fire('Text to Speech Notification', 'Please enter text to synthesize first', 'warning');      
    } else {

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            type: "POST",
            url: $('#synthesize-text-form').attr('action'),
            data: $('#synthesize-text-form').serialize(),
            beforeSend: function() {
                $('#frontend-listen-text').html('');
                $('#frontend-listen-text').prop('disabled', true);
                $('#processing').show().clone().appendTo('#frontend-listen-text');    
                $('#processing').hide();  
                $('#waveform-box').slideUp('slow')      
            },
            complete: function() {
                $('#frontend-listen-text').prop('disabled', false);
                $('#processing', '#frontend-listen-text').empty().remove();
                $('#processing').hide();
                $('#frontend-listen-text').html('Listen');                
            },
            success: function(data) {
                $('#waveform-box').slideDown('slow')
            },
            error: function(data) {
                if (data.responseJSON['error']) {
                    Swal.fire('Listen Error', 'There was an error please notify support team', 'error');
                }

                $('#frontend-listen-text').prop('disabled', false);
                $('#processing').remove();
                $('#frontend-listen-text').html('Listen'); 
                $('#waveform-box').slideUp('slow')               
            }
        }).done(function(data) {  
            
           wavesurfer.load(data['url']);

           wavesurfer.on('ready',     
                wavesurfer.play.bind(wavesurfer),
                playBtn.innerHTML = '<i class="fa fa-pause"></i>',
                playBtn.classList.add('isPlaying'),
            );
             

        })
    }
});



/* ===================================================================

   CASES

=================================================================== */    
let swiper = new Swiper('.blog-slider', {
    spaceBetween: 30,
    effect: 'fade',
    loop: true,
    mousewheel: {
        invert: false,
    },
    pagination: {
        el: '.blog-slider__pagination',
        clickable: true,
    }
});



/* ===================================================================

    Feedbacks Section Image Slider 

   =================================================================== */

   $(document).ready(function()  {

    "use strict";
  
    $('#feedbacks').slick({
       slidesToShow: 3,
       slidesToScroll: 1,
       dots: true,
       arrows: true,
       nextArrow: $('.offers-next'),
       prevArrow: $('.offers-prev'),
       autoplay: false,
       autoplaySpeed: 2000, 
       speed: 1000,
       infinite: true,
       responsive: [
        {
          breakpoint: 992,
          settings: {
            slidesToShow: 3,
            slidesToScroll: 1,
            infinite: true,         
          }
        },
        {
          breakpoint: 768,
          settings: {
            slidesToShow: 2,
            slidesToScroll: 1,
            infinite: true,
          }
        },
        {
          breakpoint: 480,
          settings: {
            slidesToShow: 1,
            slidesToScroll: 1,
            infinite: true,
          }
        },
      ]
    });
  
  });



/* ===================================================================

    Blogs Section Image Slider 

   =================================================================== */

   $(document).ready(function()  {

    "use strict";
  
    $('#blogs').slick({
       slidesToShow: 3,
       slidesToScroll: 1,
       dots: true,
       arrows: true,
       nextArrow: $('.blogs-next'),
       prevArrow: $('.blogs-prev'),
       autoplay: false,
       autoplaySpeed: 2000, 
       speed: 1000,
       infinite: true,
       responsive: [
        {
          breakpoint: 992,
          settings: {
            slidesToShow: 3,
            slidesToScroll: 1,
            infinite: true,         
          }
        },
        {
          breakpoint: 768,
          settings: {
            slidesToShow: 2,
            slidesToScroll: 1,
            infinite: true,
          }
        },
        {
          breakpoint: 480,
          settings: {
            slidesToShow: 1,
            slidesToScroll: 1,
            infinite: true,
          }
        },
      ]
    });
  
  });