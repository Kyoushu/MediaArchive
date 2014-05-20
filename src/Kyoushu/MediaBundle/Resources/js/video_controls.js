$(function(){
   
    $('[data-video-player]').each(function(){
        
        var container = $(this);
        
        var seekBySliderDelay = 100;
        var seekBySliderTimeout;
        
        var fullscreenReflowDelay = 100;
        
        var humanIdleDelay = 2500;
        var humanIdleTimeout;
        var humanIdle = true;
        
        var updateTimeTimeout;
        
        var controlsElement = container.find('[data-video-controls]');
        var videoId = controlsElement.attr('data-video-controls');
        
        var videoElement = $('#' + videoId);
        var video = videoElement[0];
        var videoLoaded = false;
        
        var playElement = controlsElement.find('[data-video-control-play]');
        var pauseElement = controlsElement.find('[data-video-control-pause]');
        var fullscreenElement = controlsElement.find('[data-video-control-fullscreen]');
        
        var seekElement = controlsElement.find('[data-video-control-seek]');
        var seekHandleElement = seekElement.find('.range-slider-handle');
        seekElement.foundation('slider', 'set_value', 0);        
        
        var timeElement = controlsElement.find('[data-video-control-time]');
        
        var fullscreen = false;
        var playing = false;
        
        function play(){
            video.play();
            startUpdateTime();
            playing = true;
        }
        
        function pause(){
            if(!videoLoaded) return;
            video.pause();
            stopUpdateTime();
            playing = false;
        }
        
        function seekByPercent(positionPercent){
            var resumePlaying = playing;
            pause();
            seekElement.foundation('slider', 'set_value', positionPercent);
            var time = (video.duration / 100) * positionPercent;
            video.currentTime = time;
            if(resumePlaying) play();
        }
        
        function seekBySlider(){
            if(!videoLoaded) return;
            var positionPercent = seekElement.attr('data-slider');
            seekByPercent(positionPercent);
        }
        
        function stopUpdateTime(){
            clearTimeout(updateTimeTimeout);
        }
        
        function startUpdateTime(){
            clearTimeout(updateTimeTimeout);
            
            var seconds = parseInt(video.currentTime % 60);
            var minutes = parseInt((video.currentTime / 60) % 60);
            
            if(seconds < 10) seconds = '0' + seconds;
            
            timeElement.text(minutes + ':' + seconds);
            
            var positionPercent = (100 / video.duration) * video.currentTime;
            seekElement.foundation('slider', 'set_value', positionPercent);
            
            updateTimeTimeout = setTimeout(startUpdateTime, 1000);
        }
        
        function delayedSeekBySlider(){
            seekBySliderTimeout = setTimeout(function(){
                seekBySlider();
            }, seekBySliderDelay);
        }
        
        var fullscreenReflowTimeout;
        
        function fullscreenReflow(){
            clearTimeout(fullscreenReflowTimeout);
            fullscreenReflowTimeout = setTimeout(function(){
                
                if(!isFullscreen()) returm;
                    
                var fullscreenCss = {
                    'width': $(window).width(),
                    'height': $(window).height()
                };
                    
                videoElement.css(fullscreenCss);
                container.css(fullscreenCss);
                
            }, fullscreenReflowDelay);
        }
        
        function onFullscreenStateChange(){
            
            if(isFullscreen()){
                container.addClass('fullscreen');
                    container.parents().addClass('fullscreen');
            }
            else{
                
                videoElement.removeAttr('style');
                container.removeAttr('style');
                container.removeClass('fullscreen');
                container.parents().removeClass('fullscreen');

                showControls();
                
            }
            
            fullscreenReflow();
            
        }
        
        function isFullscreen(){
            return fullscreen;
        }
               
        function enterFullscreen(){
            fullscreen = true;
            enterDocumentFullscreen();
            onFullscreenStateChange();
        }
        
        function leaveFullscreen(){
            fullscreen = false;
            leaveDocumentFullscreen();
            onFullscreenStateChange();
        }
        
        function isDocumentFullscreen(){
            return (document.fullscreen || document.mozFullScreen || document.webkitIsFullScreen || document.msFullscreenElement);
        }
        
        function enterDocumentFullscreen(){
            if(isDocumentFullscreen()) return;

            if(document.documentElement.requestFullScreen){  
                document.documentElement.requestFullScreen();  
            }
            else if(document.documentElement.mozRequestFullScreen){  
                document.documentElement.mozRequestFullScreen();  
            }
            else if(document.documentElement.webkitRequestFullScreen){  
                document.documentElement.webkitRequestFullScreen(Element.ALLOW_KEYBOARD_INPUT);  
            }
        }
        
        function leaveDocumentFullscreen(){
            
            if(!isDocumentFullscreen()) return;

            if(document.cancelFullScreen){  
                document.cancelFullScreen();  
            }
            else if(document.mozCancelFullScreen){  
                document.mozCancelFullScreen();  
            }
            else if(document.webkitCancelFullScreen){  
                document.webkitCancelFullScreen();  
            }
            
        }
        
        function toggleFullscreen(){
            if(fullscreen){
                leaveFullscreen();
            }
            else{
                enterFullscreen();
            }
        }
        
        function showControls(){
            controlsElement.css('display', 'block');
            container.removeClass('no-cursor');
        }
        
        function hideControls(){
            controlsElement.css('display', 'none');
            container.addClass('no-cursor');
        }
        
        function onHumanIdleStateChange(){
            if(isFullscreen()){
                if(humanIdle){
                    hideControls();
                }
                else{
                    showControls();
                }
            }
        }
        
        function humanActivity(){
            var stateChanged = humanIdle;
            humanIdle = false;
            if(stateChanged) onHumanIdleStateChange();
            clearTimeout(humanIdleTimeout);
            humanIdleTimeout = setTimeout(function(){
                humanIdle = true;
                onHumanIdleStateChange();
            }, humanIdleDelay);
        }
        
        $(window).on('resize', fullscreenReflow);
        
        document.addEventListener("fullscreenchange", function () {
            fullscreen = !!document.fullscreen;
            onFullscreenStateChange();
        }, false);

        document.addEventListener("mozfullscreenchange", function () {
            fullscreen = !!document.mozFullScreen;
            onFullscreenStateChange();
        }, false);

        document.addEventListener("webkitfullscreenchange", function () {
            fullscreen = !!document.webkitIsFullScreen;
            onFullscreenStateChange();
        }, false);

        document.addEventListener("msfullscreenchange", function () {
            fullscreen = !!document.msFullscreenElement;
            onFullscreenStateChange();
        }, false);
        
        container.on('mousemove', humanActivity);
        container.on('click', humanActivity);
        container.on('keydown', humanActivity);
        onHumanIdleStateChange();
        
        seekHandleElement.on('mousedown', stopUpdateTime);
        seekHandleElement.on('mouseup', delayedSeekBySlider);
        seekHandleElement.on('touchend', delayedSeekBySlider);
        
        seekElement.on('click', function(e){
           
           var width = seekElement.width();
           var offsetX = e.offsetX;
           var seekPercent = (100 / width) * offsetX;
           
           seekByPercent(seekPercent);
           
        });
        
        videoElement.on('loadedmetadata', function(){
            videoLoaded = true;
            playElement.removeClass('disabled');
            pauseElement.removeClass('disabled');
            fullscreenElement.removeClass('disabled');
            seekHandleElement.css('display', 'block');           
        });
        
        playElement.on('click', function(e){
            e.preventDefault();
            play();
        });
        
        pauseElement.on('click', function(e){
            e.preventDefault();
            pause();
        });
        
        fullscreenElement.on('click', function(e){
            e.preventDefault();
            toggleFullscreen();
        });
        
        video.load();
        
    });
    
});