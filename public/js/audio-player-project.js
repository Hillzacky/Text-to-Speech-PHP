/*===========================================================================
*
*  AUDIO PLAYER - SINGLE BUTTON PLAYER
*
*============================================================================*/

var current = '';
var audio = new Audio();

function resultPlay(element){

    var src = $(element).attr('src');
    var type = $(element).attr('type');
    var id = $(element).attr('id');

    var isPlaying = false;
    
    audio.src = src;
    audio.type= type;    

    if (current == id) {
        audio.pause();
        isPlaying = false;
        document.getElementById(id).innerHTML = '<i class="fa fa-play"></i>';
        document.getElementById(id).classList.remove('result-pause');
        current = '';

    } else {    
        if(isPlaying) {
            audio.pause();
            isPlaying = false;
            document.getElementById(id).innerHTML = '<i class="fa fa-play"></i>';
            document.getElementById(id).classList.remove('result-pause');
            current = '';
        } else {
            audio.play();
            isPlaying = true;
            if (current) {
                document.getElementById(current).innerHTML = '<i class="fa fa-play"></i>';
                document.getElementById(current).classList.remove('result-pause');
            }
            document.getElementById(id).innerHTML = '<i class="fa fa-pause"></i>';
            document.getElementById(id).classList.add('result-pause');
            current = id;
        }
    }

    audio.addEventListener('ended', (event) => {
        document.getElementById(id).innerHTML = '<i class="fa fa-play"></i>';
        document.getElementById(id).classList.remove('result-pause');
        isPlaying = false;
        current = '';
    });      
        
}


 


 