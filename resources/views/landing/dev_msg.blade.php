<div id="dev-msg" class="d-flex position-fixed w-100 h-100">
    <div id="content" class="content">
        <div class="head">
            <h5 class="text-center pb-3 pt-1">{{ __('landing.dev_msg.title') }}</h5>
        </div>
        <div class="body" dir={{ app()->currentLocale() == 'en' ? 'ltr' : 'rtl'}}>
            <p class="description">
                {{ __('landing.dev_msg.msg') }}
            </p>
            <div id="confirm" class="button text-center"> {{ __('landing.dev_msg.btn') }} (<span id="timer">20</span>)</div>
        </div>
    </div>
</div>

<style>
    #dev-msg {
        top: 0;
        height: 0;
        background-color: rgba(0, 0, 0, 0.7);
        z-index: 9999999999;
        justify-content: center;
        align-items: center;
    }

    #dev-msg .content {
        background-color: #fff;
        position: relative;
        padding: 20px 30px;
        border-radius: 10px;
    }

    #dev-msg .content .head {
        color: #247f9c
    }

    #dev-msg .content .body .button {
        background: #247f9c;
        color: #fff;
        width: 100px;
        padding: 3px 5px;
        border-radius: 5px;
        font-size: 15px;
        margin: 0 auto;
        margin-top: 20px;
        cursor: pointer;
    }
</style>

<script type="text/javascript">
    // on load, collect dev message, content, confirm button and timer
    window.onload = function() {
        var devMsg = document.getElementById('dev-msg');
        var confirm = document.getElementById('confirm');
        var timer = document.getElementById('timer');

        var timerCounter = function () {
            var time = parseInt(timer.innerHTML);
            time--;
            timer.innerHTML = time;
            if (time == 0) {
                removeElement('dev-msg');
            }
        };

        function removeElement(elementId) {
            // Removes an element from the document
            var element = document.getElementById(elementId);
            element.parentNode.removeChild(element);
            clearInterval(timerInterval);
        }

        // if confirm button is clicked, remove dev message
        confirm.onclick = function() {
            removeElement('dev-msg')
        }

        // if dev message is not removed, decrease timer
         var timerInterval = setInterval(timerCounter, 1000);
    }
</script>