<div id="dev-msg" class="d-flex position-fixed w-100 h-100">
    <div id="content" class="content">
        <div class="head">
            <h5 class="text-center pb-3 pt-1"> Message from developer </h5>
        </div>
        <div class="body">
            <p class="description">
                <b> New update arrived: </b>
                <ul>
                    <li> Vue applications has been initialized and ready for implementation </li>
                    <li> Login page is ready to use except change the language </li>
                </ul>
                
                <b> Features under development now: </b>
                <ul>
                    <li> Change the language in login page will be available in a few hours </li>
                    <li> Signup page with google maps to get and search current user location coords with it </li>
                    <li> Password reset system for users who have forgotten their passwords </li>
                </ul>
            </p>
            <div id="confirm" class="button text-center"> Close </div>
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
        var confirm = document.getElementById('confirm');

        function removeElement(elementId) {
            // Removes an element from the document
            var element = document.getElementById(elementId);
            element.parentNode.removeChild(element);
        }

        // if confirm button is clicked, remove dev message
        confirm.onclick = function() {
            removeElement('dev-msg')
        }
    }
</script>